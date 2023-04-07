<?php

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\LucQLController;
use App\Services\LucQLService;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

/**
 * Class LucQLControllerTest.
 *
 * @covers \App\Http\Controllers\LucQLController
 */
final class LucQLControllerTest extends TestCase
{
    private LucQLController $lucQLController;

    private LucQLService|Mock $service;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = Mockery::mock(LucQLService::class);
        $this->lucQLController = new LucQLController($this->service);
        $this->app->instance(LucQLController::class, $this->lucQLController);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->lucQLController);
        unset($this->service);
    }

    public function testLogin(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testRegister(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }

    public function testLogout(): void
    {
        /** @todo This test is incomplete. */
        $this->get('/path')
            ->assertStatus(200);
    }
}
