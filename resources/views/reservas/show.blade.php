{{-- =====================================================================
     ARCHIVO: show.blade.php
     UBICACIÓN: resources/views/reservas/show.blade.php
     ===================================================================== --}}
@extends('layouts.app')
@section('titulo', 'Reserva ' . $reserva->codigo_reserva)

@section('contenido')

<div class="row g-3">

    {{-- Columna izquierda --}}
    <div class="col-md-8">

        {{-- Header de reserva --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="fw-bold text-primary mb-1">{{ $reserva->codigo_reserva }}</h4>
                        <p class="text-muted mb-0">
                            Registrada el {{ $reserva->created_at->format('d/m/Y H:i') }}
                            por {{ $reserva->usuarioAdmin->nombre_completo }}
                        </p>
                    </div>
                    <span class="badge-estado badge-{{ $reserva->estado->nombre }} fs-6">
                        {{ ucfirst($reserva->estado->nombre) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Tour y cliente --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><i class="bi bi-person me-2"></i>Cliente</div>
                    <div class="card-body">
                        <p class="fw-bold mb-1">{{ $reserva->cliente->nombre_completo }}</p>
                        <p class="text-muted mb-1">
                            {{ $reserva->cliente->tipo_documento }}: {{ $reserva->cliente->numero_documento }}
                        </p>
                        <p class="text-muted mb-1"><i class="bi bi-whatsapp"></i> {{ $reserva->cliente->telefono_whatsapp ?? '—' }}</p>
                        <p class="text-muted mb-0"><i class="bi bi-envelope"></i> {{ $reserva->cliente->email ?? '—' }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><i class="bi bi-map me-2"></i>Tour</div>
                    <div class="card-body">
                        <p class="fw-bold mb-1">{{ $reserva->fechaTour->tour->nombre }}</p>
                        <p class="text-muted mb-1">
                            <i class="bi bi-calendar3"></i>
                            {{ $reserva->fechaTour->fecha->format('d/m/Y') }}
                            a las {{ $reserva->fechaTour->hora_salida }}
                        </p>
                        <p class="text-muted mb-1">
                            <i class="bi bi-people"></i>
                            {{ $reserva->cantidad_adultos }} adulto(s),
                            {{ $reserva->cantidad_ninos }} niño(s)
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Pasajeros --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-people me-2"></i>Pasajeros</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead style="background:#f8f9fa">
                        <tr>
                            <th class="ps-3">Nombre</th>
                            <th>Tipo</th>
                            <th>Documento</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reserva->pasajeros as $pasajero)
                        <tr>
                            <td class="ps-3">{{ $pasajero->nombre_completo }}</td>
                            <td><span class="badge bg-light text-dark">{{ ucfirst($pasajero->tipo) }}</span></td>
                            <td>{{ $pasajero->numero_documento ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagos --}}
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-cash-coin me-2"></i>Pagos registrados</span>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalPago">
                    <i class="bi bi-plus"></i> Registrar pago
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead style="background:#f8f9fa">
                        <tr>
                            <th class="ps-3">Fecha</th>
                            <th>Método</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reserva->pagos as $pago)
                        <tr>
                            <td class="ps-3">{{ $pago->fecha_pago->format('d/m/Y') }}</td>
                            <td>{{ $pago->metodoPago->nombre }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $pago->tipo_pago)) }}</td>
                            <td>S/ {{ number_format($pago->monto, 2) }}</td>
                            <td>
                                @if($pago->estado_validacion === 'verificado')
                                    <span class="badge bg-success">Verificado</span>
                                @elseif($pago->estado_validacion === 'rechazado')
                                    <span class="badge bg-danger">Rechazado</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                @endif
                            </td>
                            <td>
                                @if($pago->estado_validacion === 'pendiente')
                                <form method="POST" action="{{ route('pagos.verificar', $pago) }}" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-xs btn-outline-success btn-sm">
                                        <i class="bi bi-check"></i>
                                    </button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">Sin pagos registrados</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <span class="text-muted">Total: <strong>S/ {{ number_format($reserva->precio_total, 2) }}</strong></span>
                <span class="text-muted">Pagado: <strong class="text-success">S/ {{ number_format($reserva->monto_pagado, 2) }}</strong></span>
                <span class="text-muted">Saldo: <strong class="text-danger">S/ {{ number_format($reserva->saldo_pendiente, 2) }}</strong></span>
            </div>
        </div>

    </div>

    {{-- Columna derecha --}}
    <div class="col-md-4">

        {{-- Cambiar estado --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-arrow-repeat me-2"></i>Cambiar Estado</div>
            <div class="card-body">
                <form method="POST" action="{{ route('reservas.cambiar-estado', $reserva) }}">
                    @csrf @method('PATCH')
                    <select name="estado_id" class="form-select form-select-sm mb-2">
                        @foreach($estados ?? \App\Models\EstadoReserva::all() as $estado)
                            <option value="{{ $estado->id }}" {{ $reserva->estado_id == $estado->id ? 'selected' : '' }}>
                                {{ ucfirst($estado->nombre) }}
                            </option>
                        @endforeach
                    </select>
                    <input type="text" name="motivo" class="form-control form-control-sm mb-2"
                        placeholder="Motivo del cambio (opcional)">
                    <button type="submit" class="btn btn-primary btn-sm w-100">Actualizar estado</button>
                </form>
            </div>
        </div>

        {{-- Historial --}}
        <div class="card mb-3">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Historial</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @foreach($reserva->historialEstados->sortByDesc('fecha_cambio') as $h)
                    <li class="list-group-item py-2 px-3">
                        <div class="d-flex justify-content-between">
                            <small class="fw-semibold">{{ ucfirst($h->estadoNuevo->nombre) }}</small>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($h->fecha_cambio)->format('d/m H:i') }}</small>
                        </div>
                        @if($h->motivo)
                            <small class="text-muted">{{ $h->motivo }}</small>
                        @endif
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Logística --}}
        @if($reserva->logistica)
        <div class="card">
            <div class="card-header"><i class="bi bi-geo-alt me-2"></i>Logística</div>
            <div class="card-body">
                <p class="mb-1"><strong>Recojo:</strong> {{ $reserva->logistica->hora_recojo ?? '—' }}</p>
                <p class="mb-1"><strong>Dirección:</strong> {{ $reserva->logistica->direccion_recojo ?? '—' }}</p>
                <p class="mb-0"><strong>Guía:</strong> {{ $reserva->logistica->nombre_guia ?? '—' }}</p>
            </div>
        </div>
        @endif

    </div>
</div>

{{-- Modal: Registrar pago --}}
<div class="modal fade" id="modalPago" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('pagos.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="reserva_id" value="{{ $reserva->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Método de pago</label>
                            <select name="metodo_pago_id" class="form-select" required>
                                @foreach(\App\Models\MetodoPago::where('activo',true)->get() as $metodo)
                                    <option value="{{ $metodo->id }}">{{ $metodo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de pago</label>
                            <select name="tipo_pago" class="form-select">
                                <option value="adelanto">Adelanto</option>
                                <option value="saldo">Saldo</option>
                                <option value="pago_completo">Pago completo</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Monto (S/)</label>
                            <input type="number" name="monto" step="0.01" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Fecha de pago</label>
                            <input type="date" name="fecha_pago" class="form-control"
                                value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">N° Operación (opcional)</label>
                            <input type="text" name="numero_operacion" class="form-control"
                                placeholder="Código de la transacción">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Imagen del baucher</label>
                            <input type="file" name="archivo_baucher" class="form-control"
                                accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar pago</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection