<?php

namespace App\Factory\General;

use App\Factory\BaseFactory;
use Illuminate\Support\Str;

class TokenFactory extends BaseFactory
{
    public static function fields(): array
    {
        return ['token', 'verified_token', 'gen_token', 'remember_token'];
    }

    public function __toString(): string
    {
        return Str::random(40);
    }
}
