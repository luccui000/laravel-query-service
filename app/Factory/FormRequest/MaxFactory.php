<?php

namespace App\Factory\FormRequest;

use Illuminate\Foundation\Testing\WithFaker;

class MaxFactory
{
    use WithFaker;

    public function __construct(public int $max = 191)
    {
        $this->setUpFaker();
    }
    public function __toString(): string
    {
        return $this->faker->text($this->max);
    }
}
