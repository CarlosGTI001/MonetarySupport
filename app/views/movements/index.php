<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
    <div>
        <h3 class="fw-bold mb-1">Historial de Transacciones</h3>
        <p class="text-muted mb-0 small">Visualiza y gestiona todos tus movimientos financieros en un solo lugar.</p>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-dark fw-bold px-4 rounded-pill d-flex align-items-center" href="?module=quick">
            <i class="bi bi-lightning-fill text-warning me-2"></i> Registro Rápido
        </a>
        <a class="btn btn-primary fw-bold px-4 rounded-pill d-flex align-items-center shadow-sm" href="?module=movements&action=create">
            <i class="bi bi-plus-lg me-2"></i> Nuevo Movimiento
        </a>
    </div>
</div>

<?php if (!empty($incomeSuggestion)): ?>
    <div class="card border-0 shadow-sm mb-4 bg-primary text-white overflow-hidden">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-magic fs-4 me-2 text-warning"></i>
                <h5 class="fw-bold mb-0 text-white">Sugerencia de Distribución Inteligente</h5>
            </div>
            <div class="row g-4">
                <div class="col-12 col-lg-7">
                    <div class="list-group list-group-flush bg-transparent border-0">
                        <?php foreach (array_merge($incomeSuggestion['suggestions'], $incomeSuggestion['remainder']) as $item): ?>
                            <div class="list-group-item bg-transparent border-white border-opacity-10 text-white d-flex justify-content-between px-0">
                                <span><i class="bi bi-arrow-right-short me-1 opacity-50"></i> <?= e($item['name']) ?></span>
                                <span class="fw-bold"><?= format_money($item['amount'], 'DOP') ?> <small class="fw-normal opacity-75">→ <?= e($item['account']) ?></small></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="p-3 bg-white bg-opacity-10 rounded-3 h-100">
                        <div class="row text-center g-2">
                            <div class="col-6">
                                <div class="small opacity-75">Ingreso Total</div>
                                <div class="fw-bold fs-5"><?= format_money($incomeSuggestion['total'], 'DOP') ?></div>
                            </div>
                            <div class="col-6 text-warning">
                                <div class="small opacity-75">Libre</div>
                                <div class="fw-bold fs-5"><?= format_money($incomeSuggestion['free'], 'DOP') ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <?php 
                $lastDate = '';
                if (empty($movements)): 
                ?>
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-expressionless display-1 text-light"></i>
                        <p class="text-muted mt-3">No hay movimientos registrados todavía.</p>
                        <a href="?module=movements&action=create" class="btn btn-primary rounded-pill px-4 mt-2">Registrar el primero</a>
                    </div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($movements as $movement): 
                            if ($movement['date'] !== $lastDate): 
                                $lastDate = $movement['date'];
                                $displayDate = date('d M, Y', strtotime($lastDate));
                                if ($lastDate === date('Y-m-d')) $displayDate = 'Hoy';
                                elseif ($lastDate === date('Y-m-d', strtotime('-1 day'))) $displayDate = 'Ayer';
                        ?>
                            <div class="list-group-item bg-light text-muted fw-bold small text-uppercase py-2 px-4" style="letter-spacing: 1px;">
                                <?= $displayDate ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="list-group-item border-0 py-3 px-4 d-flex align-items-center hover-bg-light">
                            <div class="flex-shrink-0 me-3">
                                <i class="bi <?= get_movement_icon($movement['type']) ?> fs-3"></i>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-0 fw-bold text-dark text-truncate"><?= e($movement['concept']) ?></h6>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <?= get_category_badge($movement['category']) ?>
                                            <span class="text-muted small"><i class="bi bi-wallet2 me-1"></i><?= e($movement['account_origin_name']) ?></span>
                                            <?php if ($movement['account_dest_name']): ?>
                                                <i class="bi bi-chevron-right text-muted x-small"></i>
                                                <span class="text-muted small"><?= e($movement['account_dest_name']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        <?php 
                                            $amountClass = ($movement['type'] === 'ingreso') ? 'text-success' : (($movement['type'] === 'gasto' || $movement['type'] === 'gasto_laboral') ? 'text-danger' : 'text-dark');
                                            $prefix = ($movement['type'] === 'ingreso') ? '+' : (($movement['type'] === 'gasto' || $movement['type'] === 'gasto_laboral') ? '-' : '');
                                        ?>
                                        <div class="fw-bold <?= $amountClass ?> fs-5">
                                            <?= $prefix . format_money(abs((float)$movement['amount']), $movement['currency']) ?>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-link btn-sm text-muted p-0 border-0" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                                <li><a class="dropdown-item py-2" href="?module=movements&action=edit&id=<?= (int)$movement['id'] ?>"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="post" action="?module=movements&action=delete" onsubmit="return confirm('¿Eliminar este movimiento?');">
                                                        <input type="hidden" name="id" value="<?= (int)$movement['id'] ?>">
                                                        <button class="dropdown-item py-2 text-danger" type="submit"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($movement['note']): ?>
                                    <div class="mt-2 text-muted small bg-light p-2 rounded-3">
                                        <i class="bi bi-sticky me-1"></i> <?= e($movement['note']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-xl-4">
        <?php if (!empty($pendingFixedExpenses)): ?>
            <div class="card border-0 shadow-sm mb-4 border-start border-4 border-primary">
                <div class="card-body">
                    <h6 class="fw-bold mb-3 d-flex align-items-center">
                        <i class="bi bi-bell-fill text-primary me-2"></i>
                        Recordatorios Pendientes
                    </h6>
                    <div class="list-group list-group-flush">
                        <?php foreach ($pendingFixedExpenses as $item): ?>
                            <div class="list-group-item px-0 border-0 mb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold small"><?= e($item['name']) ?></div>
                                        <div class="text-muted x-small"><?= e($item['period_label']) ?></div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold small text-primary"><?= format_money((float)$item['amount'], $item['account_currency'] ?? 'DOP') ?></div>
                                        <?php if (!empty($item['account_id'])): ?>
                                            <form method="post" action="?module=movements&action=applyFixed">
                                                <input type="hidden" name="fixed_expense_id" value="<?= (int)$item['id'] ?>">
                                                <button class="btn btn-sm btn-link text-decoration-none p-0 fw-bold x-small" type="submit text-primary">APLICAR AHORA</button>
                                            </form>
                                        <?php else: ?>
                                            <a href="?module=movements&action=create&fixed_expense_id=<?= (int)$item['id'] ?>" class="btn btn-sm btn-link text-decoration-none p-0 fw-bold x-small">COMPLETAR</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm bg-dark text-white">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Resumen Rápido</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="opacity-75">Balance General (DOP)</span>
                    <span class="fw-bold"><?= format_money(array_sum(array_column($accounts, 'balance')), 'DOP') ?></span>
                </div>
                <hr class="border-secondary opacity-25">
                <div class="text-center py-2">
                    <i class="bi bi-graph-up-arrow text-success fs-1"></i>
                    <p class="small opacity-75 mt-2 mb-0">Mantienes un flujo de caja saludable este mes.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-bg-light:hover { background-color: #f8fafc; cursor: default; }
    .x-small { font-size: 0.7rem; }
</style>