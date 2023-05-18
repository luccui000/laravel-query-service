<?php

namespace App\Reflect;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Collection;

class BaseReflect
{
    protected \ReflectionClass $reflectionClass;
    protected Application $application;
    public function __construct(
        $application = null,
    )
    {
        $this->application = $application ?? new Application('/');
    }

    /**
     * @throws \ReflectionException
     */
    public function makeReflection(string $class): \ReflectionClass
    {
        return $this->setReflectionClass($class);
    }

    public function getMethods(): Collection
    {
        return new Collection($this->reflectionClass->getMethods());
    }

    public function getProperties(): Collection
    {
        return new Collection($this->reflectionClass->getProperties());
    }
    public function getMethod(string $name): \ReflectionMethod|null
    {
        return self::getMethods()
            ->first(function(\ReflectionMethod $method) use ($name) {
                return $method->getName() == $name;
            });
    }

    public function properties(): Collection
    {
        return new Collection($this->reflectionClass->getProperties());
    }

    public function dependencies(string $method)
    {
        $parameters = self::getMethod($method)
            ->getParameters();

        return collect($parameters)
            ->filter(function(\ReflectionParameter $parameter) {
                return !$parameter->getType()->isBuiltin();
            });
    }


    public function getReturnParameter(\ReflectionParameter $parameter)
    {
        $class = $parameter->getType()->getName();
        if(class_exists($class)) {
            return new $class();
        }
    }

    protected function getRoutes()
    {
        $route = $this->application->get('router')->getRoutes();
        return $route->getRoutes();
    }

    public function getUri()
    {
        $route = $this->getRoutes();

        return collect($route)
            ->first(function(\Illuminate\Routing\Route $route) {
                [$controller, $method] = explode('@', $route->getController());
                return $this->reflectionClass->getName() == $controller;
            }, '/')->getUri();
    }

    public function getAction(string $method)
    {
        return $this->getRoute($method)->getAction();
    }
    public function getRoute(string $method): \Illuminate\Routing\Route
    {
        return collect(self::getRoutes())
            ->first(function(\Illuminate\Routing\Route $route) use ( $method) {
                return $route->getAction('controller') == $this->reflectionClass->getName() . "@" . $method;
            });
    }

    public function hasMethod($method): bool
    {
        return !is_null($this->getMethod($method));
    }

    /**
     * @throws \ReflectionException
     */
    protected function setReflectionClass($reflectionClass): \ReflectionClass
    {
        return $this->reflectionClass = $reflectionClass;
    }
    public function makeClass()
    {

    }
    public function getFormRequest(string $method)
    {
        $request =  self::dependencies($method)
            ->first(function(\ReflectionParameter $parameter) {
                $request = $parameter->getType()->getName();
                return is_subclass_of($request, FormRequest::class, true) ||
                    $request == "Illuminate\Http\Request";
            });
        return $this->getReturnParameter($request);
    }

    public function getReflectionClass(): \ReflectionClass
    {
        return $this->reflectionClass;
    }

    /**
     * @throws \ReflectionException
     */
    public static function reflect(\ReflectionClass $reflectionClass): self
    {
        $baseReflect = new self();
        $baseReflect->setReflectionClass($reflectionClass);
        return $baseReflect;
    }
}
