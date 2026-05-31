<div class="row g-4 mb-5">
    <!-- Main Stats -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="p-3 bg-primary-subtle text-primary rounded-4 me-3">
                    <i class="bi bi-wallet2 fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem;">Balance Total DOP</div>
                    <div class="fs-4 fw-bold"><?= format_money($totalDop, 'DOP') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="p-3 bg-success-subtle text-success rounded-4 me-3">
                    <i class="bi bi-cash-stack fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem;">Efectivo</div>
                    <div class="fs-4 fw-bold"><?= format_money($cash, 'DOP') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3">
            <div class="d-flex align-items-center">
                <div class="p-3 bg-info-subtle text-info rounded-4 me-3">
                    <i class="bi bi-piggy-bank fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem;">Ahorros</div>
                    <div class="fs-4 fw-bold"><?= format_money($savings, 'DOP') ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm p-3 border-start border-4 <?= $projection < 0 ? 'border-danger' : 'border-success' ?>">
            <div class="d-flex align-items-center">
                <div class="p-3 <?= $projection < 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' ?> rounded-4 me-3">
                    <i class="bi <?= $projection < 0 ? 'bi-graph-down-arrow' : 'bi-graph-up-arrow' ?> fs-4"></i>
                </div>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.65rem;">Proyección Quincena</div>
                    <div class="fs-4 fw-bold <?= $projection < 0 ? 'text-danger' : 'text-success' ?>"><?= format_money($projection, 'DOP') ?></div>
                    <div class="x-small text-muted mt-1">
                        <?= format_money($spendableDop, 'DOP') ?> (liq.) - <?= format_money($upcomingTotal + $transportQuincenal, 'DOP') ?> (gastos)
                    </div>
                    <div class="x-small text-muted border-top mt-1 pt-1" style="font-size: 0.55rem;">
                        Tasa: 1 USD = <?= number_format(get_exchange_rate(), 2) ?> DOP
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Charts Section -->
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Flujo de Caja (6 meses)</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success-subtle text-success rounded-pill px-3">Ingresos</span>
                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3">Gastos</span>
                    </div>
                </div>
                <div style="height: 350px;">
                    <canvas id="cashFlowChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4 text-center">
                <h5 class="fw-bold mb-4 text-start">Gastos por Categoría</h5>
                <div style="height: 250px;" class="mb-4">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-4">
                            <div class="text-muted small mb-1">Este Mes</div>
                            <div class="fw-bold text-danger"><?= format_money($monthlyExpenses, 'DOP') ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-4">
                            <div class="text-muted small mb-1">Laborales</div>
                            <div class="fw-bold"><?= format_money($pendingWork, 'DOP') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-5">
    <!-- Tables / Details -->
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Distribución de Riqueza por Cuenta</h5>
                    <div class="small text-muted">Balance total consolidado</div>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-5">
                        <div style="height: 250px;">
                            <canvas id="wealthChart"></canvas>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="table-responsive mt-4 mt-md-0">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted small text-uppercase">
                                        <th class="border-0 px-0" style="font-size: 0.65rem;">Cuenta</th>
                                        <th class="border-0 text-end px-0" style="font-size: 0.65rem;">Balance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $activeWealthAccounts = array_filter($accounts, fn($a) => (float)$a['balance'] > 0);
                                    foreach (array_slice($activeWealthAccounts, 0, 6) as $account): 
                                    ?>
                                        <tr>
                                            <td class="px-0 py-2">
                                                <div class="fw-bold text-dark d-flex align-items-center">
                                                    <span class="d-inline-block rounded-circle me-2" style="width: 8px; height: 8px; background-color: var(--chart-color-<?= $account['id'] ?>);"></span>
                                                    <?= e($account['name']) ?>
                                                </div>
                                                <div class="text-muted x-small"><?= e($account['type']) ?></div>
                                            </td>
                                            <td class="text-end px-0 py-2 fw-bold"><?= format_money((float)$account['balance'], $account['currency']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">Próximos Pagos</h5>
                    <span class="badge bg-primary rounded-pill px-3"><?= count($upcomingFixed) + count($upcomingFinancings) ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr class="text-muted small text-uppercase">
                                <th class="border-0 px-0" style="font-size: 0.65rem;">Compromiso</th>
                                <th class="border-0 text-end px-0" style="font-size: 0.65rem;">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice(array_merge($upcomingFixed, $upcomingFinancings), 0, 5) as $item): ?>
                                <tr>
                                    <td class="px-0 py-3">
                                        <div class="fw-bold text-dark"><?= e($item['name']) ?></div>
                                        <div class="text-muted x-small"><?= e($item['next_date']) ?></div>
                                    </td>
                                    <td class="text-end px-0 py-3 fw-bold text-danger"><?= format_money((float)($item['installment_amount'] ?? $item['amount']), 'DOP') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 pt-4 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted small fw-bold text-uppercase" style="font-size: 0.6rem;">Comprometido</span>
                    <span class="fw-bold text-primary"><?= format_money($upcomingTotal, 'DOP') ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Colors palette for accounts
const accountColors = ['#0f172a', '#1e293b', '#334155', '#475569', '#64748b', '#94a3b8', '#cbd5e1', '#e2e8f0'];

// Chart 1: Category Breakdown (Doughnut)
const catCtx = document.getElementById('categoryChart');
const catData = <?= json_encode($expensesByCategory) ?>;
new Chart(catCtx, {
    type: 'doughnut',
    data: {
        labels: catData.map(i => i.category || 'Otros'),
        datasets: [{
            data: catData.map(i => Number(i.total)),
            backgroundColor: accountColors,
            borderWidth: 0,
            cutout: '70%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});

// Chart 2: Cash Flow Trend (Line)
const flowCtx = document.getElementById('cashFlowChart');
const flowData = <?= json_encode($cashFlow) ?>;
new Chart(flowCtx, {
    type: 'line',
    data: {
        labels: flowData.map(i => i.month),
        datasets: [
            {
                label: 'Ingresos',
                data: flowData.map(i => i.income),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4
            },
            {
                label: 'Gastos',
                data: flowData.map(i => i.expense),
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                fill: true,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

// Chart 3: Wealth Distribution (New Doughnut)
const wealthCtx = document.getElementById('wealthChart');
const wealthData = <?= json_encode($accounts) ?>;
new Chart(wealthCtx, {
    type: 'doughnut',
    data: {
        labels: wealthData.map(a => a.name),
        datasets: [{
            data: wealthData.map(a => Number(a.balance)),
            backgroundColor: accountColors,
            borderWidth: 0,
            cutout: '70%'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { 
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return ' ' + context.label + ': ' + context.parsed.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' DOP';
                    }
                }
            }
        }
    }
});
</script>

<style>
    .x-small { font-size: 0.7rem; }
    <?php foreach ($accounts as $index => $account): ?>
        :root { --chart-color-<?= $account['id'] ?>: <?= $index < count(['#0f172a', '#1e293b', '#334155', '#475569', '#64748b', '#94a3b8', '#cbd5e1', '#e2e8f0']) ? ['#0f172a', '#1e293b', '#334155', '#475569', '#64748b', '#94a3b8', '#cbd5e1', '#e2e8f0'][$index] : '#ccc' ?>; }
    <?php endforeach; ?>
</style>