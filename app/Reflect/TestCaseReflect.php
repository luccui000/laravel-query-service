<?php

namespace App\Reflect;

use Illuminate\Support\Str;

class TestCaseReflect extends BaseReflect
{
    protected string $method = '';
    /**
     * @throws \ReflectionException
     */
    public function __construct(public string $testCase, $application = null)
    {
        parent::__construct($application);
        $this->makeReflection($this->testCase);
    }

    public function firstMethodType($testCaseMethod): string
    {
        return sprintf("it_%s", Str::snake($testCaseMethod));
    }

    public function secondMethodType($testCaseMethod): string
    {
        return Str::camel(sprintf("test %s", $testCaseMethod));
    }

    public function makeClass()
    {
        return new $this->testCase($this->method);
    }

    public function setMethod($method)
    {
        $this->method = $method;
    }

    public function emptyMethod()
    {
        return empty($this->method);
    }
}
