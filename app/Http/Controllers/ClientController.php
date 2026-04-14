<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Models\Client;
use App\Models\Membership;
use App\Models\MembershipPlan;
use App\Models\Payment;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ClientController extends Controller
{
    public function index(): Response
    {
        $clients = Client::with(['activeMembership.plan'])
            ->orderBy('nombre')
            ->get()
            ->map(fn($c) => [
                'id'       => $c->id,
                'nombre'   => $c->nombre,
                'apellido' => $c->apellido,
                'telefono' => $c->telefono,
                'email'    => $c->email,
                'cedula'   => $c->cedula,
                'estado'   => $c->estado,
                'plan'     => $c->activeMembership?->plan?->nombre,
                'membresia_estado' => $c->activeMembership?->estado,
            ]);

        return Inertia::render('Clients/Index', compact('clients'));
    }

    public function create(): Response
    {
        $plans = MembershipPlan::where('activo', true)->get();
        return Inertia::render('Clients/Create', compact('plans'));
    }

    public function store(StoreClienteRequest $request): RedirectResponse
    {
        // 1. Crear cliente
        $client = Client::create([
            'nombre'           => $request->nombre,
            'apellido'         => $request->apellido,
            'telefono'         => $request->telefono,
            'email'            => $request->email,
            'cedula'           => $request->cedula,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'estado'           => 'activo',
            'user_id'          => auth()->id(),
        ]);

        // 2. Crear membresía
        $plan = MembershipPlan::findOrFail($request->membership_plan_id);
        $fechaInicio = Carbon::today();
        $fechaFin    = $fechaInicio->copy()->addDays($plan->duracion_dias);

        $membership = Membership::create([
            'client_id'          => $client->id,
            'membership_plan_id' => $plan->id,
            'fecha_inicio'       => $fechaInicio,
            'fecha_fin'          => $fechaFin,
            'estado'             => 'activa',
            'user_id'            => auth()->id(),
        ]);

        // 3. Registrar pago inicial
        Payment::create([
            'membership_id'     => $membership->id,
            'monto'             => $plan->precio,
            'metodo_pago'       => $request->metodo_pago,
            'estado'            => 'pagado',
            'fecha_pago'        => $fechaInicio,
            'fecha_vencimiento' => $fechaFin,
            'referencia'        => 'REF-' . strtoupper(uniqid()),
            'user_id'           => auth()->id(),
        ]);

        return redirect()->route('clients.index')
            ->with('success', "Cliente {$client->nombre} {$client->apellido} registrado correctamente.");
    }

    public function show(Client $client): Response
    {
        $client->load([
            'memberships.plan',
            'memberships.payments',
            'activePlanAssignment.trainingPlan',
            'activePlanAssignment.trainer.user',
        ]);

        return Inertia::render('Clients/Show', compact('client'));
    }

    public function edit(Client $client): Response
    {
        return Inertia::render('Clients/Edit', compact('client'));
    }

    public function update(\Illuminate\Http\Request $request, Client $client): RedirectResponse
    {
        $request->validate([
            'nombre'           => 'required|string|max:100',
            'apellido'         => 'required|string|max:100',
            'telefono'         => 'required|string|max:20',
            'email'            => 'nullable|email|unique:clients,email,' . $client->id . '|max:255',
            'cedula'           => 'required|string|unique:clients,cedula,' . $client->id . '|max:50',
            'fecha_nacimiento' => 'required|date|before:today',
        ]);

        $client->update($request->only([
            'nombre', 'apellido', 'telefono', 'email', 'cedula', 'fecha_nacimiento'
        ]));

        return redirect()->route('clients.show', $client)
            ->with('success', 'Datos del cliente actualizados correctamente.');
    }

    public function destroy(Client $client): RedirectResponse
    {
        $client->update(['estado' => 'inactivo']);
        return redirect()->route('clients.index')
            ->with('success', 'Cliente desactivado correctamente.');
    }
}
