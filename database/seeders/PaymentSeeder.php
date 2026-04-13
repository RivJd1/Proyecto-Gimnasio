<?php

namespace Database\Seeders;

use App\Models\Membership;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $admin       = User::where('role', 'admin')->first();
        $memberships = Membership::with('plan')->get();

        foreach ($memberships as $membership) {
            $precio = $membership->plan->precio;
            $estado = $membership->estado === 'activa' ? 'pagado' : 'vencido';

            Payment::create([
                'membership_id'     => $membership->id,
                'monto'             => $precio,
                'metodo_pago'       => $membership->id % 2 === 0 ? 'efectivo' : 'tarjeta',
                'estado'            => $estado,
                'fecha_pago'        => $membership->fecha_inicio,
                'fecha_vencimiento' => $membership->fecha_fin,
                'referencia'        => 'REF-' . strtoupper(uniqid()),
                'user_id'           => $admin->id,
            ]);
        }

        // Pagos vencidos extra para las alertas del dashboard
        $activeMemberships = Membership::where('estado', 'activa')->take(2)->get();
        foreach ($activeMemberships as $membership) {
            Payment::create([
                'membership_id'     => $membership->id,
                'monto'             => $membership->plan->precio,
                'metodo_pago'       => 'efectivo',
                'estado'            => 'vencido',
                'fecha_pago'        => null,
                'fecha_vencimiento' => Carbon::now()->subDays(5),
                'referencia'        => 'REF-VENCIDO-' . strtoupper(uniqid()),
                'user_id'           => $admin->id,
            ]);
        }
    }
}
