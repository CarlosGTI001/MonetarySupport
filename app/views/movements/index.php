<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Movimientos</h3>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="?module=quick">Registro rapido</a>
        <a class="btn btn-primary" href="?module=movements&action=create">Nuevo movimiento</a>
    </div>
</div>

<?php $pendingFixedExpenses = $pendingFixedExpenses ?? []; ?>

<?php if (!empty($incomeSuggestion)): ?>
    <div class="alert alert-info">
        <h5>Sugerencia de distribucion (ingreso <?= format_money($incomeSuggestion['total'], 'DOP') ?>)</h5>
        <div class="row">
            <div class="col-12 col-md-6">
                <ul class="mb-2">
                    <?php foreach ($incomeSuggestion['suggestions'] as $item): ?>
                        <li><?= e($item['name']) ?>: <?= format_money($item['amount'], 'DOP') ?> → <?= e($item['account']) ?></li>
                    <?php endforeach; ?>
                    <?php foreach ($incomeSuggestion['remainder'] as $item): ?>
                        <li><?= e($item['name']) ?>: <?= format_money($item['amount'], 'DOP') ?> → <?= e($item['account']) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="col-12 col-md-6">
                <div>Comprometido: <strong><?= format_money($incomeSuggestion['committed'], 'DOP') ?></strong></div>
                <div>Libre: <strong><?= format_money($incomeSuggestion['free'], 'DOP') ?></strong></div>
                <div class="mt-2">
                    <?php foreach ($incomeSuggestion['accountTargets'] as $name => $value): ?>
                        <div><?= e($name) ?>: <?= format_money($value, 'DOP') ?></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($pendingFixedExpenses)): ?>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title mb-3">Gastos fijos pendientes</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                    <tr>
                        <th>Gasto fijo</th>
                        <th>Periodo</th>
                        <th>Cuenta</th>
                        <th class="text-end">Monto</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pendingFixedExpenses as $item): ?>
                        <tr>
                            <td><?= e($item['name']) ?></td>
                            <td><?= e($item['period_label']) ?></td>
                            <td><?= e($item['account_name'] ?? 'Sin cuenta') ?></td>
                            <td class="text-end"><?= format_money((float)$item['amount'], $item['account_currency'] ?? 'DOP') ?></td>
                            <td class="text-end">
                                <?php if (!empty($item['account_id'])): ?>
                                    <form method="post" action="?module=movements&action=applyFixed" class="d-inline">
                                        <input type="hidden" name="fixed_expense_id" value="<?= (int)$item['id'] ?>">
                                        <button class="btn btn-sm btn-primary" type="submit">Aplicar</button>
                                    </form>
                                <?php else: ?>
                                    <a class="btn btn-sm btn-outline-primary" href="?module=movements&action=create&fixed_expense_id=<?= (int)$item['id'] ?>">Completar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Cuenta</th>
            <th>Destino</th>
            <th>Categoria</th>
            <th>Concepto</th>
            <th class="text-end">Monto</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($movements as $movement): ?>
            <tr>
                <td><?= e($movement['date']) ?></td>
                <td><?= e($movement['type']) ?></td>
                <td><?= e($movement['account_origin_name']) ?></td>
                <td><?= e($movement['account_dest_name'] ?? '-') ?></td>
                <td><?= e($movement['category']) ?></td>
                <td><?= e($movement['concept']) ?></td>
                <td class="text-end"><?= format_money((float)$movement['amount'], $movement['currency']) ?></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="?module=movements&action=edit&id=<?= (int)$movement['id'] ?>">Editar</a>
                    <form method="post" action="?module=movements&action=delete" onsubmit="return confirm('Eliminar movimiento?');">
                        <input type="hidden" name="id" value="<?= (int)$movement['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
