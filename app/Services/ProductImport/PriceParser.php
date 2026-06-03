<?php

namespace App\Services\ProductImport;

class PriceParser
{
    public function parse(mixed $value): ?int
    {
        if ($value === null) {
            return null;
        }

        if (is_int($value)) {
            return $value > 0 ? $value : null;
        }

        if (is_float($value)) {
            return $value > 0 ? (int) round($value) : null;
        }

        $value = trim((string) $value);

        if ($value === '' || in_array(mb_strtolower($value), ['-', 'n/a', 'na', 'call', 'liên hệ', 'lien he'], true)) {
            return null;
        }

        $normalized = mb_strtolower($value);
        $normalized = str_replace(['vnđ', 'vnd', 'đ', '₫'], '', $normalized);
        $normalized = preg_replace('/\s+/u', '', $normalized) ?? '';

        if (preg_match('/[a-z]/i', $normalized)) {
            return null;
        }

        if (preg_match('/^\d+(?:[.,]\d{1,2})$/', $normalized) && ! preg_match('/^\d{1,3}[.,]\d{3}$/', $normalized)) {
            return (int) round((float) str_replace(',', '.', $normalized));
        }

        $digits = preg_replace('/[^\d]/', '', $normalized);

        if (! $digits) {
            return null;
        }

        $price = (int) $digits;

        return $price > 0 ? $price : null;
    }
}
