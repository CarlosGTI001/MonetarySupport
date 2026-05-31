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
<h3><?= $isEdit ? 'Editar movimiento' : 'Nuevo movimiento' ?></h3>
<form method="post" action="?module=movements&action=<?= $isEdit ? 'update' : 'store' ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= (int)$movement['id'] ?>">
    <?php endif; ?>
    <div class="row g-3">
        <div class="col-md-3">
            <label class="form-label">Fecha</label>
            <input class="form-control" type="date" name="date" value="<?= e($movement['date'] ?? current_date()) ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Tipo</label>
            <select class="form-select" name="type" id="movementType">
                <?php foreach (['ingreso' => 'Ingreso', 'gasto' => 'Gasto', 'transferencia' => 'Transferencia', 'ajuste' => 'Ajuste', 'gasto_laboral' => 'Gasto laboral'] as $value => $label): ?>
                    <option value="<?= $value ?>" <?= (($movement['type'] ?? 'gasto') === $value) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6">
            <label class="form-label">Gasto fijo (opcional)</label>
            <select class="form-select" name="fixed_expense_id" id="fixedExpenseSelect">
                <option value="">-</option>
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
        <div class="col-md-6">
            <label class="form-label">Ahorro fijo (opcional)</label>
            <select class="form-select" name="savings_rule_id" id="savingsRuleSelect">
                <option value="">-</option>
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
        <div class="col-md-3">
            <label class="form-label">Cuenta origen</label>
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
                <button class="btn btn-outline-secondary" type="button" onclick="openAccountSearch('account_origin_id')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                      <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Cuenta destino (opcional)</label>
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
                <button class="btn btn-outline-secondary" type="button" onclick="openAccountSearch('account_dest_id')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                      <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                    </svg>
                </button>
            </div>
        </div>
        <div class="col-md-3">
            <label class="form-label">Categoria</label>
            <input class="form-control" name="category" value="<?= e($movement['category'] ?? '') ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Concepto</label>
            <input class="form-control" name="concept" value="<?= e($movement['concept'] ?? '') ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Monto</label>
            <input class="form-control" type="number" step="0.01" name="amount" value="<?= e((string)$displayAmount) ?>" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Moneda</label>
            <select class="form-select" name="currency">
                <?php foreach (['DOP', 'USD'] as $currency): ?>
                    <option value="<?= $currency ?>" <?= (($movement['currency'] ?? 'DOP') === $currency) ? 'selected' : '' ?>><?= $currency ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3" id="adjustField">
            <label class="form-label">Ajuste</label>
            <select class="form-select" name="adjust_sign">
                <option value="add">Sumar</option>
                <option value="subtract" <?= (!empty($movement['amount']) && (float)$movement['amount'] < 0) ? 'selected' : '' ?>>Restar</option>
            </select>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="reimbursable" <?= (!empty($movement['reimbursable'])) ? 'checked' : '' ?>>
                <label class="form-check-label">Reembolsable</label>
            </div>
        </div>
        <div class="col-md-3 d-flex align-items-end">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="reimbursed" <?= (!empty($movement['reimbursed'])) ? 'checked' : '' ?>>
                <label class="form-check-label">Reembolsado</label>
            </div>
        </div>
        <div class="col-12">
            <label class="form-label">Nota</label>
            <textarea class="form-control" name="note" rows="2"><?= e($movement['note'] ?? '') ?></textarea>
        </div>
    </div>
    <div class="mt-3">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="?module=movements">Cancelar</a>
    </div>
</form>

<!-- Modal de búsqueda de cuentas -->
<div class="modal fade" id="accountSearchModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Buscar cuenta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="accountSearchInput" class="form-control mb-3" placeholder="Buscar por nombre...">
                <div class="list-group" id="accountSearchList">
                    <?php foreach ($accounts as $account): ?>
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center account-search-item" 
                                data-id="<?= (int)$account['id'] ?>" 
                                data-name="<?= e($account['name']) ?>">
                            <span>
                                <strong><?= e($account['name']) ?></strong>
                                <br>
                                <small class="text-muted"><?= e($account['type']) ?> - <?= e($account['purpose']) ?></small>
                            </span>
                            <span class="badge bg-primary rounded-pill">
                                <?= format_money((float)$account['balance'], $account['currency']) ?>
                            </span>
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
    const select = document.getElementById('fixedExpenseSelect');
    if (!select) {
        return;
    }
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
    select.addEventListener('change', () => {
        const option = select.options[select.selectedIndex];
        if (!option || !option.value) {
            return;
        }
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

    const savingsSelect = document.getElementById('savingsRuleSelect');
    if (savingsSelect) {
        savingsSelect.addEventListener('change', () => {
            const option = savingsSelect.options[savingsSelect.selectedIndex];
            if (!option || !option.value) {
                return;
            }
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

    const typeSelect = document.getElementById('movementType');
    const adjustField = document.getElementById('adjustField');
    const toggleAdjust = () => {
        if (!typeSelect || !adjustField) {
            return;
        }
        const show = typeSelect.value === 'ajuste';
        adjustField.style.display = show ? '' : 'none';
        const adjustSelect = adjustField.querySelector('select');
        if (adjustSelect) {
            adjustSelect.disabled = !show;
        }
    };
    if (typeSelect) {
        typeSelect.addEventListener('change', toggleAdjust);
        toggleAdjust();
    }
})();
</script>
