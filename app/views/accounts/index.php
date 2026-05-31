<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Cuentas</h3>
    <a class="btn btn-primary" href="?module=accounts&action=create">Nueva cuenta</a>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Moneda</th>
            <th class="text-end">Balance</th>
            <th>Proposito</th>
            <th>Activa</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($accounts as $account): ?>
            <tr>
                <td><?= e($account['name']) ?></td>
                <td><?= e($account['type']) ?></td>
                <td><?= e($account['currency']) ?></td>
                <td class="text-end"><?= format_money((float)$account['balance'], $account['currency']) ?></td>
                <td><?= e($account['purpose']) ?></td>
                <td><?= $account['active'] ? 'Si' : 'No' ?></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="?module=accounts&action=edit&id=<?= (int)$account['id'] ?>">Editar</a>
                    <form class="d-inline" method="post" action="?module=accounts&action=delete" onsubmit="return confirm('Eliminar cuenta?');">
                        <input type="hidden" name="id" value="<?= (int)$account['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
