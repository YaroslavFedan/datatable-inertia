<?php

use Illuminate\Support\Str;
use Dongrim\DatatableInertia\Tests\Models\Post;

$factory->define(Post::class, function (Faker\Generator $faker) {
    $title = $faker->sentence();
    return [
        'author_id' => rand(1, 100),
        'slug' => Str::slug($title),
        'title' => $title,
        'body' => $faker->paragraph(),
    ];
});
