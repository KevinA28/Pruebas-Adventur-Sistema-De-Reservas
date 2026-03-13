{{-- =====================================================================
     ARCHIVO: index.blade.php
     UBICACIÓN: resources/views/clientes/index.blade.php
     ===================================================================== --}}
@extends('layouts.app')
@section('titulo', 'Clientes')

@section('contenido')

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>Clientes registrados</span>
        <a href="{{ route('clientes.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-person-plus me-1"></i> Nuevo Cliente
        </a>
    </div>

    {{-- Buscador --}}
    <div class="card-body border-bottom py-2">
        <form method="GET" action="{{ route('clientes.index') }}" class="d-flex gap-2">
            <input type="text" name="buscar" value="{{ request('buscar') }}"
                class="form-control form-control-sm" placeholder="Buscar por nombre o documento...">
            <button class="btn btn-primary btn-sm">Buscar</button>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary btn-sm">Limpiar</a>
        </form>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8f9fa">
                <tr>
                    <th class="ps-3" style="font-size:0.8rem">DOCUMENTO</th>
                    <th style="font-size:0.8rem">NOMBRE</th>
                    <th style="font-size:0.8rem">WHATSAPP</th>
                    <th style="font-size:0.8rem">EMAIL</th>
                    <th style="font-size:0.8rem">RESERVAS</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $cliente)
                <tr>
                    <td class="ps-3">
                        <span class="badge bg-light text-dark">{{ $cliente->tipo_documento }}</span>
                        {{ $cliente->numero_documento }}
                    </td>
                    <td class="fw-semibold">{{ $cliente->nombre_completo }}</td>
                    <td>{{ $cliente->telefono_whatsapp ?? '—' }}</td>
                    <td>{{ $cliente->email ?? '—' }}</td>
                    <td>
                        <span class="badge bg-primary">{{ $cliente->reservas_count }}</span>
                    </td>
                    <td>
                        <a href="{{ route('clientes.show', $cliente) }}"
                            class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-people fs-3 d-block mb-2"></i>
                        No hay clientes registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clientes->hasPages())
    <div class="card-footer">{{ $clientes->links() }}</div>
    @endif
</div>

@endsection