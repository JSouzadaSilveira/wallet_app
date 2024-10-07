<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void {
        
        /* Users */
        $this->call(UsersTableSeeder::class);
        User::factory()->count(50)->create();

    }
}
