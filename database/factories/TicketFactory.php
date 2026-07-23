<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Ticket;
use App\Enums\UserRole;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'subject' => $this->faker->sentence(4),
            'body' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(array_column(TicketStatus::cases(), 'value')),
            'priority' => $this->faker->randomElement(array_column(UserRole::cases(), 'value')),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
