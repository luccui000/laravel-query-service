<?php

namespace App\Factory\Database;

use App\Factory\BaseFactory;

class BigIntFactory extends IntFactory
{
    public function __construct(public int $min = -9223372036854780000, public int $max = 9223372036854779999)
    {
        parent::__construct();
    }
}
