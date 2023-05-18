<?php

namespace App\Reflect;

use Illuminate\Foundation\Http\FormRequest;

class ControllerReflect extends BaseReflect
{
    protected string $method = 'index';
    /**
     * @throws \ReflectionException
     */
    public function __construct(public $controller, $application = null)
    {
        parent::__construct($application);
        $this->makeReflection($this->controller);
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }

    public function emptyMethod(): bool
    {
        return $this->method == '';
    }

    public function makeClass()
    {
        return $this->application->make($this->controller);
    }
}
