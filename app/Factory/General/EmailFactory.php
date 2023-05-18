<?php

namespace App\Factory\General;

use App\Factory\BaseFactory;

class EmailFactory extends BaseFactory
{
    public static function fields()
    {
        return ['email'];
    }

    public function __toString(): string
    {
        return $this->faker->email();
    }
}
