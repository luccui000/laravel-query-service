<?php

namespace App\Render;

class TestProperty
{
    public function __construct(
        protected string $name,
        protected mixed $typeHint,
        protected mixed $initValue,
        protected string $visible = 'public',
    )
    {

    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTypeHint(): mixed
    {
        return $this->typeHint;
    }

    /**
     * @param mixed $typeHint
     */
    public function setTypeHint(mixed $typeHint): void
    {
        $this->typeHint = $typeHint;
    }

    /**
     * @return mixed
     */
    public function getInitValue(): mixed
    {
        return $this->initValue;
    }

    /**
     * @param mixed $initValue
     */
    public function setInitValue(mixed $initValue): void
    {
        $this->initValue = $initValue;
    }

    public function __toString(): string
    {
        if(!empty($this->typeHint)) {
            if(!is_null($this->initValue)) {
                return sprintf("\t%s %s $%s = %s;\n", $this->visible, $this->typeHint, $this->name, $this->initValue);
            } else {
                return sprintf("\t%s %s $%s;\n", $this->visible, $this->typeHint, $this->name);
            }
        } else {
            if(!is_null($this->initValue)) {
                return sprintf("\t%s $%s = %s;\n", $this->visible, $this->name, $this->initValue);
            } else {
                return sprintf("\t%s $%s;\n", $this->visible, $this->name);
            }
        }
    }
}

