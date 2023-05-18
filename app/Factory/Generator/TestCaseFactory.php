<?php

namespace App\Factory\Generator;

use Illuminate\Routing\Route;

class TestCaseFactory
{
    public function __construct(
        protected Route $route,
        protected string $dataProviderName,
        protected array $options,
    )
    {
    }

    public function makeGetRequest($uri, $dataProviderName): string
    {
        $mockup = $this->options['mockup'];
        $table = !is_null($mockup) ? $mockup->getTable() : '';

        $statement = new StatementFactory();
        $statement->append('$response = $this->getJson')
            ->append("('")
            ->append($uri)
            ->append("', $" . $dataProviderName)
            ->append(');')
            ->newLine()
            ->append('$response->assertStatus(')
            ->append(200)
            ->append(');')
            ->newLine()
            ->append('$this->assertDatabaseHas(')
            ->append(implode(', ', ["'$table'", "[]"]))
            ->append(');');

        return $statement;
    }

    public function makePostRequest($uri, $dataProviderName): string
    {
        $mockup = $this->options['mockup'];
        $table = !is_null($mockup) ? $mockup->getTable() : '';

        $statement = new StatementFactory();
        $statement->append('$response = $this->postJson')
            ->append("('")
            ->append($uri)
            ->append("', $" . $dataProviderName)
            ->append(');')
            ->newLine()
            ->append('$response->assertStatus(')
            ->append(200)
            ->append(');')
            ->newLine()
            ->append('$this->assertDatabaseHas(')
            ->append(implode(', ', ["'$table'", "[]"]))
            ->append(');');

        return $statement;
    }
    public function __toString(): string
    {
        $methods = $this->route->methods();

        if(in_array('POST', $methods)) {
            return $this->makePostRequest(
                $this->route->uri(),
                $this->dataProviderName,
            );
        } else if (in_array('GET', $methods)) {
            return $this->makePostRequest(
                $this->route->uri(),
                $this->dataProviderName,
            );
        }
    }
}
