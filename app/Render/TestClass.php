<?php

namespace App\Render;


use Illuminate\Support\Collection;

class TestClass
{
    protected Collection $implements;

    public function __construct(
        protected string $className,
        protected $extends = null,
    )
    {
        $this->implements = new Collection();
    }


    public function addImplement($implement): void
    {
        $this->implements->add($implement);
    }

    public function getImplements(): Collection
    {
        return $this->implements;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     */
    public function setClassName(string $className): void
    {
        $this->className = $className;
    }

    /**
     * @return null
     */
    public function getExtends()
    {
        return $this->extends;
    }

    /**
     * @param null $extends
     */
    public function setExtends($extends): void
    {
        $this->extends = $extends;
    }

    public function __toString(): string
    {
        if($this->extends) {
            if($this->implements->count() > 0) {
                return sprintf(
                    "class %s extends %s implements %s {\n",
                    $this->className,
                    $this->extends,
                    implode(",", $this->implements->all())
                );
            } else {
                return sprintf(
                    "class %s extends %s {\n",
                    $this->className,
                    $this->extends,
                );
            }
        } else {
            if($this->implements->count() > 0) {
                return sprintf(
                    "class %s implements %s {\n",
                    $this->className,
                    implode(",", $this->implements->all())
                );
            } else {
                return sprintf(
                    "class %s {\n",
                    $this->className,
                );
            }
        }
    }
}
