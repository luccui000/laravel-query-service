<?php

namespace App\Factory\Database;

use App\Factory\BaseFactory;

class DoubleFactory extends BaseFactory
{
    public function __toString()
    {
        return (string)$this->faker->randomFloat();
    }
}
