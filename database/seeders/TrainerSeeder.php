<?php

namespace Database\Seeders;

use App\Models\Trainer;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrainerSeeder extends Seeder
{
    public function run(): void
    {
        $trainers = User::where('role', 'trainer')->get();

        $data = [
            ['especialidad' => 'Musculación y fuerza',       'bio' => 'Especialista en hipertrofia y entrenamiento de fuerza con 5 años de experiencia.'],
            ['especialidad' => 'Cardio y pérdida de peso',   'bio' => 'Enfocada en rutinas cardiovasculares y planes de reducción de grasa corporal.'],
            ['especialidad' => 'Funcional y rehabilitación', 'bio' => 'Experto en entrenamiento funcional y recuperación de lesiones deportivas.'],
        ];

        foreach ($trainers as $index => $user) {
            Trainer::create([
                'user_id'      => $user->id,
                'especialidad' => $data[$index]['especialidad'] ?? 'General',
                'bio'          => $data[$index]['bio'] ?? '',
                'estado'       => 'activo',
            ]);
        }
    }
}
