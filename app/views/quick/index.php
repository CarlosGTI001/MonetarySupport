<h3>Registro rapido</h3>

<?php if (!empty($message)): ?>
    <div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>

<form method="post">
    <div class="mb-3">
        <label class="form-label">Texto</label>
        <input class="form-control" name="quick_text" placeholder="gaste 35 efectivo carrito Mexico">
    </div>
    <button class="btn btn-primary" type="submit">Parsear</button>
</form>

<?php if (!empty($parsed)): ?>
    <div class="card mt-3">
        <div class="card-body">
            <h5>Resultado</h5>
            <p class="mb-1"><strong>Tipo:</strong> <?= e($parsed['type']) ?></p>
            <p class="mb-1"><strong>Cuenta:</strong> <?= e($parsed['account_name']) ?></p>
            <p class="mb-1"><strong>Monto:</strong> <?= format_money($parsed['amount'], 'DOP') ?></p>
            <p class="mb-3"><strong>Concepto:</strong> <?= e($parsed['concept']) ?></p>
            <form method="post">
                <input type="hidden" name="quick_text" value="<?= e($_POST['quick_text'] ?? '') ?>">
                <input type="hidden" name="confirm" value="1">
                <button class="btn btn-success" type="submit">Confirmar registro</button>
            </form>
        </div>
    </div>
<?php endif; ?>
