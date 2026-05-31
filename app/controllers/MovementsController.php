<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\FixedExpense;
use App\Models\Movement;
use App\Models\SavingsRule;

class MovementsController extends Controller
{
    public function index(): void
    {
        $movements = Movement::all();
        $accounts = Account::all();
        $incomeSuggestion = null;
        $pendingFixedExpenses = FixedExpense::pendingForCurrentPeriod();

        if (!empty($_GET['income_id'])) {
            $movement = Movement::find((int)$_GET['income_id']);
            if ($movement && $movement['type'] === 'ingreso') {
                $incomeSuggestion = $this->buildIncomeSuggestion((float)$movement['amount'], $accounts);
            }
        }

        $this->render('movements/index', [
            'movements' => $movements,
            'accounts' => $accounts,
            'incomeSuggestion' => $incomeSuggestion,
            'pendingFixedExpenses' => $pendingFixedExpenses,
        ]);
    }

    public function create(): void
    {
        $accounts = Account::all();
        $fixedExpenses = FixedExpense::active();
        $savingsRules = SavingsRule::fixedActive();
        $movement = null;

        if (!empty($_GET['financing_id'])) {
            $db = \App\Core\Database::getConnection();
            $stmt = $db->prepare('SELECT * FROM financings WHERE id = :id');
            $stmt->execute(['id' => (int)$_GET['financing_id']]);
            $financing = $stmt->fetch();
            if ($financing) {
                $movement = [
                    'date' => current_date(),
                    'account_origin_id' => null,
                    'account_dest_id' => null,
                    'type' => 'gasto',
                    'category' => 'Financiamiento',
                    'concept' => 'Pago ' . $financing['name'],
                    'amount' => (float)$financing['installment_amount'],
                    'currency' => 'DOP',
                    'reimbursable' => 0,
                    'reimbursed' => 0,
                    'note' => '',
                    'financing_id' => (int)$financing['id'],
                ];
            }
        }

        if (!empty($_GET['fixed_expense_id'])) {
            $fixedExpense = FixedExpense::find((int)$_GET['fixed_expense_id']);
            if ($fixedExpense) {
                $currency = 'DOP';
                if (!empty($fixedExpense['account_id'])) {
                    $account = Account::find((int)$fixedExpense['account_id']);
                    if (!empty($account['currency'])) {
                        $currency = $account['currency'];
                    }
                }
                $movement = [
                    'date' => current_date(),
                    'account_origin_id' => $fixedExpense['account_id'] ?? null,
                    'account_dest_id' => null,
                    'type' => 'gasto',
                    'category' => 'Gasto fijo',
                    'concept' => $fixedExpense['name'],
                    'amount' => $fixedExpense['amount'],
                    'currency' => $currency,
                    'reimbursable' => 0,
                    'reimbursed' => 0,
                    'note' => $fixedExpense['note'] ?? '',
                    'fixed_expense_id' => (int)$fixedExpense['id'],
                ];
            }
        }

        if (!empty($_GET['savings_rule_id'])) {
            $rule = SavingsRule::find((int)$_GET['savings_rule_id']);
            if ($rule && $rule['mode'] === 'fixed') {
                $currency = 'DOP';
                if (!empty($rule['target_account_id'])) {
                    $account = Account::find((int)$rule['target_account_id']);
                    if (!empty($account['currency'])) {
                        $currency = $account['currency'];
                    }
                }
                $movement = [
                    'date' => current_date(),
                    'account_origin_id' => null,
                    'account_dest_id' => $rule['target_account_id'] ?? null,
                    'type' => 'transferencia',
                    'category' => 'Ahorro',
                    'concept' => $rule['name'],
                    'amount' => $rule['amount'] ?? 0,
                    'currency' => $currency,
                    'reimbursable' => 0,
                    'reimbursed' => 0,
                    'note' => $rule['note'] ?? '',
                    'savings_rule_id' => (int)$rule['id'],
                ];
            }
        }
        $this->render('movements/form', [
            'movement' => $movement,
            'accounts' => $accounts,
            'fixedExpenses' => $fixedExpenses,
            'savingsRules' => $savingsRules,
        ]);
    }

    public function store(): void
    {
        $data = $this->sanitize($_POST);
        $movementId = Movement::create($data);
        if ($data['type'] === 'ingreso') {
            redirect('?module=movements&action=index&income_id=' . $movementId);
        }
        redirect('?module=movements');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $movement = Movement::find($id);
        $accounts = Account::all();
        $fixedExpenses = FixedExpense::active();
        $savingsRules = SavingsRule::fixedActive();
        $this->render('movements/form', [
            'movement' => $movement,
            'accounts' => $accounts,
            'fixedExpenses' => $fixedExpenses,
            'savingsRules' => $savingsRules,
        ]);
    }

