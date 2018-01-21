<?php

namespace NitinKaware\DependableSoftDeletable\Test;


use Illuminate\Foundation\Testing\RefreshDatabase;
use NitinKaware\DependableSoftDeletable\Test\Models\Post;
use NitinKaware\DependableSoftDeletable\Test\Models\User;

class HasOneTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    function it_should_delete_post_when_owner_of_post_get_deleted()
    {
        // Given we have a user john,
        $john = factory(User::class)->create();

        // And john creates 10 posts
        factory(Post::class, 10)->create([
            'user_id' => $john->id,
        ]);

        $this->assertEquals(10, $john->post()->count());

        $john->delete();

        // Assert that johns posts gets deleted.
        $this->assertEquals(0, $john->post()->count());
    }
}