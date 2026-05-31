<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class SavingsRule
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query('
            SELECT s.*, a.name as account_name, a.currency as account_currency
            FROM savings_rules s
            LEFT JOIN accounts a ON a.id = s.target_account_id
            ORDER BY s.active DESC, s.priority ASC
        ');
        return $stmt->fetchAll();
    }

    public static function active(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query('
            SELECT s.*, a.name as account_name, a.currency as account_currency
            FROM savings_rules s
            LEFT JOIN accounts a ON a.id = s.target_account_id
            WHERE s.active = 1
            ORDER BY s.priority ASC
        ');
        return $stmt->fetchAll();
    }

    public static function activeForIncome(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query('
            SELECT s.*, a.name as account_name, a.currency as account_currency
            FROM savings_rules s
            LEFT JOIN accounts a ON a.id = s.target_account_id
            WHERE s.active = 1
            AND (
                s.mode != "fixed"
                OR (s.frequency IS NULL OR s.frequency = "" OR s.frequency = "per_income")
            )
            ORDER BY s.priority ASC
        ');
        return $stmt->fetchAll();
    }

    public static function fixedActive(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query('
            SELECT s.*, a.name as account_name, a.currency as account_currency
            FROM savings_rules s
            LEFT JOIN accounts a ON a.id = s.target_account_id
            WHERE s.active = 1
            AND s.mode = "fixed"
            ORDER BY s.priority ASC
        ');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM savings_rules WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            INSERT INTO savings_rules (name, mode, percent, amount, frequency, target_account_id, priority, active, note)
            VALUES (:name, :mode, :percent, :amount, :frequency, :target_account_id, :priority, :active, :note)
        ');
        $stmt->execute([
            'name' => $data['name'],
            'mode' => $data['mode'],
            'percent' => $data['percent'],
            'amount' => $data['amount'],
            'frequency' => $data['frequency'],
            'target_account_id' => $data['target_account_id'],
            'priority' => $data['priority'],
            'active' => $data['active'],
            'note' => $data['note'],
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            UPDATE savings_rules
            SET name = :name,
                mode = :mode,
                percent = :percent,
                amount = :amount,
                frequency = :frequency,
                target_account_id = :target_account_id,
                priority = :priority,
                active = :active,
                note = :note
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'mode' => $data['mode'],
            'percent' => $data['percent'],
            'amount' => $data['amount'],
            'frequency' => $data['frequency'],
            'target_account_id' => $data['target_account_id'],
            'priority' => $data['priority'],
            'active' => $data['active'],
            'note' => $data['note'],
        ]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM savings_rules WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function pendingForCurrentPeriod(): array
    {
        $db = Database::getConnection();
        $today = date('Y-m-d');
        $stmt = $db->prepare('
            SELECT s.*, a.name as account_name, a.currency as account_currency
            FROM savings_rules s
            LEFT JOIN accounts a ON a.id = s.target_account_id
            WHERE s.active = 1
            AND s.mode = "fixed"
            AND s.frequency IN ("monthly", "biweekly")
            ORDER BY s.priority ASC
        ');
        $stmt->execute();
        $items = $stmt->fetchAll();

        $pending = [];
        foreach ($items as $item) {
            $period = self::currentPeriod($item['frequency'], $today);
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

    private static function currentPeriod(string $frequency, string $today): ?array
    {
        if ($frequency === 'monthly') {
            $start = date('Y-m-01', strtotime($today));
            $end = date('Y-m-t', strtotime($today));
            $label = date('d/m', strtotime($start)) . ' - ' . date('d/m', strtotime($end));
            return [$start, $end, $label];
        }

        if ($frequency === 'biweekly') {
            $day = (int)date('d', strtotime($today));
            if ($day <= 15) {
                $start = date('Y-m-01', strtotime($today));
                $end = date('Y-m-15', strtotime($today));
            } else {
                $start = date('Y-m-16', strtotime($today));
                $end = date('Y-m-t', strtotime($today));
            }
            $label = date('d/m', strtotime($start)) . ' - ' . date('d/m', strtotime($end));
            return [$start, $end, $label];
        }

        return null;
    }

    private static function hasMovementInRange(int $id, string $start, string $end, $db): bool
    {
        $stmt = $db->prepare('
            SELECT 1 FROM movements
            WHERE savings_rule_id = :id
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
}
