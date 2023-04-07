<?php

namespace App\Services;

use App\Factory\DateFactory;
use App\Factory\MaxFactory;
use App\Factory\MinFactory;
use App\Factory\RequiredFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;

class ReflectionFormRequest
{
    use WithFaker;
    public function __construct(public $formRequest)
    {
        $this->getValidRule('email', $this->formRequest->rules(), 0);
    }

    public function getValidRule($field, $rules, $index)
    {
        $rules = is_array($rules) ? $rules : explode('|', $rules);
        $min = 0;
        $max = 191;

        $fieldRules = data_get($rules, $field);
        $rule = data_get($fieldRules, $index);

        foreach ($fieldRules as $item) {
            if(Str::contains($item, 'min')) {
                $fields = explode(':', $item);
                $min = data_get($fields, 1);
                break;
            }
            if (Str::contains($item, 'max') && $rule != 'min') {
                $fields = explode(':', $item);
                $max = data_get($fields, 1);
                break;
            }
        }

        return match ($rule) {
            'required' => new RequiredFactory($field, $min, $max),
            'date' => new DateFactory(),
            'min' => new MinFactory($min),
            'max' => new MaxFactory($max),
        };
    }
}
