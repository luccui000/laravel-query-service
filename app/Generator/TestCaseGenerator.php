<?php

namespace App\Generator;

use App\Factory\Generator\MethodFactory;
use App\Factory\Generator\PropertyFactory;
use App\Reflect\BaseReflect;
use App\Reflect\ControllerReflect;
use App\Reflect\TestCaseReflect;
use App\Structure\TestCaseStructure;
use Illuminate\Support\Facades\Storage;

class TestCaseGenerator extends AbstractClassGenerator
{
    protected $file;
    protected $formRequest;
    protected $model;
    protected $testMethod = '';
    protected $testClass = '';
    protected TestCaseStructure $testCaseStructure;

    /**
     * @throws \ReflectionException
     */
    public function __construct(
        protected ControllerReflect $controllerReflect,
        protected PropertyFactory $propertyFactory,
        protected MethodFactory $methodFactory,
        private readonly TestCaseReflect|null $testCaseReflect = null,
    )
    {
        parent::__construct(
            $controllerReflect,
            $this->propertyFactory,
            $this->methodFactory
        );

        $this->testCaseStructure = new TestCaseStructure(
            $this->controllerReflect->controller,
            ''
        );

        $this->file = Storage::disk('local');

        $this->testClass = $this->controllerReflect->controller;

        $this->controllerReflect->makeReflection($this->testClass);
    }

    public function makeInvalidTestCase(): array
    {
        return [];
    }
    public function makeValidTestCase(): array
    {
        return [
            'email' => 'email@gmail.com',
            'password' => 'Pass@123'
        ];
    }

    public function hasTestCase(string $testCaseMethod): bool
    {
        $this->setTestMethod($testCaseMethod);
        $this->setFormRequest($testCaseMethod);

        $testCaseMethodName = $this->testCaseReflect->firstMethodType($testCaseMethod);
        $testCaseMethodName2 = $this->testCaseReflect->secondMethodType($testCaseMethod);

        $this->testMethod = $this->testCaseReflect->hasMethod($testCaseMethodName) ?
            $testCaseMethod :
            $testCaseMethodName2;

        return $this->controllerReflect->hasMethod($testCaseMethod) &&
            (
                $this->testCaseReflect->hasMethod($testCaseMethodName) ||
                $this->testCaseReflect->hasMethod($testCaseMethodName2)
            );
    }

    private function setTestMethod(string $testCaseMethod): void
    {
        if($this->controllerReflect->emptyMethod()) {
            $this->controllerReflect->setMethod($testCaseMethod);
        }

        if($this->testCaseReflect->emptyMethod()) {
            $this->testCaseReflect->setMethod($testCaseMethod);
        }
    }

    private function setFormRequest($testCaseMethod)
    {
        $this->formRequest = $this->controllerReflect->getFormRequest($testCaseMethod);
    }

    public function setModel($model)
    {
        $this->model = new $model();
    }

    public function runTest($params)
    {
        $testClass = $this->testCaseReflect->makeClass();
        $testClass->provider = $params;
        call_user_func_array([$testClass, $this->testMethod], [...array_values($params)]);
    }

    protected function addProperties(BaseReflect $reflect): void
    {
        $reflect->getProperties()
            ->each(function(\ReflectionProperty $property) {
                $this->testCaseStructure->addProperty($property);
            });
    }

    protected function addMethods(BaseReflect $reflect): void
    {
        $reflect->getMethods()
            ->each(function(\ReflectionMethod $method)  {
                $this->testCaseStructure->addMethod($method);
            });
    }

    public function generate(\ReflectionClass $reflectionClass): void
    {
        parent::generate($reflectionClass);

        $this->addProperties($this->controllerReflect);
        $this->addMethods($this->controllerReflect);

        if($this->hasFile()) {
            dd(sprintf("Class %s exists", $this->testCaseStructure->getClass()));
        }

        $this->generateFile();
    }

    private function hasFile(): bool
    {
        return $this->file->exists($this->getPath());
    }

    private function generateFile()
    {
        $this->addPhpStartTag();
        $this->addNamespace();

        $this->addClass();

        foreach ($this->testCaseStructure->getProperties() as $property) {
            $this->addProperty($property);
        }

        foreach ($this->testCaseStructure->getMethods() as $method) {
            $logicCode = "";
            $this->addMethod($method, $logicCode);
        }

        $this->addPhpEndTag();
    }

    private function hasClass($className)
    {
        return $this->file->get($className);
    }

    private function makeNewClass($className)
    {
        $this->file->makeDirectory($className);
    }
    private function addPhpStartTag()
    {
        $this->file->append($this->getPath(), "<?php\n");
    }

    private function addPhpEndTag()
    {
        $this->file->append($this->getPath(), "\n}");
    }

    private function addNamespace()
    {
        $namespace = $this->getPath();
        $this->file->append($this->getPath(), sprintf("use %s;\n", $namespace));
    }

    private function addClass()
    {
        $this->file->append(
            "class %s extends TestCase {\n",
            $this->testCaseStructure->getName()
        );
    }

    private function addProperty(mixed $property, $initValue = null): void
    {
        $typeHint = " ";
        $propertyName = "";
        if(!empty($typeHint)) {
            if(!is_null($initValue)) {
                $this->file->append($this->getPath(), sprintf("public %s %s = %s;\n", $typeHint, $propertyName, $initValue));
            } else {
                $this->file->append($this->getPath(), sprintf("public %s %s;\n", $typeHint, $propertyName));
            }
        } else {
            if(!is_null($initValue)) {
                $this->file->append($this->getPath(), sprintf("public %s = %s;\n", $propertyName, $initValue));
            } else {
                $this->file->append($this->getPath(), sprintf("public %s;\n", $propertyName));
            }
        }
    }

    private function addMethod(mixed $method, string $logicCode): void
    {
        $methodName = "";
        $this->file->append($this->getPath(), $this->methodFactory->makeForClass());
        $this->file->append($this->getPath(), sprintf("public function %s()\n {", $methodName));
        $this->file->append($this->getPath(), $logicCode);
        $this->file->append($this->getPath(), "}\n\n");
    }

    private function getPath(): string
    {
        $classPath = str_replace('\\', '/', $this->controllerReflect->controller);
        $classPath = str_replace('::class', '', $classPath);

        return "tests/Feature/" . ltrim($classPath, '/');
    }
}
