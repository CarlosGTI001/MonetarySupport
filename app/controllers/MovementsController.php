<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\Movement;
use App\Models\SavingsRule;

class MovementsController extends Controller
{
    public function index(): void
    {
        $movements = Movement::all();
        $accounts = Account::all();
        $incomeSuggestion = null;

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
        ]);
    }

    public function create(): void
    {
        $accounts = Account::all();
        $this->render('movements/form', [
            'movement' => null,
            'accounts' => $accounts,
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
        $this->render('movements/form', [
            'movement' => $movement,
            'accounts' => $accounts,
        ]);
    }

    public function update(): void
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
        ];
    }

    private function buildIncomeSuggestion(float $amount, array $accounts): array
    {
        $rules = SavingsRule::active();
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
