<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            MembershipPlanSeeder::class,
            ClientSeeder::class,
            TrainerSeeder::class,
            MembershipSeeder::class,
            PaymentSeeder::class,
            TrainingPlanSeeder::class,
            PlanAssignmentSeeder::class,
            SupportProviderSeeder::class,
        ]);
    }
}
