<?php
$isEdit = !empty($account);
?>
<h3><?= $isEdit ? 'Editar cuenta' : 'Nueva cuenta' ?></h3>
<form method="post" action="?module=accounts&action=<?= $isEdit ? 'update' : 'store' ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)$account['id'] ?>">
    <?php endif; ?>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre</label>
            <input class="form-control" name="name" value="<?= e($account['name'] ?? '') ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <input class="form-control" name="type" value="<?= e($account['type'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Moneda</label>
            <select class="form-select" name="currency">
                <?php foreach (['DOP', 'USD'] as $currency): ?>
                    <option value="<?= $currency ?>" <?= (($account['currency'] ?? 'DOP') === $currency) ? 'selected' : '' ?>><?= $currency ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Balance</label>
            <input class="form-control" type="number" step="0.01" name="balance" value="<?= e((string)($account['balance'] ?? 0)) ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Proposito</label>
            <input class="form-control" name="purpose" value="<?= e($account['purpose'] ?? '') ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="active" <?= (!isset($account['active']) || $account['active']) ? 'checked' : '' ?>>
                <label class="form-check-label">Activa</label>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="?module=accounts">Cancelar</a>
    </div>
</form>
