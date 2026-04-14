<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Payment;
use App\Models\Membership;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function index(): Response
    {
        $clients = Client::with(['activeMembership.plan', 'memberships.plan', 'memberships.payments'])
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get()
            ->map(function ($client) {
                // Busca activa primero, si no la más reciente
                $membership = $client->activeMembership
                    ?? $client->memberships->sortByDesc('fecha_fin')->first();

                $overduePayment = $membership
                    ? $membership->payments
                        ->whereIn('estado', ['vencido', 'pendiente'])
                        ->filter(fn($p) => $p->fecha_vencimiento && $p->fecha_vencimiento < now()->toDateString())
                        ->first()
                    : null;

                $diasAtraso = 0;
                if ($overduePayment?->fecha_vencimiento) {
                    $diasAtraso = max(0, (int) now()->diffInDays($overduePayment->fecha_vencimiento, false) * -1);
                }

                return [
                    'id'               => $client->id,
                    'nombre'           => $client->nombre,
                    'apellido'         => $client->apellido,
                    'cedula'           => $client->cedula,
                    'telefono'         => $client->telefono,
                    'membresia_id'     => $membership?->id,
                    'membresia_estado' => $membership?->estado,
                    'plan'             => $membership?->plan?->nombre,
                    'precio_plan'      => $membership?->plan?->precio,
                    'fecha_fin'        => $membership?->fecha_fin,
                    'en_mora'          => $overduePayment !== null,
                    'dias_atraso'      => $diasAtraso,
                    'pago_vencido_id'  => $overduePayment?->id,
                    'monto_pendiente'  => $overduePayment?->monto,
                ];
            });

        return Inertia::render('Payments/Index', compact('clients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'membership_id' => 'required|exists:memberships,id',
            'monto'         => 'required|numeric|min:0',
            'metodo_pago'   => 'required|in:efectivo,tarjeta',
            'payment_id'    => 'nullable|exists:payments,id',
        ]);

        $membership = Membership::with('plan')->findOrFail($request->membership_id);

        if ($request->payment_id) {
            // Marcar pago vencido como pagado
            $pago = Payment::findOrFail($request->payment_id);
            $pago->update([
                'estado'      => 'pagado',
                'fecha_pago'  => Carbon::today(),
                'metodo_pago' => $request->metodo_pago,
                'user_id'     => auth()->id(),
            ]);

            // Actualizar fecha_fin de membresía desde hoy
            $nuevaFechaFin = Carbon::today()->addDays($membership->plan->duracion_dias);
            $membership->update([
                'estado'    => 'activa',
                'fecha_fin' => $nuevaFechaFin,
            ]);

            // Crear siguiente pago pendiente
            Payment::create([
                'membership_id'     => $membership->id,
                'monto'             => $membership->plan->precio,
                'metodo_pago'       => $request->metodo_pago,
                'estado'            => 'pendiente',
                'fecha_pago'        => null,
                'fecha_vencimiento' => $nuevaFechaFin,
                'referencia'        => 'REF-' . strtoupper(uniqid()),
                'user_id'           => auth()->id(),
            ]);

        } else {
            // Renovación normal — registrar pago y crear siguiente pendiente
            $fechaInicio   = Carbon::today();
            $nuevaFechaFin = $fechaInicio->copy()->addDays($membership->plan->duracion_dias);

            Payment::create([
                'membership_id'     => $membership->id,
                'monto'             => $request->monto,
                'metodo_pago'       => $request->metodo_pago,
                'estado'            => 'pagado',
                'fecha_pago'        => $fechaInicio,
                'fecha_vencimiento' => $nuevaFechaFin,
                'referencia'        => 'REF-' . strtoupper(uniqid()),
                'user_id'           => auth()->id(),
            ]);

            $membership->update([
                'fecha_fin' => $nuevaFechaFin,
                'estado'    => 'activa',
            ]);

            // Crear siguiente pago pendiente
            Payment::create([
                'membership_id'     => $membership->id,
                'monto'             => $membership->plan->precio,
                'metodo_pago'       => $request->metodo_pago,
                'estado'            => 'pendiente',
                'fecha_pago'        => null,
                'fecha_vencimiento' => $nuevaFechaFin,
                'referencia'        => 'REF-NEXT-' . strtoupper(uniqid()),
                'user_id'           => auth()->id(),
            ]);
        }

        return redirect()->route('payments.index')
            ->with('success', 'Pago registrado correctamente.');
    }
}
