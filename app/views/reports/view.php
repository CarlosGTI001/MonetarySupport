<div class="d-flex justify-content-between align-items-center mb-5 no-print">
    <a href="?module=reports" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Volver a Reportes
    </a>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold">
            <i class="bi bi-printer me-2"></i> Imprimir / PDF
        </button>
        <a href="?module=reports&action=view&type=<?= e($type) ?>&format=excel" class="btn btn-success btn-sm rounded-pill px-4 fw-bold">
            <i class="bi bi-file-earmark-excel me-2"></i> Descargar Excel
        </a>
    </div>
</div>

<div class="report-container bg-white p-5 rounded-4 shadow-sm border">
    <div class="report-header mb-5 d-flex justify-content-between align-items-start">
        <div>
            <div class="d-flex align-items-center mb-2">
                <div class="bg-dark rounded-3 p-2 me-2">
                    <i class="bi bi-intersect text-white"></i>
                </div>
                <h4 class="fw-bold mb-0 text-dark">MonetarySupport</h4>
            </div>
            <h2 class="fw-bold mb-1 mt-4"><?= e($title) ?></h2>
            <p class="text-muted small">Generado el: <?= date('d/m/Y H:i') ?></p>
        </div>
        <div class="text-end">
            <span class="badge bg-primary rounded-pill px-3 text-uppercase" style="font-size: 0.7rem; letter-spacing: 1px;">Reporte Oficial</span>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle custom-report-table">
            <thead class="table-light border-bottom">
                <tr>
                    <?php foreach ($headers as $header): ?>
                        <th class="py-3 text-uppercase text-muted fw-bold" style="font-size: 0.7rem;"><?= e($header) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)): ?>
                    <tr>
                        <td colspan="<?= count($headers) ?>" class="text-center py-5 text-muted">No se encontraron registros para este periodo.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                        <tr class="border-bottom-0">
                            <?php foreach ($row as $cell): ?>
                                <td class="py-3 small"><?= e((string)$cell) ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-5 pt-4 border-top text-center text-muted x-small">
        <p>© <?= date('Y') ?> MonetarySupport • Tu asistente financiero personal e inteligente.</p>
    </div>
</div>

<?php if ($isPrint): ?>
    <script>window.onload = () => window.print();</script>
<?php endif; ?>

<style>
    @media print {
        @page { size: auto; margin: 15mm; }
        .no-print { display: none !important; }
        body { background: white !important; font-family: 'Inter', sans-serif !important; }
        .report-container { border: none !important; box-shadow: none !important; padding: 0 !important; }
        #sidebar, .main-header { display: none !important; }
        #main-wrapper { margin-left: 0 !important; }
        main { padding: 0 !important; }
        .custom-report-table { width: 100% !important; border-collapse: collapse !important; }
        .custom-report-table th { background-color: #f8fafc !important; color: #64748b !important; -webkit-print-color-adjust: exact; }
        .badge { border: 1px solid #e2e8f0 !important; -webkit-print-color-adjust: exact; }
    }
    .custom-report-table thead th { border-top: none; }
    .custom-report-table tbody tr:last-child { border-bottom: none; }
    .x-small { font-size: 0.7rem; }
    .hover-lift { transition: transform 0.2s; }
    .hover-lift:hover { transform: translateY(-2px); }
</style>