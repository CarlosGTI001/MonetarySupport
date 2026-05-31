<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\FixedExpense;

class FixedExpensesController extends Controller
{
    public function index(): void
    {
        $items = FixedExpense::all();
        $this->render('fixed_expenses/index', ['items' => $items]);
    }

    public function create(): void
    {
        $accounts = Account::all();
        $this->render('fixed_expenses/form', ['item' => null, 'accounts' => $accounts]);
    }

    public function store(): void
    {
        FixedExpense::create($this->sanitize($_POST));
        redirect('?module=fixed_expenses');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $item = FixedExpense::find($id);
        $accounts = Account::all();
        $this->render('fixed_expenses/form', ['item' => $item, 'accounts' => $accounts]);
    }

    public function save(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        FixedExpense::update($id, $this->sanitize($_POST));
        redirect('?module=fixed_expenses');
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        FixedExpense::delete($id);
        redirect('?module=fixed_expenses');
    }

    private function sanitize(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'amount' => (float)($data['amount'] ?? 0),
            'frequency' => $data['frequency'] ?? 'monthly',
            'every_days' => !empty($data['every_days']) ? (int)$data['every_days'] : null,
            'account_id' => !empty($data['account_id']) ? (int)$data['account_id'] : null,
            'start_date' => $data['start_date'] ?: null,
            'end_date' => $data['end_date'] ?: null,
            'active' => isset($data['active']) ? 1 : 0,
            'currency' => $data['currency'] ?? 'DOP',
            'note' => trim($data['note'] ?? ''),
        ];
    }
}
