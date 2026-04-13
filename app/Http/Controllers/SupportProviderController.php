<?php

namespace App\Http\Controllers;

use App\Models\SupportProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SupportProviderController extends Controller
{
    public function index(): Response
    {
        $providers = SupportProvider::orderBy('nombre')->get()->map(fn($p) => [
            'id'       => $p->id,
            'nombre'   => $p->nombre,
            'telefono' => $p->telefono,
            'email'    => $p->email,
            'servicio' => $p->servicio,
            'notas'    => $p->notas,
            'activo'   => $p->activo,
        ]);

        return Inertia::render('Support/Index', compact('providers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email'    => 'nullable|email|max:255',
            'servicio' => 'required|string|max:255',
            'notas'    => 'nullable|string',
        ]);

        SupportProvider::create(array_merge(
            $request->only(['nombre', 'telefono', 'email', 'servicio', 'notas']),
            ['activo' => true]
        ));

        return redirect()->route('support-providers.index')
            ->with('success', 'Proveedor agregado correctamente.');
    }

    public function update(Request $request, SupportProvider $supportProvider): RedirectResponse
    {
        $request->validate([
            'nombre'   => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email'    => 'nullable|email|max:255',
            'servicio' => 'required|string|max:255',
            'notas'    => 'nullable|string',
        ]);

        $supportProvider->update($request->only(['nombre', 'telefono', 'email', 'servicio', 'notas']));

        return redirect()->route('support-providers.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(SupportProvider $supportProvider): RedirectResponse
    {
        $supportProvider->delete();
        return redirect()->route('support-providers.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
