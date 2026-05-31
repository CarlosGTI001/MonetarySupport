<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Movimientos</h3>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="?module=quick">Registro rapido</a>
        <a class="btn btn-primary" href="?module=movements&action=create">Nuevo movimiento</a>
    </div>
</div>

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
