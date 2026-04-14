<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    public function run(): void
    {
        $plan  = MembershipPlan::where('tipo', 'mensual')->first();
        $staff = User::where('role', 'receptionist')->first();

        // 5 clientes con membresía vencida para mostrar mora en dashboard
        Client::factory(5)->create(['user_id' => $staff->id])->each(function ($client) use ($plan, $staff) {
            $inicio = Carbon::now()->subDays(40);
            $fin    = Carbon::now()->subDays(10);

            $membership = $client->memberships()->create([
                'membership_plan_id' => $plan->id,
                'fecha_inicio'       => $inicio,
                'fecha_fin'          => $fin,
                'estado'             => 'vencida',
                'user_id'            => $staff->id,
            ]);

            // Pago vencido sin cobrar
            $membership->payments()->create([
                'monto'             => $plan->precio,
                'metodo_pago'       => 'efectivo',
                'estado'            => 'vencido',
                'fecha_pago'        => null,
                'fecha_vencimiento' => $fin,
                'referencia'        => 'REF-VENC-' . strtoupper(uniqid()),
                'user_id'           => $staff->id,
            ]);
        });

        // 5 clientes con membresía activa pero próxima a vencer (pendiente)
        Client::factory(5)->create(['user_id' => $staff->id])->each(function ($client) use ($plan, $staff) {
            $inicio = Carbon::now()->subDays(25);
            $fin    = Carbon::now()->addDays(5);

            $membership = $client->memberships()->create([
                'membership_plan_id' => $plan->id,
                'fecha_inicio'       => $inicio,
                'fecha_fin'          => $fin,
                'estado'             => 'activa',
                'user_id'            => $staff->id,
            ]);

            // Pago inicial pagado
            $membership->payments()->create([
                'monto'             => $plan->precio,
                'metodo_pago'       => 'efectivo',
                'estado'            => 'pagado',
                'fecha_pago'        => $inicio,
                'fecha_vencimiento' => $fin,
                'referencia'        => 'REF-' . strtoupper(uniqid()),
                'user_id'           => $staff->id,
            ]);

            // Siguiente pago pendiente
            $membership->payments()->create([
                'monto'             => $plan->precio,
                'metodo_pago'       => 'tarjeta',
                'estado'            => 'pendiente',
                'fecha_pago'        => null,
                'fecha_vencimiento' => $fin,
                'referencia'        => 'REF-NEXT-' . strtoupper(uniqid()),
                'user_id'           => $staff->id,
            ]);
        });
    }
}
