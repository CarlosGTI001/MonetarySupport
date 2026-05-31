<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Ahorros Automáticos</h3>
    <a class="btn btn-primary shadow-sm" href="?module=savings&action=create">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear-fill me-1" viewBox="0 0 16 16">
          <path d="M9.405 1.05c-.413-1.4-2.397-1.4-2.81 0l-.1.34a1.464 1.464 0 0 1-2.105.872l-.31-.17c-1.283-.698-2.686.705-1.987 1.987l.169.311c.446.82.023 1.841-.872 2.105l-.34.1c-1.4.413-1.4 2.397 0 2.81l.34.1a1.464 1.464 0 0 1 .872 2.105l-.17.31c-.698 1.283.705 2.686 1.987 1.987l.311-.169a1.464 1.464 0 0 1 2.105.872l.1.34c.413 1.4 2.397 1.4 2.81 0l.1-.34a1.464 1.464 0 0 1 2.105-.872l.31.17c1.283.698 2.686-.705 1.987-1.987l-.169-.311a1.464 1.464 0 0 1 .872-2.105l.34-.1c1.4-.413 1.4-2.397 0-2.81l-.34-.1a1.464 1.464 0 0 1-.872-2.105l.17-.31c.698-1.283-.705-2.686-1.987-1.987l-.311.169a1.464 1.464 0 0 1-2.105-.872l-.1-.34zM8 10.93a2.929 2.929 0 1 1 0-5.86 2.929 2.929 0 0 1 0 5.858z"/>
        </svg>
        Nueva Regla
    </a>
</div>

<?php
$pending = $pending ?? [];
$frequencyLabels = [
    'per_income' => 'Por ingreso',
    'monthly' => 'Mensual',
    'biweekly' => 'Quincenal',
];
?>

<?php if (!empty($pending)): ?>
    <div class="card border-0 shadow-sm mb-5 bg-primary text-white">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-4 d-flex align-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-lightning-charge-fill me-2 text-warning" viewBox="0 0 16 16">
                  <path d="M11.251.068a.5.5 0 0 1 .227.58L9.677 6.5H13a.5.5 0 0 1 .364.843l-8 8.5a.5.5 0 0 1-.842-.49L6.323 9.5H3a.5.5 0 0 1-.364-.843l8-8.5a.5.5 0 0 1 .615-.09z"/>
                </svg>
                Acciones de Ahorro Pendientes
            </h5>
            <div class="row g-3">
                <?php foreach ($pending as $item): ?>
                    <div class="col-12 col-lg-6">
                        <div class="p-3 bg-white bg-opacity-10 rounded border border-white border-opacity-25 d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold"><?= e($item['name']) ?></div>
                                <div class="small opacity-75"><?= e($item['period_label']) ?> • <?= e($item['account_name'] ?? 'Sin cuenta') ?></div>
                                <div class="fs-5 fw-bold text-warning mt-1"><?= format_money((float)$item['amount'], $item['account_currency'] ?? 'DOP') ?></div>
                            </div>
                            <div>
                                <?php if (!empty($item['target_account_id'])): ?>
                                    <a class="btn btn-warning fw-bold px-4" href="?module=movements&action=create&savings_rule_id=<?= (int)$item['id'] ?>">Ahorrar</a>
                                <?php else: ?>
                                    <a class="btn btn-outline-light btn-sm" href="?module=savings&action=edit&id=<?= (int)$item['id'] ?>">Configurar</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<h5 class="fw-bold mb-3 text-muted text-uppercase small">Estrategias de Ahorro Activas</h5>
<div class="row g-4">
    <?php foreach ($items as $item): 
        $isActive = (bool)$item['active'];
    ?>
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card shadow-sm border-0 h-100 <?= !$isActive ? 'opacity-75 bg-light' : '' ?>">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="p-3 rounded-circle <?= $isActive ? 'bg-primary-subtle text-primary' : 'bg-secondary-subtle text-secondary' ?>">
                        <?php if ($item['mode'] === 'percentage'): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-percent" viewBox="0 0 16 16">
                              <path d="M13.442 2.558a.625.625 0 0 1 0 .884l-10 10a.625.625 0 1 1-.884-.884l10-10a.625.625 0 0 1 .884 0zM4.5 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zm7 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                            </svg>
                        <?php elseif ($item['mode'] === 'fixed'): ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cash-stack" viewBox="0 0 16 16">
                              <path d="M1 3a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1H1zm7 8a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
                              <path d="M0 5a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V5zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V7a2 2 0 0 1-2-2H3z"/>
                            </svg>
                        <?php else: ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pie-chart-fill" viewBox="0 0 16 16">
                              <path d="M15.985 8.5H8.208A.625.625 0 0 1 7.5 7.87V.015c0-.496.399-.925.885-.815 3.524.799 6.338 3.392 7.371 6.643.125.402-.164.842-.771.657z"/>
                              <path d="M7.5 1.018a7 7 0 0 0-6.482 6.482A.625.625 0 0 0 1.63 8.5h5.87V1.63a.625.625 0 0 0-.615-.612z"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link btn-sm text-muted p-0" type="button" data-bs-toggle="dropdown">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-three-dots" viewBox="0 0 16 16">
                              <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                            </svg>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="?module=savings&action=edit&id=<?= (int)$item['id'] ?>">Editar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="post" action="?module=savings&action=delete" onsubmit="return confirm('¿Eliminar esta regla?');">
                                    <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                    <button class="dropdown-item text-danger" type="submit">Eliminar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <h5 class="fw-bold mb-1"><?= e($item['name']) ?></h5>
                <div class="badge bg-light text-dark text-uppercase mb-3" style="font-size: 0.65rem;">
                    <?= e($frequencyLabels[$item['frequency'] ?? 'per_income'] ?? 'Por ingreso') ?>
                </div>

                <div class="mb-3">
                    <div class="small text-muted mb-1 text-uppercase fw-bold" style="font-size: 0.65rem;">Destino</div>
                    <div class="d-flex align-items-center">
                        <div class="p-1 bg-white border rounded me-2">
                             <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bank text-primary" viewBox="0 0 16 16">
                              <path d="m8 0 6.61 3h.89a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.5.5H15v7a.5.5 0 0 1 .485.38l.5 2a.498.498 0 0 1-.485.62H.5a.498.498 0 0 1-.485-.62l.5-2A.501.501 0 0 1 1 13V6H.5a.5.5 0 0 1-.5-.5v-2A.5.5 0 0 1 .5 3h.89L8 0zM3.777 3h8.447L8 1.146 3.777 3zM2 6v7h1V6H2zm2 0v7h1V6H4zm3 0v7h1V6H7zm2 0v7h1V6H9zm2 0v7h1V6h-1zm2 0v7h1V6h-1zM1.5 14h13v1h-13v-1z"/>
                            </svg>
                        </div>
                        <span class="small fw-bold"><?= e($item['account_name'] ?? 'No asignada') ?></span>
                    </div>
                </div>

                <div class="mt-auto pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                            <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Configuración</div>
                            <div class="fs-5 fw-bold text-primary">
                                <?php if ($item['mode'] === 'percentage'): ?>
                                    <?= e((string)$item['percent']) ?>%
                                <?php elseif ($item['mode'] === 'fixed'): ?>
                                    <?= format_money((float)$item['amount'], 'DOP') ?>
                                <?php else: ?>
                                    <?= e((string)($item['percent'] ?? 50)) ?>% rest.
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted text-uppercase fw-bold" style="font-size: 0.65rem;">Prioridad</div>
                            <div class="fw-bold"><?= (int)$item['priority'] ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>