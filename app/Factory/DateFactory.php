<?php

namespace App\Factory;

use Illuminate\Foundation\Testing\WithFaker;

class DateFactory
{
    use WithFaker;

    public function __construct()
    {
        $this->setUpFaker();
    }

    public function __toString(): string
    {
        return $this->faker->dateTime();
    }
}
