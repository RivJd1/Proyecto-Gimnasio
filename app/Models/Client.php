<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;
    protected $fillable = [
        'nombre',
        'apellido',
        'telefono',
        'email',
        'cedula',
        'fecha_nacimiento',
        'estado',
        'user_id',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    // ─── Relaciones ───────────────────────────────────────────

    /** Empleado que registró al cliente */
    public function registeredBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Todas las membresías del cliente */
    public function memberships()
    {
        return $this->hasMany(Membership::class, 'client_id');
    }

    /** Membresía activa actual */
    public function activeMembership()
    {
        return $this->hasOne(Membership::class, 'client_id')
            ->where('estado', 'activa')
            ->latestOfMany();
    }

    /** Planes de entrenamiento asignados */
    public function planAssignments()
    {
        return $this->hasMany(PlanAssignment::class, 'client_id');
    }

    /** Asignación de plan activa */
    public function activePlanAssignment()
    {
        return $this->hasOne(PlanAssignment::class, 'client_id')
            ->where('estado', 'activo');
    }

    // ─── Helpers ──────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return "{$this->nombre} {$this->apellido}";
    }

    public function isActive(): bool
    {
        return $this->estado === 'activo';
    }
}
