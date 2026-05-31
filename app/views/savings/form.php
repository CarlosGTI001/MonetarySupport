<?php
$isEdit = !empty($item);
?>
<h3><?= $isEdit ? 'Editar regla' : 'Nueva regla' ?></h3>
<form method="post" action="?module=savings&action=<?= $isEdit ? 'save' : 'store' ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
    <?php endif; ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="name" value="<?= e($item['name'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Modo</label>
            <select class="form-select" name="mode">
                <?php foreach (['percentage' => 'Porcentaje', 'fixed' => 'Monto fijo', 'remainder' => 'Restante'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= (($item['mode'] ?? 'percentage') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Prioridad</label>
            <input class="form-control" type="number" name="priority" value="<?= e((string)($item['priority'] ?? 0)) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Porcentaje</label>
            <input class="form-control" type="number" step="any" name="percent" value="<?= e((string)($item['percent'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Monto fijo</label>
            <input class="form-control" type="number" step="any" name="amount" value="<?= e((string)($item['amount'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Frecuencia (solo monto fijo)</label>
            <select class="form-select" name="frequency">
                <?php foreach (['per_income' => 'Por ingreso', 'monthly' => 'Mensual', 'biweekly' => 'Quincenal'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= (($item['frequency'] ?? 'per_income') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Cuenta destino</label>
            <select class="form-select" name="target_account_id">
                <option value="">-</option>
                <?php foreach ($accounts as $account): ?>
                    <option value="<?= (int)$account['id'] ?>" <?= (!empty($item['target_account_id']) && (int)$item['target_account_id'] === (int)$account['id']) ? 'selected' : '' ?>>
                        <?= e($account['name']) ?> (<?= format_money((float)$account['balance'], $account['currency']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Nota</label>
            <input class="form-control" name="note" value="<?= e($item['note'] ?? '') ?>">
        </div>
        <div class="col-12">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="active" <?= (!isset($item['active']) || $item['active']) ? 'checked' : '' ?>>
                <label class="form-check-label">Activa</label>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="?module=savings">Cancelar</a>
    </div>
</form>
