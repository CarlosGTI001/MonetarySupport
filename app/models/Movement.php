<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;
use Throwable;

class Movement
{
    public static function all(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query('
            SELECT m.*, ao.name as account_origin_name, ad.name as account_dest_name
            FROM movements m
            JOIN accounts ao ON ao.id = m.account_origin_id
            LEFT JOIN accounts ad ON ad.id = m.account_dest_id
            ORDER BY date DESC, id DESC
        ');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM movements WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $db = Database::getConnection();
        $db->beginTransaction();
        try {
            $stmt = $db->prepare('
                INSERT INTO movements (
                    date, account_origin_id, account_dest_id, fixed_expense_id, savings_rule_id, financing_id, type, category, concept, amount, currency,
                    reimbursable, reimbursed, note
                ) VALUES (
                    :date, :account_origin_id, :account_dest_id, :fixed_expense_id, :savings_rule_id, :financing_id, :type, :category, :concept, :amount, :currency,
                    :reimbursable, :reimbursed, :note
                )
            ');
            $stmt->execute([
                'date' => $data['date'],
                'account_origin_id' => $data['account_origin_id'],
                'account_dest_id' => $data['account_dest_id'],
                'fixed_expense_id' => $data['fixed_expense_id'],
                'savings_rule_id' => $data['savings_rule_id'],
                'financing_id' => $data['financing_id'] ?? null,
                'type' => $data['type'],
                'category' => $data['category'],
                'concept' => $data['concept'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'reimbursable' => $data['reimbursable'],
                'reimbursed' => $data['reimbursed'],
                'note' => $data['note'],
            ]);

            self::applyBalance($data);
            
            if (!empty($data['financing_id'])) {
                self::applyFinancingPayment((int)$data['financing_id'], (float)$data['amount']);
            }

            $db->commit();
            return (int)$db->lastInsertId();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private static function applyFinancingPayment(int $financingId, float $amount): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM financings WHERE id = :id');
        $stmt->execute(['id' => $financingId]);
        $f = $stmt->fetch();
        if (!$f) return;

        $newPaymentsMade = (int)$f['payments_made'] + 1;
        $newTotalPaid = (float)$f['total_paid'] + abs($amount);
        $newTotalPending = max(0, (float)$f['total_pending'] - abs($amount));
        
        $nextDate = $f['next_date'];
        if ($nextDate) {
            if ($f['frequency'] === 'monthly') {
                $nextDate = date('Y-m-d', strtotime($nextDate . ' +1 month'));
            } elseif ($f['frequency'] === 'biweekly') {
                $nextDate = date('Y-m-d', strtotime($nextDate . ' +14 days'));
            }
        }

        $stmt = $db->prepare('
            UPDATE financings 
            SET payments_made = :pm, total_paid = :tp, total_pending = :tpe, next_date = :nd,
                status = CASE WHEN :pm >= total_payments THEN "pagado" ELSE status END
            WHERE id = :id
        ');
        $stmt->execute([
            'pm' => $newPaymentsMade,
            'tp' => $newTotalPaid,
            'tpe' => $newTotalPending,
            'nd' => $nextDate,
            'id' => $financingId
        ]);
    }

    public static function delete(int $id): void
    {
        $db = Database::getConnection();
        $movement = self::find($id);
        if (!$movement) {
            return;
        }

        $db->beginTransaction();
        try {
            self::reverseBalance($movement);
            
            if (!empty($movement['financing_id'])) {
                self::reverseFinancingPayment((int)$movement['financing_id'], (float)$movement['amount']);
            }

            $stmt = $db->prepare('DELETE FROM movements WHERE id = :id');
            $stmt->execute(['id' => $id]);
            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private static function reverseFinancingPayment(int $financingId, float $amount): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM financings WHERE id = :id');
        $stmt->execute(['id' => $financingId]);
        $f = $stmt->fetch();
        if (!$f) return;

        $newPaymentsMade = max(0, (int)$f['payments_made'] - 1);
        $newTotalPaid = max(0, (float)$f['total_paid'] - abs($amount));
        $newTotalPending = (float)$f['total_pending'] + abs($amount);
        
        $prevDate = $f['next_date'];
        if ($prevDate) {
            if ($f['frequency'] === 'monthly') {
                $prevDate = date('Y-m-d', strtotime($prevDate . ' -1 month'));
            } elseif ($f['frequency'] === 'biweekly') {
                $prevDate = date('Y-m-d', strtotime($prevDate . ' -14 days'));
            }
        }

        $stmt = $db->prepare('
            UPDATE financings 
            SET payments_made = :pm, total_paid = :tp, total_pending = :tpe, next_date = :nd,
                status = CASE WHEN :pm < total_payments THEN "activo" ELSE status END
            WHERE id = :id
        ');
        $stmt->execute([
            'pm' => $newPaymentsMade,
            'tp' => $newTotalPaid,
            'tpe' => $newTotalPending,
            'nd' => $prevDate,
            'id' => $financingId
        ]);
    }

    public static function update(int $id, array $data): void
    {
        $db = Database::getConnection();
        $existing = self::find($id);
        if (!$existing) {
            return;
        }

        $db->beginTransaction();
        try {
            self::reverseBalance($existing);
            if (!empty($existing['financing_id'])) {
                self::reverseFinancingPayment((int)$existing['financing_id'], (float)$existing['amount']);
            }

            $stmt = $db->prepare('
                UPDATE movements
                SET date = :date,
                    account_origin_id = :account_origin_id,
                    account_dest_id = :account_dest_id,
                    fixed_expense_id = :fixed_expense_id,
                    savings_rule_id = :savings_rule_id,
                    financing_id = :financing_id,
                    type = :type,
                    category = :category,
                    concept = :concept,
                    amount = :amount,
                    currency = :currency,
                    reimbursable = :reimbursable,
                    reimbursed = :reimbursed,
                    note = :note
                WHERE id = :id
            ');
            $stmt->execute([
                'id' => $id,
                'date' => $data['date'],
                'account_origin_id' => $data['account_origin_id'],
                'account_dest_id' => $data['account_dest_id'],
                'fixed_expense_id' => $data['fixed_expense_id'],
                'savings_rule_id' => $data['savings_rule_id'],
                'financing_id' => $data['financing_id'] ?? null,
                'type' => $data['type'],
                'category' => $data['category'],
                'concept' => $data['concept'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'reimbursable' => $data['reimbursable'],
                'reimbursed' => $data['reimbursed'],
                'note' => $data['note'],
            ]);
            self::applyBalance($data);
            if (!empty($data['financing_id'])) {
                self::applyFinancingPayment((int)$data['financing_id'], (float)$data['amount']);
            }
            $db->commit();
        } catch (Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private static function reverseBalance(array $movement): void
    {
        $data = [
            'account_origin_id' => $movement['account_origin_id'],
            'account_dest_id' => $movement['account_dest_id'],
            'type' => $movement['type'],
            'amount' => (float)$movement['amount'],
        ];

        $amount = (float)$data['amount'];
        $type = $data['type'];

        if ($type === 'ingreso') {
            Account::adjustBalance((int)$data['account_origin_id'], -$amount);
            return;
        }

        if ($type === 'gasto' || $type === 'gasto_laboral') {
            Account::adjustBalance((int)$data['account_origin_id'], $amount);
            return;
        }

        if ($type === 'transferencia') {
            Account::adjustBalance((int)$data['account_origin_id'], $amount);
            if (!empty($data['account_dest_id'])) {
                Account::adjustBalance((int)$data['account_dest_id'], -$amount);
            }
            return;
        }

        if ($type === 'ajuste') {
            Account::adjustBalance((int)$data['account_origin_id'], -$amount);
        }
    }

    public static function monthlyExpenses(string $month): float
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            SELECT SUM(amount) as total
            FROM movements
            WHERE type IN ("gasto", "gasto_laboral")
            AND strftime("%Y-%m", date) = :month
        ');
        $stmt->execute(['month' => $month]);
        $row = $stmt->fetch();
        return (float)($row['total'] ?? 0);
    }

    public static function expensesByCategory(string $month): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            SELECT category, SUM(amount) as total
            FROM movements
            WHERE type IN ("gasto", "gasto_laboral")
            AND strftime("%Y-%m", date) = :month
            GROUP BY category
            ORDER BY total DESC
        ');
        $stmt->execute(['month' => $month]);
        return $stmt->fetchAll();
    }
}
