<?php

namespace App\Factory;

use Faker\Factory as Faker;
use Faker\Generator;

class BaseFactory
{
    protected Generator $faker;
    protected int $min = -2147483648;
    protected int $max = 2147483647;

    public int $length = 255;
    public function __construct(public string $table = '', public string $field = '')
    {
        $this->faker = Faker::create();
    }
}