    public function applyFixed(): void
    {
        $id = (int)($_POST['fixed_expense_id'] ?? 0);
        $fixedExpense = $id ? FixedExpense::find($id) : null;
        if (!$fixedExpense || empty($fixedExpense['active'])) {
            redirect('?module=movements');
        }

        $pending = FixedExpense::pendingForCurrentPeriod();
        $pendingIds = array_map(static fn ($item) => (int)$item['id'], $pending);
        if (!in_array((int)$fixedExpense['id'], $pendingIds, true)) {
            redirect('?module=movements');
        }

        if (empty($fixedExpense['account_id'])) {
            redirect('?module=movements&action=create&fixed_expense_id=' . (int)$fixedExpense['id']);
        }

        $account = Account::find((int)$fixedExpense['account_id']);
        $movement = [
            'date' => current_date(),
            'account_origin_id' => (int)$fixedExpense['account_id'],
            'account_dest_id' => null,
            'fixed_expense_id' => (int)$fixedExpense['id'],
            'savings_rule_id' => null,
            'financing_id' => null,
            'apply_dgii_tax' => 0,
            'type' => 'gasto',
            'category' => 'Gasto fijo',
            'concept' => $fixedExpense['name'],
            'amount' => (float)$fixedExpense['amount'],
            'currency' => $account['currency'] ?? 'DOP',
            'reimbursable' => 0,
            'reimbursed' => 0,
            'note' => $fixedExpense['note'] ?? '',
        ];
        Movement::create($movement);
        redirect('?module=movements');
    }

    public function save(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        Movement::update($id, $this->sanitize($_POST));
        redirect('?module=movements');
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        Movement::delete($id);
        redirect('?module=movements');
    }

    private function sanitize(array $data): array
    {
        $type = $data['type'] ?? 'gasto';
        $amount = (float)($data['amount'] ?? 0);
        if ($type === 'ajuste' && ($data['adjust_sign'] ?? 'add') === 'subtract') {
            $amount = $amount * -1;
        }

        return [
            'date' => $data['date'] ?? current_date(),
            'account_origin_id' => (int)($data['account_origin_id'] ?? 0),
            'account_dest_id' => !empty($data['account_dest_id']) ? (int)$data['account_dest_id'] : null,
            'type' => $type,
            'category' => trim($data['category'] ?? ''),
            'concept' => trim($data['concept'] ?? ''),
            'amount' => $amount,
            'currency' => trim($data['currency'] ?? 'DOP'),
            'reimbursable' => isset($data['reimbursable']) ? 1 : 0,
            'reimbursed' => isset($data['reimbursed']) ? 1 : 0,
            'note' => trim($data['note'] ?? ''),
            'fixed_expense_id' => !empty($data['fixed_expense_id']) ? (int)$data['fixed_expense_id'] : null,
            'financing_id' => !empty($data['financing_id']) ? (int)$data['financing_id'] : null,
            'apply_dgii_tax' => !empty($data['apply_dgii_tax']) ? 1 : 0,
        ];
    }

    private function buildIncomeSuggestion(float $amount, array $accounts): array
    {
        $rules = SavingsRule::activeForIncome();
        $suggestions = [];
        $committed = 0.0;

        foreach ($rules as $rule) {
            if ($rule['mode'] === 'percentage') {
                $value = $amount * ((float)$rule['percent'] / 100);
            } elseif ($rule['mode'] === 'fixed') {
                $value = (float)$rule['amount'];
            } else {
                continue;
            }
            $committed += $value;
            $suggestions[] = [
                'name' => $rule['name'],
                'account' => $rule['account_name'] ?? 'Sin cuenta',
                'amount' => $value,
            ];
        }

        $remaining = max(0, $amount - $committed);
        $remainderRules = array_values(array_filter($rules, fn ($r) => $r['mode'] === 'remainder'));
        $remainderSplit = [];
        foreach ($remainderRules as $rule) {
            $percent = (float)$rule['percent'] ?: 50;
            $value = $remaining * ($percent / 100);
            $remainderSplit[] = [
                'name' => $rule['name'],
                'account' => $rule['account_name'] ?? 'Sin cuenta',
                'amount' => $value,
            ];
        }

        return [
            'total' => $amount,
            'committed' => $committed,
            'free' => max(0, $amount - $committed),
            'suggestions' => $suggestions,
            'remainder' => $remainderSplit,
            'accountTargets' => $this->targetSummary($suggestions, $remainderSplit, $accounts),
        ];
    }

    private function targetSummary(array $suggestions, array $remainderSplit, array $accounts): array
    {
        $targets = [
            'Banco Santa Cruz' => 0,
            'Efectivo' => 0,
            'Qik' => 0,
            'BHD' => 0,
            'Popular' => 0,
            'Lafise pesos' => 0,
        ];

        foreach (array_merge($suggestions, $remainderSplit) as $item) {
            if (array_key_exists($item['account'], $targets)) {
                $targets[$item['account']] += $item['amount'];
            }
        }

        return $targets;
    }
}
