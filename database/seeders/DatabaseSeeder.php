<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        if (!User::where('email', 'test@example.com')->exists()) {
            $user = User::factory()->create([
                'username' => 'admin',
                'firstname' => 'System',
                'lastname' => 'Admin',
                'email' => 'test@example.com',
            ]);
            $user->assignRole('Super Admin');
        }
    }
}
