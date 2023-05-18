<?php

namespace App\Structure;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

class TestCaseStructure
{
    protected bool $isFeatureTest = true;
    protected \ReflectionClass $reflectionClass;
    protected Collection $properties;
    protected Collection $methods;
    protected Collection $namespaces;
    protected string $extend;
    protected Collection $implements;
    protected Collection $routes;
    private string $testPath = '';

    public function __construct(protected string $path, protected string $name)
    {
        $this->properties = new Collection();
        $this->methods  = new Collection();
        $this->implements = new Collection();
        $this->routes = new Collection();
        $this->namespaces = new Collection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addProperty($property): self
    {
        $this->properties->add($property);
        return $this;
    }

    public function addMethod($method): self
    {
        $this->methods->add($method);
        return $this;
    }

    public function addImplement($implement): self
    {
        $this->implements->add($implement);
        return $this;
    }

    public function addRoute($route): self
    {
        $this->routes->add($route);
        return $this;
    }

    public function addNamespace($namespace): self
    {
        $this->namespaces->add($namespace);
        return $this;
    }
    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $name
     */
    public function setPath(string $name): void
    {
        $this->path = $name;
    }

    /**
     * @return Collection
     */
    public function getProperties(): Collection
    {
        return $this->properties;
    }

    /**
     * @param Collection $properties
     */
    public function setProperties(Collection $properties): void
    {
        $this->properties = $properties;
    }

    /**
     * @return Collection
     */
    public function getMethods(): Collection
    {
        return $this->methods;
    }

    /**
     * @param Collection $methods
     */
    public function setMethods(Collection $methods): void
    {
        $this->methods = $methods;
    }

    /**
     * @return \ReflectionClass
     */
    public function getReflectionClass(): \ReflectionClass
    {
        return $this->reflectionClass;
    }

    /**
     * @param \ReflectionClass $class
     */
    public function setReflectionClass(\ReflectionClass $class): void
    {
        $this->reflectionClass = $class;
    }

    public function getNamespaces(): Collection
    {
        return $this->namespaces;
    }

    /**
     * @param $namespaces
     */
    public function setNamespace($namespaces): void
    {
        $this->namespaces = $namespaces;
    }

    public function getTestClassPath(): string
    {
        $classPath = str_replace('\\', '/', $this->path);
        $classPath = str_replace('::class', '', $classPath);
        $classPath = str_replace('App/', '', $classPath);

        if($this->isFeatureTest) {
            return str_replace("tests/Unit/", "", $classPath);
        } else {
            return str_replace("tests/Feature/", "", $classPath);
        }
    }

    public function findRoute(\ReflectionMethod|string $method): ?\Illuminate\Routing\Route
    {
        $route = Route::getRoutes();
        $routes = $route->getRoutes();

        return collect($routes)
            ->first(function(\Illuminate\Routing\Route $route) use ($method) {
                $class = explode('@', $route->getAction('controller'));
                $methodUse = data_get($class, 0);
                $methodName = data_get($class, 1);

                if(is_string($method)) {
                    return $method == $methodName;
                } else {
                    return $method->class == $methodUse && $method->name == $methodName;
                }
            });
    }

    public function addCurrentRoute(\ReflectionMethod $method): void
    {
        $route = $this->findRoute($method);

        if($route) {
            $this->addRoute($route);
        }
    }

    /**
     * @return bool
     */
    public function isFeatureTest(): bool
    {
        return $this->isFeatureTest;
    }

    /**
     * @param bool $isFeatureTest
     */
    public function setIsFeatureTest(bool $isFeatureTest): void
    {
        $this->isFeatureTest = $isFeatureTest;
    }

    public function getTestPath(): string
    {
        return $this->testPath;
    }
    public function setTestPath(string $testPath): self
    {
        $this->testPath = $testPath;
        return $this;
    }

    /**
     * @return string
     */
    public function getExtend(): string
    {
        return $this->extend;
    }

    /**
     * @param string $extend
     */
    public function setExtend(string $extend): void
    {
        $this->extend = $extend;
    }
}
