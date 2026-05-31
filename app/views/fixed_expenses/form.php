<?php
$isEdit = !empty($item);
?>
<h3><?= $isEdit ? 'Editar gasto fijo' : 'Nuevo gasto fijo' ?></h3>
<form method="post" action="?module=fixed_expenses&action=<?= $isEdit ? 'save' : 'store' ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
    <?php endif; ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="name" value="<?= e($item['name'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Monto</label>
            <div class="input-group">
                <input class="form-control" type="number" step="any" name="amount" value="<?= e((string)($item['amount'] ?? 0)) ?>">
                <select class="form-select" name="currency" style="max-width: 90px;">
                    <option value="DOP" <?= (($item['currency'] ?? 'DOP') === 'DOP') ? 'selected' : '' ?>>DOP</option>
                    <option value="USD" <?= (($item['currency'] ?? 'DOP') === 'USD') ? 'selected' : '' ?>>USD</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Frecuencia</label>
            <select class="form-select" name="frequency">
                <?php foreach (['monthly' => 'Mensual', 'biweekly' => 'Quincenal', 'custom' => 'Personalizada'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= (($item['frequency'] ?? 'monthly') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Cada dias</label>
            <input class="form-control" type="number" name="every_days" value="<?= e((string)($item['every_days'] ?? '')) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Cuenta</label>
            <select class="form-select" name="account_id">
                <option value="">-</option>
                <?php foreach ($accounts as $account): ?>
                    <option value="<?= (int)$account['id'] ?>" <?= (!empty($item['account_id']) && (int)$item['account_id'] === (int)$account['id']) ? 'selected' : '' ?>>
                        <?= e($account['name']) ?> (<?= format_money((float)$account['balance'], $account['currency']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Inicio</label>
            <input class="form-control" type="date" name="start_date" value="<?= e($item['start_date'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Fin</label>
            <input class="form-control" type="date" name="end_date" value="<?= e($item['end_date'] ?? '') ?>">
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
        <a class="btn btn-outline-secondary" href="?module=fixed_expenses">Cancelar</a>
    </div>
</form>
