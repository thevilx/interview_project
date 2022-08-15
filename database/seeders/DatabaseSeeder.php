<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(10)->create();

        $user = \App\Models\User::find(1);

        $user->player()->create([
            'name' => 'John',
            'gem_count' => 10,
        ]);
    }
}
