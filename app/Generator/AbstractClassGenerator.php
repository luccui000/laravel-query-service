<?php

namespace App\Generator;

use App\Exceptions\CantGeneratorClassException;
use App\Factory\Generator\MethodFactory;
use App\Factory\Generator\PropertyFactory;
use App\Structure\TestCaseStructure;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

class AbstractClassGenerator
{
    protected Application $application;
    public function __construct(
        protected TestCaseStructure $testCaseStructure,
        protected PropertyFactory $propertyFactory,
        protected MethodFactory $methodFactory
    )
    {
        $application = require __DIR__ . '/../../bootstrap/app.php';
        $application->make(Kernel::class)->bootstrap();
        $this->application = $application;
    }

    /**
     * @throws CantGeneratorClassException|\ReflectionException
     */
    public function generate(\ReflectionClass $reflectionClass): void
    {
        if(!$this->canGenerateFor($reflectionClass)) {
            throw new CantGeneratorClassException(
                sprintf("Can't generate class %s", $reflectionClass->getName())
            );
        }
    }

    private function canGenerateFor(\ReflectionClass $reflectionClass): bool
    {
        if($reflectionClass->isInterface() || $reflectionClass->isAnonymous()) {
            return false;
        }

        return true;
    }

    /**
     * @throws \ReflectionException
     */
    protected function makeClass(\ReflectionClass $class): TestCaseStructure
    {
        $args = func_get_args();
        return $class->newInstance($args);
    }
}
