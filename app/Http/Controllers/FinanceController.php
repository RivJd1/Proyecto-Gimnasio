<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Membership;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FinanceController extends Controller
{
    public function index(Request $request): Response
    {
        $desde = $request->input('desde', Carbon::now()->startOfMonth()->toDateString());
        $hasta = $request->input('hasta', Carbon::now()->toDateString());

        // Ingresos totales del período
        $pagos = Payment::where('estado', 'pagado')
            ->whereBetween('fecha_pago', [$desde, $hasta])
            ->with('membership.plan')
            ->get();

        $totalIngresos = $pagos->sum('monto');

        // Desglose por tipo de plan
        $desglose = $pagos->groupBy(fn($p) => $p->membership?->plan?->tipo ?? 'otro')
            ->map(fn($grupo) => [
                'cantidad' => $grupo->count(),
                'total'    => $grupo->sum('monto'),
            ]);

        // Clientes en mora
        $enMora = Payment::where('estado', 'vencido')
            ->with('membership.client')
            ->get();

        $montoPendiente = $enMora->sum('monto');
        $clientesEnMora = $enMora->pluck('membership.client')
            ->filter()
            ->unique('id')
            ->map(fn($c) => [
                'id'      => $c->id,
                'nombre'  => $c->nombre . ' ' . $c->apellido,
                'cedula'  => $c->cedula,
            ])->values();

        // Clientes nuevos en el período
        $clientesNuevos = Client::whereBetween('created_at', [$desde, $hasta])->count();

        // Ingresos por mes (últimos 6 meses para gráfica)
        $ingresosMensuales = collect(range(5, 0))->map(function ($i) {
            $mes = Carbon::now()->subMonths($i);
            $total = Payment::where('estado', 'pagado')
                ->whereYear('fecha_pago', $mes->year)
                ->whereMonth('fecha_pago', $mes->month)
                ->sum('monto');
            return [
                'mes'   => $mes->locale('es')->isoFormat('MMM YY'),
                'total' => (float) $total,
            ];
        });

        return Inertia::render('Finance/Index', [
            'desde'             => $desde,
            'hasta'             => $hasta,
            'totalIngresos'     => (float) $totalIngresos,
            'desglose'          => $desglose,
            'montoPendiente'    => (float) $montoPendiente,
            'clientesEnMora'    => $clientesEnMora,
            'clientesNuevos'    => $clientesNuevos,
            'ingresosMensuales' => $ingresosMensuales,
        ]);
    }
}
