<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\WorkExpense;

class WorkExpensesController extends Controller
{
    public function index(): void
    {
        $items = WorkExpense::all();
        $pendingTotal = WorkExpense::pendingTotal();
        $this->render('work_expenses/index', [
            'items' => $items,
            'pendingTotal' => $pendingTotal,
        ]);
    }

    public function create(): void
    {
        $accounts = Account::all();
        $this->render('work_expenses/form', ['item' => null, 'accounts' => $accounts]);
    }

    public function store(): void
    {
        WorkExpense::create($this->sanitize($_POST));
        redirect('?module=work_expenses');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $item = WorkExpense::find($id);
        $accounts = Account::all();
        $this->render('work_expenses/form', ['item' => $item, 'accounts' => $accounts]);
    }

    public function save(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        WorkExpense::update($id, $this->sanitize($_POST));
        redirect('?module=work_expenses');
    }

    public function markAsReimbursed(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        $expense = WorkExpense::find($id);
        
        if ($expense && !$expense['reimbursed']) {
            $db = \App\Core\Database::getConnection();
            $db->beginTransaction();
            try {
                // 1. Marcar como reembolsado
                $data = $expense;
                $data['reimbursed'] = 1;
                WorkExpense::update($id, $data);

                // 2. Crear movimiento de ingreso
                \App\Models\Movement::create([
                    'date' => current_date(),
                    'account_origin_id' => $expense['account_id'],
                    'account_dest_id' => null,
                    'fixed_expense_id' => null,
                    'savings_rule_id' => null,
                    'type' => 'ingreso',
                    'category' => 'Reembolso Laboral',
                    'concept' => 'Reembolso: ' . $expense['concept'],
                    'amount' => (float)$expense['amount'],
                    'currency' => 'DOP', // Asumiendo DOP por defecto o podrías buscar la de la cuenta
                    'reimbursable' => 0,
                    'reimbursed' => 0,
                    'note' => 'Generado automáticamente desde Gastos Laborales',
                ]);

                $db->commit();
            } catch (\Throwable $e) {
                $db->rollBack();
                // Manejar error si es necesario
            }
        }
        
        redirect('?module=work_expenses');
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        WorkExpense::delete($id);
        redirect('?module=work_expenses');
    }

    private function sanitize(array $data): array
    {
        return [
            'date' => $data['date'] ?? current_date(),
            'account_id' => (int)($data['account_id'] ?? 0),
            'concept' => trim($data['concept'] ?? ''),
            'amount' => (float)($data['amount'] ?? 0),
            'project' => trim($data['project'] ?? ''),
            'reimbursed' => isset($data['reimbursed']) ? 1 : 0,
            'note' => trim($data['note'] ?? ''),
        ];
    }
}
