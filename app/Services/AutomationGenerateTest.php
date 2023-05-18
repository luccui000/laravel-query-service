<?php

namespace App\Services;

use App\Exceptions\TestCaseNotFoundException;
use App\Generator\TestCaseGenerator;

class AutomationGenerateTest
{
    /**
     * @param TestCaseGenerator $testCaseGenerator
     * @param array $validTestCase
     * @param array $invalidTestCase
     */
    public function __construct(
        private readonly TestCaseGenerator $testCaseGenerator,
        private readonly array $validTestCase,
        private readonly array $invalidTestCase
    ) {
    }

    /**
     * @throws TestCaseNotFoundException
     */
    public function perform(string $method): void
    {
        if(!$this->testCaseGenerator->hasTestCase($method)) {
            throw new TestCaseNotFoundException('Test case not found');
        }

        // handle test valid test case
        $this->testCaseGenerator->runTest($this->validTestCase);
        // handle test invalid test case
        $this->testCaseGenerator->runTest($this->invalidTestCase);
    }

    public function setModel(string $class): void
    {
        $this->testCaseGenerator->setModel($class);
    }
}
