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
