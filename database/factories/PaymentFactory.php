<?php

namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array {
        return [
            'membership_id' => Membership::factory(),
            'monto' => 500.00,
            'metodo_pago' => 'tarjeta', // Ajustado según tu corrección [cite: 75]
            'estado' => 'pagado',
            'fecha_pago' => now(),
            'fecha_vencimiento' => now()->addDays(30),
            'referencia' => 'REF-' . strtoupper(uniqid()),
            'user_id' => User::factory(), // Procesado por [cite: 75, 78]
        ];
    }
}
