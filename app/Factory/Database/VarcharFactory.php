<?php

namespace App\Factory\Database;

use App\Factory\BaseFactory;

class VarcharFactory extends BaseFactory
{
    public function __toString(): string
    {
        return $this->faker->text($this->length);
    }
}
