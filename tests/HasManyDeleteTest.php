<?php

namespace NitinKaware\DependableSoftDeletable\Test;


use Illuminate\Foundation\Testing\RefreshDatabase;
use NitinKaware\DependableSoftDeletable\Test\Models\Comment;
use NitinKaware\DependableSoftDeletable\Test\Models\Post;
use NitinKaware\DependableSoftDeletable\Test\Models\User;

class HasManyDeleteTest extends TestCase {

    use RefreshDatabase;

    /** @test */
    function it_should_delete_all_posts_when_user_deleted_()
    {
        // Given we have a user john,
        $john = factory(User::class)->create();

        // And john creates 10 posts
        factory(Post::class, 10)->create([
            'user_id' => $john->id,
        ]);

        $this->assertEquals(10, $john->posts()->count());

        $john->delete();

        // Assert that johns posts gets deleted.
        $this->assertEquals(0, $john->posts()->count());
    }

    /** @test */
    function it_should_delete_all_the_comments_when_user_deleted()
    {
        // Given we have a user john, and jane
        $john = factory(User::class)->create();

        $jane = factory(User::class)->create();

        // And john creates 10 posts
        $post = factory(Post::class)->create([
            'user_id' => $john->id,
        ]);

        // Jane replied to that post.
        $comments = factory(Comment::class, 10)->create([
            'user_id' => $jane->id,
        ]);

        $this->assertEquals(10, $post->comments()->count());

        //When the john get deleted, all the posts and comments associated
        //with that posts should get deleted.
        $john->delete();
        $this->assertEquals(0, $post->comments()->count());
        $this->assertEquals(0, $john->posts()->count());
    }
}