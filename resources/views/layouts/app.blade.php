{{-- =====================================================================
     ARCHIVO: app.blade.php
     UBICACIÓN: resources/views/layouts/app.blade.php
     ===================================================================== --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADVENTUR — @yield('titulo', 'Sistema de Reservas')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar */
        .sidebar {
            width: 240px; min-height: 100vh;
            background: #1a2340; color: #fff;
            position: fixed; top: 0; left: 0;
            display: flex; flex-direction: column;
        }
        .sidebar-brand {
            padding: 20px;
            background: #111b33;
            font-size: 1.2rem; font-weight: 700;
            letter-spacing: 2px; color: #f0a500;
        }
        .sidebar-brand span { color: #fff; font-weight: 300; }
        .sidebar nav a {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 20px; color: #a0aec0;
            text-decoration: none; font-size: 0.9rem;
            transition: all 0.2s;
        }
        .sidebar nav a:hover, .sidebar nav a.active {
            background: #2d3a5e; color: #fff;
            border-left: 3px solid #f0a500;
        }
        .sidebar nav a i { font-size: 1rem; width: 20px; }
        .sidebar-section {
            padding: 10px 20px 4px;
            font-size: 0.7rem; text-transform: uppercase;
            letter-spacing: 1.5px; color: #4a5568; margin-top: 8px;
        }

        /* Main content */
        .main-content {
            margin-left: 240px;
            min-height: 100vh;
        }
        .topbar {
            background: #fff; padding: 14px 28px;
            border-bottom: 1px solid #e2e8f0;
            display: flex; justify-content: space-between; align-items: center;
        }
        .topbar h5 { margin: 0; font-weight: 600; color: #1a2340; }
        .page-body { padding: 24px 28px; }

        /* Cards */
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 6px rgba(0,0,0,0.07); }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; padding: 16px 20px; font-weight: 600; }

        /* Badges de estado */
        .badge-consulta     { background: #6b7280; color: #fff; }
        .badge-pre-reserva  { background: #f59e0b; color: #fff; }
        .badge-confirmada   { background: #10b981; color: #fff; }
        .badge-cancelada    { background: #ef4444; color: #fff; }
        .badge-finalizada   { background: #1d4ed8; color: #fff; }
        .badge-estado { padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
    </style>
</head>
<body>

{{-- SIDEBAR --}}
<div class="sidebar">
    <div class="sidebar-brand">ADV<span>ENTUR</span></div>
    <nav class="mt-2">
        <div class="sidebar-section">Principal</div>
        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2"></i> Dashboard
        </a>

        <div class="sidebar-section">Operaciones</div>
        <a href="{{ route('reservas.index') }}" class="{{ request()->routeIs('reservas.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i> Reservas
        </a>
        <a href="{{ route('clientes.index') }}" class="{{ request()->routeIs('clientes.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i> Clientes
        </a>
        <a href="{{ route('tours.index') }}" class="{{ request()->routeIs('tours.*') ? 'active' : '' }}">
            <i class="bi bi-map"></i> Tours
        </a>

        <div class="sidebar-section">Finanzas</div>
        <a href="#"><i class="bi bi-cash-coin"></i> Pagos</a>
        <a href="#"><i class="bi bi-receipt"></i> Comprobantes</a>
    </nav>
</div>

{{-- CONTENIDO PRINCIPAL --}}
<div class="main-content">
    <div class="topbar">
        <h5>@yield('titulo', 'Dashboard')</h5>
        <div class="d-flex align-items-center gap-3">
            <span class="text-muted" style="font-size:0.85rem">
                <i class="bi bi-person-circle"></i> Admin ADVENTUR
            </span>
        </div>
    </div>

    <div class="page-body">

        {{-- Mensajes de éxito --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('contenido')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>