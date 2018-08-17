<?php

use Faker\Generator as Faker;

$factory->define(App\Post::class, function (Faker $faker) {
    return [
        'title' => $faker->words(4, true),
        'body' => $faker->text,
        'user_id' => function () {
            return create(\App\User::class)->id;
        },
        'user_ip' => $faker->ipv4,
        'avg_rating' => 0,
    ];
});
