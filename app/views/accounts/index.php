<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
    <div>
        <h3 class="fw-bold mb-1">Mis Cuentas</h3>
        <p class="text-muted mb-0 small">Gestiona tus activos y monitorea tus saldos en tiempo real.</p>
    </div>
    <a class="btn btn-primary fw-bold px-4 rounded-pill d-flex align-items-center shadow-sm" href="?module=accounts&action=create">
        <i class="bi bi-plus-lg me-2"></i> Nueva Cuenta
    </a>
</div>

<div class="row g-4">
    <?php foreach ($accounts as $account): 
        $isAhorro = str_contains(strtolower((string)$account['purpose']), 'ahorro');
        $cardBg = $isAhorro ? 'bg-primary' : 'bg-dark';
        $typeIcon = match(strtolower((string)$account['type'])) {
            'efectivo' => 'bi-cash',
            'banco' => 'bi-bank',
            'tarjeta' => 'bi-credit-card',
            default => 'bi-wallet2'
        };
    ?>
    <div class="col-12 col-md-6 col-xxl-4">
        <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div class="p-3 rounded-3 bg-light text-dark shadow-sm">
                        <i class="bi <?= $typeIcon ?> fs-4"></i>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link btn-sm text-muted p-0 border-0" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical fs-5"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                            <li><a class="dropdown-item py-2" href="?module=accounts&action=edit&id=<?= (int)$account['id'] ?>"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="post" action="?module=accounts&action=delete" onsubmit="return confirm('¿Eliminar esta cuenta? Se perderá el historial asociado.');">
                                    <input type="hidden" name="id" value="<?= (int)$account['id'] ?>">
                                    <button class="dropdown-item py-2 text-danger" type="submit"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <h5 class="fw-bold mb-1"><?= e($account['name']) ?></h5>
                <div class="d-flex align-items-center gap-2 mb-4">
                    <span class="badge bg-light text-dark text-uppercase small" style="font-size: 0.65rem;"><?= e($account['type']) ?></span>
                    <span class="text-muted small">•</span>
                    <span class="text-muted small text-uppercase" style="font-size: 0.65rem;"><?= e($account['purpose']) ?></span>
                </div>

                <div class="pt-2 border-top">
                    <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">Saldo Disponible</div>
                    <div class="d-flex align-items-baseline gap-2">
                        <span class="fs-2 fw-bold text-dark"><?= number_format((float)$account['balance'], 2) ?></span>
                        <span class="text-muted fw-semibold"><?= e($account['currency']) ?></span>
                    </div>
                </div>

                <!-- Decorative elements -->
                <div class="position-absolute opacity-10" style="bottom: -10px; right: -10px; transform: rotate(-15deg);">
                    <i class="bi <?= $typeIcon ?>" style="font-size: 8rem;"></i>
                </div>
            </div>
            
            <?php if (!$account['active']): ?>
                <div class="position-absolute top-0 start-0 w-100 h-100 bg-white bg-opacity-75 d-flex align-items-center justify-content-center" style="z-index: 5;">
                    <span class="badge bg-danger rounded-pill px-3 py-2 fw-bold shadow-sm">CUENTA INACTIVA</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="mt-5 p-4 bg-white rounded-4 border shadow-sm">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h5 class="fw-bold mb-1">Análisis de Distribución</h5>
            <p class="text-muted small mb-0">Tus activos están distribuidos principalmente en cuentas de <?= e($accounts[0]['type'] ?? 'banco') ?>.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button class="btn btn-outline-dark fw-bold rounded-pill px-4">Ver Reporte Detallado</button>
        </div>
    </div>
</div>