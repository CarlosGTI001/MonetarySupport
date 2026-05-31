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
            SELECT s.*, a.name as account_name
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
            SELECT s.*, a.name as account_name
            FROM savings_rules s
            LEFT JOIN accounts a ON a.id = s.target_account_id
            WHERE s.active = 1
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
            INSERT INTO savings_rules (name, mode, percent, amount, target_account_id, priority, active, note)
            VALUES (:name, :mode, :percent, :amount, :target_account_id, :priority, :active, :note)
        ');
        $stmt->execute([
            'name' => $data['name'],
            'mode' => $data['mode'],
            'percent' => $data['percent'],
            'amount' => $data['amount'],
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
}
