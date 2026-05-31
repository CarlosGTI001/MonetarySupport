<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class Financing
{
    public static function all(): array
    {
        $db = Database::getConnection();
        return $db->query('SELECT * FROM financings ORDER BY status ASC, name ASC')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM financings WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            INSERT INTO financings (
                name, installment_amount, frequency, total_payments, payments_made, start_date, end_date,
                status, total_paid, total_pending, next_date, note
            ) VALUES (
                :name, :installment_amount, :frequency, :total_payments, :payments_made, :start_date, :end_date,
                :status, :total_paid, :total_pending, :next_date, :note
            )
        ');
        $stmt->execute([
            'name' => $data['name'],
            'installment_amount' => $data['installment_amount'],
            'frequency' => $data['frequency'],
            'total_payments' => $data['total_payments'],
            'payments_made' => $data['payments_made'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
            'total_paid' => $data['total_paid'],
            'total_pending' => $data['total_pending'],
            'next_date' => $data['next_date'],
            'note' => $data['note'],
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            UPDATE financings
            SET name = :name,
                installment_amount = :installment_amount,
                frequency = :frequency,
                total_payments = :total_payments,
                payments_made = :payments_made,
                start_date = :start_date,
                end_date = :end_date,
                status = :status,
                total_paid = :total_paid,
                total_pending = :total_pending,
                next_date = :next_date,
                note = :note
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'installment_amount' => $data['installment_amount'],
            'frequency' => $data['frequency'],
            'total_payments' => $data['total_payments'],
            'payments_made' => $data['payments_made'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => $data['status'],
            'total_paid' => $data['total_paid'],
            'total_pending' => $data['total_pending'],
            'next_date' => $data['next_date'],
            'note' => $data['note'],
        ]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM financings WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
