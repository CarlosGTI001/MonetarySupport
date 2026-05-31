<?php
$movement = $movement ?? [];
$fixedExpenses = $fixedExpenses ?? [];
$savingsRules = $savingsRules ?? [];
$isEdit = !empty($movement['id']);
$displayAmount = $movement['amount'] ?? '';
if (!empty($movement) && ($movement['type'] ?? '') === 'ajuste') {
    $displayAmount = abs((float)$movement['amount']);
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold mb-0"><?= $isEdit ? 'Editar movimiento' : 'Nuevo movimiento' ?></h3>
    <a class="btn btn-outline-secondary btn-sm" href="?module=movements">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>
        Volver
    </a>
</div>

<div class="card shadow-sm mb-5">
    <div class="card-body p-4">
        <form method="post" action="?module=movements&action=<?= $isEdit ? 'save' : 'store' ?>">
            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= (int)$movement['id'] ?>">
            <?php endif; ?>
            <?php if (!empty($movement['financing_id'])): ?>
                <input type="hidden" name="financing_id" value="<?= (int)$movement['financing_id'] ?>">
            <?php endif; ?>
            
            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-bold small text-uppercase">Fecha</label>
                    <input class="form-control" type="date" name="date" value="<?= e($movement['date'] ?? current_date()) ?>">
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-bold small text-uppercase">Tipo</label>
                    <select class="form-select" name="type" id="movementType">
                        <?php foreach (['ingreso' => 'Ingreso', 'gasto' => 'Gasto', 'transferencia' => 'Transferencia', 'ajuste' => 'Ajuste', 'gasto_laboral' => 'Gasto laboral'] as $value => $label): ?>
                            <option value="<?= $value ?>" <?= (($movement['type'] ?? 'gasto') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-bold small text-uppercase">Monto</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input class="form-control fw-bold text-primary" type="number" step="0.01" name="amount" value="<?= e((string)$displayAmount) ?>" required>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <label class="form-label fw-bold small text-uppercase">Moneda</label>
                    <select class="form-select" name="currency">
                        <?php foreach (['DOP', 'USD'] as $currency): ?>
                            <option value="<?= $currency ?>" <?= (($movement['currency'] ?? 'DOP') === $currency) ? 'selected' : '' ?>><?= $currency ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small text-uppercase">Cuenta origen</label>
                    <div class="input-group">
                        <select class="form-select" name="account_origin_id" id="account_origin_id" required>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?= (int)$account['id'] ?>" 
                                        data-balance="<?= e((string)$account['balance']) ?>"
                                        data-currency="<?= e($account['currency']) ?>"
                                        <?= (!empty($movement['account_origin_id']) && (int)$movement['account_origin_id'] === (int)$account['id']) ? 'selected' : '' ?>>
                                    <?= e($account['name']) ?> (<?= format_money((float)$account['balance'], $account['currency']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-outline-primary px-3" type="button" onclick="openAccountSearch('account_origin_id')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                              <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold small text-uppercase">Cuenta destino (opcional)</label>
                    <div class="input-group">
                        <select class="form-select" name="account_dest_id" id="account_dest_id">
                            <option value="">-</option>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?= (int)$account['id'] ?>" 
                                        data-balance="<?= e((string)$account['balance']) ?>"
                                        data-currency="<?= e($account['currency']) ?>"
                                        <?= (!empty($movement['account_dest_id']) && (int)$movement['account_dest_id'] === (int)$account['id']) ? 'selected' : '' ?>>
                                    <?= e($account['name']) ?> (<?= format_money((float)$account['balance'], $account['currency']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-outline-primary px-3" type="button" onclick="openAccountSearch('account_dest_id')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                              <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Sección: Calculadora de Conversión (Dinámica) -->
                <div class="col-12" id="currencyCalculator" style="display: none;">
                    <div class="card border-primary border-opacity-25 bg-primary bg-opacity-10 shadow-none mb-2">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-calculator fs-5 me-2 text-primary"></i>
                                <h6 class="fw-bold mb-0 text-primary">Calculadora de Conversión</h6>
                                <span class="badge bg-white text-primary border ms-auto">1 USD = <?= number_format(get_exchange_rate(), 2) ?> DOP</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-12 col-md-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white fw-bold" id="calcSourceLabel">DOP</span>
                                        <input type="number" step="0.01" id="calcSourceAmount" class="form-control" placeholder="Monto a enviar">
                                    </div>
                                </div>
                                <div class="col-12 col-md-2 text-center align-self-center">
                                    <i class="bi bi-arrow-left-right text-muted"></i>
                                </div>
                                <div class="col-12 col-md-5">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white fw-bold" id="calcTargetLabel">USD</span>
                                        <input type="number" step="0.01" id="calcTargetAmount" class="form-control" placeholder="Monto a recibir">
                                    </div>
                                </div>
                                <div class="col-12 mt-2">
                                    <button type="button" id="applyCalcAmount" class="btn btn-sm btn-primary w-100 fw-bold">
                                        USAR ESTE MONTO EN EL FORMULARIO
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-bold small text-uppercase">Categoría</label>
                    <input class="form-control" name="category" value="<?= e($movement['category'] ?? '') ?>" placeholder="Ej: Comida, Transporte...">
                </div>
                <div class="col-12 col-md-8">
                    <label class="form-label fw-bold small text-uppercase">Concepto</label>
                    <input class="form-control" name="concept" value="<?= e($movement['concept'] ?? '') ?>" placeholder="Descripción breve del movimiento">
                </div>

                <div class="col-12 col-md-4" id="adjustField">
                    <label class="form-label fw-bold small text-uppercase text-info">Tipo de Ajuste</label>
                    <select class="form-select border-info" name="adjust_sign">
                        <option value="add">Sumar al balance</option>
                        <option value="subtract" <?= (!empty($movement['amount']) && (float)$movement['amount'] < 0) ? 'selected' : '' ?>>Restar al balance</option>
                    </select>
                </div>
                
                <div class="col-12 col-md-4 d-flex align-items-center mt-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="reimbursable" id="reimbursable" <?= (!empty($movement['reimbursable'])) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold small text-uppercase" for="reimbursable">Reembolsable</label>
                    </div>
                </div>
                <div class="col-12 col-md-4 d-flex align-items-center mt-md-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="reimbursed" id="reimbursed" <?= (!empty($movement['reimbursed'])) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold small text-uppercase" for="reimbursed">Reembolsado</label>
                    </div>
                </div>

                <!-- Sección: Impuesto DGII (RD) -->
                <div class="col-12 col-md-4 d-flex align-items-center mt-md-4" id="dgiiTaxField">
                    <div class="form-check form-switch p-3 border rounded bg-light bg-opacity-50">
                        <input class="form-check-input" type="checkbox" name="apply_dgii_tax" id="apply_dgii_tax" value="1" <?= (!empty($movement['apply_dgii_tax'])) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-bold small text-uppercase text-danger" for="apply_dgii_tax">
                            <i class="bi bi-percent me-1"></i> Impuesto DGII (0.15%)
                        </label>
                        <div class="x-small text-muted mt-1">Aplica para transferencias a terceros en RD.</div>
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-bold small text-uppercase">Nota adicional</label>
                    <textarea class="form-control" name="note" rows="3" placeholder="Detalles adicionales opcionales..."><?= e($movement['note'] ?? '') ?></textarea>
                </div>

                <div class="col-12">
                    <div class="accordion border-0" id="suggestionsAccordion">
                        <div class="accordion-item border-0 rounded shadow-sm overflow-hidden">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed fw-bold small text-uppercase bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSuggestions">
                                    Vincular a Gasto Fijo o Ahorro
                                </button>
                            </h2>
                            <div id="collapseSuggestions" class="accordion-collapse collapse" data-bs-parent="#suggestionsAccordion">
                                <div class="accordion-body border-top">
                                    <div class="row g-3">
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold">Gasto fijo</label>
                                            <select class="form-select form-select-sm" name="fixed_expense_id" id="fixedExpenseSelect">
                                                <option value="">- Seleccionar -</option>
                                                <?php foreach ($fixedExpenses as $item): ?>
                                                    <option value="<?= (int)$item['id'] ?>"
                                                            data-amount="<?= e((string)$item['amount']) ?>"
                                                            data-name="<?= e($item['name']) ?>"
                                                            data-account="<?= e((string)($item['account_id'] ?? '')) ?>"
                                                            data-currency="<?= e((string)($item['account_currency'] ?? 'DOP')) ?>"
                                                            data-note="<?= e($item['note'] ?? '') ?>"
                                                            data-category="Gasto fijo"
                                                        <?= (!empty($movement['fixed_expense_id']) && (int)$movement['fixed_expense_id'] === (int)$item['id']) ? 'selected' : '' ?>>
                                                        <?= e($item['name']) ?> (<?= format_money((float)$item['amount'], $item['account_currency'] ?? 'DOP') ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-6">
                                            <label class="form-label small fw-bold">Regla de ahorro</label>
                                            <select class="form-select form-select-sm" name="savings_rule_id" id="savingsRuleSelect">
                                                <option value="">- Seleccionar -</option>
                                                <?php foreach ($savingsRules as $rule): ?>
                                                    <option value="<?= (int)$rule['id'] ?>"
                                                            data-amount="<?= e((string)($rule['amount'] ?? 0)) ?>"
                                                            data-name="<?= e($rule['name']) ?>"
                                                            data-account="<?= e((string)($rule['target_account_id'] ?? '')) ?>"
                                                            data-currency="<?= e((string)($rule['account_currency'] ?? 'DOP')) ?>"
                                                            data-note="<?= e($rule['note'] ?? '') ?>"
                                                            data-category="Ahorro"
                                                        <?= (!empty($movement['savings_rule_id']) && (int)$movement['savings_rule_id'] === (int)$rule['id']) ? 'selected' : '' ?>>
                                                        <?= e($rule['name']) ?> (<?= format_money((float)($rule['amount'] ?? 0), $rule['account_currency'] ?? 'DOP') ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-5 pt-3 border-top d-flex flex-column flex-md-row gap-2">
                <button class="btn btn-primary px-5 py-2 fw-bold flex-grow-1 flex-md-grow-0" type="submit">
                    <?= $isEdit ? 'Actualizar Movimiento' : 'Guardar Movimiento' ?>
                </button>
                <a class="btn btn-outline-secondary px-4 py-2" href="?module=movements">Cancelar</a>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="accountSearchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">Seleccionar Cuenta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="input-group mb-4 shadow-sm rounded">
                    <span class="input-group-text bg-white border-end-0"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg></span>
                    <input type="text" id="accountSearchInput" class="form-control border-start-0 ps-0" placeholder="Filtrar por nombre...">
                </div>
                <div class="list-group list-group-flush rounded border" id="accountSearchList">
                    <?php foreach ($accounts as $account): ?>
                        <button type="button" class="list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center account-search-item" 
                                data-id="<?= (int)$account['id'] ?>" 
                                data-name="<?= e($account['name']) ?>">
                            <div>
                                <div class="fw-bold text-dark"><?= e($account['name']) ?></div>
                                <div class="small text-muted text-uppercase" style="font-size: 0.7rem;"><?= e($account['type']) ?> • <?= e($account['purpose']) ?></div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-primary"><?= format_money((float)$account['balance'], $account['currency']) ?></div>
                            </div>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(() => {
    let activeTargetSelectId = null;
    let modalInstance = null;

    window.openAccountSearch = (targetId) => {
        activeTargetSelectId = targetId;
        const modalEl = document.getElementById('accountSearchModal');
        const inputEl = document.getElementById('accountSearchInput');
        const items = document.querySelectorAll('.account-search-item');
        
        if (!modalInstance) {
            modalInstance = new bootstrap.Modal(modalEl);
            inputEl.addEventListener('input', (e) => {
                const q = e.target.value.toLowerCase();
                items.forEach(item => {
                    const name = item.dataset.name.toLowerCase();
                    item.style.display = name.includes(q) ? '' : 'none';
                });
            });
            items.forEach(item => {
                item.addEventListener('click', () => {
                    const targetSelect = document.getElementById(activeTargetSelectId);
                    if (targetSelect) {
                        targetSelect.value = item.dataset.id;
                        targetSelect.dispatchEvent(new Event('change'));
                    }
                    modalInstance.hide();
                });
            });
        }
        
        inputEl.value = '';
        items.forEach(i => i.style.display = '');
        modalInstance.show();
        setTimeout(() => inputEl.focus(), 500);
    };
})();

(() => {
    const setValue = (name, value) => {
        const field = document.querySelector(`[name="${name}"]`);
        if (field && value !== undefined && value !== null && value !== '') {
            field.value = value;
        }
    };
    const clearSelect = (other) => {
        if (other && other.value) {
            other.value = '';
        }
    };

    const fixedSelect = document.getElementById('fixedExpenseSelect');
    if (fixedSelect) {
        fixedSelect.addEventListener('change', () => {
            const option = fixedSelect.options[fixedSelect.selectedIndex];
            if (!option || !option.value) return;
            clearSelect(document.getElementById('savingsRuleSelect'));
            setValue('type', 'gasto');
            setValue('concept', option.dataset.name);
            setValue('amount', option.dataset.amount);
            setValue('category', option.dataset.category || 'Gasto fijo');
            setValue('account_origin_id', option.dataset.account);
            setValue('currency', option.dataset.currency);
            const noteField = document.querySelector('[name="note"]');
            if (noteField && !noteField.value && option.dataset.note) {
                noteField.value = option.dataset.note;
            }
        });
    }

    const savingsSelect = document.getElementById('savingsRuleSelect');
    if (savingsSelect) {
        savingsSelect.addEventListener('change', () => {
            const option = savingsSelect.options[savingsSelect.selectedIndex];
            if (!option || !option.value) return;
            clearSelect(document.getElementById('fixedExpenseSelect'));
            setValue('type', 'transferencia');
            setValue('concept', option.dataset.name);
            setValue('amount', option.dataset.amount);
            setValue('category', option.dataset.category || 'Ahorro');
            setValue('account_dest_id', option.dataset.account);
            setValue('currency', option.dataset.currency);
            const noteField = document.querySelector('[name="note"]');
            if (noteField && !noteField.value && option.dataset.note) {
                noteField.value = option.dataset.note;
            }
        });
    }

    const destSelect = document.getElementById('account_dest_id');
    if (destSelect) {
        destSelect.addEventListener('change', () => {
            if (destSelect.value !== '' && typeSelect.value === 'gasto') {
                typeSelect.value = 'transferencia';
                toggleFields();
            }
        });
    }

    const typeSelect = document.getElementById('movementType');
    const adjustField = document.getElementById('adjustField');
    const dgiiField = document.getElementById('dgiiTaxField');
    const calcField = document.getElementById('currencyCalculator');
    const exchangeRate = <?= get_exchange_rate() ?>;

    const getAccountCurrency = (selectId) => {
        const sel = document.getElementById(selectId);
        if (!sel || sel.selectedIndex < 0) return null;
        return sel.options[sel.selectedIndex].dataset.currency;
    };

    const toggleFields = () => {
        if (!typeSelect) return;
        const type = typeSelect.value;
        const movCurrency = document.querySelector('[name="currency"]').value;
        const originCurrency = getAccountCurrency('account_origin_id');
        const destCurrency = getAccountCurrency('account_dest_id');
        
        // Ajuste logic
        if (adjustField) {
            const showAdjust = type === 'ajuste';
            adjustField.style.display = showAdjust ? '' : 'none';
            const adjustSelect = adjustField.querySelector('select');
            if (adjustSelect) adjustSelect.disabled = !showAdjust;
        }

        // DGII logic
        if (dgiiField) {
            const showDgii = (type === 'gasto' || type === 'transferencia' || type === 'gasto_laboral');
            dgiiField.style.display = showDgii ? '' : 'none';
        }

        // Calculator logic
        if (calcField) {
            const isTransfer = type === 'transferencia' && destCurrency;
            const isCrossCurrency = (originCurrency && originCurrency !== movCurrency) || 
                                    (destCurrency && destCurrency !== movCurrency) ||
                                    (originCurrency && destCurrency && originCurrency !== destCurrency);
            
            if (isCrossCurrency || isTransfer) {
                calcField.style.display = '';
                document.getElementById('calcSourceLabel').textContent = originCurrency || 'DOP';
                document.getElementById('calcTargetLabel').textContent = destCurrency || movCurrency;
            } else {
                calcField.style.display = 'none';
            }
        }
    };

    // Calculator Live Logic
    const srcInput = document.getElementById('calcSourceAmount');
    const targetInput = document.getElementById('calcTargetAmount');
    const mainAmountInput = document.querySelector('[name="amount"]');

    srcInput.addEventListener('input', () => {
        const srcCur = document.getElementById('calcSourceLabel').textContent;
        const tarCur = document.getElementById('calcTargetLabel').textContent;
        if (srcCur === 'DOP' && tarCur === 'USD') targetInput.value = (srcInput.value / exchangeRate).toFixed(2);
        else if (srcCur === 'USD' && tarCur === 'DOP') targetInput.value = (srcInput.value * exchangeRate).toFixed(2);
        else targetInput.value = srcInput.value;
    });

    targetInput.addEventListener('input', () => {
        const srcCur = document.getElementById('calcSourceLabel').textContent;
        const tarCur = document.getElementById('calcTargetLabel').textContent;
        if (tarCur === 'DOP' && srcCur === 'USD') srcInput.value = (targetInput.value / exchangeRate).toFixed(2);
        else if (tarCur === 'USD' && srcCur === 'DOP') srcInput.value = (targetInput.value * exchangeRate).toFixed(2);
        else srcInput.value = targetInput.value;
    });

    document.getElementById('applyCalcAmount').addEventListener('click', () => {
        const movCurrency = document.querySelector('[name="currency"]').value;
        const tarCur = document.getElementById('calcTargetLabel').textContent;
        const srcCur = document.getElementById('calcSourceLabel').textContent;
        
        // If movement currency is same as target, use target value. Else use source.
        if (movCurrency === tarCur) mainAmountInput.value = targetInput.value;
        else mainAmountInput.value = srcInput.value;
        
        mainAmountInput.dispatchEvent(new Event('input'));
    });

    if (typeSelect) {
        typeSelect.addEventListener('change', toggleFields);
        document.getElementById('account_origin_id').addEventListener('change', toggleFields);
        document.getElementById('account_dest_id').addEventListener('change', toggleFields);
        document.querySelector('[name="currency"]').addEventListener('change', toggleFields);
        toggleFields();
    }
})();
</script>