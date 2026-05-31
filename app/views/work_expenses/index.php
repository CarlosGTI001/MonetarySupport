<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-5">
    <div>
        <h3 class="fw-bold mb-1">Gastos Laborales</h3>
        <p class="text-muted mb-0 small">Seguimiento de gastos reembolsables y proyectos de trabajo.</p>
    </div>
    <a class="btn btn-primary fw-bold px-4 rounded-pill d-flex align-items-center shadow-sm" href="?module=work_expenses&action=create">
        <i class="bi bi-plus-lg me-2"></i> Nuevo Gasto Laboral
    </a>
</div>

<div class="row g-4 mb-5">
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm bg-warning text-dark h-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="p-2 bg-white bg-opacity-50 rounded-3">
                        <i class="bi bi-cash-coin fs-4"></i>
                    </div>
                    <span class="badge bg-dark rounded-pill px-3">PENDIENTE</span>
                </div>
                <div class="small fw-bold text-uppercase opacity-75" style="font-size: 0.65rem;">Por Reembolsar</div>
                <div class="fs-2 fw-bold"><?= format_money($pendingTotal, 'DOP') ?></div>
                <p class="small mt-2 mb-0 opacity-75">Este monto debe ser devuelto por la empresa o cliente.</p>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm h-100 bg-dark text-white">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="row w-100 align-items-center">
                    <div class="col-md-7">
                        <h5 class="fw-bold mb-2">Consejo Profesional</h5>
                        <p class="text-white-50 small mb-0">Mantén tus recibos digitalizados. Un seguimiento preciso garantiza que nunca pierdas dinero en gastos de proyectos.</p>
                    </div>
                    <div class="col-md-5 text-end d-none d-md-block">
                        <i class="bi bi-briefcase display-4 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="border-0 ps-4 py-3" style="font-size: 0.65rem;">Gasto / Proyecto</th>
                        <th class="border-0 py-3" style="font-size: 0.65rem;">Fecha & Cuenta</th>
                        <th class="border-0 text-end py-3" style="font-size: 0.65rem;">Monto</th>
                        <th class="border-0 text-center py-3" style="font-size: 0.65rem;">Estado</th>
                        <th class="border-0 pe-4 py-3 text-end" style="font-size: 0.65rem;"></th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php foreach ($items as $item): 
                        $isReimbursed = (bool)$item['reimbursed'];
                    ?>
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark"><?= e($item['concept']) ?></div>
                            <div class="text-muted x-small text-uppercase"><i class="bi bi-tag me-1"></i><?= e($item['project'] ?: 'Sin Proyecto') ?></div>
                        </td>
                        <td class="py-3">
                            <div class="small text-dark"><?= e($item['date']) ?></div>
                            <div class="text-muted x-small"><i class="bi bi-credit-card-2-back me-1"></i><?= e($item['account_name']) ?></div>
                        </td>
                        <td class="text-end py-3">
                            <div class="fw-bold text-dark"><?= format_money((float)$item['amount'], 'DOP') ?></div>
                        </td>
                        <td class="text-center py-3">
                            <?php if ($isReimbursed): ?>
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">REEMBOLSADO</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis rounded-pill px-3">PENDIENTE</span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <div class="dropdown">
                                <button class="btn btn-link btn-sm text-muted p-0 border-0" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                                    <li><a class="dropdown-item py-2" href="?module=work_expenses&action=edit&id=<?= (int)$item['id'] ?>"><i class="bi bi-pencil me-2"></i>Editar</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form method="post" action="?module=work_expenses&action=delete" onsubmit="return confirm('¿Eliminar este gasto?');">
                                            <input type="hidden" name="id" value="<?= (int)$item['id'] ?>">
                                            <button class="dropdown-item py-2 text-danger" type="submit"><i class="bi bi-trash me-2"></i>Eliminar</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if (empty($items)): ?>
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-light"></i>
                <p class="text-muted mt-3">No hay gastos laborales registrados.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .x-small { font-size: 0.7rem; }
</style>