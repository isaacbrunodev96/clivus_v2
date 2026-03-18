<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Phone implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = preg_replace('/[^0-9]/', '', (string) $value);
        
        // Brazilian phones are 10 (fixed) or 11 (mobile) digits
        if (!in_array(strlen($value), [10, 11])) {
            $fail('O número de telefone informado é inválido.');
        }
    }
}
