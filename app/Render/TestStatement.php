<?php

namespace App\Render;

use Illuminate\Support\Collection;

class TestStatement implements Renderer
{
    protected Collection $statements;
    public function __construct($statements)
    {
        $this->statements = $statements ?? new Collection();
    }

    public function __toString(): string
    {
        $stmt = "";
        foreach ($this->statements as $statement) {

        }
        return $stmt;


    }
}
