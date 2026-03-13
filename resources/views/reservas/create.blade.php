{{-- =====================================================================
     ARCHIVO: create.blade.php
     UBICACIÓN: resources/views/reservas/create.blade.php
     ===================================================================== --}}
@extends('layouts.app')
@section('titulo', 'Nueva Reserva')

@section('contenido')

<div class="row justify-content-center">
<div class="col-md-10">

<form method="POST" action="{{ route('reservas.store') }}">
@csrf

{{-- PASO 1: Cliente --}}
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-person me-2"></i>1. Datos del Cliente</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Buscar por DNI / RUC</label>
                <div class="input-group">
                    <input type="text" id="buscar_doc" class="form-control"
                        placeholder="Ingresa DNI o RUC">
                    <button type="button" class="btn btn-outline-primary" onclick="buscarCliente()">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
                <small class="text-muted">Si no existe, regístralo primero</small>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Cliente seleccionado</label>
                <input type="hidden" name="cliente_id" id="cliente_id">
                <div id="cliente_info" class="form-control bg-light text-muted" style="min-height:38px">
                    Ninguno seleccionado
                </div>
                @error('cliente_id')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="mt-2">
            <a href="{{ route('clientes.create') }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-person-plus me-1"></i> Registrar nuevo cliente
            </a>
        </div>
    </div>
</div>

{{-- PASO 2: Tour y fecha --}}
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-map me-2"></i>2. Tour y Fecha</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Fecha de Tour disponible</label>
                <select name="fecha_tour_id" class="form-select" required>
                    <option value="">— Seleccionar —</option>
                    @foreach($fechas as $fecha)
                        <option value="{{ $fecha->id }}">
                            {{ $fecha->tour->nombre }} —
                            {{ $fecha->fecha->format('d/m/Y') }}
                            {{ $fecha->hora_salida }} —
                            Cupos: {{ $fecha->cupo_disponible }}
                        </option>
                    @endforeach
                </select>
                @error('fecha_tour_id')
                    <div class="text-danger small">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Adultos</label>
                <input type="number" name="cantidad_adultos" value="1"
                    min="1" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Niños</label>
                <input type="number" name="cantidad_ninos" value="0"
                    min="0" class="form-control">
            </div>
        </div>
    </div>
</div>

{{-- PASO 3: Canal y observaciones --}}
<div class="card mb-3">
    <div class="card-header"><i class="bi bi-chat me-2"></i>3. Canal de Contacto</div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Canal</label>
                <select name="canal_contacto" class="form-select" required>
                    <option value="whatsapp">📱 WhatsApp</option>
                    <option value="presencial">🏪 Presencial</option>
                    <option value="llamada">📞 Llamada</option>
                    <option value="redes_sociales">📸 Redes Sociales</option>
                    <option value="web">🌐 Web</option>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Observaciones</label>
                <textarea name="observaciones" class="form-control" rows="2"
                    placeholder="Notas adicionales..."></textarea>
            </div>
        </div>
    </div>
</div>

{{-- PASO 4: Pasajeros --}}
<div class="card mb-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>4. Pasajeros</span>
        <button type="button" class="btn btn-sm btn-outline-success" onclick="agregarPasajero()">
            <i class="bi bi-plus"></i> Agregar
        </button>
    </div>
    <div class="card-body">
        <div id="pasajeros_lista">
            {{-- Se genera dinámicamente --}}
        </div>
    </div>
</div>

{{-- Botones --}}
<div class="d-flex justify-content-end gap-2">
    <a href="{{ route('reservas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
    <button type="submit" class="btn btn-primary px-4">
        <i class="bi bi-check-circle me-1"></i> Crear Reserva
    </button>
</div>

</form>
</div>
</div>

@endsection

@section('scripts')
<script>
let contadorPasajeros = 0;

// Agregar fila de pasajero dinámicamente
function agregarPasajero() {
    const lista = document.getElementById('pasajeros_lista');
    const idx   = contadorPasajeros++;
    const div   = document.createElement('div');
    div.className = 'row g-2 mb-2 align-items-center';
    div.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="pasajeros[${idx}][nombre_completo]"
                class="form-control form-control-sm" placeholder="Nombre completo" required>
        </div>
        <div class="col-md-3">
            <select name="pasajeros[${idx}][tipo]" class="form-select form-select-sm">
                <option value="adulto">Adulto</option>
                <option value="nino">Niño</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="pasajeros[${idx}][numero_documento]"
                class="form-control form-control-sm" placeholder="DNI (opcional)">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-sm btn-outline-danger w-100"
                onclick="this.closest('.row').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </div>`;
    lista.appendChild(div);
}

// Buscar cliente por documento vía AJAX
function buscarCliente() {
    const doc = document.getElementById('buscar_doc').value.trim();
    if (!doc) return alert('Ingresa un número de documento');

    fetch(`/clientes/buscar-documento?numero=${doc}`)
        .then(r => r.json())
        .then(data => {
            if (data.encontrado) {
                document.getElementById('cliente_id').value = data.cliente.id;
                document.getElementById('cliente_info').innerHTML =
                    `<strong>${data.cliente.nombre_completo}</strong> — ${data.cliente.tipo_documento}: ${data.cliente.numero_documento}`;
                document.getElementById('cliente_info').classList.remove('text-muted');
            } else {
                alert('Cliente no encontrado. Regístralo primero con el botón de abajo.');
            }
        });
}

// Cargar un pasajero inicial al abrir el formulario
agregarPasajero();
</script>
@endsection