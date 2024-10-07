<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Account;

class UsersTableSeeder extends Seeder
{
    public function run():void {
        $users = [
            [
                'name' => 'João Silva',
                'document' => '12345678901',
                'email' => 'joao@example.com',
                'phone' => '11987654321',
                'email_verified_at' => now(),
                'zip' => '12345-678',
                'address' => 'Rua A, 123',
                'city' => 'São Paulo',
                'state' => 'SP',
                'password' => bcrypt('123456789'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maria Oliveira',
                'document' => '98765432100',
                'email' => 'maria@example.com',
                'phone' => '11912345678',
                'email_verified_at' => now(),
                'zip' => '87654-321',
                'address' => 'Avenida B, 456',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'password' => bcrypt('987654321'),
                'remember_token' => Str::random(10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            $userId = DB::table('users')->insertGetId($userData);

            Account::create([
                'user_id' => $userId,
                'account_number' => Str::random(10),
                'balance' => rand(0, 10000),
            ]);
        }
    }
}

