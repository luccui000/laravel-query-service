<?php

namespace App\Factory\General;

use App\Factory\BaseFactory;
use Illuminate\Support\Facades\DB;

class IDFactory extends BaseFactory
{
    public function __toString(): string
    {
        $maxId = DB::table($this->table)->max($this->field) + 1;
        return $maxId ?? $this->faker->numberBetween(1, $this->max);
    }
}
