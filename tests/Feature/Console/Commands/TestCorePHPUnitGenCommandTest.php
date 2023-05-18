<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\TestCorePHPUnitGenCommand;
use Tests\TestCase;

/**
 * Class TestCorePHPUnitGenCommandTest.
 *
 * @covers \App\Console\Commands\TestCorePHPUnitGenCommand
 */
final class TestCorePHPUnitGenCommandTest extends TestCase
{
    private TestCorePHPUnitGenCommand $testCorePHPUnitGenCommand;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->testCorePHPUnitGenCommand = new TestCorePHPUnitGenCommand();
        $this->app->instance(TestCorePHPUnitGenCommand::class, $this->testCorePHPUnitGenCommand);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->testCorePHPUnitGenCommand);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->artisan('app:test-core-p-h-p-unit-gen-command')
            ->expectsOutput('Some expected output')
            ->assertExitCode(0);
    }
}
