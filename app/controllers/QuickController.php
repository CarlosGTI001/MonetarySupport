<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\Movement;
use App\Core\QuickParser;

class QuickController extends Controller
{
    public function index(): void
    {
        $accounts = Account::all();
        $parsed = null;
        $message = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $text = trim($_POST['quick_text'] ?? '');
            $parsed = QuickParser::parse($text, $accounts);
            if (!empty($_POST['confirm']) && $parsed) {
                $movementId = Movement::create([
                    'date' => current_date(),
                    'account_origin_id' => $parsed['account_id'],
                    'account_dest_id' => null,
                    'fixed_expense_id' => null,
                    'savings_rule_id' => null,
                    'financing_id' => null,
                    'apply_dgii_tax' => 0,
                    'type' => $parsed['type'],
                    'category' => $parsed['category'],
                    'concept' => $parsed['concept'],
                    'amount' => $parsed['amount'],
                    'currency' => 'DOP',
                    'reimbursable' => 0,
                    'reimbursed' => 0,
                    'note' => 'Registro rapido',
                ]);
                $message = 'Movimiento creado (#' . $movementId . ').';
                $parsed = null;
            }
        }

        $this->render('quick/index', [
            'parsed' => $parsed,
            'message' => $message,
        ]);
    }
}
