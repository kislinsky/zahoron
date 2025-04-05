<?php

namespace App\Traits;

trait HasPhone
{
    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^\d]/', '', $phone);

        if (strlen($digits) === 11 && str_starts_with($digits, '8')) {
            return '+7' . substr($digits, 1);
        }

        if (strlen($digits) === 10) {
            return '+7' . $digits;
        }

        if (strlen($digits) === 11 && str_starts_with($digits, '7')) {
            return '+' . $digits;
        }

        return $phone;
    }
}