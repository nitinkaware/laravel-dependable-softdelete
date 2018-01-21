<?php

use Faker\Generator as Faker;
use NitinKaware\DependableSoftDeletable\Test\Models\Comment;
use NitinKaware\DependableSoftDeletable\Test\Models\Post;
use NitinKaware\DependableSoftDeletable\Test\Models\User;

$factory->define(Comment::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return User::all()->random()->id;
        },
        'post_id' => function () {
            return Post::all()->random()->id;
        },
        'title'   => $faker->text('50'),
        'body'    => $faker->text('250'),
    ];
});
