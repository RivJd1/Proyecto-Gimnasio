<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\MembershipPlan;

class ClientSeeder extends Seeder
{
    /*public function run(): void
    {
        $receptionist = User::where('role', 'receptionist')->first();

        $clients = [
            ['nombre' => 'Juan',     'apellido' => 'Pérez',    'telefono' => '9901-1111', 'email' => 'juan@mail.com',    'cedula' => '0801-1990-1111', 'fecha_nacimiento' => '1990-05-15', 'estado' => 'activo'],
            ['nombre' => 'Sofía',    'apellido' => 'Martínez', 'telefono' => '9902-2222', 'email' => 'sofia@mail.com',   'cedula' => '0801-1992-2222', 'fecha_nacimiento' => '1992-08-22', 'estado' => 'activo'],
            ['nombre' => 'Roberto',  'apellido' => 'García',   'telefono' => '9903-3333', 'email' => 'roberto@mail.com', 'cedula' => '0801-1988-3333', 'fecha_nacimiento' => '1988-03-10', 'estado' => 'activo'],
            ['nombre' => 'Valeria',  'apellido' => 'López',    'telefono' => '9904-4444', 'email' => 'valeria@mail.com', 'cedula' => '0801-1995-4444', 'fecha_nacimiento' => '1995-11-30', 'estado' => 'activo'],
            ['nombre' => 'Carlos',   'apellido' => 'Reyes',    'telefono' => '9905-5555', 'email' => 'carlos@mail.com',  'cedula' => '0801-1985-5555', 'fecha_nacimiento' => '1985-07-04', 'estado' => 'activo'],
            ['nombre' => 'Daniela',  'apellido' => 'Torres',   'telefono' => '9906-6666', 'email' => 'daniela@mail.com', 'cedula' => '0801-1998-6666', 'fecha_nacimiento' => '1998-01-18', 'estado' => 'inactivo'],
            ['nombre' => 'Miguel',   'apellido' => 'Flores',   'telefono' => '9907-7777', 'email' => 'miguel@mail.com',  'cedula' => '0801-1993-7777', 'fecha_nacimiento' => '1993-09-25', 'estado' => 'activo'],
            ['nombre' => 'Fernanda', 'apellido' => 'Ruiz',     'telefono' => '9908-8888', 'email' => 'fernanda@mail.com','cedula' => '0801-1997-8888', 'fecha_nacimiento' => '1997-04-12', 'estado' => 'activo'],
        ];

        foreach ($clients as $data) {
            Client::create(array_merge($data, ['user_id' => $receptionist->id]));
        }
    }*/
    public function run(): void {
        $plan = MembershipPlan::first(); //
        $staff = User::where('role', 'receptionist')->first(); // [cite: 122]

        Client::factory(30)->create(['user_id' => $staff->id])->each(function ($cliente) use ($plan, $staff) {

            $membresia = $cliente->memberships()->create([
                'membership_plan_id' => $plan->id,
                'fecha_inicio' => now(),
                'fecha_fin' => now()->addDays($plan->duracion_dias),
                'estado' => 'activa',
                'user_id' => $staff->id
            ]); // [cite: 30, 60, 63, 64]


            $membresia->payments()->create([
                'monto' => $plan->precio,
                'metodo_pago' => 'tarjeta',
                'estado' => 'pagado',
                'fecha_pago' => now(),
                'fecha_vencimiento' => now()->addDays($plan->duracion_dias),
                'user_id' => $staff->id,
                'referencia' => 'SEED-' . rand(1000, 9999)
            ]); // [cite: 65, 75, 78, 79]
        });
    }
}
