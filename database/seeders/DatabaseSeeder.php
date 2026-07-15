<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Enums\UserRole;
use App\Enums\TicketStatus;
use App\Enums\TicketPriority;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@demo',
            'password' => 'password'
        ]);
        User::factory()->create([
            'name' => 'Test Agent',
            'email' => 'agent@demo',
            'password' => 'password'
        ]);
        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@demo',
            'password' => 'password'
        ]);
    }
}
