<?php
$isEdit = !empty($item);
?>
<h3><?= $isEdit ? 'Editar item transporte' : 'Nuevo item transporte' ?></h3>
<form method="post" action="?module=transport&action=<?= $isEdit ? 'update' : 'store' ?>">
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
            <input class="form-control" type="number" step="0.01" name="amount" value="<?= e((string)($item['amount'] ?? 0)) ?>">
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="active" <?= (!isset($item['active']) || $item['active']) ? 'checked' : '' ?>>
                <label class="form-check-label">Activo</label>
            </div>
        </div>
    </div>
    <div class="mt-3">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="?module=transport">Cancelar</a>
    </div>
</form>
