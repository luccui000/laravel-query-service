<?php

namespace App\Factory\Database;

use App\Factory\BaseFactory;

class UnsignedIntFactory extends BaseFactory
{
    protected int $min = 0;
    protected int $max = 4294967295;

    public function __toString(): string
    {
        return $this->faker->numberBetween($this->min, $this->max);
    }
}
