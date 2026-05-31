<div class="row g-3">
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted">Balance total DOP</div>
                <div class="fs-4 fw-bold"><?= format_money($totalDop, 'DOP') ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted">Balance total USD</div>
                <div class="fs-4 fw-bold"><?= format_money($totalUsd, 'USD') ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted">Efectivo disponible</div>
                <div class="fs-4 fw-bold"><?= format_money($cash, 'DOP') ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6 col-lg-3">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted">Ahorro actual</div>
                <div class="fs-4 fw-bold"><?= format_money($savings, 'DOP') ?></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12 col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Gastos por categoria (mes actual)</h5>
                <canvas id="expensesChart" height="110"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="text-muted">Gastos del mes</div>
                <div class="fs-4 fw-bold"><?= format_money($monthlyExpenses, 'DOP') ?></div>
            </div>
        </div>
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="text-muted">Gastos laborales pendientes</div>
                <div class="fs-4 fw-bold"><?= format_money($pendingWork, 'DOP') ?></div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted">Proyeccion a proxima quincena</div>
                <div class="fs-4 fw-bold <?= $projection < 0 ? 'text-danger' : 'text-success' ?>"><?= format_money($projection, 'DOP') ?></div>
                <div class="small text-muted">
                    <?= format_money($spendableDop, 'DOP') ?> (liq.) - 
                    <?= format_money($upcomingTotal + $transportQuincenal, 'DOP') ?> (gastos)
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Saldos por cuenta</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>Cuenta</th>
                            <th>Moneda</th>
                            <th class="text-end">Balance</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($accounts as $account): ?>
                            <tr>
                                <td><?= e($account['name']) ?></td>
                                <td><?= e($account['currency']) ?></td>
                                <td class="text-end"><?= format_money((float)$account['balance'], $account['currency']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <h5 class="card-title">Proximos pagos</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
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
                                <td><?= e($item['next_date']) ?></td>
                                <td class="text-end"><?= format_money((float)$item['amount'], 'DOP') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php foreach ($upcomingFinancings as $item): ?>
                            <tr>
                                <td><?= e($item['name']) ?> (fin)</td>
                                <td><?= e($item['next_date']) ?></td>
                                <td class="text-end"><?= format_money((float)$item['installment_amount'], 'DOP') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end fw-bold">Total: <?= format_money($upcomingTotal, 'DOP') ?></div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Transporte quincenal</h5>
                <form class="d-flex gap-2" method="get">
                    <input type="hidden" name="module" value="dashboard">
                    <input class="form-control" type="number" min="0" name="work_days" value="<?= (int)$workDays ?>">
                    <button class="btn btn-outline-primary" type="submit">Calcular</button>
                </form>
                <div class="mt-2">Total: <strong><?= format_money($transportQuincenal, 'DOP') ?></strong></div>
            </div>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('expensesChart');
const chartData = <?= json_encode($expensesByCategory) ?>;
const labels = chartData.map(item => item.category || 'Sin categoria');
const values = chartData.map(item => Number(item.total));
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Gastos',
            data: values,
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } }
    }
});
</script>
