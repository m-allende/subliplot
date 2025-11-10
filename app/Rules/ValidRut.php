<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidRut implements Rule
{
    public function passes($attribute, $value)
    {
        $rut = strtoupper(str_replace(['.', ' '], '', $value));
        if (!str_contains($rut, '-')) return false;
        [$num, $dv] = explode('-', $rut);
        if (!ctype_digit($num)) return false;

        $sum = 0; $factor = 2;
        for ($i = strlen($num) - 1; $i >= 0; $i--) {
            $sum += $num[$i] * $factor;
            $factor = $factor === 7 ? 2 : $factor + 1;
        }

        $expected = 11 - ($sum % 11);
        $expectedDv = $expected == 11 ? '0' : ($expected == 10 ? 'K' : (string) $expected);
        return $dv === $expectedDv;
    }

    public function message()
    {
        return 'El RUT ingresado no es válido.';
    }
}
