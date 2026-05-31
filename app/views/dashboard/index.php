<div class="row g-3">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Balance total DOP</div>
                <div class="fs-4 fw-bold text-primary"><?= format_money($totalDop, 'DOP') ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Balance total USD</div>
                <div class="fs-4 fw-bold text-success"><?= format_money($totalUsd, 'USD') ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Efectivo disponible</div>
                <div class="fs-4 fw-bold text-warning"><?= format_money($cash, 'DOP') ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <div class="text-muted small text-uppercase fw-bold">Ahorro actual</div>
                <div class="fs-4 fw-bold text-info"><?= format_money($savings, 'DOP') ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold">Gastos por categoría (mes actual)</h5>
                <div style="position: relative; height: 300px;">
                    <canvas id="expensesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="row g-3">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Gastos del mes</div>
                        <div class="fs-4 fw-bold text-danger"><?= format_money($monthlyExpenses, 'DOP') ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Gastos laborales pendientes</div>
                        <div class="fs-4 fw-bold text-secondary"><?= format_money($pendingWork, 'DOP') ?></div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card shadow-sm border-start border-4 <?= $projection < 0 ? 'border-danger' : 'border-success' ?>">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-bold">Proyección a próxima quincena</div>
                        <div class="fs-4 fw-bold <?= $projection < 0 ? 'text-danger' : 'text-success' ?>"><?= format_money($projection, 'DOP') ?></div>
                        <div class="small text-muted mt-1">
                            <?= format_money($spendableDop, 'DOP') ?> (liq.) - 
                            <?= format_money($upcomingTotal + $transportQuincenal, 'DOP') ?> (gastos)
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12 col-xl-6">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Saldos por cuenta</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>Cuenta</th>
                            <th>Moneda</th>
                            <th class="text-end">Balance</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($accounts as $account): ?>
                            <tr>
                                <td class="fw-medium"><?= e($account['name']) ?></td>
                                <td><span class="badge bg-light text-dark"><?= e($account['currency']) ?></span></td>
                                <td class="text-end fw-bold"><?= format_money((float)$account['balance'], $account['currency']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-xl-6">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Próximos pagos</h5>
                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="table-light">
                        <tr>
                            <th>Nombre</th>
                            <th>Fecha</th>
                            <th class="text-end">Monto</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($upcomingFixed as $item): ?>
                            <tr>
                                <td><?= e($item['name']) ?></td>
                                <td><small class="text-muted"><?= e($item['next_date']) ?></small></td>
                                <td class="text-end"><?= format_money((float)$item['amount'], 'DOP') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php foreach ($upcomingFinancings as $item): ?>
                            <tr>
                                <td><?= e($item['name']) ?> <span class="badge bg-secondary-subtle text-secondary small">Fin</span></td>
                                <td><small class="text-muted"><?= e($item['next_date']) ?></small></td>
                                <td class="text-end"><?= format_money((float)$item['installment_amount'], 'DOP') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                    <span class="text-muted fw-bold">Total compromisos:</span>
                    <span class="fs-5 fw-bold text-primary"><?= format_money($upcomingTotal, 'DOP') ?></span>
                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title fw-bold mb-3">Transporte quincenal</h5>
                <form class="row g-2 align-items-center" method="get">
                    <input type="hidden" name="module" value="dashboard">
                    <div class="col-auto">
                        <label class="small text-muted">Días laborales:</label>
                    </div>
                    <div class="col">
                        <input class="form-control form-control-sm" type="number" min="0" name="work_days" value="<?= (int)$workDays ?>">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-primary" type="submit">Actualizar</button>
                    </div>
                </form>
                <div class="mt-3 p-2 bg-light rounded d-flex justify-content-between">
                    <span class="text-muted">Total proyectado:</span>
                    <strong class="text-dark"><?= format_money($transportQuincenal, 'DOP') ?></strong>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('expensesChart');
const chartData = <?= json_encode($expensesByCategory) ?>;
const labels = chartData.map(item => item.category || 'Sin categoría');
const values = chartData.map(item => Number(item.total));
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Gastos',
            data: values,
            backgroundColor: '#2c3e50',
            borderRadius: 6,
            borderSkipped: false,
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
                        return ' ' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2}) + ' DOP';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { display: false }
            },
            x: {
                grid: { display: false }
            }
        }
    }
});
</script>