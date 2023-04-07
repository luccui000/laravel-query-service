<?php

namespace Tests\Unit\Models;

use App\Models\Post;
use Tests\TestCase;

/**
 * Class PostTest.
 *
 * @covers \App\Models\Post
 */
final class PostTest extends TestCase
{
    private Post $post;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @todo Correctly instantiate tested object to use it. */
        $this->post = new Post();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->post);
    }

    public function testAuthor(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testComments(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }

    public function testTags(): void
    {
        /** @todo This test is incomplete. */
        $this->markTestIncomplete();
    }
}
