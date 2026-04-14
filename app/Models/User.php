<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',       // nuestro
        'active',     // nuestro
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Breeze
            'password'          => 'hashed',   // Breeze
            'active'            => 'boolean',  // nuestro
        ];
    }


    /** Clientes que este empleado ha registrado */
    public function registeredClients()
    {
        return $this->hasMany(Client::class, 'user_id');
    }

    /** Membresías que este empleado ha gestionado */
    public function managedMemberships()
    {
        return $this->hasMany(Membership::class, 'user_id');
    }

    /** Pagos que este empleado ha procesado */
    public function processedPayments()
    {
        return $this->hasMany(Payment::class, 'user_id');
    }

    /** Perfil de entrenador (solo si el usuario tiene role = trainer) */
    public function trainer()
    {
        return $this->hasOne(Trainer::class, 'user_id');
    }

    // ─── Helpers ──────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }
}
