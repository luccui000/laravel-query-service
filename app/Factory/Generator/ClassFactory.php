<?php

namespace App\Factory\Generator;

use App\Render\TestClass;

class ClassFactory
{
    public function makeClass(string $name, $extend): TestClass
    {
        return new TestClass($name, $extend);
    }
}
