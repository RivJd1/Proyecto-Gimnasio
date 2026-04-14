<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'           => 'required|string|max:100',
            'apellido'         => 'required|string|max:100',
            'telefono'         => 'required|string|max:20',
            'email'            => 'nullable|email|unique:clients,email|max:255',
            'cedula'           => 'required|string|unique:clients,cedula|max:50',
            'fecha_nacimiento' => 'required|date|before:today',
            'membership_plan_id' => 'required|exists:membership_plans,id',
            'metodo_pago'      => 'required|in:efectivo,tarjeta',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required'            => 'El nombre es obligatorio.',
            'apellido.required'          => 'El apellido es obligatorio.',
            'telefono.required'          => 'El teléfono es obligatorio.',
            'email.unique'               => 'Este correo ya está registrado.',
            'cedula.required'            => 'La cédula es obligatoria.',
            'cedula.unique'              => 'Esta cédula ya está registrada.',
            'fecha_nacimiento.required'  => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before'    => 'La fecha de nacimiento debe ser anterior a hoy.',
            'membership_plan_id.required'=> 'Debe seleccionar un plan.',
            'membership_plan_id.exists'  => 'El plan seleccionado no existe.',
            'metodo_pago.required'       => 'Debe seleccionar un método de pago.',
        ];
    }
}
