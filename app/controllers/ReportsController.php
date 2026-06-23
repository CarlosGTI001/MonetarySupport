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
        $data = $this->getReportData($type, $format);

        if ($format === 'excel') {
            $this->exportExcel($type, $data);
            return;
        }

        if ($format === 'pdf_lib') {
            $this->exportPdf($type, $data);
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

    private function exportPdf(string $type, array $data): void
    {
        require_once __DIR__ . '/../core/libs/fpdf.php';

        $pdf = new \FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        
        // Header / Branding
        $pdf->SetFillColor(15, 23, 42); // #0f172a
        $pdf->Rect(0, 0, 210, 40, 'F');
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Text(20, 20, 'MonetarySupport');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Text(20, 28, 'Reporte de Gestion Financiera Personal');
        
        $pdf->SetY(50);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 14);
        
        $reportTitle = isset($data['title']) ? (string)$data['title'] : 'Reporte';
        $pdf->Cell(0, 10, $this->toIso($reportTitle), 0, 1, 'L');
        
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(100, 100, 100);
        $pdf->Cell(0, 5, 'Generado el: ' . date('d/m/Y H:i'), 0, 1, 'L');
        $pdf->Ln(10);

        // Table
        if (!empty($data['headers'])) {
            $pdf->SetFillColor(248, 250, 252);
            $pdf->SetTextColor(100, 116, 139);
            $pdf->SetFont('Arial', 'B', 8);
            
            $headerCount = count($data['headers']);
            $colWidth = 170 / ($headerCount > 0 ? $headerCount : 1);
            
            foreach ($data['headers'] as $header) {
                $pdf->Cell($colWidth, 10, strtoupper($this->toIso((string)$header)), 0, 0, 'L', true);
            }
            $pdf->Ln();

            $pdf->SetTextColor(30, 41, 59);
            $pdf->SetFont('Arial', '', 8);
            foreach ($data['rows'] as $row) {
                foreach ($row as $cell) {
                    $pdf->Cell($colWidth, 8, $this->toIso((string)$cell), 'B', 0, 'L');
                }
                $pdf->Ln();
            }
        } else {
            $pdf->Cell(0, 10, 'No hay datos para mostrar en este reporte.', 0, 1);
        }

        $pdf->Output('D', $type . '_' . date('Ymd_His') . '.pdf');
        exit;
    }

    private function toIso(string $str): string
    {
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
        }
        return utf8_decode($str);
    }

    private function getReportData(string $type, string $format = 'html'): array
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

            case 'gastos_laborales_pendientes':
                $title = 'Gastos Laborales Pendientes';
                $headers = ['Concepto', 'Transporte', 'Fecha', 'Monto'];
                $data = $db->query('
                    SELECT w.concept, w.project, w.date, w.amount
                    FROM work_expenses w
                    WHERE w.reimbursed = 0
                    ORDER BY w.date ASC, w.id ASC
                ')->fetchAll();
                foreach ($data as $r) {
                    $amountVal = (float)$r['amount'];
                    $rows[] = [
                        $r['concept'],
                        $r['project'],
                        $r['date'],
                        ($format === 'excel') ? $amountVal : format_money($amountVal, 'DOP')
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
        require_once __DIR__ . '/../core/libs/SimpleXLSXGen.php';
        
        if ($type === 'gastos_laborales_pendientes') {
            $templatePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'ReporteGastosLaborales.xlsx';
            
            $zipProcessed = false;
            $tempFile = '';
            
            if (file_exists($templatePath) && class_exists('\ZipArchive')) {
                $tempFile = tempnam(sys_get_temp_dir(), 'rep_') . '.xlsx';
                if (copy($templatePath, $tempFile)) {
                    $zip = new \ZipArchive();
                    if ($zip->open($tempFile) === true) {
                        $xml = $zip->getFromName('xl/worksheets/sheet1.xml');
                        if ($xml !== false) {
                            $row7Pos = strpos($xml, '<row r="7"');
                            $row22Pos = strpos($xml, '<row r="22"');
                            
                            if ($row7Pos !== false && $row22Pos !== false) {
                                $before = substr($xml, 0, $row7Pos);
                                $after = substr($xml, $row22Pos);
                                
                                $middle = '';
                                for ($i = 0; $i < 15; $i++) {
                                    $rowIdx = 7 + $i;
                                    $middle .= '<row r="' . $rowIdx . '" spans="1:5" x14ac:dyDescent="0.3">';
                                    
                                    if (isset($data['rows'][$i])) {
                                        $row = $data['rows'][$i];
                                        $concept = htmlspecialchars((string)$row[0], ENT_QUOTES, 'UTF-8');
                                        $transport = htmlspecialchars((string)$row[1], ENT_QUOTES, 'UTF-8');
                                        $date = htmlspecialchars((string)$row[2], ENT_QUOTES, 'UTF-8');
                                        $amount = (float)$row[3];
                                        
                                        $middle .= '<c r="A' . $rowIdx . '" s="11" t="inlineStr"><is><t>' . $concept . '</t></is></c>';
                                        $middle .= '<c r="B' . $rowIdx . '" s="5" t="inlineStr"><is><t>' . $transport . '</t></is></c>';
                                        $middle .= '<c r="C' . $rowIdx . '" s="4" t="inlineStr"><is><t>' . $date . '</t></is></c>';
                                        $middle .= '<c r="D' . $rowIdx . '" s="9"><v>' . $amount . '</v></c>';
                                    } else {
                                        // Empty rows with template styles to preserve borders
                                        $middle .= '<c r="A' . $rowIdx . '" s="11"/>';
                                        $middle .= '<c r="B' . $rowIdx . '" s="5"/>';
                                        $middle .= '<c r="C' . $rowIdx . '" s="4"/>';
                                        $middle .= '<c r="D' . $rowIdx . '" s="9"/>';
                                    }
                                    $middle .= '</row>';
                                }
                                
                                $newXml = $before . $middle . $after;
                                
                                $zip->deleteName('xl/worksheets/sheet1.xml');
                                $zip->addFromString('xl/worksheets/sheet1.xml', $newXml);
                                $zipProcessed = true;
                            }
                        }
                        $zip->close();
                    }
                }
            }

            if ($zipProcessed && file_exists($tempFile) && filesize($tempFile) > 0) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment; filename="ReporteGastosLaborales_Pendientes_' . date('Ymd_His') . '.xlsx"');
                header('Content-Length: ' . filesize($tempFile));
                readfile($tempFile);
                @unlink($tempFile);
                exit;
            }

            if ($tempFile && file_exists($tempFile)) {
                @unlink($tempFile);
            }

            // Fallback dynamics
            $xlsxData = [];
            
            // Merged title block rows (A1:D5)
            $xlsxData[] = ["<style center>Gastos Extras no estipulados por facturas, Corredor, Moto Concho y Metro de Santo Domingo, para transportes a Bancas y otros sucesos.</style>", "", "", ""];
            $xlsxData[] = ["", "", "", ""];
            $xlsxData[] = ["", "", "", ""];
            $xlsxData[] = ["", "", "", ""];
            $xlsxData[] = ["", "", "", ""];
            
            // Header Row (Row 6)
            $xlsxData[] = [
                "<style center>Concepto</style>", 
                "<style center>Transporte</style>", 
                "<style center>Fecha</style>", 
                "<style center>Monto</style>"
            ];
            
            // Data Rows (Row 7+)
            $N = count($data['rows']);
            foreach ($data['rows'] as $row) {
                $amount = $row[3];
                $xlsxData[] = [
                    "<style center>" . (string)$row[0] . "</style>",
                    "<style center>" . (string)$row[1] . "</style>",
                    "<style center>" . (string)$row[2] . "</style>",
                    "<style center>" . (is_numeric($amount) ? (float)$amount : $amount) . "</style>"
                ];
            }
            
            // Total Row (Row 7+N)
            $totalRowIdx = 7 + $N;
            $xlsxData[] = [
                "", 
                "", 
                "<style bold center>Total</style>",
                "<style bold center>=SUM(D7:D" . ($totalRowIdx - 1) . ")</style>"
            ];
            
            $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($xlsxData);
            
            // Merge A1:D5
            $xlsx->mergeCells("A1:D5");
            
            // Explicit widths from the template
            $xlsx->setColWidth(0, 40); // Col A (Concepto)
            $xlsx->setColWidth(1, 26); // Col B (Transporte)
            $xlsx->setColWidth(2, 12); // Col C (Fecha)
            $xlsx->setColWidth(3, 17); // Col D (Monto)
            
            $xlsx->downloadAs($type . '_' . date('Ymd_His') . '.xlsx');
            exit;
        }

        $colCount = count($data['headers']);
        $lastCol = \Shuchkin\SimpleXLSXGen::coord2cell($colCount - 1);
        
        $xlsxData = [];
        // Branding Header
        $xlsxData[] = ["<style bold center>MONETARY SUPPORT</style>"];
        $xlsxData[] = ["<style bold center>" . mb_strtoupper(utf8_decode($data['title'])) . "</style>"];
        $xlsxData[] = ["Generado el: " . date('d/m/Y H:i')];
        $xlsxData[] = [""]; // Spacer
        
        // Table Headers
        $styledHeaders = [];
        foreach ($data['headers'] as $h) {
            $styledHeaders[] = "<style bold fill-dark center>" . mb_strtoupper(utf8_decode($h)) . "</style>";
        }
        $xlsxData[] = $styledHeaders;

        // Data Rows
        foreach ($data['rows'] as $row) {
            $xlsxData[] = $row;
        }

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($xlsxData);
        
        // Apply "Template" settings
        $xlsx->mergeCells("A1:{$lastCol}1");
        $xlsx->mergeCells("A2:{$lastCol}2");
        $xlsx->mergeCells("A3:{$lastCol}3");
        
        // Auto-size columns (approximate)
        for ($i = 0; $i < $colCount; $i++) {
            $xlsx->setColWidth($i, 20);
        }

        $xlsx->downloadAs($type . '_' . date('Ymd_His') . '.xlsx');
        exit;
    }
}
