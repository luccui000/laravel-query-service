<?php

namespace Tests\Unit\Jobs;

use App\Jobs\HandleQueueJob;
use Tests\TestCase;

/**
 * Class HandleQueueJobTest.
 *
 * @covers \App\Jobs\HandleQueueJob
 */
final class HandleQueueJobTest extends TestCase
{
    private HandleQueueJob $handleQueueJob;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->handleQueueJob = new HandleQueueJob();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->handleQueueJob);
    }

    public function testHandle(): void
    {
        /** @todo This test is incomplete. */
        $this->handleQueueJob->handle();
    }
}
