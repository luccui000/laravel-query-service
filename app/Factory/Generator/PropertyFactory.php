<?php

namespace App\Factory\Generator;

use App\Render\TestProperty;
use App\Structure\TestCaseStructure;

class PropertyFactory
{
    public function makeForClass(TestCaseStructure $class, \ReflectionProperty $property)
    {

    }

    public function makePublicProperty(string $typeHint, string $propertyName, mixed $initValue)
    {
        return new TestProperty($typeHint, $propertyName, $initValue, 'public');
    }
}
