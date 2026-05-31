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
            <select class="form-select" name="account_origin_id" required>
                <?php foreach ($accounts as $account): ?>
                    <option value="<?= (int)$account['id'] ?>" <?= (!empty($movement['account_origin_id']) && (int)$movement['account_origin_id'] === (int)$account['id']) ? 'selected' : '' ?>><?= e($account['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Cuenta destino (opcional)</label>
            <select class="form-select" name="account_dest_id">
                <option value="">-</option>
                <?php foreach ($accounts as $account): ?>
                    <option value="<?= (int)$account['id'] ?>" <?= (!empty($movement['account_dest_id']) && (int)$movement['account_dest_id'] === (int)$account['id']) ? 'selected' : '' ?>><?= e($account['name']) ?></option>
                <?php endforeach; ?>
            </select>
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
<script>
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
