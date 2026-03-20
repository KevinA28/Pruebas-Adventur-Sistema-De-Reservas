<?php
// =====================================================================
// ARCHIVO: StorePagoRequest.php
// UBICACIÓN: app/Http/Requests/StorePagoRequest.php
// =====================================================================

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reserva_id'      => 'required|exists:reservas,id',
            'metodo_pago_id'  => 'required|exists:metodos_pago,id',
            'monto'           => 'required|numeric|min:1',
            'tipo_pago'       => 'required|in:adelanto,saldo,pago_completo',
            'fecha_pago'      => 'required|date',
            'archivo_baucher' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'referencia'      => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'reserva_id.required'     => 'La reserva es obligatoria.',
            'reserva_id.exists'       => 'La reserva seleccionada no existe.',
            'metodo_pago_id.required' => 'El método de pago es obligatorio.',
            'metodo_pago_id.exists'   => 'El método de pago no existe.',
            'monto.min'               => 'El monto debe ser mayor a 0.',
            'tipo_pago.in'            => 'El tipo debe ser adelanto, saldo o pago_completo.',
            'fecha_pago.required'     => 'La fecha de pago es obligatoria.',
            'archivo_baucher.mimes'   => 'El baucher debe ser JPG, PNG o PDF.',
            'archivo_baucher.max'     => 'El baucher no puede superar 5MB.',
        ];
    }
}