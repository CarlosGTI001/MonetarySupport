<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\SavingsRule;

class SavingsController extends Controller
{
    public function index(): void
    {
        $items = SavingsRule::all();
        $this->render('savings/index', ['items' => $items]);
    }

    public function create(): void
    {
        $accounts = Account::all();
        $this->render('savings/form', ['item' => null, 'accounts' => $accounts]);
    }

    public function store(): void
    {
        SavingsRule::create($this->sanitize($_POST));
        redirect('?module=savings');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $item = SavingsRule::find($id);
        $accounts = Account::all();
        $this->render('savings/form', ['item' => $item, 'accounts' => $accounts]);
    }

    public function update(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        SavingsRule::update($id, $this->sanitize($_POST));
        redirect('?module=savings');
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        SavingsRule::delete($id);
        redirect('?module=savings');
    }

    private function sanitize(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'mode' => $data['mode'] ?? 'percentage',
            'percent' => $data['percent'] !== '' ? (float)$data['percent'] : null,
            'amount' => $data['amount'] !== '' ? (float)$data['amount'] : null,
            'target_account_id' => !empty($data['target_account_id']) ? (int)$data['target_account_id'] : null,
            'priority' => (int)($data['priority'] ?? 0),
            'active' => isset($data['active']) ? 1 : 0,
            'note' => trim($data['note'] ?? ''),
        ];
    }
}
