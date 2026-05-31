<?php
$isEdit = !empty($item);
?>
<h3><?= $isEdit ? 'Editar financiamiento' : 'Nuevo financiamiento' ?></h3>
<form method="post" action="?module=financings&action=<?= $isEdit ? 'save' : 'store' ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
    <?php endif; ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="name" value="<?= e($item['name'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Monto cuota</label>
            <div class="input-group">
                <input class="form-control" type="number" step="0.01" name="installment_amount" value="<?= e((string)($item['installment_amount'] ?? 0)) ?>">
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
            <label class="form-label">Total pagos</label>
            <input class="form-control" type="number" name="total_payments" value="<?= e((string)($item['total_payments'] ?? '')) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Pagos realizados</label>
            <input class="form-control" type="number" name="payments_made" value="<?= e((string)($item['payments_made'] ?? 0)) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Inicio</label>
            <input class="form-control" type="date" name="start_date" value="<?= e($item['start_date'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Fin</label>
            <input class="form-control" type="date" name="end_date" value="<?= e($item['end_date'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <input class="form-control" name="status" value="<?= e($item['status'] ?? 'activo') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Total pagado</label>
            <input class="form-control" type="number" step="0.01" name="total_paid" value="<?= e((string)($item['total_paid'] ?? 0)) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Total pendiente</label>
            <input class="form-control" type="number" step="0.01" name="total_pending" value="<?= e((string)($item['total_pending'] ?? 0)) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Proxima fecha</label>
            <input class="form-control" type="date" name="next_date" value="<?= e($item['next_date'] ?? '') ?>">
        </div>
        <div class="col-12">
            <label class="form-label">Nota</label>
            <input class="form-control" name="note" value="<?= e($item['note'] ?? '') ?>">
        </div>
    </div>
    <div class="mt-3">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="?module=financings">Cancelar</a>
    </div>
</form>
