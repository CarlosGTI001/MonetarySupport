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
            INSERT INTO fixed_expenses (name, amount, frequency, every_days, account_id, start_date, end_date, active, note)
            VALUES (:name, :amount, :frequency, :every_days, :account_id, :start_date, :end_date, :active, :note)
        ');
        $stmt->execute([
            'name' => $data['name'],
            'amount' => $data['amount'],
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
        $start = $item['start_date'] ?: $today;

        if ($item['frequency'] === 'monthly') {
            $date = date('Y-m-d', strtotime(date('Y-m-01') . ' +1 month'));
            return $date;
        }

        if ($item['frequency'] === 'biweekly') {
            return date('Y-m-d', strtotime($today . ' +14 days'));
        }

        if ($item['frequency'] === 'custom' && !empty($item['every_days'])) {
            return date('Y-m-d', strtotime($today . ' +' . (int)$item['every_days'] . ' days'));
        }

        return null;
    }
}
