<?php

use Faker\Generator as Faker;
use NitinKaware\DependableSoftDeletable\Test\Models\Post;
use NitinKaware\DependableSoftDeletable\Test\Models\User;

$factory->define(Post::class, function (Faker $faker) {
    return [
        'user_id' => function () {
            return User::all()->random()->id;
        },
        'title'   => $faker->text('50'),
        'body'    => $faker->text('250'),
    ];
});
