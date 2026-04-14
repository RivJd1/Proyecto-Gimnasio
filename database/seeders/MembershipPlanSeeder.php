<?php

namespace Database\Seeders;

use App\Models\MembershipPlan;
use Illuminate\Database\Seeder;

class MembershipPlanSeeder extends Seeder
{
    public function run(): void
    {
        MembershipPlan::create([
            'nombre'        => 'Plan Mensual',
            'descripcion'   => 'Acceso completo al gimnasio por 30 días.',
            'precio'        => 500.00,
            'duracion_dias' => 30,
            'tipo'          => 'mensual',
            'activo'        => true,
        ]);

        MembershipPlan::create([
            'nombre'        => 'Plan Semestral',
            'descripcion'   => 'Acceso completo por 6 meses con descuento.',
            'precio'        => 2500.00,
            'duracion_dias' => 180,
            'tipo'          => 'semestral',
            'activo'        => true,
        ]);

        MembershipPlan::create([
            'nombre'        => 'Plan Anual',
            'descripcion'   => 'Acceso completo por un año, mejor precio.',
            'precio'        => 4500.00,
            'duracion_dias' => 365,
            'tipo'          => 'anual',
            'activo'        => true,
        ]);
    }

//    public function run(): void {
//        MembershipPlan::create(['nombre' => 'Plan Estándar', 'precio' => 350, 'duracion_dias' => 30, 'tipo' => 'mensual', 'activo' => true]);
//        MembershipPlan::create(['nombre' => 'Plan Black', 'precio' => 600, 'duracion_dias' => 30, 'tipo' => 'mensual', 'activo' => true]); // [cite: 70, 71]
//    }
}
