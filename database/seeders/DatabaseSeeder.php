<?php

namespace Database\Seeders;

use Faker\Factory as FakerFactory;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $numberOfUsers = 100;
        $numberOfPostsPerUser = 33;
        $faker = FakerFactory::create();

        User::factory()
            ->has(
                Post::factory()
                    ->count($numberOfPostsPerUser)
                    ->state(function () use ($faker) {
                        return ['created_at' => $faker->dateTime];
                    })
            )
            ->count($numberOfUsers)
            ->create();
    }
}
