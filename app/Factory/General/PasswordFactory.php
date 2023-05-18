<?php

namespace App\Factory\General;

use App\Factory\BaseFactory;

class PasswordFactory extends BaseFactory
{
    public function __construct(int $max = 10)
    {
        parent::__construct();
        $this->max = $max;
    }

    public static function fields(): array
    {
        return ['password', 'password_confirmation'];
    }

    public function __toString(): string
    {
        return $this->faker->password();
    }
}
