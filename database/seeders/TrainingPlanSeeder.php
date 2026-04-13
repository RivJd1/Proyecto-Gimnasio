<?php

namespace Database\Seeders;

use App\Models\Trainer;
use App\Models\TrainingPlan;
use Illuminate\Database\Seeder;

class TrainingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = Trainer::all();

        $plans = [
            ['nombre' => 'Hipertrofia Básica',       'descripcion' => 'Plan de musculación para principiantes enfocado en volumen.',        'tipo' => 'asistido',  'dias_semana' => 3],
            ['nombre' => 'Cardio Quema Grasa',        'descripcion' => 'Rutina de cardio intervalado para pérdida de peso efectiva.',        'tipo' => 'asistido',  'dias_semana' => 4],
            ['nombre' => 'Fuerza Avanzada',           'descripcion' => 'Entrenamiento de fuerza con progresión para atletas intermedios.',   'tipo' => 'asistido',  'dias_semana' => 5],
            ['nombre' => 'Entrenamiento Libre',       'descripcion' => 'Plan de ejercicios básicos para entrenar de forma independiente.',   'tipo' => 'libre',     'dias_semana' => 3],
            ['nombre' => 'Funcional Full Body',       'descripcion' => 'Movimientos funcionales para mejorar coordinación y resistencia.',   'tipo' => 'asistido',  'dias_semana' => 4],
        ];

        foreach ($plans as $index => $plan) {
            TrainingPlan::create(array_merge($plan, [
                'trainer_id' => $trainers[$index % $trainers->count()]->id,
                'activo'     => true,
            ]));
        }
    }
}
