<?php

namespace App\Structure;

use App\Reflect\ControllerReflect;
use Illuminate\Routing\Route;

class ControllerStructure extends AbstractStructure
{
    /**
     * @throws \ReflectionException
     */
    public function __construct(
        public $controller,
        private string $method = 'index',
        private readonly ControllerReflect|null $controllerReflect = null
    )
    {
        $this->controllerReflect
            ->makeReflection($this->controller);
    }

    public function getMethods(): array|string
    {
        return $this->controllerReflect->getMethods();
    }

    public function getMethod(): \ReflectionMethod
    {
        return $this->controllerReflect->getMethod($this->method);
    }

    public function getMethodName(): string
    {
        return $this->getMethod()->getName();
    }

    public function getUri(): string
    {
        return $this->controllerReflect->getUri();
    }

    public function getAction(): array|\Closure
    {
        return $this->controllerReflect->getAction(
            $this->method
        );
    }

    public function toRoute(): Route
    {
        return new Route(
            $this->getMethods(),
            $this->getUri(),
            $this->getAction()
        );
    }

    public function setMethod(string $method)
    {
        $this->method = $method;
    }
}
