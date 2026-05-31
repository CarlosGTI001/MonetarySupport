<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Gastos fijos</h3>
    <a class="btn btn-primary" href="?module=fixed_expenses&action=create">Nuevo gasto fijo</a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Frecuencia</th>
            <th class="text-end">Monto</th>
            <th>Cuenta</th>
            <th>Activa</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['name']) ?></td>
                <td><?= e($item['frequency']) ?></td>
                <td class="text-end"><?= format_money((float)$item['amount'], 'DOP') ?></td>
                <td><?= e($item['account_name'] ?? '-') ?></td>
                <td><?= $item['active'] ? 'Si' : 'No' ?></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="?module=fixed_expenses&action=edit&id=<?= (int)$item['id'] ?>">Editar</a>
                    <form class="d-inline" method="post" action="?module=fixed_expenses&action=delete" onsubmit="return confirm('Eliminar gasto fijo?');">
                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
