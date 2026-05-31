<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

class WorkExpense
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query('
            SELECT w.*, a.name as account_name
            FROM work_expenses w
            JOIN accounts a ON a.id = w.account_id
            ORDER BY date DESC, id DESC
        ');
        return $stmt->fetchAll();
    }

    public static function pendingTotal(): float
    {
        $db = Database::getConnection();
        $stmt = $db->query('SELECT SUM(amount) as total FROM work_expenses WHERE reimbursed = 0');
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0);
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM work_expenses WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            INSERT INTO work_expenses (date, account_id, concept, amount, project, reimbursed, note)
            VALUES (:date, :account_id, :concept, :amount, :project, :reimbursed, :note)
        ');
        $stmt->execute([
            'date' => $data['date'],
            'account_id' => $data['account_id'],
            'concept' => $data['concept'],
            'amount' => $data['amount'],
            'project' => $data['project'],
            'reimbursed' => $data['reimbursed'],
            'note' => $data['note'],
        ]);
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            UPDATE work_expenses
            SET date = :date,
                account_id = :account_id,
                concept = :concept,
                amount = :amount,
                project = :project,
                reimbursed = :reimbursed,
                note = :note
            WHERE id = :id
        ');
        $stmt->execute([
            'id' => $id,
            'date' => $data['date'],
            'account_id' => $data['account_id'],
            'concept' => $data['concept'],
            'amount' => $data['amount'],
            'project' => $data['project'],
            'reimbursed' => $data['reimbursed'],
            'note' => $data['note'],
        ]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('DELETE FROM work_expenses WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
