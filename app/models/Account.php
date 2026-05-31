<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Account
{
    public static function all(): array
    {
        $db = Database::getConnection();
        return $db->query('SELECT * FROM accounts ORDER BY active DESC, name ASC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM accounts WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            INSERT INTO accounts (name, type, currency, balance, purpose, active)
            VALUES (:name, :type, :currency, :balance, :purpose, :active)
        ');
        $stmt->execute([
            'name' => $data['name'],
            'type' => $data['type'],
            'currency' => $data['currency'],
            'balance' => $data['balance'],
            'purpose' => $data['purpose'],
            'active' => $data['active'],
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            UPDATE accounts
            SET name = :name,
                type = :type,
                currency = :currency,
                balance = :balance,
                purpose = :purpose,
                active = :active
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'type' => $data['type'],
            'currency' => $data['currency'],
            'balance' => $data['balance'],
            'purpose' => $data['purpose'],
            'active' => $data['active'],
        ]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM accounts WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public static function adjustBalance(int $id, float $delta): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('UPDATE accounts SET balance = balance + :delta WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'delta' => $delta,
        ]);
    }

    public static function totalsByCurrency(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query('SELECT currency, SUM(balance) as total FROM accounts WHERE active = 1 GROUP BY currency');
        $totals = [];
        foreach ($stmt->fetchAll() as $row) {
            $totals[$row['currency']] = (float)$row['total'];
        }
        return $totals;
    }
}
