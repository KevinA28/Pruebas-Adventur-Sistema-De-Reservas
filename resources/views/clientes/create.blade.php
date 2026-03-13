{{-- =====================================================================
     ARCHIVO: create.blade.php
     UBICACIÓN: resources/views/clientes/create.blade.php
     ===================================================================== --}}
@extends('layouts.app')
@section('titulo', 'Nuevo Cliente')

@section('contenido')

<div class="row justify-content-center">
<div class="col-md-7">
<div class="card">
    <div class="card-header"><i class="bi bi-person-plus me-2"></i>Registrar Cliente</div>
    <div class="card-body">
        <form method="POST" action="{{ route('clientes.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tipo de documento</label>
                    <select name="tipo_documento" class="form-select" required>
                        <option value="DNI">DNI</option>
                        <option value="RUC">RUC</option>
                        <option value="CE">Carnet Extranjería</option>
                        <option value="PASAPORTE">Pasaporte</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Número de documento</label>
                    <input type="text" name="numero_documento"
                        class="form-control @error('numero_documento') is-invalid @enderror"
                        value="{{ old('numero_documento') }}" required>
                    @error('numero_documento')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Nombre completo</label>
                    <input type="text" name="nombre_completo"
                        class="form-control @error('nombre_completo') is-invalid @enderror"
                        value="{{ old('nombre_completo') }}" required>
                    @error('nombre_completo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">WhatsApp</label>
                    <input type="text" name="telefono_whatsapp"
                        class="form-control" value="{{ old('telefono_whatsapp') }}"
                        placeholder="Ej: 999888777">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email"
                        class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Razón social (solo si es empresa)</label>
                    <input type="text" name="razon_social"
                        class="form-control" value="{{ old('razon_social') }}">
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Guardar Cliente
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>

@endsection