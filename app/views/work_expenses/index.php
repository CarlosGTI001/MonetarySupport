<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Gastos laborales</h3>
    <a class="btn btn-primary" href="?module=work_expenses&action=create">Nuevo gasto laboral</a>
</div>

<div class="alert alert-warning">
    Pendiente de reembolso: <strong><?= format_money($pendingTotal, 'DOP') ?></strong>
</div>

<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Fecha</th>
            <th>Cuenta</th>
            <th>Concepto</th>
            <th>Proyecto</th>
            <th class="text-end">Monto</th>
            <th>Reembolsado</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['date']) ?></td>
                <td><?= e($item['account_name']) ?></td>
                <td><?= e($item['concept']) ?></td>
                <td><?= e($item['project']) ?></td>
                <td class="text-end"><?= format_money((float)$item['amount'], 'DOP') ?></td>
                <td><?= $item['reimbursed'] ? 'Si' : 'No' ?></td>
                <td class="text-end">
                    <a class="btn btn-sm btn-outline-secondary" href="?module=work_expenses&action=edit&id=<?= (int)$item['id'] ?>">Editar</a>
                    <form class="d-inline" method="post" action="?module=work_expenses&action=delete" onsubmit="return confirm('Eliminar gasto?');">
                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
