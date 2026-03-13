{{-- =====================================================================
     ARCHIVO: dashboard.blade.php
     UBICACIÓN: resources/views/dashboard.blade.php
     ===================================================================== --}}
@extends('layouts.app')
@section('titulo', 'Dashboard')

@section('contenido')

{{-- Tarjetas de resumen --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1" style="font-size:0.8rem">RESERVAS HOY</p>
                    <h3 class="mb-0 fw-bold">0</h3>
                </div>
                <div style="background:#e8f4ff;padding:12px;border-radius:10px">
                    <i class="bi bi-calendar-check text-primary fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1" style="font-size:0.8rem">CONFIRMADAS</p>
                    <h3 class="mb-0 fw-bold">0</h3>
                </div>
                <div style="background:#e8fff4;padding:12px;border-radius:10px">
                    <i class="bi bi-check-circle text-success fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1" style="font-size:0.8rem">CLIENTES</p>
                    <h3 class="mb-0 fw-bold">0</h3>
                </div>
                <div style="background:#fff8e8;padding:12px;border-radius:10px">
                    <i class="bi bi-people text-warning fs-4"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <p class="text-muted mb-1" style="font-size:0.8rem">INGRESOS MES</p>
                    <h3 class="mb-0 fw-bold">S/ 0</h3>
                </div>
                <div style="background:#f0e8ff;padding:12px;border-radius:10px">
                    <i class="bi bi-cash-coin text-purple fs-4" style="color:#7c3aed"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Acceso rápido --}}
<div class="row g-3">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-clock-history me-2"></i>Últimas reservas</span>
                <a href="{{ route('reservas.index') }}" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8f9fa">
                        <tr>
                            <th class="ps-3" style="font-size:0.8rem">CÓDIGO</th>
                            <th style="font-size:0.8rem">CLIENTE</th>
                            <th style="font-size:0.8rem">TOUR</th>
                            <th style="font-size:0.8rem">ESTADO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                                No hay reservas aún
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-lightning me-2"></i>Acciones rápidas</div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('reservas.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Nueva Reserva
                </a>
                <a href="{{ route('clientes.create') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-person-plus me-2"></i>Nuevo Cliente
                </a>
                <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-map me-2"></i>Ver Tours
                </a>
            </div>
        </div>
    </div>
</div>

@endsection