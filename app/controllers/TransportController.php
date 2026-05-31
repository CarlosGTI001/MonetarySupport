<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\QuickParser;
use App\Models\Account;
use App\Models\Movement;
use App\Models\TransportItem;

class TransportController extends Controller
{
    public function index(): void
    {
        $message = $_GET['message'] ?? null;
        $status = $_GET['status'] ?? 'success';
        $this->render('transport/index', $this->buildIndexData([
            'message' => $message,
            'status' => $status,
        ]));
    }

    public function create(): void
    {
        $this->render('transport/form', ['item' => null]);
    }

    public function store(): void
    {
        TransportItem::create($this->sanitize($_POST));
        redirect('?module=transport');
    }

    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $items = TransportItem::all();
        $item = null;
        foreach ($items as $row) {
            if ((int)$row['id'] === $id) {
                $item = $row;
                break;
            }
        }
        $this->render('transport/form', ['item' => $item]);
    }

    public function save(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        TransportItem::update($id, $this->sanitize($_POST));
        redirect('?module=transport');
    }

    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        TransportItem::delete($id);
        redirect('?module=transport');
    }

    public function logDaily(): void
    {
        $accountId = (int)($_POST['account_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $date = $_POST['date'] ?? current_date();
        $concept = trim($_POST['concept'] ?? 'Transporte diario');
        $note = trim($_POST['note'] ?? '');

        if ($accountId <= 0 || $amount <= 0) {
            redirect('?module=transport&status=error&message=Datos%20invalidos%20para%20gasto%20diario');
        }

        Movement::create([
            'date' => $date,
            'account_origin_id' => $accountId,
            'account_dest_id' => null,
            'type' => 'gasto',
            'category' => 'transporte',
            'concept' => $concept,
            'amount' => $amount,
            'currency' => 'DOP',
            'reimbursable' => 0,
            'reimbursed' => 0,
            'note' => $note,
        ]);

        redirect('?module=transport&message=Gasto%20diario%20registrado');
    }

    public function logItems(): void
    {
        $accountId = (int)($_POST['account_id'] ?? 0);
        $date = $_POST['date'] ?? current_date();
        $quantities = $_POST['items'] ?? [];
        if (!is_array($quantities)) {
            $quantities = [];
        }
        $note = trim($_POST['note'] ?? '');

        if ($accountId <= 0) {
            redirect('?module=transport&status=error&message=Seleccione%20una%20cuenta');
        }

        $items = TransportItem::all();
        $itemMap = [];
        foreach ($items as $item) {
            $itemMap[(int)$item['id']] = $item;
        }

        $created = 0;
        foreach ($quantities as $id => $qty) {
            $qty = (int)$qty;
            if ($qty <= 0 || !isset($itemMap[(int)$id])) {
                continue;
            }
            $item = $itemMap[(int)$id];
            if (!$item['active']) {
                continue;
            }

            $amount = (float)$item['amount'] * $qty;
            $concept = $item['name'];
            if ($qty > 1) {
                $concept .= ' x' . $qty;
            }

            Movement::create([
                'date' => $date,
                'account_origin_id' => $accountId,
                'account_dest_id' => null,
                'type' => 'gasto',
                'category' => 'transporte',
                'concept' => $concept,
                'amount' => $amount,
                'currency' => 'DOP',
                'reimbursable' => 0,
                'reimbursed' => 0,
                'note' => $note,
            ]);
            $created++;
        }

        if ($created === 0) {
            redirect('?module=transport&status=error&message=No%20hay%20items%20seleccionados');
        }

        redirect('?module=transport&message=Gastos%20por%20tramo%20registrados');
    }

    public function quick(): void
    {
        $accounts = Account::all();
        $text = trim($_POST['quick_text'] ?? '');
        $parsed = QuickParser::parse($text, $accounts);

        if (!empty($_POST['confirm']) && $parsed) {
            $parsed['type'] = 'gasto';
            $parsed['category'] = 'transporte';
            $movementId = Movement::create([
                'date' => current_date(),
                'account_origin_id' => $parsed['account_id'],
                'account_dest_id' => null,
                'type' => $parsed['type'],
                'category' => $parsed['category'],
                'concept' => $parsed['concept'],
                'amount' => $parsed['amount'],
                'currency' => 'DOP',
                'reimbursable' => 0,
                'reimbursed' => 0,
                'note' => 'Registro rapido transporte',
            ]);
            redirect('?module=transport&message=Registro%20rapido%20creado%20%28%23' . $movementId . '%29');
        }

        if ($parsed === null) {
            $this->render('transport/index', $this->buildIndexData([
                'message' => 'No se pudo parsear el texto.',
                'status' => 'error',
                'quickText' => $text,
            ]));
            return;
        }

        $this->render('transport/index', $this->buildIndexData([
            'quickParsed' => $parsed,
            'quickText' => $text,
        ]));
    }

    private function sanitize(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'amount' => (float)($data['amount'] ?? 0),
            'active' => isset($data['active']) ? 1 : 0,
        ];
    }

    private function buildIndexData(array $extra = []): array
    {
        $items = TransportItem::all();
        $activeItems = array_values(array_filter($items, fn ($item) => (int)$item['active'] === 1));
        $dailyTotal = TransportItem::dailyTotal();
        $workDays = isset($_GET['work_days']) ? max(0, (int)$_GET['work_days']) : 10;
        $quincenal = $dailyTotal * $workDays;
        $accounts = Account::all();

        return array_merge([
            'items' => $items,
            'activeItems' => $activeItems,
            'dailyTotal' => $dailyTotal,
            'workDays' => $workDays,
            'quincenal' => $quincenal,
            'accounts' => $accounts,
            'quickParsed' => null,
            'quickText' => null,
            'message' => null,
            'status' => 'success',
        ], $extra);
    }
}
