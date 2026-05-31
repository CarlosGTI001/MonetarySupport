<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Account;
use App\Models\FixedExpense;
use App\Models\Financing;
use App\Models\Movement;
use App\Models\TransportItem;
use App\Models\WorkExpense;
use App\Models\SavingsRule;
use App\Core\Database;

class DashboardController extends Controller
{
    public function index(): void
    {
        $accounts = Account::all();
        $totals = Account::totalsByCurrency();
        $cash = 0.0;
        $savings = 0.0;

        foreach ($accounts as $account) {
            if ($account['name'] === 'Efectivo') {
                $cash = (float)$account['balance'];
            }
            if (str_contains(strtolower((string)$account['purpose']), 'ahorro')) {
                $savings += (float)$account['balance'];
            }
        }

        $currentMonth = date('Y-m');
        $monthlyExpenses = Movement::monthlyExpenses($currentMonth);
        $expensesByCategory = Movement::expensesByCategory($currentMonth);
        $pendingWork = WorkExpense::pendingTotal();

        $workDays = isset($_GET['work_days']) ? max(0, (int)$_GET['work_days']) : 10;
        $dailyTransport = TransportItem::dailyTotal();
        $transportQuincenal = $dailyTransport * $workDays;

        $nextPayDate = date('Y-m-d', strtotime('+' . 15 . ' days'));
        $upcomingFixed = FixedExpense::upcoming($nextPayDate);
        $upcomingTotal = 0.0;
        foreach ($upcomingFixed as $item) {
            $upcomingTotal += (float)$item['amount'];
        }

        $db = Database::getConnection();
        $stmt = $db->prepare('SELECT * FROM financings WHERE next_date IS NOT NULL AND next_date <= :next');
        $stmt->execute(['next' => $nextPayDate]);
        $upcomingFinancings = $stmt->fetchAll();
        foreach ($upcomingFinancings as $item) {
            $upcomingTotal += (float)$item['installment_amount'];
        }

        $totalDop = (float)($totals['DOP'] ?? 0);
        $totalUsd = (float)($totals['USD'] ?? 0);

        $spendableDop = 0.0;
        foreach ($accounts as $account) {
            if ($account['currency'] === 'DOP' && !str_contains(strtolower((string)$account['purpose']), 'ahorro')) {
                $spendableDop += (float)$account['balance'];
            }
        }

        $projection = $spendableDop - $upcomingTotal - $transportQuincenal;

        $this->render('dashboard/index', [
            'accounts' => $accounts,
            'totals' => $totals,
            'cash' => $cash,
            'savings' => $savings,
            'spendableDop' => $spendableDop,
            'monthlyExpenses' => $monthlyExpenses,
            'pendingWork' => $pendingWork,
            'upcomingFixed' => $upcomingFixed,
            'upcomingFinancings' => $upcomingFinancings,
            'upcomingTotal' => $upcomingTotal,
            'transportQuincenal' => $transportQuincenal,
            'workDays' => $workDays,
            'projection' => $projection,
            'totalDop' => $totalDop,
            'totalUsd' => $totalUsd,
            'expensesByCategory' => $expensesByCategory,
        ]);
    }
}
