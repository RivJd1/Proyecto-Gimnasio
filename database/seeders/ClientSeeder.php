<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\MembershipPlan;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $plan  = MembershipPlan::first();
        $staff = User::where('role', 'receptionist')->first();

        Client::factory(30)->create(['user_id' => $staff->id])->each(function ($client) use ($plan, $staff) {
            $inicio = now();
            $fin    = now()->addDays($plan->duracion_dias);

            $membership = $client->memberships()->create([
                'membership_plan_id' => $plan->id,
                'fecha_inicio'       => $inicio,
                'fecha_fin'          => $fin,
                'estado'             => 'activa',
                'user_id'            => $staff->id,
            ]);

            // Pago inicial cobrado
            $membership->payments()->create([
                'monto'             => $plan->precio,
                'metodo_pago'       => 'tarjeta',
                'estado'            => 'pagado',
                'fecha_pago'        => $inicio,
                'fecha_vencimiento' => $fin,
                'referencia'        => 'SEED-' . rand(1000, 9999),
                'user_id'           => $staff->id,
            ]);

            // Siguiente pago pendiente
            $membership->payments()->create([
                'monto'             => $plan->precio,
                'metodo_pago'       => 'efectivo',
                'estado'            => 'pendiente',
                'fecha_pago'        => null,
                'fecha_vencimiento' => $fin,
                'referencia'        => 'SEED-NEXT-' . rand(1000, 9999),
                'user_id'           => $staff->id,
            ]);
        });
    }
}
