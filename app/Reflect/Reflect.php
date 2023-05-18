<?php

namespace App\Reflect;

class Reflect
{
    /**
     * @throws \ReflectionException
     */
    public static function parameters($controller, $method): array
    {
        $reflectionClass = new \ReflectionClass($controller);
        return $reflectionClass->getMethod($method)->getParameters();
    }

}
