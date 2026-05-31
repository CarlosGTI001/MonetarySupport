<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
    <div>
        <h3 class="fw-bold mb-1">Gastos Fijos</h3>
        <p class="text-muted mb-0 small">Planifica y gestiona tus compromisos financieros recurrentes.</p>
    </div>
    <a class="btn btn-primary fw-bold px-4 rounded-pill d-flex align-items-center shadow-sm" href="?module=fixed_expenses&action=create">
        <i class="bi bi-plus-lg me-2"></i> Nuevo Gasto Fijo
    </a>
</div>

<div class="row g-4">
    <?php foreach ($items as $item): 
        $isActive = (bool)$item['active'];
        $freqLabel = match($item['frequency']) {
            'monthly' => 'Mensual',
            'biweekly' => 'Quincenal',
            'weekly' => 'Semanal',
            'yearly' => 'Anual',
            'custom' => 'Personalizado',
            default => e($item['frequency'])
        };
    ?>
    <div class="col-12 col-md-6 col-xxl-4">
        <div class="card border-0 shadow-sm h-100 <?= !$isActive ? 'opacity-75' : '' ?>">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="p-3 rounded-3 bg-danger-subtle text-danger shadow-sm">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link btn-sm text-muted p-0 border-0" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                            <li><a class="dropdown-item py-2" href="?module=fixed_expenses&action=edit&id=<?= (int)$item['id'] ?>"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="post" action="?module=fixed_expenses&action=delete" onsubmit="return confirm('¿Eliminar este gasto fijo?');">
                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <button class="dropdown-item py-2 text-danger" type="submit"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <h5 class="fw-bold mb-1"><?= e($item['name']) ?></h5>
                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="badge bg-light text-dark text-uppercase" style="font-size: 0.65rem;"><?= $freqLabel ?></span>
                    <?php if (!$isActive): ?>
                        <span class="badge bg-secondary-subtle text-secondary text-uppercase" style="font-size: 0.65rem;">Inactivo</span>
                    <?php else: ?>
                        <span class="badge bg-success-subtle text-success text-uppercase" style="font-size: 0.65rem;">Activo</span>
                    <?php endif; ?>
                </div>

                <div class="mb-4">
                    <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Cuenta de Débito</div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-bank2 text-muted"></i>
                        <span class="fw-semibold small text-dark"><?= e($item['account_name'] ?? 'No asignada') ?></span>
                    </div>
                </div>

                <div class="pt-3 border-top d-flex justify-content-between align-items-end">
                    <div>
                        <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem;">Monto Estimado</div>
                        <div class="fs-3 fw-bold text-dark"><?= format_money((float)$item['amount'], $item['currency'] ?? 'DOP') ?></div>
                    </div>
                    <a href="?module=movements&action=create&fixed_expense_id=<?= (int)$item['id'] ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
                        Pagar Ahora
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if (empty($items)): ?>
    <div class="text-center py-5 bg-white rounded-4 border shadow-sm">
        <i class="bi bi-calendar-x display-1 text-light"></i>
        <h5 class="mt-3 fw-bold">No hay gastos fijos configurados</h5>
        <p class="text-muted">Empieza agregando tus suscripciones, alquileres o servicios.</p>
        <a href="?module=fixed_expenses&action=create" class="btn btn-primary rounded-pill px-4">Agregar mi primer gasto</a>
    </div>
<?php endif; ?>