<?php

namespace App\Factory\Generator;

class StatementFactory
{
    protected string $statements = '';
    protected int $tab = 2;
    public function __construct()
    {
    }

    public function append($statement): self
    {
        $this->statements .= $statement;
        return $this;
    }

    public function newLine(): self
    {
        $this->statements .= "\n";
        $this->statements .= $this->addTab();
        return $this;
    }

    public function __toString(): string
    {
        return $this->statements;
    }

    private function addTab(): string
    {
        return str_repeat("\t", $this->tab);
    }
}
