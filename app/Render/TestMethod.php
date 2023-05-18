<?php

namespace App\Render;

use Illuminate\Support\Collection;

class TestMethod
{
    protected Collection $parameters;
    protected bool $isStatic = true;
    public function __construct(
        protected string $name,
        protected string $visible = 'public',
        protected string $statement = "statement",
    )
    {
        $this->parameters = new Collection();
    }

    public function addParameter($parameter): void
    {
        $this->parameters->add($parameter);
    }

    public function __toString()
    {
        $params = [];
        foreach ($this->parameters as $parameter) {
            $params[] = data_get($parameter, 'type') . " $" . data_get($parameter, 'name');
        }
        if($this->isStatic) {
            $content  = sprintf("\tpublic static function %s(%s)\n\t{\n", $this->name, implode(',', $params));
        } else {
            $content  = sprintf("\tpublic function %s(%s)\n\t{\n", $this->name, implode(',', $params));
        }
        $content .= "\t\t" . $this->statement;
        $content .= "\n\t}\n";
        return $content;
    }

    /**
     * @return bool
     */
    public function isStatic(): bool
    {
        return $this->isStatic;
    }

    /**
     * @param bool $isStatic
     */
    public function setIsStatic(bool $isStatic): void
    {
        $this->isStatic = $isStatic;
    }
}
