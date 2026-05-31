<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Ahorros automaticos</h3>
    <a class="btn btn-primary" href="?module=savings&action=create">Nueva regla</a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Modo</th>
            <th>Cuenta</th>
            <th>Prioridad</th>
            <th>Activa</th>
            <th class="text-end">Valor</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['name']) ?></td>
                <td><?= e($item['mode']) ?></td>
                <td><?= e($item['account_name'] ?? '-') ?></td>
                <td><?= (int)$item['priority'] ?></td>
                <td><?= $item['active'] ? 'Si' : 'No' ?></td>
                <td class="text-end">
                    <?php if ($item['mode'] === 'percentage'): ?>
                        <?= e((string)$item['percent']) ?>%
                    <?php elseif ($item['mode'] === 'fixed'): ?>
                        <?= format_money((float)$item['amount'], 'DOP') ?>
                    <?php else: ?>
                        <?= e((string)($item['percent'] ?? 50)) ?>% restante
                    <?php endif; ?>
                </td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="?module=savings&action=edit&id=<?= (int)$item['id'] ?>">Editar</a>
                    <form class="d-inline" method="post" action="?module=savings&action=delete" onsubmit="return confirm('Eliminar regla?');">
                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
