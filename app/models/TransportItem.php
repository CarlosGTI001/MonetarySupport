<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class TransportItem
{
    public static function all(): array
    {
        $db = Database::getConnection();
        return $db->query('SELECT * FROM transport_items ORDER BY id ASC')->fetchAll();
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            INSERT INTO transport_items (name, amount, active)
            VALUES (:name, :amount, :active)
        ');
        $stmt->execute([
            'name' => $data['name'],
            'amount' => $data['amount'],
            'active' => $data['active'],
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            UPDATE transport_items
            SET name = :name, amount = :amount, active = :active
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'amount' => $data['amount'],
            'active' => $data['active'],
        ]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM transport_items WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function dailyTotal(): float
    {
        $db = Database::getConnection();
        $stmt = $db->query('SELECT SUM(amount) as total FROM transport_items WHERE active = 1');
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0);
    }
}
