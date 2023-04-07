<?php

namespace App;

use App\Services\ReflectionController;
use Illuminate\Routing\Route;

class ControllerStructure
{
    public function __construct(
        public Route $route
    )
    {
    }

    public function getController()
    {
        return $this->route->getControllerClass();
    }

    public function getMethod()
    {
        return $this->route->getActionMethod();
    }

    public function getParameters()
    {
        $reflect = new ReflectionController(app());
        $controller = $this->getController();
        $method = $reflect->method(new \ReflectionClass($controller), $this->getMethod());
        return $method->getParameters();
    }

    public function getUri()
    {
        return $this->route->uri();
    }

    public function toArray()
    {
        return [
            'controller' => $this->getController(),
            'method' => $this->getMethod(),
            'parameters' => $this->getParameters(),
            'uri' => $this->getUri()
        ];
    }
}
