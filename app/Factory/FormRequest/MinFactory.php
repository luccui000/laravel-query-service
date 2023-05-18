<?php

namespace App\Factory\FormRequest;

use Illuminate\Foundation\Testing\WithFaker;

class MinFactory
{
    use WithFaker;
    public function __construct(public $min = 0, public $max = 191)
    {
        $this->setUpFaker();
    }

    public function __toString(): string
    {
        return $this->faker->text($this->min);
    }
}
