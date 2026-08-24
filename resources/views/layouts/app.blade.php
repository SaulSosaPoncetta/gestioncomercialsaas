<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Gestión Comercial') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        body {
            min-height: 100vh;
        }

        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: #14532d;
            transition: width .2s ease;
            flex-shrink: 0;
            overflow-x: hidden;
        }

        .sidebar.collapsed {
            width: 68px;
        }

        .sidebar .brand {
            color: #ffffff;
        }

        .sidebar .brand-text {
            white-space: nowrap;
        }

        .sidebar.collapsed .brand-text,
        .sidebar.collapsed .nav-header,
        .sidebar.collapsed .link-label {
            display: none;
        }

        .sidebar .btn-toggle {
            color: #ffffff;
            background: transparent;
            border: none;
        }

        .sidebar .btn-toggle:hover {
            background: rgba(255, 255, 255, .1);
        }

        .sidebar .nav-link {
            color: #ffffff;
            padding: .55rem .85rem;
            display: flex;
            align-items: center;
            white-space: nowrap;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: .55rem 0;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #ffffff;
            background: #1e6b3a;
            border-radius: .375rem;
        }

        .sidebar .nav-header {
            color: #bfe6cd;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            padding: .75rem 1rem .25rem;
        }

        .main-content {
            flex: 1;
            min-height: 100vh;
            background: #f4f6f8;
            min-width: 0;
        }
    </style>
</head>

