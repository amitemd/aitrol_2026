@extends('layouts.app-template-h')

@section('content')
    @php
        $diasSemana = [
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
        ];
        $meses = [
            1 => 'enero',
            2 => 'febrero',
            3 => 'marzo',
            4 => 'abril',
            5 => 'mayo',
            6 => 'junio',
            7 => 'julio',
            8 => 'agosto',
            9 => 'septiembre',
            10 => 'octubre',
            11 => 'noviembre',
            12 => 'diciembre',
        ];
        $fechaActual = $diasSemana[(int) date('N')] . ', ' . date('d') . ' de ' . $meses[(int) date('n')] . ' de ' . date('Y');
        $nombreUsuario = trim((Auth::user()->nombre1 ?? '') . ' ' . (Auth::user()->apellido1 ?? ''));
        $nombreUsuario = $nombreUsuario !== '' ? $nombreUsuario : 'Profesional de salud';
    @endphp

    <style>
        .hospital-home {
            --medical-navy: #073b66;
            --medical-blue: #0877be;
            --medical-teal: #0d9488;
            --medical-ink: #17324d;
            --medical-muted: #66798d;
            --medical-line: #dbe8f1;
            --medical-surface: #ffffff;
            padding: 1.25rem 0 2rem;
            color: var(--medical-ink);
        }

        .hospital-home,
        .hospital-home * {
            box-sizing: border-box;
        }

        .hospital-hero {
            position: relative;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 2rem;
            align-items: center;
            min-height: 220px;
            padding: 2.25rem 2.5rem;
            overflow: hidden;
            border-radius: 22px;
            background: linear-gradient(120deg, #06365f 0%, #086fa8 58%, #0d9488 100%);
            box-shadow: 0 18px 45px rgba(7, 59, 102, .16);
            color: #fff;
        }

        .hospital-hero::before,
        .hospital-hero::after {
            content: '';
            position: absolute;
            border: 1px solid rgba(255, 255, 255, .13);
            border-radius: 50%;
            pointer-events: none;
        }

        .hospital-hero::before {
            width: 300px;
            height: 300px;
            right: 4%;
            top: -180px;
        }

        .hospital-hero::after {
            width: 210px;
            height: 210px;
            right: -65px;
            bottom: -120px;
        }

        .hospital-hero-content,
        .hospital-date-card {
            position: relative;
            z-index: 1;
        }

        .hospital-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            margin-bottom: .7rem;
            color: #bcecf0;
            font-size: .85rem;
            font-weight: 700;
            letter-spacing: .13em;
            text-transform: uppercase;
        }

        .hospital-eyebrow::before {
            content: '';
            width: 28px;
            height: 2px;
            border-radius: 999px;
            background: #7ee0d6;
        }

        .hospital-hero h1 {
            margin: 0;
            color: #fff;
            font-size: clamp(1.8rem, 3vw, 2.75rem);
            font-weight: 700;
            line-height: 1.12;
        }

        .hospital-hero-copy {
            max-width: 670px;
            margin: .8rem 0 0;
            color: rgba(255, 255, 255, .84);
            font-size: 1.08rem;
            line-height: 1.65;
        }

        .hospital-welcome {
            display: flex;
            align-items: center;
            gap: .6rem;
            margin-top: 1.15rem;
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
        }

        .hospital-welcome i {
            color: #8be1d8;
            font-size: 1.12rem;
        }

        .hospital-date-card {
            width: 210px;
            padding: 1.15rem 1.25rem;
            border: 1px solid rgba(255, 255, 255, .24);
            border-radius: 16px;
            background: rgba(255, 255, 255, .13);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .15);
            backdrop-filter: blur(8px);
        }

        .hospital-date-card span {
            display: block;
            color: #beeaf0;
            font-size: .8rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .hospital-date-card strong {
            display: block;
            margin-top: .45rem;
            color: #fff;
            font-size: 1.08rem;
            font-weight: 600;
            line-height: 1.45;
        }

        .hospital-date-card i {
            margin-right: .4rem;
            color: #8be1d8;
        }

        .hospital-body {
            display: grid;
            grid-template-columns: minmax(280px, .78fr) minmax(0, 2.22fr);
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .hospital-panel {
            border: 1px solid var(--medical-line);
            border-radius: 18px;
            background: var(--medical-surface);
            box-shadow: 0 10px 30px rgba(18, 69, 116, .07);
        }

        .hospital-feature {
            position: relative;
            display: flex;
            min-height: 100%;
            padding: 1.75rem;
            overflow: hidden;
            color: #fff;
            text-decoration: none !important;
            background:
                linear-gradient(180deg, rgba(4, 48, 82, .08) 0%, rgba(4, 48, 82, .96) 100%),
                url('{{ asset('/hc4/img/agenda_quirofano.png') }}') center / cover no-repeat;
        }

        .hospital-feature::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(6, 106, 153, .42), transparent 60%);
        }

        .hospital-feature-content {
            position: relative;
            z-index: 1;
            display: flex;
            flex: 1;
            flex-direction: column;
            justify-content: flex-end;
        }

        .hospital-feature-badge {
            align-self: flex-start;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: auto;
            padding: .52rem .75rem;
            border: 1px solid rgba(255, 255, 255, .32);
            border-radius: 999px;
            background: rgba(7, 59, 102, .56);
            color: #fff;
            font-size: .84rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .hospital-feature-badge i {
            font-size: 1rem;
        }

        .hospital-feature h2 {
            margin: 4.5rem 0 .55rem;
            color: #fff;
            font-size: 1.78rem;
            font-weight: 700;
        }

        .hospital-feature p {
            margin: 0;
            color: rgba(255, 255, 255, .82);
            font-size: 1rem;
            line-height: 1.55;
        }

        .hospital-feature-action {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 1.4rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, .25);
            color: #fff;
            font-size: 1rem;
            font-weight: 700;
        }

        .hospital-feature-action i {
            font-size: 1.2rem;
        }

        .hospital-services {
            padding: 1.6rem;
        }

        .hospital-section-header {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.25rem;
        }

        .hospital-section-header h2 {
            margin: 0;
            color: var(--medical-navy);
            font-size: 1.55rem;
            font-weight: 700;
        }

        .hospital-section-header p {
            margin: .35rem 0 0;
            color: var(--medical-muted);
            font-size: .98rem;
        }

        .hospital-service-count {
            flex: 0 0 auto;
            padding: .42rem .7rem;
            border-radius: 999px;
            background: #eef8fb;
            color: #087788;
            font-size: .84rem;
            font-weight: 700;
        }

        .hospital-section-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .65rem;
            flex-wrap: wrap;
        }

        .hospital-services-config {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .5rem .72rem;
            border: 1px solid var(--medical-line);
            border-radius: 999px;
            background: #fff;
            color: var(--medical-navy);
            font-size: .78rem;
            font-weight: 700;
            text-decoration: none !important;
        }

        .hospital-services-config:hover {
            border-color: var(--medical-blue);
            color: var(--medical-blue);
        }

        .hospital-services-empty {
            grid-column: 1 / -1;
            padding: 2.2rem 1rem;
            border: 1px dashed var(--medical-line);
            border-radius: 14px;
            color: var(--medical-muted);
            text-align: center;
        }

        .hospital-services-empty i {
            display: block;
            margin-bottom: .6rem;
            color: var(--medical-blue);
            font-size: 1.5rem;
        }

        .hospital-module-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .9rem;
        }

        .hospital-module {
            position: relative;
            display: grid;
            min-height: 152px;
            grid-template-columns: 60px minmax(0, 1fr);
            grid-template-rows: auto auto;
            align-content: center;
            align-items: center;
            column-gap: 1rem;
            padding: 1.3rem 2.5rem 1.3rem 1.3rem;
            overflow: hidden;
            border: 1px solid var(--medical-line);
            border-radius: 14px;
            color: var(--medical-ink);
            text-decoration: none !important;
            background: #fff;
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .hospital-module::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: var(--module-color, var(--medical-blue));
            opacity: .8;
        }

        .hospital-module:hover,
        .hospital-module:focus {
            border-color: var(--module-color, var(--medical-blue));
            color: var(--medical-ink);
            transform: translateY(-4px);
            box-shadow: 0 12px 24px rgba(18, 69, 116, .12);
        }

        .hospital-module:focus-visible,
        .hospital-feature:focus-visible {
            outline: 3px solid rgba(8, 119, 190, .28);
            outline-offset: 3px;
        }

        .hospital-module-icon {
            display: grid;
            grid-column: 1;
            grid-row: 1 / span 2;
            width: 60px;
            height: 60px;
            align-self: center;
            place-items: center;
            border-radius: 16px;
            background: var(--module-soft, #eaf5fb);
            color: var(--module-color, var(--medical-blue));
            font-size: 1.75rem;
        }

        .hospital-module h3 {
            grid-column: 2;
            grid-row: 1;
            align-self: end;
            margin: 0 0 .3rem;
            color: var(--medical-ink);
            font-size: 1.13rem;
            font-weight: 700;
        }

        .hospital-module p {
            grid-column: 2;
            grid-row: 2;
            align-self: start;
            margin: 0;
            color: var(--medical-muted);
            font-size: .86rem;
            line-height: 1.4;
        }

        .hospital-module-arrow {
            position: absolute;
            right: 1rem;
            top: 50%;
            color: #9eb1c1;
            font-size: 1rem;
            transform: translateY(-50%);
            transition: transform .2s ease, color .2s ease;
        }

        .hospital-module:hover .hospital-module-arrow {
            color: var(--module-color, var(--medical-blue));
            transform: translate(3px, -50%);
        }

        .module-red { --module-color: #d94a4a; --module-soft: #fff0f0; }
        .module-blue { --module-color: #0877be; --module-soft: #eaf5fb; }
        .module-purple { --module-color: #7756b5; --module-soft: #f3effc; }
        .module-green { --module-color: #158b72; --module-soft: #eaf8f4; }
        .module-cyan { --module-color: #1387a6; --module-soft: #eaf8fb; }
        .module-pink { --module-color: #c34f7a; --module-soft: #fceff4; }
        .module-orange { --module-color: #d27b20; --module-soft: #fff5e8; }
        .module-teal { --module-color: #0d9488; --module-soft: #e8f8f5; }

        .dark-layout .hospital-panel,
        .dark-layout .hospital-module {
            border-color: #3b4b5d;
            background: #283447;
        }

        /* La regla anterior no debe reemplazar la fotografía del acceso principal. */
        html.dark-layout .hospital-feature {
            border-color: #426078;
            background:
                linear-gradient(180deg, rgba(5, 35, 59, .16) 0%, rgba(4, 35, 60, .90) 100%),
                url('{{ asset('/hc4/img/agenda_quirofano.png') }}') center / cover no-repeat;
            box-shadow: 0 14px 34px rgba(0, 0, 0, .22);
        }

        html.dark-layout .hospital-feature::after {
            background: linear-gradient(135deg, rgba(0, 139, 173, .25), transparent 62%);
        }

        html.dark-layout .hospital-feature-badge {
            border-color: rgba(159, 226, 239, .48);
            background: rgba(3, 42, 70, .72);
        }

        html.dark-layout .hospital-feature p {
            color: rgba(244, 250, 255, .88);
        }

        .dark-layout .hospital-section-header h2,
        .dark-layout .hospital-module h3 {
            color: #f3f7fa;
        }

        .dark-layout .hospital-section-header p,
        .dark-layout .hospital-module p {
            color: #b8c4cf;
        }

        .dark-layout .hospital-services-config {
            border-color: #47586a;
            background: #283447;
            color: #dcecf7;
        }

        @media (max-width: 1199.98px) {
            .hospital-body {
                grid-template-columns: 1fr;
            }

            .hospital-feature {
                min-height: 320px;
            }

            .hospital-feature h2 {
                margin-top: 5rem;
            }
        }

        @media (max-width: 991.98px) {
            .hospital-module-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .hospital-home {
                padding-top: .75rem;
            }

            .hospital-hero {
                grid-template-columns: 1fr;
                gap: 1.25rem;
                padding: 1.6rem;
                border-radius: 16px;
            }

            .hospital-date-card {
                width: 100%;
            }

            .hospital-body {
                gap: 1rem;
                margin-top: 1rem;
            }

            .hospital-services,
            .hospital-feature {
                padding: 1.25rem;
                border-radius: 16px;
            }
        }

        @media (max-width: 479.98px) {
            .hospital-module-grid {
                grid-template-columns: 1fr;
            }

            .hospital-module {
                min-height: 132px;
            }

            .hospital-section-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .hospital-module,
            .hospital-module-arrow {
                transition: none;
            }
        }
    </style>

    <main class="hospital-home" aria-labelledby="hospital-dashboard-title">
        <section class="hospital-hero" data-tour="hospital-welcome" aria-label="Bienvenida al área médica">
            <div class="hospital-hero-content">
                <span class="hospital-eyebrow">Gestión clínica hospitalaria</span>
                <h1 id="hospital-dashboard-title">Área médica y hospitalización</h1>
                <p class="hospital-hero-copy">
                    Accede de forma rápida a los servicios clínicos, áreas críticas y procesos de atención hospitalaria.
                </p>
                <div class="hospital-welcome">
                    <i class="fa fa-user-md" aria-hidden="true"></i>
                    Bienvenido, {{ $nombreUsuario }}
                </div>
            </div>

            <div class="hospital-date-card" aria-label="Fecha actual">
                <span>Jornada médica</span>
                <strong><i class="fa fa-calendar-check-o" aria-hidden="true"></i>{{ $fechaActual }}</strong>
            </div>
        </section>

        <div class="hospital-body">
            <a href="{{ route('hospital.gcuartos') }}" class="hospital-panel hospital-feature" data-tour="hospital-reception">
                <div class="hospital-feature-content">
                    <span class="hospital-feature-badge">
                        <i class="fa fa-bed" aria-hidden="true"></i>
                        Gestión hospitalaria
                    </span>
                    <h2>Recepción y gestión de camas</h2>
                    <p>Consulta la disponibilidad, organiza habitaciones y administra el ingreso de pacientes.</p>
                    <span class="hospital-feature-action">
                        Ir a recepción
                        <i class="fa fa-arrow-right" aria-hidden="true"></i>
                    </span>
                </div>
            </a>

            <section class="hospital-panel hospital-services" aria-labelledby="hospital-services-title">
                <div class="hospital-section-header">
                    <div>
                        <h2 id="hospital-services-title">Servicios hospitalarios</h2>
                        <p>Selecciona el área clínica en la que deseas trabajar.</p>
                    </div>
                    <div class="hospital-section-actions">
                        @if($canConfigureHospitalServices)
                            <a class="hospital-services-config" href="{{ route('hospital.services.configuration') }}"><i class="fa fa-cog" aria-hidden="true"></i> Configurar</a>
                        @endif
                        <span class="hospital-service-count">{{ $hospitalServices->count() }} {{ $hospitalServices->count() === 1 ? 'área disponible' : 'áreas disponibles' }}</span>
                    </div>
                </div>

                <div class="hospital-module-grid">
                    @forelse($hospitalServices as $service)
                        <a href="{{ route($service['route'], $service['parameters']) }}" class="hospital-module {{ $service['color'] }}" data-tour="{{ $service['tour'] }}">
                            <span class="hospital-module-icon"><i class="fa {{ $service['icon'] }}" aria-hidden="true"></i></span>
                            <span class="hospital-module-arrow"><i class="fa fa-chevron-right" aria-hidden="true"></i></span>
                            <h3>{{ $service['label'] }}</h3>
                            <p>{{ $service['description'] }}</p>
                        </a>
                    @empty
                        <div class="hospital-services-empty"><i class="fa fa-info-circle" aria-hidden="true"></i>No hay servicios hospitalarios habilitados para esta empresa.</div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
@endsection
