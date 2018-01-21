<?php

namespace NitinKaware\DependableSoftDeletable\Test;

use Illuminate\Foundation\Testing\RefreshDatabase;
use NitinKaware\DependableSoftDeletable\Test\Models\Comment;
use NitinKaware\DependableSoftDeletable\Test\Models\Post;
use NitinKaware\DependableSoftDeletable\Test\Models\User;

class DependableDeleteTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    function it_should_delete_posts_and_their_associated_comments()
    {
        // Given we have two user John and Jane,
        $John = factory(User::class)->create();
        $Jane = factory(User::class)->create();

        //John creates a new post.
        $post = factory(Post::class)->create([
            'user_id' => $John->id,
        ]);

        // Jane then replies to John's post,
        factory(Comment::class)->create([
            'post_id' => $post->id,
            'user_id' => $Jane->id,
        ]);

        $John->delete();

        // Now when the John gets deleted, all the John's posts
        // and their comments should get deleted.
        $this->assertEquals(0, $John->comments()->count());
        $this->assertEquals(0, $John->posts()->count());
    }
}