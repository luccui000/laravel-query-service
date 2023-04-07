<?php

namespace App\Services;

use App\ControllerStructure;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class ReflectionController
{
    protected $application;
    public function __construct(
        $application = null,
    )
    {
        $this->application = $application ?? new Application('/');
    }

    public function methods(\ReflectionClass $class): Collection
    {
        return new Collection($class->getMethods());
    }

    public function method(\ReflectionClass $class, string $name): \ReflectionMethod
    {
        return self::methods($class)
            ->first(function(\ReflectionMethod $method) use ($name) {
                return $method->getShortName() == $name;
            });
    }

    public function properties(\ReflectionClass $class): Collection
    {
        return new Collection($class->getProperties());
    }

    public function dependencies(\ReflectionClass $class, $method)
    {
        $parameters = self::method($class, $method)
            ->getParameters();

        return collect($parameters)
            ->filter(function(\ReflectionParameter $parameter) {
                return !$parameter->getType()->isBuiltin();
            });
    }

    public function formRequest(\ReflectionClass $class, string $name): \Symfony\Component\HttpFoundation\Request
    {
        $request =  self::dependencies($class, $name)
            ->first(function(\ReflectionParameter $parameter) {
                $request = $parameter->getType()->getName();
                return is_subclass_of($request, FormRequest::class, true) ||
                    $request == "Illuminate\Http\Request";
            });

        return self::getReturnParameter($request);
    }

    public function getReturnParameter(\ReflectionParameter $parameter)
    {
        $class = $parameter->getType()->getName();
        if(class_exists($class)) {
            return new $class();
        }
    }

    public function resolveDependencies(\ReflectionClass $class)
    {
        $dependencies = self::dependencies($class, '__construct');

        foreach ($dependencies as $dependency) {
//            self::dependencies()
        }
    }

    public function getRoutes()
    {
        $route = $this->application->get('router')->getRoutes();
        return $route->getRoutes();
    }
    public function getRoute(\ReflectionClass $class, string $method): \Illuminate\Routing\Route
    {
        return collect(self::getRoutes())
            ->first(function(\Illuminate\Routing\Route $route) use ($class, $method) {
                return $route->getAction('controller') == $class->getName() . "@" . $method;
            });
    }

    public function getControllerStructure(\ReflectionClass $class, string $method): ControllerStructure
    {
        $router = self::getRoute($class, $method);

        return new ControllerStructure($router);
    }
}
