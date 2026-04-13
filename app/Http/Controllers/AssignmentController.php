<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\PlanAssignment;
use App\Models\Trainer;
use App\Models\TrainingPlan;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssignmentController extends Controller
{
    public function index(): Response
    {
        $assignments = PlanAssignment::with(['client', 'trainingPlan', 'trainer.user'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($a) => [
                'id'           => $a->id,
                'client_name'  => $a->client->nombre . ' ' . $a->client->apellido,
                'plan_nombre'  => $a->trainingPlan?->nombre ?? '—',
                'trainer_name' => $a->trainer?->user?->name ?? '—',
                'fecha_inicio' => $a->fecha_inicio,
                'fecha_fin'    => $a->fecha_fin,
                'estado'       => $a->estado,
                'notas'        => $a->notas,
            ]);

        $clients = Client::where('estado', 'activo')
            ->get()
            ->map(fn($c) => [
                'id'             => $c->id,
                'name'           => $c->nombre . ' ' . $c->apellido,
                'membresia_ok'   => $c->activeMembership !== null,
            ]);

        $plans = TrainingPlan::where('activo', true)
            ->get()
            ->map(fn($p) => [
                'id'          => $p->id,
                'nombre'      => $p->nombre,
                'tipo'        => $p->tipo,
                'dias_semana' => $p->dias_semana,
                'trainer_id'  => $p->trainer_id,
            ]);

        $trainers = Trainer::with('user')
            ->where('estado', 'activo')
            ->get()
            ->map(fn($t) => ['id' => $t->id, 'name' => $t->user?->name ?? '—']);

        return Inertia::render('Assignments/Index', compact('assignments', 'clients', 'plans', 'trainers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'client_id'        => 'required|exists:clients,id',
            'training_plan_id' => 'required|exists:training_plans,id',
            'trainer_id'       => 'nullable|exists:trainers,id',
            'fecha_inicio'     => 'required|date',
            'fecha_fin'        => 'nullable|date|after:fecha_inicio',
            'notas'            => 'nullable|string',
        ]);

        // Desactivar asignación activa anterior
        PlanAssignment::where('client_id', $request->client_id)
            ->where('estado', 'activo')
            ->update(['estado' => 'inactivo']);

        PlanAssignment::create([
            'client_id'        => $request->client_id,
            'training_plan_id' => $request->training_plan_id,
            'trainer_id'       => $request->trainer_id,
            'fecha_inicio'     => $request->fecha_inicio,
            'fecha_fin'        => $request->fecha_fin,
            'estado'           => 'activo',
            'notas'            => $request->notas,
        ]);

        return redirect()->route('assignments.index')
            ->with('success', 'Plan asignado correctamente.');
    }

    public function destroy(PlanAssignment $assignment): RedirectResponse
    {
        $assignment->update(['estado' => 'inactivo']);
        return redirect()->route('assignments.index')
            ->with('success', 'Asignación desactivada.');
    }
}
