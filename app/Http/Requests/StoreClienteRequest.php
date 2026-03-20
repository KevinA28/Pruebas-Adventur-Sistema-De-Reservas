<?php
// =====================================================================
// ARCHIVO: StoreClienteRequest.php
// UBICACIÓN: app/Http/Requests/StoreClienteRequest.php
// =====================================================================

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
            'tipo_documento'    => 'required|in:DNI,RUC,CE,PASAPORTE',
            'numero_documento'  => 'required|string|unique:clientes,numero_documento',
            'nombre_completo'   => 'required|string|max:200',
            'email'             => 'nullable|email|max:200',
            'telefono_whatsapp' => 'nullable|string|max:20',
            'direccion'         => 'nullable|string|max:300',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_documento.required'   => 'El tipo de documento es obligatorio.',
            'tipo_documento.in'         => 'El tipo debe ser DNI, RUC, CE o PASAPORTE.',
            'numero_documento.required' => 'El número de documento es obligatorio.',
            'numero_documento.unique'   => 'Ya existe un cliente con ese número de documento.',
            'nombre_completo.required'  => 'El nombre completo es obligatorio.',
            'email.email'               => 'El email no tiene un formato válido.',
        ];
    }
}