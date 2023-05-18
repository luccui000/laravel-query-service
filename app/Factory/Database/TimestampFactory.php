<?php

namespace App\Factory\Database;

use App\Factory\BaseFactory;
use Carbon\Carbon;

class TimestampFactory extends BaseFactory
{
    public function __toString(): string
    {
        return Carbon::parse($this->faker->dateTime());
    }
}
