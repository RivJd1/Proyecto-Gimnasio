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
        $clients   = Client::all();
        $plans     = MembershipPlan::all()->keyBy('tipo');
        $admin     = User::where('role', 'admin')->first();

        $assignments = [
            // cliente 1 — mensual activa
            [0, 'mensual',   'activa',  Carbon::now()->subDays(10), Carbon::now()->addDays(20)],
            // cliente 2 — semestral activa
            [1, 'semestral', 'activa',  Carbon::now()->subDays(60), Carbon::now()->addDays(120)],
            // cliente 3 — anual activa
            [2, 'anual',     'activa',  Carbon::now()->subDays(30), Carbon::now()->addDays(335)],
            // cliente 4 — mensual vencida
            [3, 'mensual',   'vencida', Carbon::now()->subDays(40), Carbon::now()->subDays(10)],
            // cliente 5 — mensual activa
            [4, 'mensual',   'activa',  Carbon::now()->subDays(5),  Carbon::now()->addDays(25)],
            // cliente 6 — semestral vencida
            [5, 'semestral', 'vencida', Carbon::now()->subDays(200), Carbon::now()->subDays(20)],
            // cliente 7 — mensual activa
            [6, 'mensual',   'activa',  Carbon::now()->subDays(2),  Carbon::now()->addDays(28)],
            // cliente 8 — anual activa
            [7, 'anual',     'activa',  Carbon::now()->subDays(15), Carbon::now()->addDays(350)],
        ];

        foreach ($assignments as [$clientIndex, $tipo, $estado, $inicio, $fin]) {
            Membership::create([
                'client_id'          => $clients[$clientIndex]->id,
                'membership_plan_id' => $plans[$tipo]->id,
                'fecha_inicio'       => $inicio,
                'fecha_fin'          => $fin,
                'estado'             => $estado,
                'user_id'            => $admin->id,
            ]);
        }
    }
}
