{{-- =====================================================================
     ARCHIVO: index.blade.php
     UBICACIÓN: resources/views/reservas/index.blade.php
     ===================================================================== --}}
@extends('layouts.app')
@section('titulo', 'Reservas')

@section('contenido')

{{-- Barra de filtros --}}
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ route('reservas.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="buscar" value="{{ request('buscar') }}"
                    class="form-control form-control-sm"
                    placeholder="Buscar por código o cliente...">
            </div>
            <div class="col-md-3">
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    @foreach($estados as $estado)
                        <option value="{{ $estado->id }}" {{ request('estado') == $estado->id ? 'selected' : '' }}>
                            {{ ucfirst($estado->nombre) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="canal" class="form-select form-select-sm">
                    <option value="">Todos los canales</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="presencial">Presencial</option>
                    <option value="llamada">Llamada</option>
                    <option value="redes_sociales">Redes Sociales</option>
                    <option value="web">Web</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla de reservas --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-calendar-check me-2"></i>Lista de Reservas</span>
        <a href="{{ route('reservas.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Nueva Reserva
        </a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8f9fa">
                <tr>
                    <th class="ps-3" style="font-size:0.8rem">CÓDIGO</th>
                    <th style="font-size:0.8rem">CLIENTE</th>
                    <th style="font-size:0.8rem">TOUR</th>
                    <th style="font-size:0.8rem">FECHA</th>
                    <th style="font-size:0.8rem">PASAJEROS</th>
                    <th style="font-size:0.8rem">TOTAL</th>
                    <th style="font-size:0.8rem">ESTADO</th>
                    <th style="font-size:0.8rem">CANAL</th>
                    <th style="font-size:0.8rem"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservas as $reserva)
                <tr>
                    <td class="ps-3">
                        <span class="fw-bold text-primary">{{ $reserva->codigo_reserva }}</span>
                    </td>
                    <td>{{ $reserva->cliente->nombre_completo }}</td>
                    <td>{{ $reserva->fechaTour->tour->nombre }}</td>
                    <td>{{ $reserva->fechaTour->fecha->format('d/m/Y') }}</td>
                    <td>
                        <span class="badge bg-light text-dark">
                            {{ $reserva->cantidad_adultos + $reserva->cantidad_ninos }} pax
                        </span>
                    </td>
                    <td>S/ {{ number_format($reserva->precio_total, 2) }}</td>
                    <td>
                        <span class="badge-estado badge-{{ $reserva->estado->nombre }}">
                            {{ ucfirst($reserva->estado->nombre) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $iconos = ['whatsapp'=>'whatsapp','presencial'=>'shop','llamada'=>'telephone','redes_sociales'=>'instagram','web'=>'globe'];
                        @endphp
                        <i class="bi bi-{{ $iconos[$reserva->canal_contacto] ?? 'chat' }}" title="{{ $reserva->canal_contacto }}"></i>
                    </td>
                    <td>
                        <a href="{{ route('reservas.show', $reserva) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No hay reservas registradas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reservas->hasPages())
    <div class="card-footer">
        {{ $reservas->links() }}
    </div>
    @endif
</div>

@endsection