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

    public function view(): void
    {
        $type = $_GET['type'] ?? 'accounts';
        $format = $_GET['format'] ?? 'html';
        $data = $this->getReportData($type);

        if ($format === 'excel') {
            $this->exportExcel($type, $data);
            return;
        }

        $this->render('reports/view', [
            'type' => $type,
            'title' => $data['title'],
            'headers' => $data['headers'],
            'rows' => $data['rows'],
            'isPrint' => ($format === 'pdf')
        ]);
    }

    private function getReportData(string $type): array
    {
        $db = Database::getConnection();
        $title = '';
        $headers = [];
        $rows = [];

        switch ($type) {
            case 'accounts':
                $title = 'Estado de Cuentas';
                $headers = ['Nombre', 'Tipo', 'Moneda', 'Balance', 'Propósito', 'Estado'];
                $data = $db->query('SELECT * FROM accounts ORDER BY name ASC')->fetchAll();
                foreach ($data as $r) {
                    $rows[] = [
                        $r['name'], $r['type'], $r['currency'], 
                        format_money((float)$r['balance'], $r['currency']),
                        $r['purpose'], $r['active'] ? 'Activa' : 'Inactiva'
                    ];
                }
                break;

            case 'ingresos':
                $title = 'Reporte de Ingresos';
                $headers = ['Fecha', 'Cuenta', 'Categoría', 'Concepto', 'Monto'];
                $data = $db->query('
                    SELECT m.date, a.name as cuenta, m.category, m.concept, m.amount, m.currency
                    FROM movements m
                    JOIN accounts a ON a.id = m.account_origin_id
                    WHERE m.type = "ingreso"
                    ORDER BY m.date DESC
                ')->fetchAll();
                foreach ($data as $r) {
                    $rows[] = [$r['date'], $r['cuenta'], $r['category'], $r['concept'], format_money((float)$r['amount'], $r['currency'])];
                }
                break;

            case 'gastos_personales':
                $title = 'Gastos Personales';
                $headers = ['Fecha', 'Cuenta', 'Categoría', 'Concepto', 'Monto'];
                $data = $db->query('
                    SELECT m.date, a.name as cuenta, m.category, m.concept, m.amount, m.currency
                    FROM movements m
                    JOIN accounts a ON a.id = m.account_origin_id
                    WHERE m.type = "gasto"
                    ORDER BY m.date DESC
                ')->fetchAll();
                foreach ($data as $r) {
                    $rows[] = [$r['date'], $r['cuenta'], $r['category'], $r['concept'], format_money((float)$r['amount'], $r['currency'])];
                }
                break;

            case 'gastos_laborales':
                $title = 'Gastos Laborales';
                $headers = ['Fecha', 'Cuenta', 'Concepto', 'Monto', 'Proyecto', 'Estado'];
                $data = $db->query('
                    SELECT w.date, a.name as cuenta, w.concept, w.amount, w.project, w.reimbursed
                    FROM work_expenses w
                    JOIN accounts a ON a.id = w.account_id
                    ORDER BY w.date DESC
                ')->fetchAll();
                foreach ($data as $r) {
                    $rows[] = [
                        $r['date'], $r['cuenta'], $r['concept'], 
                        format_money((float)$r['amount'], 'DOP'),
                        $r['project'], $r['reimbursed'] ? 'Reembolsado' : 'Pendiente'
                    ];
                }
                break;

            case 'gastos_fijos':
                $title = 'Gastos Fijos';
                $headers = ['Nombre', 'Monto', 'Frecuencia', 'Cuenta', 'Estado'];
                $data = $db->query('
                    SELECT f.*, a.name as cuenta
                    FROM fixed_expenses f
                    LEFT JOIN accounts a ON a.id = f.account_id
                    ORDER BY f.name ASC
                ')->fetchAll();
                foreach ($data as $r) {
                    $rows[] = [$r['name'], format_money((float)$r['amount'], 'DOP'), $r['frequency'], $r['cuenta'], $r['active'] ? 'Activo' : 'Inactivo'];
                }
                break;

            case 'financiamientos':
                $title = 'Estado de Financiamientos';
                $headers = ['Nombre', 'Cuota', 'Pagos', 'Pagado', 'Pendiente', 'Estado'];
                $data = $db->query('SELECT * FROM financings ORDER BY name ASC')->fetchAll();
                foreach ($data as $r) {
                    $rows[] = [
                        $r['name'], format_money((float)$r['installment_amount'], 'DOP'),
                        $r['payments_made'] . ' / ' . $r['total_payments'],
                        format_money((float)$r['total_paid'], 'DOP'),
                        format_money((float)$r['total_pending'], 'DOP'),
                        ucfirst((string)$r['status'])
                    ];
                }
                break;

            case 'resumen_mensual':
                $title = 'Resumen Mensual de Flujo';
                $headers = ['Mes', 'Ingresos', 'Gastos Personales', 'Gastos Laborales'];
                $data = $db->query('
                    SELECT strftime("%Y-%m", date) as mes,
                           SUM(CASE WHEN type = "ingreso" THEN amount ELSE 0 END) as ingresos,
                           SUM(CASE WHEN type = "gasto" THEN amount ELSE 0 END) as gastos,
                           SUM(CASE WHEN type = "gasto_laboral" THEN amount ELSE 0 END) as gastos_laborales
                    FROM movements
                    GROUP BY strftime("%Y-%m", date)
                    ORDER BY mes DESC
                ')->fetchAll();
                foreach ($data as $r) {
                    $rows[] = [
                        $r['mes'], 
                        format_money((float)$r['ingresos'], 'DOP'),
                        format_money((float)$r['gastos'], 'DOP'),
                        format_money((float)$r['gastos_laborales'], 'DOP')
                    ];
                }
                break;
        }

        return ['title' => $title, 'headers' => $headers, 'rows' => $rows];
    }

    private function exportExcel(string $type, array $data): void
    {
        $filename = $type . '_' . date('Ymd_His') . '.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename);

        echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>';
        echo '<h2>' . e($data['title']) . '</h2>';
        echo '<table border="1">';
        echo '<tr>';
        foreach ($data['headers'] as $h) echo '<th style="background-color: #0f172a; color: white;">' . e($h) . '</th>';
        echo '</tr>';
        foreach ($data['rows'] as $row) {
            echo '<tr>';
            foreach ($row as $cell) echo '<td>' . e((string)$cell) . '</td>';
            echo '</tr>';
        }
        echo '</table></body></html>';
        exit;
    }
}
