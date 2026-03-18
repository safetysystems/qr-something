<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = env('ADMIN_PASSWORD');

        if (! $password && app()->environment(['local', 'testing'])) {
            $password = 'password';
        }

        if (! $password) {
            return;
        }

        User::query()->updateOrCreate([
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
        ], [
            'name' => env('ADMIN_NAME', 'System Administrator'),
            'password' => Hash::make($password),
            'is_super_admin' => true,
        ]);
    }
}
