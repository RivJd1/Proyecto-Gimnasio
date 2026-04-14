<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

/**
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array {
        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'telefono' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'cedula' => $this->faker->unique()->numerify('##########'),
            'fecha_nacimiento' => $this->faker->date(),
            'estado' => 'activo',
            'user_id' => User::factory(), // Empleado que lo registra [cite: 14, 25]
        ];
    }
}
