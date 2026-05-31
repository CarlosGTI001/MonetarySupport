<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class FixedExpense
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query('
            SELECT f.*, a.name as account_name
            FROM fixed_expenses f
            LEFT JOIN accounts a ON a.id = f.account_id
            ORDER BY active DESC, name ASC
        ');
        return $stmt->fetchAll();
    }

    public static function active(): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            SELECT f.*, a.name as account_name, a.currency as account_currency
            FROM fixed_expenses f
            LEFT JOIN accounts a ON a.id = f.account_id
            WHERE f.active = 1
            AND (f.end_date IS NULL OR f.end_date >= :today)
            ORDER BY f.name ASC
        ');
        $stmt->execute(['today' => date('Y-m-d')]);
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM fixed_expenses WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            INSERT INTO fixed_expenses (name, amount, currency, frequency, every_days, account_id, start_date, end_date, active, note)
            VALUES (:name, :amount, :currency, :frequency, :every_days, :account_id, :start_date, :end_date, :active, :note)
        ');
        $stmt->execute([
            'name' => $data['name'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'DOP',
            'frequency' => $data['frequency'],
            'every_days' => $data['every_days'],
            'account_id' => $data['account_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'active' => $data['active'],
            'note' => $data['note'],
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            UPDATE fixed_expenses
            SET name = :name,
                amount = :amount,
                currency = :currency,
                frequency = :frequency,
                every_days = :every_days,
                account_id = :account_id,
                start_date = :start_date,
                end_date = :end_date,
                active = :active,
                note = :note
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'DOP',
            'frequency' => $data['frequency'],
            'every_days' => $data['every_days'],
            'account_id' => $data['account_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'active' => $data['active'],
            'note' => $data['note'],
        ]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM fixed_expenses WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function pendingForCurrentPeriod(): array
    {
        $db = Database::getConnection();
        $today = date('Y-m-d');
        $stmt = $db->prepare('
            SELECT f.*, a.name as account_name, a.currency as account_currency
            FROM fixed_expenses f
            LEFT JOIN accounts a ON a.id = f.account_id
            WHERE f.active = 1
            AND (f.end_date IS NULL OR f.end_date >= :today)
            ORDER BY f.name ASC
        ');
        $stmt->execute(['today' => $today]);
        $items = $stmt->fetchAll();

        $pending = [];
        foreach ($items as $item) {
            if (!empty($item['start_date']) && $item['start_date'] > $today) {
                continue;
            }

            $period = self::currentPeriod($item, $today, $db);
            if (!$period) {
                continue;
            }
            [$periodStart, $periodEnd, $label] = $period;

            if (self::hasMovementInRange((int)$item['id'], $periodStart, $periodEnd, $db)) {
                continue;
            }

            $item['period_start'] = $periodStart;
            $item['period_end'] = $periodEnd;
            $item['period_label'] = $label;
            $pending[] = $item;
        }

        return $pending;
    }

    public static function upcoming(string $untilDate): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            SELECT * FROM fixed_expenses
            WHERE active = 1
            AND (end_date IS NULL OR end_date >= :today)
        ');
        $stmt->execute(['today' => date('Y-m-d')]);
        $items = $stmt->fetchAll();

        $upcoming = [];
        foreach ($items as $item) {
            $nextDate = self::nextDueDate($item);
            if ($nextDate && $nextDate <= $untilDate) {
                $item['next_date'] = $nextDate;
                $upcoming[] = $item;
            }
        }

        usort($upcoming, fn ($a, $b) => strcmp($a['next_date'], $b['next_date']));
        return $upcoming;
    }

    public static function nextDueDate(array $item): ?string
    {
        $today = date('Y-m-d');
        $day = (int)date('d');

        if ($item['frequency'] === 'monthly') {
            $dueDay = 1;
            if (!empty($item['start_date'])) {
                $dueDay = (int)date('d', strtotime($item['start_date']));
            }
            
            if ($day < $dueDay) {
                return date('Y-m-') . sprintf('%02d', $dueDay);
            } else {
                return date('Y-m-d', strtotime(date('Y-m-') . sprintf('%02d', $dueDay) . ' +1 month'));
            }
        }

        if ($item['frequency'] === 'biweekly') {
            if ($day < 15) {
                return date('Y-m-15');
            } elseif ($day < 30) {
                return date('Y-m-t');
            } else {
                return date('Y-m-15', strtotime('+1 month'));
            }
        }

        if ($item['frequency'] === 'custom' && !empty($item['every_days'])) {
            return date('Y-m-d', strtotime($today . ' +' . (int)$item['every_days'] . ' days'));
        }

        return null;
    }

    private static function currentPeriod(array $item, string $today, $db): ?array
    {
        $periodStart = null;
        $periodEnd = null;
        $label = null;

        if ($item['frequency'] === 'monthly') {
            $periodStart = date('Y-m-01', strtotime($today));
            $periodEnd = date('Y-m-t', strtotime($today));
        } elseif ($item['frequency'] === 'biweekly') {
            $day = (int)date('d', strtotime($today));
            if ($day <= 15) {
                $periodStart = date('Y-m-01', strtotime($today));
                $periodEnd = date('Y-m-15', strtotime($today));
            } else {
                $periodStart = date('Y-m-16', strtotime($today));
                $periodEnd = date('Y-m-t', strtotime($today));
            }
        } elseif ($item['frequency'] === 'custom' && !empty($item['every_days'])) {
            $last = self::lastMovementDate((int)$item['id'], $db);
            if ($last) {
                $base = $last;
            } elseif (!empty($item['start_date'])) {
                $base = $item['start_date'];
            } else {
                $base = date('Y-m-d', strtotime($today . ' -' . (int)$item['every_days'] . ' days'));
            }
            $due = date('Y-m-d', strtotime($base . ' +' . (int)$item['every_days'] . ' days'));
            if ($due > $today) {
                return null;
            }
            return [$due, $due, 'Vence ' . $due];
        } else {
            return null;
        }

        if (!empty($item['start_date']) && $item['start_date'] > $periodEnd) {
            return null;
        }
        if (!empty($item['end_date']) && $item['end_date'] < $periodStart) {
            return null;
        }
        if (!empty($item['start_date']) && $item['start_date'] > $periodStart) {
            $periodStart = $item['start_date'];
        }
        if (!empty($item['end_date']) && $item['end_date'] < $periodEnd) {
            $periodEnd = $item['end_date'];
        }

        $label = date('d/m', strtotime($periodStart)) . ' - ' . date('d/m', strtotime($periodEnd));
        return [$periodStart, $periodEnd, $label];
    }

    private static function hasMovementInRange(int $id, string $start, string $end, $db): bool
    {
        $stmt = $db->prepare('
            SELECT 1 FROM movements
            WHERE fixed_expense_id = :id
            AND date >= :start
            AND date <= :end
            LIMIT 1
        ');
        $stmt->execute([
            'id' => $id,
            'start' => $start,
            'end' => $end,
        ]);
        return (bool)$stmt->fetchColumn();
    }

    private static function lastMovementDate(int $id, $db): ?string
    {
        $stmt = $db->prepare('SELECT MAX(date) as last_date FROM movements WHERE fixed_expense_id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        if (!$row || empty($row['last_date'])) {
            return null;
        }
        return $row['last_date'];
    }
}
