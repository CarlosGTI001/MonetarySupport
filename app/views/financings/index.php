<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Financiamientos</h3>
    <a class="btn btn-primary" href="?module=financings&action=create">Nuevo financiamiento</a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Frecuencia</th>
            <th>Estado</th>
            <th class="text-end">Cuota</th>
            <th>Pagos</th>
            <th>Proxima fecha</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['name']) ?></td>
                <td><?= e($item['frequency']) ?></td>
                <td><?= e($item['status']) ?></td>
                <td class="text-end"><?= format_money((float)$item['installment_amount'], 'DOP') ?></td>
                <td><?= (int)$item['payments_made'] ?>/<?= (int)($item['total_payments'] ?? 0) ?></td>
                <td><?= e($item['next_date'] ?? '-') ?></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="?module=financings&action=edit&id=<?= (int)$item['id'] ?>">Editar</a>
                    <form class="d-inline" method="post" action="?module=financings&action=delete" onsubmit="return confirm('Eliminar financiamiento?');">
                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
