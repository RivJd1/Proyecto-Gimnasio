<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::orderBy('name')->get()->map(fn($u) => [
            'id'         => $u->id,
            'name'       => $u->name,
            'email'      => $u->email,
            'role'       => $u->role,
            'active'     => $u->active,
            'created_at' => $u->created_at->toDateString(),
        ]);

        return Inertia::render('Users/Index', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'role'     => 'required|in:admin,receptionist,trainer',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'password' => Hash::make($request->password),
            'active'   => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Empleado creado correctamente.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,receptionist,trainer',
        ]);

        $user->update($request->only(['name', 'email', 'role']));

        return redirect()->route('admin.users.index')
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update(['active' => false]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Empleado desactivado correctamente.');
    }

    public function reactivate(User $user): RedirectResponse
    {
        $user->update(['active' => true]);
        return redirect()->route('admin.users.index')
            ->with('success', 'Empleado reactivado correctamente.');
    }
}
