<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\LoginRequest;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * Class LoginRequestTest.
 *
 * @covers \App\Http\Requests\LoginRequest
 */
final class LoginRequestTest extends TestCase
{
    private LoginRequest $loginRequest;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->loginRequest = new LoginRequest();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->loginRequest);
    }

    public function testLogin()
    {
        $args = func_get_args();
        $this->postJson('/test', $args)
            ->assertStatus(Response::HTTP_NOT_FOUND);
    }
    public function testAuthorize(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testRules(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
