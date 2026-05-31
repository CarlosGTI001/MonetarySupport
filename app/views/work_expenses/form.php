<?php
$isEdit = !empty($item);
?>
<h3><?= $isEdit ? 'Editar gasto laboral' : 'Nuevo gasto laboral' ?></h3>
<form method="post" action="?module=work_expenses&action=<?= $isEdit ? 'save' : 'store' ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
    <?php endif; ?>
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Fecha</label>
            <input class="form-control" type="date" name="date" value="<?= e($item['date'] ?? current_date()) ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Cuenta</label>
            <select class="form-select" name="account_id">
                <?php foreach ($accounts as $account): ?>
                    <option value="<?= (int)$account['id'] ?>" <?= (!empty($item['account_id']) && (int)$item['account_id'] === (int)$account['id']) ? 'selected' : '' ?>>
                        <?= e($account['name']) ?> (<?= format_money((float)$account['balance'], $account['currency']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-5">
            <label class="form-label">Concepto</label>
            <input class="form-control" name="concept" value="<?= e($item['concept'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Monto</label>
            <input class="form-control" type="number" step="0.01" name="amount" value="<?= e((string)($item['amount'] ?? 0)) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Proyecto/Motivo</label>
            <input class="form-control" name="project" value="<?= e($item['project'] ?? '') ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="reimbursed" <?= (!empty($item['reimbursed'])) ? 'checked' : '' ?>>
                <label class="form-check-label">Reembolsado</label>
            </div>
        </div>
        <div class="col-12">
            <label class="form-label">Nota</label>
            <input class="form-control" name="note" value="<?= e($item['note'] ?? '') ?>">
        </div>
    </div>
    <div class="mt-3">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="?module=work_expenses">Cancelar</a>
    </div>
</form>
