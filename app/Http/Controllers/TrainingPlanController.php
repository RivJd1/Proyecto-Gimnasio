<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Models\TrainingPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TrainingPlanController extends Controller
{
    public function index(): Response
    {
        $plans = TrainingPlan::with('trainer.user')
            ->orderBy('nombre')
            ->get()
            ->map(fn($p) => [
                'id'           => $p->id,
                'nombre'       => $p->nombre,
                'descripcion'  => $p->descripcion,
                'tipo'         => $p->tipo,
                'dias_semana'  => $p->dias_semana,
                'activo'       => $p->activo,
                'trainer_name' => $p->trainer?->user?->name ?? '—',
            ]);

        $trainers = Trainer::with('user')
            ->where('estado', 'activo')
            ->get()
            ->map(fn($t) => ['id' => $t->id, 'name' => $t->user?->name ?? '—']);

        return Inertia::render('TrainingPlans/Index', compact('plans', 'trainers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'tipo'        => 'required|in:asistido,libre',
            'dias_semana' => 'required|integer|min:1|max:7',
            'trainer_id'  => 'required_if:tipo,asistido|nullable|exists:trainers,id',
        ]);

        TrainingPlan::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'tipo'        => $request->tipo,
            'dias_semana' => $request->dias_semana,
            'trainer_id'  => $request->trainer_id,
            'activo'      => true,
        ]);

        return redirect()->route('training-plans.index')
            ->with('success', 'Plan de entrenamiento creado correctamente.');
    }

    public function update(Request $request, TrainingPlan $trainingPlan): RedirectResponse
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'tipo'        => 'required|in:asistido,libre',
            'dias_semana' => 'required|integer|min:1|max:7',
            'trainer_id'  => 'required_if:tipo,asistido|nullable|exists:trainers,id',
            'activo'      => 'boolean',
        ]);

        $trainingPlan->update($request->only([
            'nombre', 'descripcion', 'tipo', 'dias_semana', 'trainer_id', 'activo'
        ]));

        return redirect()->route('training-plans.index')
            ->with('success', 'Plan actualizado correctamente.');
    }

    public function destroy(TrainingPlan $trainingPlan): RedirectResponse
    {
        $trainingPlan->update(['activo' => false]);
        return redirect()->route('training-plans.index')
            ->with('success', 'Plan desactivado correctamente.');
    }
}
