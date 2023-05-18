<?php

namespace App\Factory\FormRequest;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class RequiredFactory
{
    use WithFaker;

    public function __construct(
        public string $field,
        public int $min = 0,
        public int $max = 191
    )
    {
    }

    public function __toString(): string
    {
        if(Str::contains($this->field, 'email')) {
            return $this->faker->email();
        } else if (Str::contains($this->field, 'password')) {
            return $this->faker->password($this->min, $this->max);
        } else {
            return $this->faker->text($this->min, $this->max);
        }
    }
}
