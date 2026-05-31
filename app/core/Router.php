<?php
declare(strict_types=1);

namespace App\Core;

class Router
{
    public static function dispatch(): void
    {
        $module = $_GET['module'] ?? 'dashboard';
        $action = $_GET['action'] ?? 'index';

        $map = [
            'dashboard' => \App\Controllers\DashboardController::class,
            'accounts' => \App\Controllers\AccountsController::class,
            'movements' => \App\Controllers\MovementsController::class,
            'fixed_expenses' => \App\Controllers\FixedExpensesController::class,
            'financings' => \App\Controllers\FinancingsController::class,
            'transport' => \App\Controllers\TransportController::class,
            'work_expenses' => \App\Controllers\WorkExpensesController::class,
            'savings' => \App\Controllers\SavingsController::class,
            'reports' => \App\Controllers\ReportsController::class,
            'quick' => \App\Controllers\QuickController::class,
        ];

        if (!array_key_exists($module, $map)) {
            http_response_code(404);
            echo 'Modulo no encontrado.';
            return;
        }

        $controllerClass = $map[$module];
        $controller = new $controllerClass();

        if (!method_exists($controller, $action)) {
            http_response_code(404);
            echo 'Accion no encontrada.';
            return;
        }

        $controller->$action();
    }
}
