<?php

namespace App\Reflect;

use App\Factory\FormRequest\DateFactory;
use App\Factory\FormRequest\MaxFactory;
use App\Factory\FormRequest\MinFactory;
use App\Factory\FormRequest\RequiredFactory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;

class RequestReflect extends BaseReflect
{
    use WithFaker;

    public function __construct(
        public Request $formRequest,
        $application = null
    ) {
        parent::__construct($application);
    }

    public function makeValidRule(): array
    {
        $fields = array_keys($this->getRules());

        $testCases = [];
        foreach ($fields as $field) {
            $testCases[$field] =  $this->makeValidByField($field, $this->getRules(), 0);
        }

        return $testCases;
    }

    public function makeValidByField($field, $rules, $index): string
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

    private function getRules(): array
    {
        return $this->formRequest->rules();
    }

    public function makeClass()
    {
        return $this->application->make($this->formRequest);
    }
}