<body>
    <div id="app" class="d-flex">
        <nav class="sidebar d-flex flex-column p-2" id="sidebar">
            <div class="d-flex align-items-center justify-content-between px-1 py-2 mb-2">
                <a class="brand d-flex align-items-center text-decoration-none" href="{{ route('dashboard') }}">
                    <i class="bi bi-shop fs-4"></i>
                    <span class="brand-text fs-5 fw-semibold ms-2">Gestión Comercial</span>
                </a>
                <button type="button" class="btn-toggle" id="btn-toggle-sidebar" title="Colapsar/expandir menú">
                    <i class="bi bi-list fs-4"></i>
                </button>
            </div>

            <div class="nav-header">Panel</div>
            <a class="nav-link {{ request()->is('home') ? 'active' : '' }}" href="{{ route('dashboard') }}"
                title="Dashboard">
                <i class="bi bi-speedometer2"></i><span class="link-label ms-2">Dashboard</span>
            </a>

            <div class="nav-header">Catálogo</div>
            @if (Route::has('categorias.index'))
                <a class="nav-link {{ request()->is('categorias*') ? 'active' : '' }}"
                    href="{{ route('categorias.index') }}" title="Categorías">
                    <i class="bi bi-diagram-3"></i><span class="link-label ms-2">Categorías</span>
                </a>
            @endif
            @if (Route::has('marcas.index'))
                <a class="nav-link {{ request()->is('marcas*') ? 'active' : '' }}" href="{{ route('marcas.index') }}"
                    title="Marcas">
                    <i class="bi bi-tags"></i><span class="link-label ms-2">Marcas</span>
                </a>
            @endif
            @if (Route::has('productos.index'))
                <a class="nav-link {{ request()->is('productos*') ? 'active' : '' }}"
                    href="{{ route('productos.index') }}" title="Productos">
                    <i class="bi bi-box-seam"></i><span class="link-label ms-2">Productos</span>
                </a>
            @endif
            @if (Route::has('listas-precios.index'))
                <a class="nav-link {{ request()->is('listas-precios*') ? 'active' : '' }}"
                    href="{{ route('listas-precios.index') }}" title="Listas de Precios">
                    <i class="bi bi-cash-coin"></i><span class="link-label ms-2">Listas de Precios</span>
                </a>
            @endif

            <div class="nav-header">Personas</div>
            @if (Route::has('personas.index'))
                <a class="nav-link {{ request()->is('personas*') ? 'active' : '' }}"
                    href="{{ route('personas.index') }}" title="Clientes / Proveedores">
                    <i class="bi bi-people"></i><span class="link-label ms-2">Clientes / Proveedores</span>
                </a>
            @endif

            <div class="nav-header">Inventario</div>
            @if (Route::has('almacenes.index'))
                <a class="nav-link {{ request()->is('almacenes*') ? 'active' : '' }}"
                    href="{{ route('almacenes.index') }}" title="Almacenes">
                    <i class="bi bi-building"></i><span class="link-label ms-2">Almacenes</span>
                </a>
            @endif
            @if (Route::has('stock.index'))
                <a class="nav-link {{ request()->is('stock*') ? 'active' : '' }}" href="{{ route('stock.index') }}"
                    title="Stock">
                    <i class="bi bi-boxes"></i><span class="link-label ms-2">Stock</span>
                </a>
            @endif
            @if (Route::has('transferencias.index'))
                <a class="nav-link {{ request()->is('transferencias*') ? 'active' : '' }}"
                    href="{{ route('transferencias.index') }}" title="Transferencias">
                    <i class="bi bi-arrow-left-right"></i><span class="link-label ms-2">Transferencias</span>
                </a>
            @endif

            <div class="nav-header">Compras y Ventas</div>
            @if (Route::has('compras.index'))
                <a class="nav-link {{ request()->is('compras*') ? 'active' : '' }}"
                    href="{{ route('compras.index') }}" title="Compras">
                    <i class="bi bi-cart-plus"></i><span class="link-label ms-2">Compras</span>
                </a>
                @if (Route::has('notas-credito-compra.index'))
                    <a class="nav-link {{ request()->is('notas-credito-compra*') ? 'active' : '' }}"
                        href="{{ route('notas-credito-compra.index') }}" title="Notas de Crédito (Compras)">
                        <i class="bi bi-arrow-return-right"></i><span class="link-label ms-2">N. Créd. Compras</span>
                    </a>
                @endif
            @endif
            @if (Route::has('ventas.index'))
                <a class="nav-link {{ request()->is('ventas*') ? 'active' : '' }}" href="{{ route('ventas.index') }}"
                    title="Ventas">
                    <i class="bi bi-cart-check"></i><span class="link-label ms-2">Ventas</span>
                </a>
                @if (Route::has('notas-credito-venta.index'))
                    <a class="nav-link {{ request()->is('notas-credito-venta*') ? 'active' : '' }}"
                        href="{{ route('notas-credito-venta.index') }}" title="Notas de Crédito (Ventas)">
                        <i class="bi bi-arrow-return-left"></i><span class="link-label ms-2">N. Créd. Ventas</span>
                    </a>
                @endif
            @endif
            @if (Route::has('cajas.index'))
                <a class="nav-link {{ request()->is('cajas*') ? 'active' : '' }}" href="{{ route('cajas.index') }}"
                    title="Cajas">
                    <i class="bi bi-cash-stack"></i><span class="link-label ms-2">Cajas</span>
                </a>
            @endif
            @if (Route::has('cuentas-por-cobrar.index'))
                <a class="nav-link {{ request()->is('cuentas-por-cobrar*') ? 'active' : '' }}"
                    href="{{ route('cuentas-por-cobrar.index') }}" title="Cuentas por Cobrar">
                    <i class="bi bi-cash"></i><span class="link-label ms-2">Cuentas por Cobrar</span>
                </a>
            @endif
            @if (Route::has('cuentas-por-pagar.index'))
                <a class="nav-link {{ request()->is('cuentas-por-pagar*') ? 'active' : '' }}"
                    href="{{ route('cuentas-por-pagar.index') }}" title="Cuentas por Pagar">
                    <i class="bi bi-cash-stack"></i><span class="link-label ms-2">Cuentas por Pagar</span>
                </a>
            @endif

            @can('gestionar-configuracion')
                <div class="nav-header">Configuración</div>
                @if (Route::has('empresas.index'))
                    <a class="nav-link {{ request()->is('empresas*') ? 'active' : '' }}"
                        href="{{ route('empresas.index') }}" title="Empresas">
                        <i class="bi bi-building-gear"></i><span class="link-label ms-2">Empresas</span>
                    </a>
                @endif
                @if (Route::has('sucursales.index'))
                    <a class="nav-link {{ request()->is('sucursales*') ? 'active' : '' }}"
                        href="{{ route('sucursales.index') }}" title="Sucursales">
                        <i class="bi bi-geo-alt"></i><span class="link-label ms-2">Sucursales</span>
                    </a>
                @endif
                @if (Route::has('impuestos.index'))
                    <a class="nav-link {{ request()->is('impuestos*') ? 'active' : '' }}"
                        href="{{ route('impuestos.index') }}" title="Impuestos">
                        <i class="bi bi-percent"></i><span class="link-label ms-2">Impuestos</span>
                    </a>
                @endif
                @if (Route::has('monedas.index'))
                    <a class="nav-link {{ request()->is('monedas*') ? 'active' : '' }}"
                        href="{{ route('monedas.index') }}" title="Monedas">
                        <i class="bi bi-currency-exchange"></i><span class="link-label ms-2">Monedas</span>
                    </a>
                @endif
            @endcan

            <div class="mt-auto">
                <a class="nav-link" href="{{ route('logout') }}" title="Salir"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="bi bi-box-arrow-left"></i><span class="link-label ms-2">Salir
                        ({{ Auth::user()->name ?? '' }})</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </nav>

        <div class="main-content">
            <div class="container-fluid py-4 px-4">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const sidebar = document.getElementById('sidebar');
            const btn = document.getElementById('btn-toggle-sidebar');
            const guardado = localStorage.getItem('sidebarCollapsed') === '1';
            if (guardado) sidebar.classList.add('collapsed');

            btn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
            });
        })();
    </script>
</body>

</html>
