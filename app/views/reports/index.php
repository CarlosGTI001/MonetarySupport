<div class="mb-5">
    <h3 class="fw-bold mb-1">Centro de Reportes</h3>
    <p class="text-muted mb-0 small">Exporta y analiza tus datos financieros en formatos compatibles.</p>
</div>

<div class="row g-4">
    <?php
    $reports = [
        ['type' => 'resumen_mensual', 'title' => 'Resumen Mensual', 'desc' => 'Balance general de ingresos y gastos del mes actual.', 'icon' => 'bi-calendar-range', 'color' => 'primary'],
        ['type' => 'accounts', 'title' => 'Estado de Cuentas', 'desc' => 'Saldos actuales y detalles de todas tus cuentas activas.', 'icon' => 'bi-bank', 'color' => 'dark'],
        ['type' => 'ingresos', 'title' => 'Reporte de Ingresos', 'desc' => 'Historial detallado de todas las entradas de dinero.', 'icon' => 'bi-cash-stack', 'color' => 'success'],
        ['type' => 'gastos_personales', 'title' => 'Gastos Personales', 'desc' => 'Análisis de consumos y compras del periodo.', 'icon' => 'bi-cart-check', 'color' => 'danger'],
        ['type' => 'gastos_laborales', 'title' => 'Gastos Laborales', 'desc' => 'Seguimiento de reembolsos y costos por proyecto.', 'icon' => 'bi-briefcase', 'color' => 'warning'],
        ['type' => 'gastos_fijos', 'title' => 'Gastos Fijos', 'desc' => 'Resumen de suscripciones y pagos recurrentes.', 'icon' => 'bi-clock-history', 'color' => 'info'],
        ['type' => 'financiamientos', 'title' => 'Financiamientos', 'desc' => 'Estado de cuotas, deudas y progreso de pagos.', 'icon' => 'bi-credit-card', 'color' => 'secondary'],
    ];
    ?>

    <?php foreach ($reports as $r): ?>
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100 hover-lift">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-4">
                    <div class="p-3 rounded-4 bg-<?= $r['color'] ?>-subtle text-<?= $r['color'] ?> me-3">
                        <i class="bi <?= $r['icon'] ?> fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark"><?= $r['title'] ?></h5>
                        <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.6rem;">Formato CSV</span>
                    </div>
                </div>
                <p class="text-muted small mb-4"><?= $r['desc'] ?></p>
                <a href="?module=reports&action=export&type=<?= $r['type'] ?>" class="btn btn-outline-<?= $r['color'] ?> w-100 fw-bold rounded-pill d-flex align-items-center justify-content-center">
                    <i class="bi bi-download me-2"></i> Exportar Datos
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<style>
    .hover-lift { transition: transform 0.2s, box-shadow 0.2s; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
</style>