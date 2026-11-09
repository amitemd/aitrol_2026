@extends('layouts.app-template-h')

@section('content')
    @php
        $totalProductos = $medicamento->count();
        $totalExistencias = $medicamento->sum('existencia');
    @endphp

    <style>
        .pharmacy-page {
            --pharmacy-navy: #073b66;
            --pharmacy-blue: #0877be;
            --pharmacy-green: #158b72;
            --pharmacy-ink: #17324d;
            --pharmacy-muted: #65788b;
            --pharmacy-line: #dbe7ef;
            --pharmacy-soft: #f4f8fb;
            padding: .5rem 0 2rem;
            color: var(--pharmacy-ink);
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
        }

        .pharmacy-page,
        .pharmacy-page * {
            box-sizing: border-box;
        }

        .pharmacy-breadcrumb {
            display: flex;
            align-items: center;
            gap: .55rem;
            margin-bottom: 1rem;
            color: var(--pharmacy-muted);
            font-size: .82rem;
        }

        .pharmacy-breadcrumb a {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: var(--pharmacy-blue);
            font-weight: 600;
            text-decoration: none;
        }

        .pharmacy-breadcrumb a:hover {
            color: var(--pharmacy-navy);
        }

        .pharmacy-heading {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            padding: 1.5rem 1.7rem;
            overflow: hidden;
            border-radius: 18px;
            background: linear-gradient(115deg, #063b67 0%, #0877ad 62%, #158b72 100%);
            box-shadow: 0 14px 34px rgba(7, 59, 102, .14);
            color: #fff;
        }

        .pharmacy-heading::after {
            content: '';
            position: absolute;
            width: 210px;
            height: 210px;
            right: -70px;
            top: -115px;
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 50%;
        }

        .pharmacy-heading-main {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .pharmacy-heading-icon {
            display: grid;
            width: 58px;
            height: 58px;
            flex: 0 0 58px;
            place-items: center;
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 16px;
            background: rgba(255, 255, 255, .14);
            color: #fff;
            font-size: 1.55rem;
        }

        .pharmacy-heading-label {
            display: block;
            margin-bottom: .28rem;
            color: #c8efe9;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .pharmacy-heading h1 {
            margin: 0;
            color: #fff;
            font-size: 1.65rem;
            font-weight: 700;
            line-height: 1.25;
        }

        .pharmacy-heading p {
            max-width: 720px;
            margin: .35rem 0 0;
            color: rgba(255, 255, 255, .82);
            font-size: .86rem;
        }

        .pharmacy-heading-stats {
            position: relative;
            z-index: 1;
            display: flex;
            gap: .65rem;
        }

        .pharmacy-stat {
            min-width: 116px;
            padding: .72rem .9rem;
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 13px;
            background: rgba(255, 255, 255, .12);
            text-align: center;
        }

        .pharmacy-stat strong {
            display: block;
            color: #fff;
            font-size: 1.35rem;
            line-height: 1;
        }

        .pharmacy-stat span {
            display: block;
            margin-top: .35rem;
            color: #d0efeb;
            font-size: .65rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .pharmacy-workspace {
            margin-top: 1.25rem;
            overflow: hidden;
            border: 1px solid var(--pharmacy-line);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(18, 69, 116, .07);
        }

        .pharmacy-workspace-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--pharmacy-line);
        }

        .pharmacy-workspace-title {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .pharmacy-workspace-title-icon {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 10px;
            background: #e9f7f3;
            color: var(--pharmacy-green);
        }

        .pharmacy-workspace-header h2 {
            margin: 0;
            color: var(--pharmacy-ink);
            font-size: 1.05rem;
            font-weight: 700;
        }

        .pharmacy-workspace-header p {
            margin: .15rem 0 0;
            color: var(--pharmacy-muted);
            font-size: .75rem;
        }

        .pharmacy-card-tool {
            display: grid;
            width: 34px;
            height: 34px;
            padding: 0 !important;
            place-items: center;
            border: 1px solid var(--pharmacy-line) !important;
            border-radius: 9px !important;
            background: #fff !important;
            color: var(--pharmacy-muted) !important;
        }

        .pharmacy-filter {
            padding: 1.35rem 1.5rem;
            border-bottom: 1px solid var(--pharmacy-line);
            background: var(--pharmacy-soft);
        }

        .pharmacy-filter-grid {
            display: grid;
            grid-template-columns: 1.15fr .9fr .9fr 1fr;
            gap: 1rem;
        }

        .pharmacy-filter-group {
            min-width: 0;
            margin: 0;
        }

        .pharmacy-page .pharmacy-filter-group label {
            display: block;
            margin: 0 0 .45rem;
            color: var(--pharmacy-ink) !important;
            font-size: .75rem;
            font-weight: 700;
        }

        .pharmacy-input-wrap {
            position: relative;
        }

        .pharmacy-input-wrap > i {
            position: absolute;
            z-index: 2;
            left: .85rem;
            top: 50%;
            color: #8ca1b4;
            font-size: .88rem;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .pharmacy-page .pharmacy-input-wrap .form-control {
            width: 100%;
            height: 42px;
            padding: .58rem .8rem .58rem 2.45rem;
            border: 1px solid #ccdae5;
            border-radius: 10px;
            background: #fff;
            color: var(--pharmacy-ink) !important;
            font-size: .84rem !important;
            box-shadow: none;
        }

        .pharmacy-page .pharmacy-input-wrap .form-control:focus {
            border-color: var(--pharmacy-green);
            box-shadow: 0 0 0 3px rgba(21, 139, 114, .12);
        }

        .pharmacy-page .pharmacy-input-wrap select.form-control {
            appearance: auto;
        }

        .pharmacy-filter-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-top: 1rem;
        }

        .pharmacy-filter-actions-left {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
        }

        .pharmacy-page .pharmacy-primary-btn,
        .pharmacy-page .pharmacy-secondary-btn {
            display: inline-flex;
            min-height: 40px;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            padding: .62rem 1rem;
            border-radius: 10px;
            font-size: .8rem !important;
            font-weight: 700;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .pharmacy-page .pharmacy-primary-btn {
            border: 1px solid var(--pharmacy-green);
            background: var(--pharmacy-green);
            color: #fff;
        }

        .pharmacy-page .pharmacy-secondary-btn {
            border: 1px solid #b8d8e7;
            background: #fff;
            color: var(--pharmacy-navy);
        }

        .pharmacy-page .pharmacy-primary-btn:hover,
        .pharmacy-page .pharmacy-primary-btn:focus {
            color: #fff;
            background: #10735e;
            box-shadow: 0 7px 16px rgba(21, 139, 114, .2);
            transform: translateY(-1px);
        }

        .pharmacy-page .pharmacy-secondary-btn:hover,
        .pharmacy-page .pharmacy-secondary-btn:focus {
            color: var(--pharmacy-navy);
            border-color: var(--pharmacy-blue);
            background: #edf7fb;
        }

        .pharmacy-filter-date {
            color: var(--pharmacy-muted);
            font-size: .73rem;
        }

        .pharmacy-table-section {
            padding: 1.35rem 1.5rem 1.5rem;
        }

        .pharmacy-table-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .9rem;
        }

        .pharmacy-table-heading h3 {
            margin: 0;
            color: var(--pharmacy-ink);
            font-size: .95rem;
            font-weight: 700;
        }

        .pharmacy-table-heading span {
            padding: .35rem .62rem;
            border-radius: 999px;
            background: #e9f7f3;
            color: var(--pharmacy-green);
            font-size: .68rem;
            font-weight: 700;
        }

        .pharmacy-page .pharmacy-table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            color: var(--pharmacy-ink) !important;
            font-size: .8rem !important;
        }

        .pharmacy-page .pharmacy-table thead th {
            padding: .78rem .85rem;
            border-top: 1px solid var(--pharmacy-line);
            border-bottom: 1px solid var(--pharmacy-line);
            background: #f5f8fb;
            color: #40576d !important;
            font-size: .68rem !important;
            font-weight: 700 !important;
            letter-spacing: .035em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .pharmacy-page .pharmacy-table thead th:first-child {
            border-left: 1px solid var(--pharmacy-line);
            border-radius: 10px 0 0 10px;
        }

        .pharmacy-page .pharmacy-table thead th:last-child {
            border-right: 1px solid var(--pharmacy-line);
            border-radius: 0 10px 10px 0;
        }

        .pharmacy-page .pharmacy-table tbody td {
            padding: .95rem .85rem;
            border-top: 0;
            border-bottom: 1px solid #edf2f6;
            color: var(--pharmacy-ink) !important;
            font-size: .78rem !important;
            font-weight: 500;
            vertical-align: middle;
        }

        .pharmacy-page .pharmacy-table tbody tr:hover td {
            background: #f8fbfd;
        }

        .pharmacy-stock {
            display: inline-flex;
            min-width: 86px;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            padding: .34rem .55rem;
            border-radius: 999px;
            font-size: .68rem;
            font-weight: 700;
        }

        .pharmacy-stock-available {
            background: #e7f7f0;
            color: #147154;
        }

        .pharmacy-stock-empty {
            background: #fff0f0;
            color: #c13f3f;
        }

        .pharmacy-status {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: var(--pharmacy-green);
            font-size: .72rem;
            font-weight: 700;
        }

        .pharmacy-status::before {
            content: '';
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #25a879;
            box-shadow: 0 0 0 3px rgba(37, 168, 121, .13);
        }

        .pharmacy-empty-state {
            padding: 3.5rem 1.5rem !important;
            border-bottom: 0 !important;
            text-align: center;
        }

        .pharmacy-empty-icon {
            display: inline-flex;
            width: 58px;
            height: 58px;
            margin: 0 auto .9rem;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: #e9f7f3;
            color: var(--pharmacy-green);
            line-height: 1;
        }

        .pharmacy-empty-icon > i {
            display: block;
            margin: 0;
            font-size: 1.65rem;
            line-height: 1;
        }

        .pharmacy-empty-state strong {
            display: block;
            color: var(--pharmacy-ink);
            font-size: .9rem;
        }

        .pharmacy-empty-copy {
            display: block;
            margin-top: .3rem;
            color: var(--pharmacy-muted);
            font-size: .76rem;
        }

        .dark-layout .pharmacy-workspace,
        .dark-layout .pharmacy-card-tool,
        .dark-layout .pharmacy-page .pharmacy-input-wrap .form-control {
            border-color: #3d4c5e;
            background: #283447;
        }

        .dark-layout .pharmacy-filter,
        .dark-layout .pharmacy-page .pharmacy-table thead th {
            border-color: #3d4c5e;
            background: #222d3f;
        }

        .dark-layout .pharmacy-workspace-header h2,
        .dark-layout .pharmacy-table-heading h3,
        .dark-layout .pharmacy-empty-state strong,
        .dark-layout .pharmacy-page .pharmacy-filter-group label,
        .dark-layout .pharmacy-page .pharmacy-input-wrap .form-control,
        .dark-layout .pharmacy-page .pharmacy-table tbody td {
            color: #f1f5f8 !important;
        }

        @media (max-width: 1199.98px) {
            .pharmacy-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .pharmacy-heading {
                align-items: flex-start;
                flex-direction: column;
                padding: 1.3rem;
                border-radius: 15px;
            }

            .pharmacy-heading-stats {
                width: 100%;
            }

            .pharmacy-stat {
                flex: 1;
            }

            .pharmacy-workspace {
                border-radius: 15px;
            }

            .pharmacy-filter-grid {
                grid-template-columns: 1fr;
            }

            .pharmacy-filter-actions {
                align-items: flex-start;
                flex-direction: column;
            }

            .pharmacy-filter-date {
                order: -1;
            }
        }

        @media (max-width: 479.98px) {
            .pharmacy-heading-main {
                align-items: flex-start;
            }

            .pharmacy-heading-icon {
                width: 48px;
                height: 48px;
                flex-basis: 48px;
                border-radius: 13px;
            }

            .pharmacy-heading h1 {
                font-size: 1.3rem;
            }

            .pharmacy-heading-stats {
                flex-direction: column;
            }

            .pharmacy-workspace-header,
            .pharmacy-filter,
            .pharmacy-table-section {
                padding-right: 1rem;
                padding-left: 1rem;
            }

            .pharmacy-filter-actions-left,
            .pharmacy-page .pharmacy-primary-btn,
            .pharmacy-page .pharmacy-secondary-btn {
                width: 100%;
            }
        }
    </style>

    <main class="pharmacy-page">
        <nav class="pharmacy-breadcrumb" aria-label="Navegación">
            <a href="{{ route('hospital.index') }}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Área médica
            </a>
            <i class="fa fa-angle-right" aria-hidden="true"></i>
            <span>{{ trans('farmacia.Farmacia') }}</span>
        </nav>

        @if (session('error'))
            <div class="alert alert-warning" role="alert">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                {{ session('error') }}
            </div>
        @endif

        <section class="pharmacy-heading" aria-labelledby="pharmacy-page-title">
            <div class="pharmacy-heading-main">
                <span class="pharmacy-heading-icon"><i class="fa fa-medkit" aria-hidden="true"></i></span>
                <div>
                    <span class="pharmacy-heading-label">Gestión hospitalaria</span>
                    <h1 id="pharmacy-page-title">Farmacia e inventario</h1>
                    <p>Consulta existencias de medicamentos por fecha y bodega, o inicia un nuevo proceso de facturación.</p>
                </div>
            </div>

            <div class="pharmacy-heading-stats">
                <div class="pharmacy-stat">
                    <strong>{{ $totalProductos }}</strong>
                    <span>productos</span>
                </div>
                <div class="pharmacy-stat">
                    <strong>{{ number_format($totalExistencias, 0) }}</strong>
                    <span>unidades disponibles</span>
                </div>
            </div>
        </section>

        <section class="pharmacy-workspace card card-primary">
            <header class="pharmacy-workspace-header card-header with-border">
                <div class="pharmacy-workspace-title">
                    <span class="pharmacy-workspace-title-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <div>
                        <h2>Consulta de medicamentos</h2>
                        <p>Utiliza uno o varios filtros para revisar el inventario.</p>
                    </div>
                </div>
                <div class="card-tools">
                    <button type="button" class="btn btn-card-tool pharmacy-card-tool" data-widget="collapse" aria-label="Contraer panel">
                        <i class="fa fa-minus" aria-hidden="true"></i>
                    </button>
                </div>
            </header>

            <div class="card-body p-0">
                <form id="form_buscar" method="POST" action="{{ route('farmacia.buscar_medicina') }}" class="pharmacy-filter">
                    {{ csrf_field() }}

                    <div class="pharmacy-filter-grid">
                        <div class="form-group pharmacy-filter-group">
                            <label for="producto">{{ trans('farmacia.Medicamento') }}</label>
                            <div class="pharmacy-input-wrap">
                                <i class="fa fa-medkit" aria-hidden="true"></i>
                                <input type="text" style="text-transform:uppercase" class="form-control input-sm" name="producto" id="producto" autocomplete="off" value="{{ isset($producto) ? $producto : '' }}" placeholder="Nombre del medicamento">
                            </div>
                        </div>

                        <div class="form-group pharmacy-filter-group">
                            <label for="fecha_desde">{{ trans('farmacia.FechaDesde') }}</label>
                            <div class="pharmacy-input-wrap">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                <input type="date" data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_desde" id="fecha_desde" autocomplete="off" value="{{ $fecha_desde }}">
                            </div>
                        </div>

                        <div class="form-group pharmacy-filter-group">
                            <label for="fecha_hasta">{{ trans('farmacia.FechaHasta') }}</label>
                            <div class="pharmacy-input-wrap">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                <input type="date" data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{ $fecha_hasta }}">
                            </div>
                        </div>

                        <div class="form-group pharmacy-filter-group">
                            <label for="bodega">{{ trans('farmacia.Bodega') }}</label>
                            <div class="pharmacy-input-wrap">
                                <i class="fa fa-archive" aria-hidden="true"></i>
                                <select class="form-control" id="bodega" name="bodega" old="{{ old('bodega') }}">
                                    <option value="">HOSPITAL</option>
                                    @foreach($bodegas as $b)
                                        <option value="{{ $b->id }}" @if(isset($bodega) && $bodega == $b->id) selected @endif>{{ $b->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="pharmacy-filter-actions">
                        <div class="pharmacy-filter-actions-left">
                            <button class="btn pharmacy-primary-btn" type="submit">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                Buscar medicamentos
                            </button>
                            <a href="{{ route('hospital.invoice') }}" class="btn pharmacy-secondary-btn">
                                <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                Nueva factura
                            </a>
                        </div>
                        <span class="pharmacy-filter-date">
                            <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                            Periodo: {{ $fecha_desde }} al {{ $fecha_hasta }}
                        </span>
                    </div>
                </form>

                <div class="pharmacy-table-section table-responsive">
                    <div class="pharmacy-table-heading">
                        <h3>Inventario de farmacia</h3>
                        <span>{{ $totalProductos }} registros</span>
                    </div>

                    <table class="table table-hover pharmacy-table">
                        <thead>
                            <tr>
                                <th>{{ trans('farmacia.Fecha') }}</th>
                                <th>{{ trans('farmacia.Medicamento') }}</th>
                                <th>{{ trans('farmacia.Bodega') }}</th>
                                <th>{{ trans('farmacia.ExistenciadelProducto') }}</th>
                                <th>Información</th>
                                <th>{{ trans('farmacia.Accion') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($medicamento as $m)
                                <tr>
                                    <td>{{ $m->created_at ? $m->created_at : 'Catálogo inicial' }}</td>
                                    <td>
                                        <strong>{{ $m->producto_nombre }}</strong><br>
                                        <small class="text-muted">{{ $m->codigo }} · {{ $m->producto_descripcion }}</small>
                                    </td>
                                    <td>{{ $m->bodega_nombre ? $m->bodega_nombre : 'Sin ingreso a bodega' }}</td>
                                    <td>
                                        <span class="pharmacy-stock {{ $m->existencia > 0 ? 'pharmacy-stock-available' : 'pharmacy-stock-empty' }}">
                                            <i class="fa {{ $m->existencia > 0 ? 'fa-check-circle' : 'fa-exclamation-circle' }}" aria-hidden="true"></i>
                                            {{ number_format($m->existencia, 0) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($m->id_inventario && $m->estado == 1)
                                            <span class="pharmacy-status">En farmacia</span>
                                        @else
                                            <span class="text-muted">Pendiente de ingreso</span>
                                        @endif
                                    </td>
                                    <td><span class="text-muted">—</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="pharmacy-empty-state">
                                        <span class="pharmacy-empty-icon"><i class="fa fa-medkit" aria-hidden="true"></i></span>
                                        <strong>No se encontraron medicamentos</strong>
                                        <span class="pharmacy-empty-copy">Prueba con otro periodo, medicamento o bodega.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </main>
@endsection
