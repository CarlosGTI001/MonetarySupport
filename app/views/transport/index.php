<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Transporte</h3>
    <a class="btn btn-primary" href="?module=transport&action=create">Nuevo item</a>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-<?= ($status ?? 'success') === 'error' ? 'danger' : 'success' ?>">
        <?= e($message) ?>
    </div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Configurar transporte diario</h5>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-end">Monto</th>
                            <th>Activo</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= e($item['name']) ?></td>
                                <td class="text-end"><?= format_money((float)$item['amount'], 'DOP') ?></td>
                                <td><?= $item['active'] ? 'Si' : 'No' ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-secondary" href="?module=transport&action=edit&id=<?= (int)$item['id'] ?>">Editar</a>
                                    <form class="d-inline" method="post" action="?module=transport&action=delete" onsubmit="return confirm('Eliminar item?');">
                                        <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-end fw-bold">Total diario: <?= format_money($dailyTotal, 'DOP') ?></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Calculo quincenal</h5>
                <form class="d-flex gap-2" method="get">
                    <input type="hidden" name="module" value="transport">
                    <input class="form-control" type="number" min="0" name="work_days" value="<?= (int)$workDays ?>">
                    <button class="btn btn-outline-primary" type="submit">Calcular</button>
                </form>
                <div class="mt-2">Total quincenal: <strong><?= format_money($quincenal, 'DOP') ?></strong></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Gasto diario (monto libre)</h5>
                <form method="post" action="?module=transport&action=logDaily">
                    <div class="mb-2">
                        <label class="form-label">Fecha</label>
                        <input class="form-control" type="date" name="date" value="<?= current_date() ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Cuenta</label>
                        <select class="form-select" name="account_id" required>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?= (int)$account['id'] ?>"><?= e($account['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Monto (default total diario)</label>
                        <input class="form-control" type="number" step="any" name="amount" value="<?= e((string)$dailyTotal) ?>" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Concepto</label>
                        <input class="form-control" name="concept" value="Transporte diario">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Nota</label>
                        <input class="form-control" name="note">
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Registrar gasto</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Gastos por tramo</h5>
                <form method="post" action="?module=transport&action=logItems">
                    <div class="mb-2">
                        <label class="form-label">Fecha</label>
                        <input class="form-control" type="date" name="date" value="<?= current_date() ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Cuenta</label>
                        <select class="form-select" name="account_id" required>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?= (int)$account['id'] ?>"><?= e($account['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="table-responsive mb-2">
                        <table class="table table-sm align-middle">
                            <thead>
                            <tr>
                                <th>Item</th>
                                <th class="text-end">Monto</th>
                                <th class="text-end">Cantidad</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($activeItems as $item): ?>
                                <tr>
                                    <td><?= e($item['name']) ?></td>
                                    <td class="text-end"><?= format_money((float)$item['amount'], 'DOP') ?></td>
                                    <td class="text-end">
                                        <input class="form-control form-control-sm text-end" type="number" min="0" name="items[<?= (int)$item['id'] ?>]" value="0">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Nota</label>
                        <input class="form-control" name="note">
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Registrar tramos</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Registro rapido transporte</h5>
                <form method="post" action="?module=transport&action=quick">
                    <div class="mb-2">
                        <label class="form-label">Texto</label>
                        <input class="form-control" name="quick_text" value="<?= e($quickText ?? '') ?>" placeholder="gaste 35 efectivo mexico ida">
                    </div>
                    <button class="btn btn-outline-primary w-100" type="submit">Parsear</button>
                </form>

                <?php if (!empty($quickParsed)): ?>
                    <div class="border rounded p-2 mt-3">
                        <div><strong>Cuenta:</strong> <?= e($quickParsed['account_name']) ?></div>
                        <div><strong>Monto:</strong> <?= format_money($quickParsed['amount'], 'DOP') ?></div>
                        <div><strong>Concepto:</strong> <?= e($quickParsed['concept']) ?></div>
                        <form method="post" action="?module=transport&action=quick" class="mt-2">
                            <input type="hidden" name="quick_text" value="<?= e($quickText ?? '') ?>">
                            <input type="hidden" name="confirm" value="1">
                            <button class="btn btn-success w-100" type="submit">Confirmar registro</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
