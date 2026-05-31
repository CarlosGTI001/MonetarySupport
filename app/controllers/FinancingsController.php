<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Financing;

class FinancingsController extends Controller
{
    public function index(): void
    {
        $items = Financing::all();
        $this->render('financings/index', ['items' => $items]);
    }

    public function create(): void
    {
        $this->render('financings/form', ['item' => null]);
    }

    public function store(): void
    {
        Financing::create($this->sanitize($_POST));
        redirect('?module=financings');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $item = Financing::find($id);
        $this->render('financings/form', ['item' => $item]);
    }

    public function update(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        Financing::update($id, $this->sanitize($_POST));
        redirect('?module=financings');
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        Financing::delete($id);
        redirect('?module=financings');
    }

    private function sanitize(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'installment_amount' => (float)($data['installment_amount'] ?? 0),
            'frequency' => $data['frequency'] ?? 'monthly',
            'total_payments' => !empty($data['total_payments']) ? (int)$data['total_payments'] : null,
            'payments_made' => (int)($data['payments_made'] ?? 0),
            'start_date' => $data['start_date'] ?: null,
            'end_date' => $data['end_date'] ?: null,
            'status' => $data['status'] ?? 'activo',
            'total_paid' => (float)($data['total_paid'] ?? 0),
            'total_pending' => (float)($data['total_pending'] ?? 0),
            'next_date' => $data['next_date'] ?: null,
            'note' => trim($data['note'] ?? ''),
        ];
    }
}
