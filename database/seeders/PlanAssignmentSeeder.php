<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\PlanAssignment;
use App\Models\Trainer;
use App\Models\TrainingPlan;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PlanAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $clients       = Client::where('estado', 'activo')->get();
        $trainers      = Trainer::all();
        $trainingPlans = TrainingPlan::all();

        foreach ($clients->take(5) as $index => $client) {
            PlanAssignment::create([
                'client_id'        => $client->id,
                'training_plan_id' => $trainingPlans[$index % $trainingPlans->count()]->id,
                'trainer_id'       => $trainers[$index % $trainers->count()]->id,
                'fecha_inicio'     => Carbon::now()->subDays(rand(5, 30)),
                'fecha_fin'        => Carbon::now()->addDays(rand(30, 90)),
                'estado'           => 'activo',
                'notas'            => 'Asignación inicial del cliente.',
            ]);
        }
    }
}
