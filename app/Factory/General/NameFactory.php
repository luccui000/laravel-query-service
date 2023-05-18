<?php

namespace App\Factory\General;

use App\Factory\BaseFactory;

class NameFactory extends BaseFactory
{
    public static function fields(): array
    {
        return ['name', 'first_name', 'last_name'];
    }

    public function __toString(): string
    {
        return $this->faker->name();
    }
}
