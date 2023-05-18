<?php

namespace App\Generator;

use App\Factory\Generator\ClassFactory;
use App\Factory\Generator\MethodFactory;
use App\Factory\Generator\PropertyFactory;
use App\Factory\Generator\TestCaseFactory;
use App\Reflect\BaseReflect;
use App\Structure\TestCaseStructure;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class FeatureTestGenerator extends AbstractClassGenerator
{
    protected Filesystem $file;
    protected array $originDataProvider = [];
    protected array $providerData = [];
    protected DatabaseGenerator $mockupDatabase;
    public function __construct(
        protected TestCaseStructure $testCaseStructure,
        protected ClassFactory $classFactory,
        protected PropertyFactory $propertyFactory,
        protected MethodFactory $methodFactory,
    )
    {
        parent::__construct(
            $this->testCaseStructure,
            $this->propertyFactory,
            $this->methodFactory
        );

        $this->file = Storage::disk('local');
    }

    public function generate(\ReflectionClass $reflectionClass): void
    {
        parent::generate($reflectionClass);
        $reflected = BaseReflect::reflect($reflectionClass);

        $this->testCaseStructure->addNamespace(TestCase::class);
        $this->addProperties($reflected);
        $this->addImplements($reflected);
        $this->addExtends(TestCase::class);
        $this->addMethods($reflected);

        if(!empty($this->getPath()) && $this->hasFile()) {
            dd(sprintf("Class %s exists", $this->testCaseStructure->getName()));
        }

        $this->generateFile();
        $this->addTestCaseContent();
        dd("DONE");
    }
    protected function addProperties(BaseReflect $reflected): void
    {
        $reflected->getProperties()
            ->each(function(\ReflectionProperty $property) {
                $this->testCaseStructure->addProperty($property);
                if($property->class) {
                    $this->testCaseStructure->addNamespace($property->class);
                }
            });
    }

    protected function addMethods(BaseReflect $reflected): void
    {
        $reflected->getMethods()
            ->each(function(\ReflectionMethod $method)  {
                if($method->isPublic() && !Str::startsWith($method->getName(), "__") ) {
                    $this->testCaseStructure->addMethod($method);
                    $this->testCaseStructure->addCurrentRoute($method);
                }
            });
    }

    private function hasFile(): bool
    {
        return $this->file->exists($this->getPath());
    }

    private function generateFile()
    {
        $paths = explode("/", $this->getPath());
        $paths = collect($paths)->slice(0, -1)->all();
        $paths = implode("/", $paths);
        $testPath = $paths . "/" . $this->testCaseStructure->getName() . ".php";

        $this->testCaseStructure->setTestPath($testPath);
        $this->file->put($this->testCaseStructure->getTestPath(), '');
    }

    /**
     * @throws \ReflectionException
     */
    private function addTestCaseContent()
    {
        $this->addPhpStartTag();
        $this->addNamespace();
        $this->addClass();

        foreach ($this->testCaseStructure->getProperties() as $property) {
            $this->addProperty($property, null);
        }

        foreach ($this->testCaseStructure->getMethods() as $method) {
            $methodName = $method->getName();

            if($this->canAddTest($methodName)) {
                $testMethodName = Str::camel($methodName);
                $dataProviderName = 'dataTest' . ucfirst(Str::camel($methodName));
                $statement = $this->getStatement($methodName, $dataProviderName);
                $this->addTestMethod($testMethodName, $statement, $dataProviderName);

                $this->removeNotUseDataProvider($methodName);
                $this->addProviderMethod($dataProviderName);
            }
        }

        $this->addPhpEndTag();
    }

    private function addPhpStartTag()
    {
        $this->file->append($this->getPath(), "<?php\n");
    }

    private function addPhpEndTag()
    {
        $this->file->append($this->getPath(), "}\n");
    }

    private function addNamespace()
    {
        $namespaces = $this->testCaseStructure->getNamespaces()->all();

        foreach (array_unique($namespaces) as $namespace) {
            if(class_exists($namespace))  {
                $this->file->append($this->getPath(), sprintf("use %s;", $namespace));
            }
        }

        $this->file->append($this->getPath(), "\n");
    }

    private function addClass()
    {
        $this->file->append(
            $this->getPath(),
            $this->classFactory->makeClass(
                $this->testCaseStructure->getName(),
                'TestCase'
            ),
        );
    }

    private function addProperty(\ReflectionProperty $property, $initValue): void
    {
        $partialClass = explode("\\", $property->class);
        $typeHint = collect($partialClass)->last();
        $propertyName = $property->getName();

        $this->file->append(
            $this->getPath(),
            $this->propertyFactory->makePublicProperty(
                $propertyName,
                $typeHint,
                $initValue
            )
        );
    }

    public function addTestMethod($method, $statement,  $dataProvider = null)
    {
        $file = "
     /**
     * @test
     * @dataProvider $dataProvider
     */";
        if(!is_null($dataProvider)) {
            $this->file->append($this->getPath(), $file);
        }

        $parameters = [
            [
                'type' => 'array',
                'name' => $dataProvider
            ]
        ];

        $this->addMethod($method, $statement, $parameters);
    }

    private function addProviderMethod(string $method)
    {
        $statement = "return [\n\t\t\t[";
        foreach ($this->providerData as $key => $value) {
            if(is_numeric($value)) {
                $statement .= sprintf("\n\t\t\t\t'%s' => %d,", $key, $value);
            } else {
                if(Str::contains($value, "'")) {
                    $statement .= "\n\t\t\t\t\"$key\" => \"" . $value .  "\",";
                } else {
                    $statement .= "\n\t\t\t\t'$key' => '$value',";
                }
            }
        }
        $statement .= "\n\t\t\t],\n\t\t];";
        $this->addMethod($method, $statement, [], true);
    }
    private function addMethod($method, $statement, $parameters = [], $isStatic = false): void
    {
        $methodFactory = $this->methodFactory->makePublicMethod(
            $method,
            $statement,
        );
        $methodFactory->setIsStatic($isStatic);

        foreach ($parameters as $parameter) {
            $methodFactory->addParameter($parameter);
        }

        $this->file->append($this->getPath(), $methodFactory);
    }

    public function setMockup(DatabaseGenerator $databaseGenerator)
    {
        $this->mockupDatabase = $databaseGenerator;
    }

    private function addEndClass()
    {
        $this->file->append($this->getPath(), "\n}\n");
    }

    private function addImplements(BaseReflect $reflect)
    {
        //
    }

    private function addExtends(string $extend): void
    {
        $this->testCaseStructure->setExtend($extend);
    }
    private function getPath(): ?string
    {
        return $this->testCaseStructure->getTestPath();
    }

    public function setProviderData(array $data)
    {
        $this->providerData = $data;
        $this->originDataProvider = $data;
    }

    private function canAddTest($method): bool
    {
        $methods = $this->testCaseStructure
            ->getMethods()
            ->map(function (\ReflectionMethod $item) {
                return $item->getName();
            })->all();

        $config = $this->application->make('config');
        $excludes = $config->get('generator')['excludes']['controller'];
        return !in_array($method, $excludes) && in_array($method, $methods);
    }

    /**
     * @throws \ReflectionException
     */
    private function getStatement($method, $dataProviderName): string
    {
        $route = $this->testCaseStructure->findRoute($method);
        if(!$route) {
            return "";
        }

        $options = [
            'mockup' => $this->mockupDatabase
        ];

        return new TestCaseFactory($route, $dataProviderName, $options);
    }

    /**
     * @throws \ReflectionException
     */
    private function removeNotUseDataProvider($methodName)
    {
        $this->providerData = $this->originDataProvider;
        $route = $this->testCaseStructure->findRoute($methodName);
        if(!$route) {
            return ;
        }

        [$controller, $method] = explode('@', $route->getAction('controller'));
        $baseReflect = BaseReflect::reflect(new \ReflectionClass($controller));
        $formRequest = $baseReflect->getFormRequest($method);

        if($formRequest) {
            $ruleKeys = [];
            if(is_subclass_of($formRequest, FormRequest::class, true)) {
                $ruleKeys = array_keys($formRequest->rules());
            }

            if(count($ruleKeys) > 0) {
                $this->providerData = collect($this->providerData)->only($ruleKeys)->all();
            }
        }
    }
}
