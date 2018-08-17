<?php

use Faker\Generator as Faker;

$factory->define(App\Rating::class, function (Faker $faker) {
    return [
        'post_id' => function () {
            return create(\App\Post::class)->id;
        },
        'rating' => $faker->numberBetween(1, 5),
    ];
});
