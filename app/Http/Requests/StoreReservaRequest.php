<?php
// =====================================================================
// ARCHIVO: StoreReservaRequest.php
// UBICACIÓN: app/Http/Requests/StoreReservaRequest.php
// =====================================================================

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id'                  => 'required|exists:clientes,id',
            'fecha_tour_id'               => 'required|exists:fechas_tour,id',
            'cantidad_adultos'            => 'required|integer|min:1',
            'cantidad_ninos'              => 'required|integer|min:0',
            'canal_contacto'              => 'required|in:whatsapp,presencial,llamada,redes_sociales,web',
            'observaciones'               => 'nullable|string|max:1000',
            'pasajeros'                   => 'required|array|min:1',
            'pasajeros.*.nombre_completo' => 'required|string|max:200',
            'pasajeros.*.tipo'            => 'required|in:adulto,nino',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required'                  => 'Debes seleccionar un cliente.',
            'cliente_id.exists'                    => 'El cliente seleccionado no existe.',
            'fecha_tour_id.required'               => 'Debes seleccionar una fecha de tour.',
            'fecha_tour_id.exists'                 => 'La fecha de tour no existe.',
            'cantidad_adultos.min'                 => 'Debe haber al menos 1 adulto.',
            'canal_contacto.in'                    => 'El canal de contacto no es válido.',
            'pasajeros.required'                   => 'Debes agregar al menos un pasajero.',
            'pasajeros.*.nombre_completo.required' => 'El nombre del pasajero es obligatorio.',
            'pasajeros.*.tipo.in'                  => 'El tipo de pasajero debe ser adulto o niño.',
        ];
    }
}