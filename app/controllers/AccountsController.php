<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;

class AccountsController extends Controller
{
    public function index(): void
    {
        $accounts = Account::all();
        $this->render('accounts/index', ['accounts' => $accounts]);
    }

    public function create(): void
    {
        $this->render('accounts/form', ['account' => null]);
    }

    public function store(): void
    {
        Account::create($this->sanitize($_POST));
        redirect('?module=accounts');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $account = Account::find($id);
        $this->render('accounts/form', ['account' => $account]);
    }

    public function save(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        Account::update($id, $this->sanitize($_POST));
        redirect('?module=accounts');
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        Account::delete($id);
        redirect('?module=accounts');
    }

    private function sanitize(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'type' => trim($data['type'] ?? ''),
            'currency' => trim($data['currency'] ?? 'DOP'),
            'balance' => (float)($data['balance'] ?? 0),
            'purpose' => trim($data['purpose'] ?? ''),
            'active' => isset($data['active']) ? 1 : 0,
        ];
    }
}
