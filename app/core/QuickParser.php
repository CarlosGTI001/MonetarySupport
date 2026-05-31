<?php
declare(strict_types=1);

namespace App\Core;

class QuickParser
{
    public static function parse(string $text, array $accounts): ?array
    {
        if ($text === '') {
            return null;
        }

        $lower = strtolower($text);

        preg_match('/(\d+([.,]\d+)?)/', $lower, $amountMatch);
        $amount = $amountMatch[1] ?? null;
        if ($amount === null) {
            return null;
        }
        $amount = (float)str_replace(',', '.', $amount);

        $type = 'gasto';
        if (str_contains($lower, 'ingreso') || str_contains($lower, 'cobre') || str_contains($lower, 'recibi')) {
            $type = 'ingreso';
        } elseif (str_contains($lower, 'transfer')) {
            $type = 'transferencia';
        }

        $accountId = null;
        $accountName = null;
        foreach ($accounts as $account) {
            $name = strtolower($account['name']);
            if ($name !== '' && str_contains($lower, $name)) {
                $accountId = (int)$account['id'];
                $accountName = $account['name'];
                break;
            }
        }

        if ($accountId === null) {
            foreach ($accounts as $account) {
                if (str_contains($lower, 'efectivo') && $account['name'] === 'Efectivo') {
                    $accountId = (int)$account['id'];
                    $accountName = $account['name'];
                    break;
                }
            }
        }

        if ($accountId === null) {
            return null;
        }

        $concept = $lower;
        $concept = preg_replace('/\d+([.,]\d+)?/', '', $concept);
        if ($accountName) {
            $concept = str_replace(strtolower($accountName), '', $concept);
        }
        $concept = str_replace(['gaste', 'gasto', 'pague', 'pago', 'ingreso', 'cobre', 'recibi', 'transferi', 'transferencia'], '', $concept);
        $concept = trim(preg_replace('/\s+/', ' ', $concept));
        if ($concept === '') {
            $concept = 'Registro rapido';
        }

        return [
            'amount' => $amount,
            'account_id' => $accountId,
            'account_name' => $accountName,
            'type' => $type,
            'concept' => ucfirst($concept),
            'category' => 'rapido',
        ];
    }
}
