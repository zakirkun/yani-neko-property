<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        if (! app()->environment(['local', 'testing'])) {
            return;
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Development Admin',
                'password' => 'password',
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles('super-admin');

        $this->call(DemoDataSeeder::class);
    }
}
