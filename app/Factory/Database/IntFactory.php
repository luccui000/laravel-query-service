<?php

namespace App\Factory\Database;

use App\Factory\BaseFactory;

class IntFactory extends BaseFactory
{
    public function __toString(): string
    {
        return $this->faker->numberBetween($this->min, $this->max);
    }
}
