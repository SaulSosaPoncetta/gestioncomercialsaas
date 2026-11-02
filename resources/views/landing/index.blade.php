<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestión Comercial — Ventas, stock, compras y caja en un solo sistema | MiGestión</title>
    <meta name="description" content="El sistema de gestión para tu negocio: ventas y facturación, stock por almacén, compras, caja y multi-sucursal. Empezá en minutos, sin instalar nada.">
    <meta property="og:title" content="Gestión Comercial — MiGestión">
    <meta property="og:description" content="Ventas, stock, compras y caja en un solo sistema, para negocios de Argentina.">
    <meta property="og:type" content="website">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* ============================================================
           Paleta Gestión Comercial (verde) — cambiar acá para modificar todo
           ============================================================ */
        :root {
            --gc-verde:       #14532d;
            --gc-verde-claro: #2e7d4f;
            --gc-profundo:    #0f2e1a;
            --gc-menta:       #86efac;
            --gc-suave:       #e6f4ea;
            --gc-fondo:       #f4faf6;
            --gc-pizarra:     #1e293b;
            --gc-gris:        #64748b;
            --gc-borde:       #e2e8f0;
        }

        html { scroll-behavior: smooth; }
        body.landing { background: #fff; color: var(--gc-pizarra); font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; }
        .landing section[id] { scroll-margin-top: 70px; }
        .landing a:focus-visible, .landing button:focus-visible, .landing input:focus-visible {
            outline: 3px solid var(--gc-verde-claro); outline-offset: 2px;
        }

        /* Bootstrap en verde */
        .landing .text-primary { color: var(--gc-verde) !important; }
        .landing .btn-primary { --bs-btn-bg: var(--gc-verde); --bs-btn-border-color: var(--gc-verde); --bs-btn-hover-bg: var(--gc-profundo); --bs-btn-hover-border-color: var(--gc-profundo); --bs-btn-active-bg: var(--gc-profundo); --bs-btn-active-border-color: var(--gc-profundo); --bs-btn-disabled-bg: var(--gc-verde); --bs-btn-disabled-border-color: var(--gc-verde); }
        .landing .btn-outline-primary { --bs-btn-color: var(--gc-verde); --bs-btn-border-color: var(--gc-verde); --bs-btn-hover-bg: var(--gc-verde); --bs-btn-hover-border-color: var(--gc-verde); --bs-btn-active-bg: var(--gc-verde); --bs-btn-active-border-color: var(--gc-verde); }
        .landing .btn-menta { background: var(--gc-menta); border-color: var(--gc-menta); color: var(--gc-profundo); }
        .landing .btn-menta:hover { background: #a7f3c4; border-color: #a7f3c4; color: var(--gc-profundo); }
        .landing .accordion-button:not(.collapsed) { background: var(--gc-suave); color: var(--gc-verde); box-shadow: none; }
        .landing .accordion-button:focus { border-color: var(--gc-verde-claro); box-shadow: 0 0 0 .2rem rgba(46,125,79,.25); }
        .landing .form-control:focus { border-color: var(--gc-verde-claro); box-shadow: 0 0 0 .2rem rgba(46,125,79,.2); }

        /* Barra superior */
        .landing-nav { background-color: rgba(255,255,255,.96); backdrop-filter: blur(6px); box-shadow: 0 1px 0 rgba(0,0,0,.06); }
        .landing-nav .navbar-brand { color: var(--gc-pizarra); }
        .landing-nav .nav-link { color: var(--gc-pizarra); font-weight: 500; }
        .landing-nav .nav-link:hover { color: var(--gc-verde); }

        /* Hero */
        .landing-hero {
            background:
                radial-gradient(circle at 85% 15%, rgba(134,239,172,.20), transparent 40%),
                linear-gradient(150deg, var(--gc-profundo) 0%, var(--gc-verde) 55%, #0f4a26 100%);
            color: #fff; padding: 5rem 0 6rem;
        }
        .landing-hero .lead { color: rgba(255,255,255,.85); }
        .text-resaltado { color: var(--gc-menta); }

        .landing-chip { display: inline-block; padding: .3rem .8rem; border-radius: 999px; background-color: rgba(255,255,255,.15); font-size: .85rem; font-weight: 600; }
        .landing-chip.landing-chip-claro { background-color: rgba(20,83,45,.1); color: var(--gc-verde); }

        /* Maqueta del sistema */
        .landing-mock { background: #fff; border-radius: 1rem; box-shadow: 0 30px 60px rgba(0,0,0,.3); color: var(--gc-pizarra); overflow: hidden; transform: rotate(1.5deg); transition: transform .3s ease; }
        .landing-mock:hover { transform: rotate(0); }
        .landing-mock-barra { background: #e9ecef; padding: .5rem .75rem; display: flex; gap: .35rem; }
        .landing-mock-barra span { width: 10px; height: 10px; border-radius: 50%; background: #ced4da; }
        .landing-mock-barra span:nth-child(1) { background: #f87171; }
        .landing-mock-barra span:nth-child(2) { background: #fbbf24; }
        .landing-mock-barra span:nth-child(3) { background: #34d399; }
        .landing-mock-portada { background: linear-gradient(135deg, var(--gc-verde), var(--gc-verde-claro)); color: #fff; padding: 1.25rem; }
        .landing-mock-item { display: flex; align-items: center; gap: .75rem; padding: .65rem .5rem; border-bottom: 1px solid #e9ecef; font-size: .9rem; }
        .landing-mock-item > i { font-size: 1.4rem; }
        .landing-mock-item span { color: var(--gc-gris); font-size: .8rem; }
        .landing-mock-item:last-child { border-bottom: 0; }
        .landing-mock-nota { font-size: .75rem; color: rgba(255,255,255,.6); text-align: center; margin-top: .75rem; }

        /* Tarjetas */
        .landing-card, .landing-plan { border: 1px solid var(--gc-borde); border-radius: 1rem; padding: 2rem; background: #fff; transition: box-shadow .2s ease, transform .2s ease; }
        .landing-card:hover, .landing-plan:hover { box-shadow: 0 12px 30px rgba(0,0,0,.08); transform: translateY(-3px); }
        .landing-icono { width: 56px; height: 56px; border-radius: 14px; background: rgba(20,83,45,.1); color: var(--gc-verde); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin-bottom: 1rem; }
        .landing-gris { background: var(--gc-fondo); }
        .landing-funcion { background: #fff; border-radius: 1rem; padding: 1.5rem; border: 1px solid rgba(20,83,45,.1); }
        .landing-funcion > i { font-size: 1.8rem; color: var(--gc-verde); }

        /* Pasos */
        .landing-paso { text-align: center; padding: 1rem; }
        .landing-paso-numero { width: 52px; height: 52px; margin: 0 auto 1rem; border-radius: 50%; background: var(--gc-menta); color: var(--gc-profundo); font-weight: 800; font-size: 1.3rem; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 0 6px rgba(134,239,172,.3); }

        /* Sección oscura */
        .landing-oscuro { background: linear-gradient(160deg, var(--gc-pizarra) 0%, var(--gc-profundo) 100%); }
        .landing-lista li { display: flex; gap: .75rem; margin-bottom: .75rem; }
        .landing-lista li i { color: var(--gc-menta); font-size: 1.2rem; }
        .landing-dato { background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12); border-radius: 1rem; padding: 1.5rem; }
        .landing-dato i { font-size: 1.8rem; color: var(--gc-menta); display: block; margin-bottom: .5rem; }

        /* Planes */
        .landing-plan { display: flex; flex-direction: column; position: relative; }
        .landing-plan.landing-plan-destacado { border: 2px solid var(--gc-verde); box-shadow: 0 16px 40px rgba(20,83,45,.15); }
        .landing-plan-cinta { position: absolute; top: -14px; right: 1.5rem; background: var(--gc-menta); color: var(--gc-profundo); font-size: .8rem; font-weight: 700; padding: .3rem .8rem; border-radius: 999px; }
        .landing-precio { font-size: 2.2rem; font-weight: 800; color: var(--gc-pizarra); }
        .landing-precio span { font-size: 1rem; font-weight: 500; color: var(--gc-gris); }
        .landing-incluye li { margin-bottom: .5rem; }
        .landing-incluye li i { color: var(--gc-verde); margin-right: .5rem; font-weight: 700; }

        /* Llamado final y pie */
        .landing-cta { background: linear-gradient(135deg, var(--gc-verde), var(--gc-profundo)); }
        .landing-pie { background: var(--gc-pizarra); color: rgba(255,255,255,.75); }

        /* Aviso flotante (éxito / error de activación) */
        .landing-aviso { z-index: 9999; min-width: min(400px, 92vw); }

        @media (max-width: 991.98px) {
            .landing-hero { padding: 3rem 0 4rem; }
            .landing-mock { transform: none; }
        }
        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            .landing-mock, .landing-card, .landing-plan { transition: none; }
            .landing-mock { transform: none; }
        }
    </style>
</head>
<body class="landing">

{{-- ===================== Barra superior ===================== --}}
<nav class="navbar navbar-expand-lg landing-nav sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#inicio">
            <i class="bi bi-briefcase-fill me-1" style="color: var(--gc-verde-claro)"></i> Gestión Comercial
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuLanding" aria-label="Abrir menú">
            <i class="bi bi-list fs-2"></i>
        </button>
        <div class="collapse navbar-collapse" id="menuLanding">
            <ul class="navbar-nav mx-auto gap-lg-2">
                <li class="nav-item"><a class="nav-link" href="#funciones">Funciones</a></li>
                <li class="nav-item"><a class="nav-link" href="#como-funciona">Cómo funciona</a></li>
                <li class="nav-item"><a class="nav-link" href="#sucursales">Sucursales</a></li>
                <li class="nav-item"><a class="nav-link" href="#planes">Planes</a></li>
                <li class="nav-item"><a class="nav-link" href="#preguntas">Preguntas</a></li>
            </ul>
            <div class="d-flex gap-2 mt-2 mt-lg-0">
                <a href="{{ route('login') }}" class="btn btn-outline-primary">Ingresar</a>
                <a href="#planes" class="btn btn-primary">Empezar</a>
            </div>
        </div>
    </div>
</nav>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5 landing-aviso" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-5 landing-aviso" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

{{-- ===================== Hero ===================== --}}
<header id="inicio" class="landing-hero">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="landing-chip mb-3"><i class="bi bi-stars me-1"></i> Hecho en Argentina, para el comercio argentino</span>
                <h1 class="display-5 fw-bold mb-3">Tu mostrador, tu stock y tu caja, <span class="text-resaltado">cerrando juntos</span>.</h1>
                <p class="lead mb-4">
                    Registrá ventas y facturas, controlá el stock de cada almacén, cargá tus compras y cerrá la caja
                    del día sin pasar nada a una planilla. Sin instalar nada: funciona desde el navegador.
                </p>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <a href="#planes" class="btn btn-menta btn-lg fw-semibold">
                        <i class="bi bi-rocket-takeoff me-1"></i> Elegir mi plan
                    </a>
                    <a href="#funciones" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-grid me-1"></i> Ver funciones
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-4 small opacity-75">
                    <span><i class="bi bi-check-circle me-1"></i>Varias sucursales y almacenes</span>
                    <span><i class="bi bi-check-circle me-1"></i>Usuarios con permisos</span>
                    <span><i class="bi bi-check-circle me-1"></i>Soporte incluido</span>
                </div>
            </div>

            {{-- Vista previa del sistema (maqueta en HTML) --}}
            <div class="col-lg-6">
                <div class="landing-mock" aria-hidden="true">
                    <div class="landing-mock-barra"><span></span><span></span><span></span></div>
                    <div class="landing-mock-portada">
                        <div class="small opacity-75">Sucursal Centro · {{ now('America/Argentina/Buenos_Aires')->format('d/m/Y') }}</div>
                        <div class="h5 fw-bold mb-0">Caja abierta — Turno mañana</div>
                    </div>
                    <div class="p-3">
                        <div class="landing-mock-item"><i class="bi bi-cart-check text-success"></i><div><strong>Venta + factura</strong><br><span>Cliente mostrador · 3 productos</span></div><span class="badge bg-success ms-auto">Cobrada</span></div>
                        <div class="landing-mock-item"><i class="bi bi-box-seam text-warning"></i><div><strong>Stock bajo</strong><br><span>2 productos por debajo del mínimo</span></div><span class="badge bg-warning text-dark ms-auto">Reponer</span></div>
                        <div class="landing-mock-item"><i class="bi bi-truck" style="color: var(--gc-verde)"></i><div><strong>Compra recibida</strong><br><span>Ingresó al almacén central</span></div></div>
                        <div class="landing-mock-item"><i class="bi bi-cash-stack" style="color: var(--gc-verde)"></i><div><strong>Cierre de caja</strong><br><span>Efectivo y otros medios, por turno</span></div></div>
                    </div>
                </div>
                <p class="landing-mock-nota">Vista de ejemplo del sistema</p>
            </div>
        </div>
    </div>
</header>

{{-- ===================== Para quién ===================== --}}
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            @foreach ([
                ['bi-shop', 'Para comercios', 'Local, kiosco, ferretería, distribuidora: ventas, precios y stock ordenados, con la caja cerrando todos los días.'],
                ['bi-building', 'Para empresas con sedes', 'Sucursales, almacenes y transferencias entre ellos. Cada usuario ve solo lo que le corresponde.'],
                ['bi-file-earmark-spreadsheet', 'Para dejar las planillas', 'Reemplazá los Excel sueltos por un solo sistema donde todo queda registrado y se puede consultar.'],
            ] as [$icono, $titulo, $texto])
                <div class="col-md-4">
                    <div class="landing-card h-100">
                        <div class="landing-icono"><i class="bi {{ $icono }}"></i></div>
                        <h3 class="h5 fw-bold">{{ $titulo }}</h3>
                        <p class="text-muted mb-0">{{ $texto }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== Funciones ===================== --}}
<section id="funciones" class="py-5 landing-gris">
    <div class="container">
        <div class="text-center mb-5">
            <span class="landing-chip landing-chip-claro">Funciones</span>
            <h2 class="fw-bold mt-2">Todo lo que tu negocio necesita, en un solo lugar</h2>
            <p class="text-muted">Pensado para el día a día del mostrador, no para expertos en sistemas.</p>
        </div>
        <div class="row g-4">
            @foreach ([
                ['bi-cart-check', 'Ventas y facturación', 'Registrá ventas, emití facturas y cobrá en segundos. Con detalle de cada producto vendido.'],
                ['bi-arrow-return-left', 'Notas de crédito', 'Devoluciones y ajustes de ventas y de compras, con su comprobante y el movimiento de stock.'],
                ['bi-box-seam', 'Stock por almacén', 'Existencias por almacén, con cada movimiento de inventario registrado.'],
                ['bi-arrow-left-right', 'Transferencias', 'Pasá mercadería de un almacén o sucursal a otro y mantené el stock al día en los dos.'],
                ['bi-truck', 'Compras y proveedores', 'Cargá compras, ingresá la mercadería y llevá el historial de cada proveedor.'],
                ['bi-cash-stack', 'Caja y caja chica', 'Apertura y cierre de caja, movimientos del día y control de la caja chica.'],
                ['bi-wallet2', 'Cuentas por cobrar y pagar', 'Seguí lo que te deben tus clientes y lo que debés a tus proveedores.'],
                ['bi-tags', 'Precios, impuestos y monedas', 'Listas de precios, impuestos y monedas configurables, con categorías y marcas para ordenar tu catálogo.'],
            ] as [$icono, $titulo, $texto])
                <div class="col-md-6 col-lg-3">
                    <div class="landing-funcion h-100">
                        <i class="bi {{ $icono }}"></i>
                        <h3 class="h6 fw-bold mt-3">{{ $titulo }}</h3>
                        <p class="small text-muted mb-0">{{ $texto }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== Cómo funciona ===================== --}}
<section id="como-funciona" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="landing-chip landing-chip-claro">Cómo funciona</span>
            <h2 class="fw-bold mt-2">En marcha en pocos minutos</h2>
        </div>
        <div class="row g-4">
            @foreach ([
                ['Elegí tu plan', 'Mirá los planes más abajo y elegí el que se adapte a tu negocio. Todos incluyen el sistema completo.'],
                ['Creá tu cuenta', 'Cargá la razón social, el CUIT y tus datos de acceso. Es un formulario corto.'],
                ['Activá tu cuenta', 'Te enviamos un mail con un enlace de activación. Un clic y tu empresa queda habilitada.'],
                ['Cargá tu catálogo y vendé', 'Dá de alta tus productos, precios y almacenes, abrí la caja y empezá a registrar ventas.'],
            ] as $i => [$titulo, $texto])
                <div class="col-md-6 col-lg-3">
                    <div class="landing-paso">
                        <div class="landing-paso-numero">{{ $i + 1 }}</div>
                        <h3 class="h6 fw-bold">{{ $titulo }}</h3>
                        <p class="small text-muted mb-0">{{ $texto }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== Sucursales ===================== --}}
<section id="sucursales" class="py-5 landing-oscuro text-white">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="landing-chip">Para empresas con varias sedes</span>
                <h2 class="fw-bold mt-2 mb-3">Todas tus sucursales en la misma cuenta, cada una en su lugar</h2>
                <p class="opacity-75 mb-4">Administrá sucursales, almacenes y cajas desde un solo sistema. Cada empresa trabaja con sus propios datos, separados de los de cualquier otra.</p>
                <ul class="list-unstyled landing-lista">
                    <li><i class="bi bi-building"></i> Varias sucursales y almacenes por empresa</li>
                    <li><i class="bi bi-arrow-left-right"></i> Transferencias de mercadería entre sedes</li>
                    <li><i class="bi bi-shield-lock"></i> Roles y permisos para cada usuario</li>
                    <li><i class="bi bi-cash-stack"></i> Cajas independientes con apertura y cierre</li>
                    <li><i class="bi bi-database-lock"></i> Datos de tu empresa separados de los demás</li>
                </ul>
                <a href="#planes" class="btn btn-menta btn-lg fw-semibold mt-2">Ver planes</a>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    @foreach ([['bi-building', 'Sucursales', 'las que necesites'], ['bi-boxes', 'Almacenes', 'con stock propio'], ['bi-cash-stack', 'Cajas', 'apertura y cierre'], ['bi-people', 'Usuarios', 'con permisos por rol']] as [$i, $t, $s])
                        <div class="col-6">
                            <div class="landing-dato">
                                <i class="bi {{ $i }}"></i>
                                <div class="fw-bold">{{ $t }}</div>
                                <div class="small opacity-75">{{ $s }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===================== Planes ===================== --}}
<section id="planes" class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <span class="landing-chip landing-chip-claro">Planes</span>
            <h2 class="fw-bold mt-2">Elegí el plan que mejor se adapta</h2>
            <p class="text-muted">Todos los planes incluyen acceso completo al sistema.</p>
        </div>

        @if ($planes->isEmpty())
            <div class="alert alert-info text-center">No hay planes disponibles en este momento.</div>
        @else
            <div class="row g-4 justify-content-center">
                @foreach ($planes as $index => $plan)
                    @php $destacado = $index === 1 || $planes->count() === 1; @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="landing-plan {{ $destacado ? 'landing-plan-destacado' : '' }} h-100">
                            @if ($destacado)<span class="landing-plan-cinta">Más elegido</span>@endif
                            <div class="landing-icono"><i class="bi bi-briefcase"></i></div>
                            <h3 class="h4 fw-bold">{{ $plan->nombre }}</h3>
                            @if ($plan->descripcion)
                                <p class="text-muted">{{ $plan->descripcion }}</p>
                            @endif
                            <div class="landing-precio">${{ number_format($plan->precio, 0, ',', '.') }} <span>/ mes</span></div>
                            <ul class="list-unstyled landing-incluye my-4">
                                <li><i class="bi bi-check2"></i> Acceso completo al sistema</li>
                                <li><i class="bi bi-check2"></i> Ventas, compras y stock</li>
                                <li><i class="bi bi-check2"></i> Caja y multi-sucursal</li>
                                <li><i class="bi bi-check2"></i> Soporte incluido</li>
                                <li><i class="bi bi-check2"></i> Actualizaciones sin costo</li>
                            </ul>
                            <button type="button"
                                    class="btn {{ $destacado ? 'btn-primary' : 'btn-outline-primary' }} btn-lg w-100 mt-auto js-elegir-plan"
                                    data-id="{{ $plan->id }}"
                                    data-nombre="{{ $plan->nombre }}"
                                    data-precio="{{ (float) $plan->precio }}">
                                Elegir este plan
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ===================== Preguntas ===================== --}}
<section id="preguntas" class="py-5 landing-gris">
    <div class="container" style="max-width: 820px">
        <div class="text-center mb-4">
            <span class="landing-chip landing-chip-claro">Preguntas frecuentes</span>
            <h2 class="fw-bold mt-2">¿Tenés dudas?</h2>
        </div>
        <div class="accordion" id="faq">
            @foreach ([
                ['¿Necesito instalar algo?', 'No. Funciona desde el navegador de la compu, la tablet o el celular, con tu usuario y contraseña.'],
                ['¿Cómo empiezo?', 'Elegís un plan, completás el formulario con la razón social, el CUIT y tus datos, y activás la cuenta con el enlace que te llega por mail. Después podés iniciar sesión.'],
                ['¿Puedo manejar más de una sucursal?', 'Sí. Podés tener varias sucursales y almacenes, con transferencias de mercadería entre ellos y cajas independientes.'],
                ['¿Pueden entrar varios usuarios a la vez?', 'Sí. Cada persona de tu equipo tiene su usuario, con permisos según su rol: quién vende, quién carga compras, quién ve la caja o la configuración.'],
                ['¿Mis datos se mezclan con los de otras empresas?', 'No. Cada empresa trabaja solo con su propia información, separada de las demás.'],
                ['¿Qué incluye el plan?', 'Todos los planes incluyen el sistema completo, el soporte y las actualizaciones. Lo que cambia entre planes lo ves en cada tarjeta de la sección Planes.'],
            ] as $i => [$pregunta, $respuesta])
                <div class="accordion-item">
                    <h3 class="accordion-header">
                        <button class="accordion-button {{ $i ? 'collapsed' : '' }} fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $i }}">
                            {{ $pregunta }}
                        </button>
                    </h3>
                    <div id="faq{{ $i }}" class="accordion-collapse collapse {{ $i ? '' : 'show' }}" data-bs-parent="#faq">
                        <div class="accordion-body text-muted">{{ $respuesta }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ===================== Llamado final ===================== --}}
<section class="py-5 landing-cta text-white text-center">
    <div class="container">
        <h2 class="fw-bold mb-3">Tu próxima venta puede quedar registrada hoy</h2>
        <p class="opacity-75 mb-4">Creá tu cuenta y empezá a ordenar tu negocio.</p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="#planes" class="btn btn-menta btn-lg fw-semibold">Ver planes</a>
            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Iniciar sesión</a>
        </div>
    </div>
</section>

{{-- ===================== Pie ===================== --}}
<footer class="landing-pie py-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3 small">
        <span><i class="bi bi-briefcase-fill me-1" style="color: var(--gc-menta)"></i><strong>Gestión Comercial</strong> · un sistema de
            <a href="https://www.migestion.com.ar" class="text-reset fw-semibold">MiGestión</a> © {{ date('Y') }}</span>
        <a href="{{ route('login') }}" class="text-reset"><i class="bi bi-box-arrow-in-right me-1"></i>Iniciar sesión</a>
    </div>
</footer>

{{-- ===================== Modal de registro ===================== --}}
<div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroTitulo" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-header border-0 pb-0">
                <div>
                    <h4 class="fw-bold mb-1" id="modalRegistroTitulo">Crear cuenta</h4>
                    <p class="text-muted mb-0" id="modalPlanNombre"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <form id="formRegistro">
                    <input type="hidden" id="reg_plan_id" name="plan_id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="reg_razon_social">Razón social <span class="text-danger">*</span></label>
                            <input type="text" name="razon_social" id="reg_razon_social" class="form-control form-control-lg" placeholder="Nombre de tu empresa" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="reg_cuit">CUIT / Identificación fiscal <span class="text-danger">*</span></label>
                            <input type="text" name="identificacion_fiscal" id="reg_cuit" class="form-control form-control-lg" placeholder="Ej: 20-12345678-9" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="reg_nombre_admin">Tu nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre_admin" id="reg_nombre_admin" class="form-control form-control-lg" placeholder="Nombre completo" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" for="reg_email">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="reg_email" class="form-control form-control-lg" placeholder="tu@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="reg_pass">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="reg_pass" class="form-control form-control-lg" placeholder="Mínimo 8 caracteres" required minlength="8">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="reg_pass2">Confirmar contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="reg_pass2" class="form-control form-control-lg" placeholder="Repetí la contraseña" required minlength="8">
                        </div>
                        <div class="col-12">
                            <div id="planResumen" class="p-3 rounded-3" style="background: var(--gc-suave); border: 1px solid #b8dfc4"></div>
                        </div>
                        <div id="regError" class="col-12 d-none">
                            <div class="alert alert-danger mb-0" id="regErrorMsg"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary rounded-pill px-5" id="btnRegistrar">
                    <i class="bi bi-check-circle me-1"></i>Registrarme
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ===================== Modal de éxito ===================== --}}
<div class="modal fade" id="modalExito" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4">
            <div class="modal-body text-center p-5">
                <div class="mb-3" style="font-size:64px">✅</div>
                <h4 class="fw-bold mb-2">Registro exitoso</h4>
                <p class="text-muted mb-4" id="exitoMensaje"></p>
                <a href="{{ route('landing.index') }}" class="btn btn-primary rounded-pill px-5">Volver al inicio</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const pesos = v => '$' + Number(v).toLocaleString('es-AR');
    const modalRegistro = new bootstrap.Modal(document.getElementById('modalRegistro'));
    const modalExito = new bootstrap.Modal(document.getElementById('modalExito'));
    const form = document.getElementById('formRegistro');
    const errorBox = document.getElementById('regError');
    const errorMsg = document.getElementById('regErrorMsg');
    const btnRegistrar = document.getElementById('btnRegistrar');

    // Elegir plan: carga el resumen y abre el formulario
    document.querySelectorAll('.js-elegir-plan').forEach(btn => {
        btn.addEventListener('click', () => {
            const nombre = btn.dataset.nombre;
            const precio = parseFloat(btn.dataset.precio);

            document.getElementById('reg_plan_id').value = btn.dataset.id;

            const titulo = document.getElementById('modalPlanNombre');
            titulo.textContent = '';
            const badge = document.createElement('span');
            badge.className = 'badge bg-primary';
            badge.textContent = nombre;
            titulo.append(badge, ' — ' + pesos(precio) + ' / mensual');

            const resumen = document.getElementById('planResumen');
            resumen.innerHTML = '';
            const fila = document.createElement('div');
            fila.className = 'd-flex justify-content-between align-items-center';
            const izq = document.createElement('div');
            const t = document.createElement('div');
            t.className = 'fw-semibold';
            t.textContent = 'Plan ' + nombre;
            const p = document.createElement('div');
            p.className = 'text-muted small';
            p.textContent = 'Mensual';
            izq.append(t, p);
            const der = document.createElement('div');
            der.className = 'fw-bold text-primary fs-5';
            der.textContent = pesos(precio);
            fila.append(izq, der);
            resumen.append(fila);

            errorBox.classList.add('d-none');
            modalRegistro.show();
        });
    });

    // Enviar el registro
    btnRegistrar.addEventListener('click', () => {
        errorBox.classList.add('d-none');
        btnRegistrar.disabled = true;

        fetch(@json(route('landing.registrar')), {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json' },
            body: new FormData(form),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                modalRegistro.hide();
                document.getElementById('exitoMensaje').textContent = data.message;
                setTimeout(() => modalExito.show(), 400);
                form.reset();
            } else {
                errorMsg.textContent = data.message || 'Error al registrar. Intentá de nuevo.';
                errorBox.classList.remove('d-none');
            }
        })
        .catch(() => {
            errorMsg.textContent = 'Error de conexión. Intentá de nuevo.';
            errorBox.classList.remove('d-none');
        })
        .finally(() => { btnRegistrar.disabled = false; });
    });
});
</script>
</body>
</html>
