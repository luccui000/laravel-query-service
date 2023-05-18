<?php

namespace App\Factory\Generator;

use App\Render\TestMethod;

class MethodFactory
{
    public function makeForClass(
        $name,
        string $statement,
    ): TestMethod
    {
        return $this->makePublicMethod($name, $statement);
    }

    public function makePublicMethod($name, $statement): TestMethod
    {
        return new TestMethod($name, 'public', $statement);
    }
}
