<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Uuid implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value): bool
    {
        if (! is_string($value)) {
            return false;
        }

        return (bool) preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $value);
    }

    public function message(): string
    {
        return 'O campo :attribute deve ser um UUID válido.';
    }
}
