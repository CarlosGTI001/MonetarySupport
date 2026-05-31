<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0">Financiamientos</h3>
    <a class="btn btn-primary shadow-sm" href="?module=financings&action=create">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg me-1" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5.5h5.5a.5.5 0 0 1 0 1h-5.5v5.5a.5.5 0 0 1-1 0v-5.5h-5.5a.5.5 0 0 1 0-1h5.5v-5.5a.5.5 0 0 1 .5-.5z"/>
        </svg>
        Nuevo
    </a>
</div>

<div class="row g-4">
    <?php foreach ($items as $item): 
        $totalPayments = (int)($item['total_payments'] ?? 0);
        $made = (int)$item['payments_made'];
        $percent = $totalPayments > 0 ? round(($made / $totalPayments) * 100) : 0;
        $statusClass = $item['status'] === 'activo' ? 'text-success' : 'text-secondary';
        $isOver = $percent >= 100;
    ?>
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card shadow-sm border-0 h-100 overflow-hidden">
            <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                <span class="badge rounded-pill <?= $isOver ? 'bg-secondary' : 'bg-success-subtle text-success' ?> text-uppercase" style="font-size: 0.65rem;">
                    <?= e($item['status']) ?>
                </span>
                <div class="dropdown">
                    <button class="btn btn-link btn-sm text-muted p-0" type="button" data-bs-toggle="dropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-three-dots-vertical" viewBox="0 0 16 16">
                          <path d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                        </svg>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li><a class="dropdown-item" href="?module=financings&action=edit&id=<?= (int)$item['id'] ?>">Editar</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="post" action="?module=financings&action=delete" onsubmit="return confirm('¿Eliminar este financiamiento?');">
                                <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                <button class="dropdown-item text-danger" type="submit">Eliminar</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <h5 class="fw-bold mb-1 text-dark"><?= e($item['name']) ?></h5>
                <div class="text-muted small mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="bi bi-calendar3 me-1" viewBox="0 0 16 16">
                      <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2zM1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857V3.857z"/>
                      <path d="M6.5 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-9 3a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm3 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                    </svg>
                    Próximo: <?= e($item['next_date'] ?? 'No definida') ?>
                </div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="small fw-bold">Progreso de pagos</span>
                    <span class="small fw-bold <?= $isOver ? 'text-success' : '' ?>"><?= $percent ?>%</span>
                </div>
                <div class="progress mb-3" style="height: 8px;">
                    <div class="progress-bar <?= $isOver ? 'bg-success' : 'bg-primary' ?> progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: <?= $percent ?>%"></div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <div class="small text-muted text-uppercase" style="font-size: 0.6rem;">Cuota</div>
                            <div class="fw-bold text-primary"><?= format_money((float)$item['installment_amount'], 'DOP') ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded text-center">
                            <div class="small text-muted text-uppercase" style="font-size: 0.6rem;">Realizados</div>
                            <div class="fw-bold text-dark"><?= $made ?> / <?= $totalPayments ?></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small text-muted" style="font-size: 0.75rem;">Pendiente:</div>
                        <div class="fw-bold text-danger"><?= format_money((float)($item['total_pending'] ?? 0), 'DOP') ?></div>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted" style="font-size: 0.75rem;">Total pagado:</div>
                        <div class="fw-bold text-success"><?= format_money((float)($item['total_paid'] ?? 0), 'DOP') ?></div>
                    </div>
                </div>
            </div>
            <?php if (!$isOver): ?>
            <div class="card-footer bg-light border-0 p-0">
                <a href="?module=movements&action=create&financing_id=<?= (int)$item['id'] ?>" 
                   class="btn btn-primary w-100 rounded-0 py-2 fw-bold">
                   Registrar Pago
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>