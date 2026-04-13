<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Membership;
use App\Models\Payment;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Clientes con pagos vencidos
        $overduePayments = Payment::where('estado', 'vencido')
            ->with(['membership.client', 'membership.plan'])
            ->get()
            ->map(function ($payment) {
                $diasAtraso = now()->diffInDays($payment->fecha_vencimiento, false) * -1;
                return [
                    'id'             => $payment->id,
                    'client_name'    => $payment->membership->client->nombre . ' ' . $payment->membership->client->apellido,
                    'plan'           => $payment->membership->plan->nombre,
                    'monto'          => $payment->monto,
                    'dias_atraso'    => max(0, (int) $diasAtraso),
                    'fecha_vencimiento' => $payment->fecha_vencimiento,
                ];
            });

        $stats = [
            'clientes_activos'   => Client::where('estado', 'activo')->count(),
            'clientes_inactivos' => Client::where('estado', 'inactivo')->count(),
            'membresias_activas' => Membership::where('estado', 'activa')->count(),
            'pagos_vencidos'     => $overduePayments->count(),
        ];

        return Inertia::render('Dashboard', [
            'stats'           => $stats,
            'overduePayments' => $overduePayments,
        ]);
    }
}
