<?php
declare(strict_types=1);

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function asset(string $path): string
{
    return $path;
}

function format_money(float $value, string $currency = 'DOP'): string
{
    return number_format($value, 2) . ' ' . $currency;
}

function current_date(): string
{
    return date('Y-m-d');
}

function get_exchange_rate(): float
{
    // Tasa fija por defecto, podría ser una configuración en la DB
    return 60.00;
}

function convert_currency(float $amount, string $from, string $to): float
{
    if ($from === $to) return $amount;
    
    $rate = get_exchange_rate();
    if ($from === 'USD' && $to === 'DOP') {
        return $amount * $rate;
    }
    if ($from === 'DOP' && $to === 'USD') {
        return $amount / $rate;
    }
    
    return $amount;
}

function get_movement_icon(string $type): string
{
    return match ($type) {
        'ingreso' => 'bi-arrow-down-left-circle-fill text-success',
        'gasto', 'gasto_laboral' => 'bi-arrow-up-right-circle-fill text-danger',
        'transferencia' => 'bi-arrow-left-right text-primary',
        'ajuste' => 'bi-sliders2 text-info',
        default => 'bi-question-circle'
    };
}

function get_category_badge(string $category): string
{
    $colors = [
        'Comida' => 'bg-warning-subtle text-warning-emphasis',
        'Transporte' => 'bg-info-subtle text-info-emphasis',
        'Ahorro' => 'bg-primary-subtle text-primary-emphasis',
        'Gasto fijo' => 'bg-danger-subtle text-danger-emphasis',
        'Financiamiento' => 'bg-dark-subtle text-dark-emphasis',
    ];
    
    $class = $colors[$category] ?? 'bg-secondary-subtle text-secondary-emphasis';
    return '<span class="badge ' . $class . ' rounded-pill px-3">' . e($category) . '</span>';
}
