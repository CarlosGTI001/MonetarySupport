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
                    date, account_origin_id, account_dest_id, fixed_expense_id, savings_rule_id, financing_id, apply_dgii_tax, exchange_rate, type, category, concept, amount, currency,
                    reimbursable, reimbursed, note
                ) VALUES (
                    :date, :account_origin_id, :account_dest_id, :fixed_expense_id, :savings_rule_id, :financing_id, :apply_dgii_tax, :exchange_rate, :type, :category, :concept, :amount, :currency,
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
                'apply_dgii_tax' => $data['apply_dgii_tax'] ?? 0,
                'exchange_rate' => $data['exchange_rate'] ?? null,
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
        $db = Database::getConnection();
        $originAccount = Account::find((int)$movement['account_origin_id']);
        $destAccount = !empty($movement['account_dest_id']) ? Account::find((int)$movement['account_dest_id']) : null;

        $amount = (float)$movement['amount'];
        $type = $movement['type'];
        $movCurrency = $movement['currency'];
        $applyTax = (bool)($movement['apply_dgii_tax'] ?? false);
        $customRate = !empty($movement['exchange_rate']) ? (float)$movement['exchange_rate'] : null;

        // 1. Reverse Origin Account
        $originAdjAmount = convert_currency($amount, $movCurrency, $originAccount['currency'], $customRate);

        if ($type === 'ingreso') {
            Account::adjustBalance((int)$originAccount['id'], -$originAdjAmount);
        } elseif ($type === 'ajuste') {
            Account::adjustBalance((int)$originAccount['id'], -$originAdjAmount);
        } else {
            // Gasto, Gasto Laboral or Transferencia
            Account::adjustBalance((int)$originAccount['id'], $originAdjAmount);
            
            // Reverse Tax if applied (only for outgoing)
            if ($applyTax) {
                $tax = abs($originAdjAmount) * 0.0015;
                Account::adjustBalance((int)$originAccount['id'], $tax);
            }
        }

        // 2. Reverse Destination Account (if it was a transfer)
        if ($destAccount && $type !== 'ajuste') {
            $destAdjAmount = convert_currency($amount, $movCurrency, $destAccount['currency'], $customRate);
            Account::adjustBalance((int)$destAccount['id'], -$destAdjAmount);
        }
    }

    private static function applyBalance(array $data): void
    {
        $originAccount = Account::find((int)$data['account_origin_id']);
        $destAccount = !empty($data['account_dest_id']) ? Account::find((int)$data['account_dest_id']) : null;

        $amount = (float)$data['amount'];
        $type = $data['type'];
        $movCurrency = $data['currency'];
        $applyTax = (bool)($data['apply_dgii_tax'] ?? false);
        $customRate = !empty($data['exchange_rate']) ? (float)$data['exchange_rate'] : null;

        // 1. Apply to Origin Account
        $originAdjAmount = convert_currency($amount, $movCurrency, $originAccount['currency'], $customRate);

        if ($type === 'ingreso') {
            Account::adjustBalance((int)$originAccount['id'], $originAdjAmount);
        } elseif ($type === 'ajuste') {
            Account::adjustBalance((int)$originAccount['id'], $originAdjAmount);
        } else {
            // Gasto, Gasto Laboral or Transferencia
            Account::adjustBalance((int)$originAccount['id'], -$originAdjAmount);
            
            // Apply Tax (only for outgoing)
            if ($applyTax) {
                $tax = abs($originAdjAmount) * 0.0015;
                Account::adjustBalance((int)$originAccount['id'], -$tax);
            }
        }

        // 2. Apply to Destination Account (if present and NOT an adjustment)
        if ($destAccount && $type !== 'ajuste') {
            $destAdjAmount = convert_currency($amount, $movCurrency, $destAccount['currency'], $customRate);
            Account::adjustBalance((int)$destAccount['id'], $destAdjAmount);
        }
    }

    public static function monthlyExpenses(string $month): float
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            SELECT amount, currency
            FROM movements
            WHERE type IN ("gasto", "gasto_laboral")
            AND strftime("%Y-%m", date) = :month
        ');
        $stmt->execute(['month' => $month]);
        $rows = $stmt->fetchAll();
        
        $total = 0.0;
        foreach ($rows as $row) {
            $amt = (float)$row['amount'];
            if ($row['currency'] === 'USD') {
                $amt = convert_currency($amt, 'USD', 'DOP');
            }
            $total += $amt;
        }
        return $total;
    }

    public static function expensesByCategory(string $month): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare('
            SELECT category, amount, currency
            FROM movements
            WHERE type IN ("gasto", "gasto_laboral")
            AND strftime("%Y-%m", date) = :month
        ');
        $stmt->execute(['month' => $month]);
        $rows = $stmt->fetchAll();
        
        $categories = [];
        foreach ($rows as $row) {
            $cat = $row['category'] ?: 'Sin categoría';
            $amt = (float)$row['amount'];
            if ($row['currency'] === 'USD') {
                $amt = convert_currency($amt, 'USD', 'DOP');
            }
            
            if (!isset($categories[$cat])) {
                $categories[$cat] = 0.0;
            }
            $categories[$cat] += $amt;
        }
        
        $result = [];
        foreach ($categories as $cat => $total) {
            $result[] = ['category' => $cat, 'total' => $total];
        }
        
        usort($result, fn($a, $b) => $b['total'] <=> $a['total']);
        return $result;
    }
}
