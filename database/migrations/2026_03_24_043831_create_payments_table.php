<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')
                ->constrained('memberships')
                ->restrictOnDelete();
            $table->decimal('monto', 8, 2);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta']);
            $table->enum('estado', ['pagado', 'pendiente', 'vencido', 'anulado'])->default('pendiente');
            $table->date('fecha_pago')->nullable();
            $table->date('fecha_vencimiento');
            $table->string('referencia')->nullable()->comment('Número de referencia para pagos con tarjeta');
            $table->foreignId('user_id')
                ->comment('Empleado que procesó el pago')
                ->constrained('users')
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
