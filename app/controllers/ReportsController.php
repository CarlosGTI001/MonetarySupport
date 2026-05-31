<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class ReportsController extends Controller
{
    public function index(): void
    {
        $this->render('reports/index');
    }

    public function export(): void
    {
        $type = $_GET['type'] ?? 'accounts';
        $db = Database::getConnection();

        $filename = $type . '_' . date('Ymd_His') . '.csv';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);

        $output = fopen('php://output', 'w');

        switch ($type) {
            case 'accounts':
                fputcsv($output, ['ID', 'Nombre', 'Tipo', 'Moneda', 'Balance', 'Proposito', 'Activa']);
                $rows = $db->query('SELECT * FROM accounts ORDER BY name ASC')->fetchAll();
                foreach ($rows as $row) {
                    fputcsv($output, [
                        $row['id'], $row['name'], $row['type'], $row['currency'],
                        $row['balance'], $row['purpose'], $row['active']
                    ]);
                }
                break;
            case 'ingresos':
                fputcsv($output, ['Fecha', 'Cuenta', 'Categoria', 'Concepto', 'Monto', 'Moneda']);
                $rows = $db->query('
                    SELECT m.date, a.name as cuenta, m.category, m.concept, m.amount, m.currency
                    FROM movements m
                    JOIN accounts a ON a.id = m.account_origin_id
                    WHERE m.type = "ingreso"
                    ORDER BY m.date DESC
                ')->fetchAll();
                foreach ($rows as $row) {
                    fputcsv($output, [$row['date'], $row['cuenta'], $row['category'], $row['concept'], $row['amount'], $row['currency']]);
                }
                break;
            case 'gastos_personales':
                fputcsv($output, ['Fecha', 'Cuenta', 'Categoria', 'Concepto', 'Monto', 'Moneda']);
                $rows = $db->query('
                    SELECT m.date, a.name as cuenta, m.category, m.concept, m.amount, m.currency
                    FROM movements m
                    JOIN accounts a ON a.id = m.account_origin_id
                    WHERE m.type = "gasto"
                    ORDER BY m.date DESC
                ')->fetchAll();
                foreach ($rows as $row) {
                    fputcsv($output, [$row['date'], $row['cuenta'], $row['category'], $row['concept'], $row['amount'], $row['currency']]);
                }
                break;
            case 'gastos_laborales':
                fputcsv($output, ['Fecha', 'Cuenta', 'Concepto', 'Monto', 'Proyecto', 'Reembolsado']);
                $rows = $db->query('
                    SELECT w.date, a.name as cuenta, w.concept, w.amount, w.project, w.reimbursed
                    FROM work_expenses w
                    JOIN accounts a ON a.id = w.account_id
                    ORDER BY w.date DESC
                ')->fetchAll();
                foreach ($rows as $row) {
                    fputcsv($output, [$row['date'], $row['cuenta'], $row['concept'], $row['amount'], $row['project'], $row['reimbursed']]);
                }
                break;
            case 'gastos_fijos':
                fputcsv($output, ['Nombre', 'Monto', 'Frecuencia', 'Cuenta', 'Activa', 'Nota']);
                $rows = $db->query('
                    SELECT f.*, a.name as cuenta
                    FROM fixed_expenses f
                    LEFT JOIN accounts a ON a.id = f.account_id
                    ORDER BY f.name ASC
                ')->fetchAll();
                foreach ($rows as $row) {
                    fputcsv($output, [$row['name'], $row['amount'], $row['frequency'], $row['cuenta'], $row['active'], $row['note']]);
                }
                break;
            case 'financiamientos':
                fputcsv($output, ['Nombre', 'Cuota', 'Frecuencia', 'Total pagos', 'Pagos realizados', 'Estado', 'Total pagado', 'Total pendiente', 'Proxima fecha']);
                $rows = $db->query('SELECT * FROM financings ORDER BY name ASC')->fetchAll();
                foreach ($rows as $row) {
                    fputcsv($output, [
                        $row['name'], $row['installment_amount'], $row['frequency'],
                        $row['total_payments'], $row['payments_made'], $row['status'],
                        $row['total_paid'], $row['total_pending'], $row['next_date']
                    ]);
                }
                break;
            case 'resumen_mensual':
                fputcsv($output, ['Mes', 'Ingresos', 'Gastos', 'Gastos laborales']);
                $rows = $db->query('
                    SELECT strftime("%Y-%m", date) as mes,
                           SUM(CASE WHEN type = "ingreso" THEN amount ELSE 0 END) as ingresos,
                           SUM(CASE WHEN type = "gasto" THEN amount ELSE 0 END) as gastos,
                           SUM(CASE WHEN type = "gasto_laboral" THEN amount ELSE 0 END) as gastos_laborales
                    FROM movements
                    GROUP BY strftime("%Y-%m", date)
                    ORDER BY mes DESC
                ')->fetchAll();
                foreach ($rows as $row) {
                    fputcsv($output, [$row['mes'], $row['ingresos'], $row['gastos'], $row['gastos_laborales']]);
                }
                break;
            default:
                fputcsv($output, ['Reporte no soportado']);
        }

        fclose($output);
        exit;
    }
}
