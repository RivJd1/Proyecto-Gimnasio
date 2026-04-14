<?php

namespace Database\Factories;

use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Membership>
 */
class MembershipFactory extends Factory
{
    public function definition(): array {
        return [
            'client_id' => Client::factory(),
            'membership_plan_id' => MembershipPlan::factory(),
            'fecha_inicio' => now(),
            'fecha_fin' => now()->addDays(30),
            'estado' => 'activa',
            'user_id' => User::factory(), // Gestión [cite: 60, 64]
        ];
    }
}
