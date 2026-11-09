@extends('layouts.app-template-h')

@section('content')
    @php
        $modulo_traducciones = [
            2 => 'hospital.Emergencia',
            3 => 'hospital.Hospitalizacion',
            4 => 'hospital.Imagenes',
            5 => 'hospital.Cirugia',
            6 => 'hospital.UCI',
            7 => 'hospital.UCIN',
        ];
        $modulo_iconos = [
            2 => 'fa-ambulance',
            3 => 'fa-hospital-o',
            4 => 'fa-file-image-o',
            5 => 'fa-bed',
            6 => 'fa-heartbeat',
            7 => 'fa-heartbeat',
        ];
        $modulo_descripciones = [
            2 => 'Consulta y seguimiento de pacientes atendidos en emergencia.',
            3 => 'Control de pacientes ingresados y atención hospitalaria.',
            4 => 'Gestión de pacientes y procedimientos de diagnóstico por imagen.',
            5 => 'Organización y seguimiento de procedimientos quirúrgicos.',
            6 => 'Seguimiento de pacientes en cuidados intensivos.',
            7 => 'Seguimiento de pacientes en cuidados intensivos neonatales.',
        ];
        $id_paso = $ho_estado_paso->id;
        $paso = $ho_estado_paso;
        $modulo_nombre = isset($modulo_traducciones[$id_paso])
            ? trans($modulo_traducciones[$id_paso])
            : $ho_estado_paso->nombre_paso;
        $modulo_icono = isset($modulo_iconos[$id_paso]) ? $modulo_iconos[$id_paso] : 'fa-user-md';
        $modulo_descripcion = isset($modulo_descripciones[$id_paso])
            ? $modulo_descripciones[$id_paso]
            : 'Consulta y seguimiento de pacientes del área médica.';
    @endphp

    <style>
        .hospital-module-page {
            --module-navy: #073b66;
            --module-blue: #0877be;
            --module-teal: #0d9488;
            --module-ink: #17324d;
            --module-muted: #65788b;
            --module-line: #dbe7ef;
            --module-soft: #f4f8fb;
            padding: .5rem 0 2rem;
            color: var(--module-ink);
            font-family: "Segoe UI", Tahoma, Arial, sans-serif;
        }

        .hospital-module-page,
        .hospital-module-page * {
            box-sizing: border-box;
        }

        .module-breadcrumb {
            display: flex;
            align-items: center;
            gap: .55rem;
            margin-bottom: 1rem;
            color: var(--module-muted);
            font-size: .82rem;
        }

        .module-breadcrumb a {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: var(--module-blue);
            font-weight: 600;
            text-decoration: none;
        }

        .module-breadcrumb a:hover {
            color: var(--module-navy);
        }

        .module-heading {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            padding: 1.5rem 1.7rem;
            overflow: hidden;
            border-radius: 18px;
            background: linear-gradient(115deg, #063b67 0%, #0877ad 68%, #0d9488 100%);
            box-shadow: 0 14px 34px rgba(7, 59, 102, .14);
            color: #fff;
        }

        .module-heading::after {
            content: '';
            position: absolute;
            width: 210px;
            height: 210px;
            right: -70px;
            top: -115px;
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 50%;
        }

        .module-heading-main {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .module-heading-icon {
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

        .module-heading-label {
            display: block;
            margin-bottom: .28rem;
            color: #bde9ee;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .11em;
            text-transform: uppercase;
        }

        .module-heading h1 {
            margin: 0;
            color: #fff;
            font-size: 1.65rem;
            font-weight: 700;
            line-height: 1.25;
        }

        .module-heading p {
            max-width: 720px;
            margin: .35rem 0 0;
            color: rgba(255, 255, 255, .82);
            font-size: .86rem;
        }

        .module-heading-count {
            position: relative;
            z-index: 1;
            min-width: 132px;
            padding: .72rem 1rem;
            border: 1px solid rgba(255, 255, 255, .25);
            border-radius: 13px;
            background: rgba(255, 255, 255, .12);
            text-align: center;
        }

        .module-heading-count strong {
            display: block;
            color: #fff;
            font-size: 1.45rem;
            line-height: 1;
        }

        .module-heading-count span {
            display: block;
            margin-top: .35rem;
            color: #ccecef;
            font-size: .68rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .module-workspace {
            margin-top: 1.25rem;
            overflow: hidden;
            border: 1px solid var(--module-line);
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(18, 69, 116, .07);
        }

        .module-workspace-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--module-line);
        }

        .module-workspace-title {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .module-workspace-title-icon {
            display: grid;
            width: 38px;
            height: 38px;
            place-items: center;
            border-radius: 10px;
            background: #eaf5fb;
            color: var(--module-blue);
        }

        .module-workspace-header h2 {
            margin: 0;
            color: var(--module-ink);
            font-size: 1.05rem;
            font-weight: 700;
        }

        .module-workspace-header p {
            margin: .15rem 0 0;
            color: var(--module-muted);
            font-size: .75rem;
        }

        .module-card-tool {
            display: grid;
            width: 34px;
            height: 34px;
            padding: 0 !important;
            place-items: center;
            border: 1px solid var(--module-line) !important;
            border-radius: 9px !important;
            background: #fff !important;
            color: var(--module-muted) !important;
        }

        .module-filter {
            padding: 1.35rem 1.5rem;
            border-bottom: 1px solid var(--module-line);
            background: var(--module-soft);
        }

        .module-filter-grid {
            display: grid;
            grid-template-columns: .9fr 1.3fr .9fr .9fr;
            gap: 1rem;
        }

        .module-filter-group {
            min-width: 0;
            margin: 0;
        }

        .hospital-module-page .module-filter-group label {
            display: block;
            margin: 0 0 .45rem;
            color: var(--module-ink) !important;
            font-size: .75rem;
            font-weight: 700;
        }

        .module-input-wrap {
            position: relative;
        }

        .module-input-wrap > i {
            position: absolute;
            z-index: 2;
            left: .85rem;
            top: 50%;
            color: #8ca1b4;
            font-size: .88rem;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .hospital-module-page .module-input-wrap .form-control {
            width: 100%;
            height: 42px;
            padding: .58rem .8rem .58rem 2.45rem;
            border: 1px solid #ccdae5;
            border-radius: 10px;
            background: #fff;
            color: var(--module-ink) !important;
            font-size: .84rem !important;
            box-shadow: none;
            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .hospital-module-page .module-input-wrap .form-control:focus {
            border-color: var(--module-blue);
            box-shadow: 0 0 0 3px rgba(8, 119, 190, .12);
        }

        .module-filter-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            margin-top: 1rem;
        }

        .module-filter-actions-left {
            display: flex;
            flex-wrap: wrap;
            gap: .65rem;
        }

        .hospital-module-page .module-primary-btn,
        .hospital-module-page .module-secondary-btn {
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

        .hospital-module-page .module-primary-btn {
            border: 1px solid var(--module-blue);
            background: var(--module-blue);
            color: #fff;
        }

        .hospital-module-page .module-secondary-btn {
            border: 1px solid #b8d8e7;
            background: #fff;
            color: var(--module-navy);
        }

        .hospital-module-page .module-primary-btn:hover,
        .hospital-module-page .module-primary-btn:focus {
            color: #fff;
            background: #0667a5;
            box-shadow: 0 7px 16px rgba(8, 119, 190, .2);
            transform: translateY(-1px);
        }

        .hospital-module-page .module-secondary-btn:hover,
        .hospital-module-page .module-secondary-btn:focus {
            color: var(--module-navy);
            border-color: var(--module-teal);
            background: #edf9f7;
        }

        .module-filter-date {
            color: var(--module-muted);
            font-size: .73rem;
        }

        .module-table-section {
            padding: 1.35rem 1.5rem 1.5rem;
        }

        .module-table-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: .9rem;
        }

        .module-table-heading h3 {
            margin: 0;
            color: var(--module-ink);
            font-size: .95rem;
            font-weight: 700;
        }

        .module-table-heading span {
            padding: .35rem .62rem;
            border-radius: 999px;
            background: #edf7fb;
            color: var(--module-blue);
            font-size: .68rem;
            font-weight: 700;
        }

        .hospital-module-page .module-patient-table {
            width: 100%;
            min-width: 1180px;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
            color: var(--module-ink) !important;
            font-size: .8rem !important;
            table-layout: fixed;
        }

        .hospital-module-page .module-patient-table thead th {
            padding: .78rem .85rem;
            border-top: 1px solid var(--module-line);
            border-bottom: 1px solid var(--module-line);
            background: #f5f8fb;
            color: #40576d !important;
            font-size: .68rem !important;
            font-weight: 700 !important;
            letter-spacing: .035em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .hospital-module-page .module-patient-table thead th:first-child {
            border-left: 1px solid var(--module-line);
            border-radius: 10px 0 0 10px;
        }

        .hospital-module-page .module-patient-table thead th:last-child {
            border-right: 1px solid var(--module-line);
            border-radius: 0 10px 10px 0;
        }

        .hospital-module-page .module-patient-table tbody td {
            padding: .95rem .85rem;
            border-top: 0;
            border-bottom: 1px solid #edf2f6;
            color: var(--module-ink) !important;
            font-size: .78rem !important;
            font-weight: 500;
            vertical-align: middle;
        }

        .hospital-module-page .module-patient-table tbody tr:hover td {
            background: #f8fbfd;
        }

        .module-priority {
            display: inline-flex;
            min-width: 88px;
            justify-content: center;
            padding: .34rem .55rem;
            border-radius: 999px;
            color: #fff;
            font-size: .68rem;
            font-weight: 700;
        }

        .priority-1 { background: #d93a3a; }
        .priority-2 { background: #e47718; }
        .priority-3 { background: #c99a00; }
        .priority-4 { background: #208b54; }
        .priority-5 { background: #2864ad; }

        .module-action-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(105px, 1fr));
            gap: .4rem;
            width: 100%;
        }

        .pdf-preview-modal .modal-dialog {
            --module-navy: #073b66;
            --module-blue: #0877be;
            --module-teal: #0d9488;
            --module-ink: #17324d;
            --module-line: #dbe7ef;
            width: calc(100% - 32px);
            max-width: 1500px;
            margin: 16px auto;
        }

        .pdf-preview-modal .modal-content {
            overflow: hidden;
            border: 0;
            border-radius: 16px;
            box-shadow: 0 22px 60px rgba(7, 59, 102, .28);
        }

        .pdf-preview-modal .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 58px;
            padding: .9rem 1.2rem;
            border-bottom: 1px solid var(--module-line);
            background: #fff;
        }

        .pdf-preview-modal .modal-title {
            display: flex;
            min-width: 0;
            align-items: center;
            gap: .65rem;
            margin: 0;
            color: var(--module-ink);
            font-size: 1rem;
            font-weight: 700;
        }

        .pdf-preview-modal .modal-title i {
            color: var(--module-blue);
        }

        .pdf-preview-close {
            display: inline-flex;
            flex: 0 0 38px;
            width: 38px;
            height: 38px;
            margin-left: 1rem;
            padding: 0;
            align-items: center;
            justify-content: center;
            border: 1px solid #d8e2ea;
            border-radius: 50%;
            background: #f4f7fa;
            box-shadow: 0 3px 10px rgba(23, 50, 77, .08);
            color: #496174;
            font-family: Arial, sans-serif;
            font-size: 25px;
            font-weight: 400;
            line-height: 1;
            cursor: pointer;
            transition: background-color .18s ease, border-color .18s ease, color .18s ease, box-shadow .18s ease, transform .18s ease;
        }

        .pdf-preview-close span {
            display: block;
            transform: translateY(-1px);
        }

        .pdf-preview-close:hover {
            border-color: #f2b8b5;
            background: #fff1f0;
            box-shadow: 0 5px 14px rgba(180, 35, 24, .14);
            color: #b42318;
            transform: translateY(-1px);
        }

        .pdf-preview-close:focus {
            outline: 0;
            border-color: var(--module-blue);
            box-shadow: 0 0 0 3px rgba(8, 119, 190, .2);
            color: var(--module-navy);
        }

        .pdf-preview-close:active {
            transform: translateY(0);
        }

        .pdf-preview-modal .modal-body {
            position: relative;
            height: calc(100vh - 150px);
            min-height: 460px;
            padding: 0;
            background: #eef3f7;
            overflow: hidden;
        }

        .pdf-preview-loading {
            position: absolute;
            z-index: 2;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #f8fbfd 0%, #edf4f8 100%);
            opacity: 1;
            visibility: visible;
            transition: opacity .2s ease, visibility .2s ease;
        }

        .pdf-preview-loading.is-hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        .pdf-preview-loading-content {
            max-width: 320px;
            padding: 1.5rem;
            color: #52697d;
            text-align: center;
        }

        .pdf-preview-spinner {
            width: 46px;
            height: 46px;
            margin: 0 auto 1rem;
            border: 4px solid #d5e5ee;
            border-top-color: var(--module-blue);
            border-radius: 50%;
            animation: pdfPreviewSpin .8s linear infinite;
        }

        .pdf-preview-loading-content strong {
            display: block;
            margin-bottom: .35rem;
            color: var(--module-ink);
            font-size: .95rem;
        }

        .pdf-preview-loading-content span {
            display: block;
            font-size: .75rem;
            line-height: 1.5;
        }

        @keyframes pdfPreviewSpin {
            to { transform: rotate(360deg); }
        }

        .pdf-preview-frame {
            display: block;
            width: 100%;
            height: 100%;
            border: 0;
            background: #fff;
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s ease;
        }

        .pdf-preview-frame.is-ready {
            opacity: 1;
            visibility: visible;
        }

        .pdf-preview-modal .modal-footer {
            padding: .75rem 1.2rem;
            border-top: 1px solid var(--module-line);
        }

        .pdf-preview-modal .module-primary-btn,
        .pdf-preview-modal .module-secondary-btn {
            display: inline-flex;
            min-height: 38px;
            align-items: center;
            justify-content: center;
            gap: .45rem;
            padding: .55rem .9rem;
            border-radius: 9px;
            font-size: .78rem;
            font-weight: 700;
        }

        .pdf-preview-modal .module-primary-btn {
            border: 1px solid var(--module-blue);
            background: var(--module-blue);
            color: #fff;
        }

        .pdf-preview-modal .module-secondary-btn {
            border: 1px solid #b8d8e7;
            background: #fff;
            color: var(--module-navy);
        }

        @media (max-width: 767px) {
            .pdf-preview-modal .modal-dialog {
                width: calc(100% - 16px);
                margin: 8px auto;
            }

            .pdf-preview-modal .modal-body {
                height: calc(100vh - 132px);
                min-height: 360px;
            }
        }

        .hospital-module-page .module-action-list .btn {
            display: inline-flex;
            width: 100%;
            min-height: 32px;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            margin: 0;
            padding: .4rem .58rem;
            border-radius: 8px;
            font-size: .67rem !important;
            font-weight: 700;
        }

        .module-empty-state {
            padding: 3.5rem 1.5rem !important;
            border-bottom: 0 !important;
            text-align: center;
        }

        .module-empty-icon {
            display: inline-flex;
            width: 58px;
            height: 58px;
            margin: 0 auto .9rem;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            background: #eaf5fb;
            color: var(--module-blue);
            line-height: 1;
        }

        .module-empty-icon > i {
            display: block;
            margin: 0;
            font-size: 1.65rem;
            line-height: 1;
        }

        .module-empty-state strong {
            display: block;
            color: var(--module-ink);
            font-size: .9rem;
        }

        .module-empty-copy {
            display: block;
            margin-top: .3rem;
            color: var(--module-muted);
            font-size: .76rem;
        }

        .dark-layout .module-workspace,
        .dark-layout .module-card-tool,
        .dark-layout .hospital-module-page .module-input-wrap .form-control {
            border-color: #3d4c5e;
            background: #283447;
        }

        .dark-layout .module-filter,
        .dark-layout .hospital-module-page .module-patient-table thead th {
            border-color: #3d4c5e;
            background: #222d3f;
        }

        .dark-layout .module-workspace-header h2,
        .dark-layout .module-table-heading h3,
        .dark-layout .module-empty-state strong,
        .dark-layout .hospital-module-page .module-filter-group label,
        .dark-layout .hospital-module-page .module-input-wrap .form-control,
        .dark-layout .hospital-module-page .module-patient-table tbody td {
            color: #f1f5f8 !important;
        }

        .dark-layout .module-workspace-header p,
        .dark-layout .module-filter-date,
        .dark-layout .module-empty-copy {
            color: #b6c2cd;
        }

        @media (max-width: 1199.98px) {
            .module-filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 767.98px) {
            .hospital-module-page {
                padding-top: 0;
            }

            .module-heading {
                align-items: flex-start;
                flex-direction: column;
                padding: 1.3rem;
                border-radius: 15px;
            }

            .module-heading-count {
                width: 100%;
                text-align: left;
            }

            .module-heading-count strong,
            .module-heading-count span {
                display: inline;
            }

            .module-heading-count span {
                margin-left: .4rem;
            }

            .module-workspace {
                border-radius: 15px;
            }

            .module-filter-grid {
                grid-template-columns: 1fr;
            }

            .module-filter-actions,
            .module-workspace-header {
                align-items: flex-start;
            }

            .module-filter-actions {
                flex-direction: column;
            }

            .module-filter-date {
                order: -1;
            }
        }

        @media (max-width: 479.98px) {
            .module-heading-main {
                align-items: flex-start;
            }

            .module-heading-icon {
                width: 48px;
                height: 48px;
                flex-basis: 48px;
                border-radius: 13px;
            }

            .module-heading h1 {
                font-size: 1.3rem;
            }

            .module-workspace-header,
            .module-filter,
            .module-table-section {
                padding-right: 1rem;
                padding-left: 1rem;
            }

            .module-filter-actions-left,
            .hospital-module-page .module-primary-btn,
            .hospital-module-page .module-secondary-btn {
                width: 100%;
            }
        }
    </style>

    <main class="hospital-module-page">
        <nav class="module-breadcrumb" aria-label="Navegación">
            <a href="{{ route('hospital.index') }}">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Área médica
            </a>
            <i class="fa fa-angle-right" aria-hidden="true"></i>
            <span>{{ $modulo_nombre }}</span>
        </nav>

        <section class="module-heading" aria-labelledby="module-page-title">
            <div class="module-heading-main">
                <span class="module-heading-icon"><i class="fa {{ $modulo_icono }}" aria-hidden="true"></i></span>
                <div>
                    <span class="module-heading-label">Gestión clínica</span>
                    <h1 id="module-page-title">Pacientes en {{ $modulo_nombre }}</h1>
                    <p>{{ $modulo_descripcion }}</p>
                </div>
            </div>
            <div class="module-heading-count" aria-label="Pacientes encontrados">
                <strong>{{ $solicitudes->count() }}</strong>
                <span>pacientes encontrados</span>
            </div>
        </section>

        <section class="module-workspace card card-primary">
            <header class="module-workspace-header card-header with-border">
                <div class="module-workspace-title">
                    <span class="module-workspace-title-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
                    <div>
                        <h2>Consulta de pacientes</h2>
                        <p>Utiliza uno o varios filtros para localizar registros.</p>
                    </div>
                </div>
                <div class="card-tools">
                    <button type="button" class="btn btn-card-tool module-card-tool" data-widget="collapse" aria-label="Contraer panel">
                        <i class="fa fa-minus" aria-hidden="true"></i>
                    </button>
                </div>
            </header>

            <div class="card-body p-0">
                <form id="form_buscar" method="POST" action="{{ route('hospital.index_modulos', ['id_paso' => $ho_estado_paso->id]) }}" class="module-filter">
                    {{ csrf_field() }}
                    <input type="hidden" name="id_estado_paso" id="id_estado_paso" value="{{ $ho_estado_paso->id }}">

                    <div class="module-filter-grid">
                        <div class="form-group module-filter-group">
                            <label for="cedula">{{ trans('hospitalizacion.Cedula') }}</label>
                            <div class="module-input-wrap">
                                <i class="fa fa-id-card-o" aria-hidden="true"></i>
                                <input type="text" class="form-control input-sm" name="cedula" id="cedula" autocomplete="off" value="{{ $cedula }}" placeholder="Número de identificación">
                            </div>
                        </div>

                        <div class="form-group module-filter-group">
                            <label for="paciente">{{ trans('hospitalizacion.Pacientes') }}</label>
                            <div class="module-input-wrap">
                                <i class="fa fa-user-o" aria-hidden="true"></i>
                                <input type="text" class="form-control input-sm" name="paciente" id="paciente" autocomplete="off" value="{{ $paciente }}" placeholder="Nombres o apellidos">
                            </div>
                        </div>

                        <div class="form-group module-filter-group">
                            <label for="fecha_desde">{{ trans('hospitalizacion.FechaDesde') }}</label>
                            <div class="module-input-wrap">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                <input type="text" data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_desde" id="fecha_desde" autocomplete="off" value="{{ $desde }}">
                            </div>
                        </div>

                        <div class="form-group module-filter-group">
                            <label for="fecha_hasta">{{ trans('hospitalizacion.FechaHasta') }}</label>
                            <div class="module-input-wrap">
                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                <input type="text" data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{ $hasta }}">
                            </div>
                        </div>

                        <!-- El filtro de sala se conserva para una futura reactivacion. -->
                        <!--
                        <div class="form-group module-filter-group">
                            <label for="sala">{{ trans('hospitalizacion.Salas') }}</label>
                            <select class="form-control" id="sala" name="sala">
                                <option value=" ">{{ trans('hospitalizacion.SeleccioneOpcion') }}</option>
                                @foreach ($salas as $s)
                                    <option value="{{ $s->id }}">{{ $s->nombre_sala }}</option>
                                @endforeach
                            </select>
                        </div>
                        -->
                    </div>

                    <div class="module-filter-actions">
                        <div class="module-filter-actions-left">
                            <button class="btn module-primary-btn" type="submit">
                                <i class="fa fa-search" aria-hidden="true"></i>
                                Buscar pacientes
                            </button>
                            <a class="btn module-secondary-btn" href="{{ route('hospital.admision', ['id_paso' => $ho_estado_paso->id]) }}">
                                <i class="fa fa-plus" aria-hidden="true"></i>
                                {{ trans('hospitalizacion.Ingreso') }}
                            </a>
                        </div>
                        <span class="module-filter-date">
                            @if($desde === '' && $hasta === '')
                                <i class="fa fa-bed" aria-hidden="true"></i>
                                Mostrando ingresos activos
                            @else
                                <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                                Periodo: {{ $desde !== '' ? $desde : 'inicio' }} al {{ $hasta !== '' ? $hasta : 'hoy' }}
                            @endif
                        </span>
                    </div>
                </form>

            @include('hospital.workflow.pending-transfers')
            <div class="module-table-section table-responsive" id="tablaajax">
                    <div class="module-table-heading">
                        <h3>Listado de pacientes</h3>
                        <span>{{ $solicitudes->count() }} registros</span>
                    </div>

                    <table class="table table-hover module-patient-table">
                        <colgroup>
                            <col style="width: 8%">
                            <col style="width: 8%">
                            <col style="width: 10%">
                            <col style="width: 20%">
                            <col style="width: 14%">
                            @if($id_paso == 2)
                                <col style="width: 10%">
                                <col style="width: 30%">
                            @elseif($id_paso == 5)
                                <col style="width: 14%">
                                <col style="width: 26%">
                            @else
                                <col style="width: 40%">
                            @endif
                        </colgroup>
                        <thead>
                            <tr>
                                <th>{{ trans('hospitalizacion.Fecha') }}</th>
                                <th>{{ trans('hospitalizacion.Ingreso') }}</th>
                                <th>{{ trans('hospitalizacion.Cedula') }}</th>
                                <th>{{ trans('hospitalizacion.Paciente') }}</th>
                                <th>
                                    @if($id_paso == 4 or $id_paso == 5)
                                        {{ trans('hospitalizacion.Doctor') }}
                                    @else
                                        {{ trans('hospitalizacion.Usuario') }}
                                    @endif
                                </th>
                                @if($id_paso == 2)
                                    <th>{{ trans('emergencia.PrioridadTabla') }}</th>
                                @endif
                                @if($id_paso == 5)
                                    <th>QUIROFANO / SALA</th>
                                @endif
                                <th>{{ trans('hospitalizacion.Accion') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($solicitudes as $solicitud)
                                @php
                                    $triaje = $solicitud->manchester->first();
                                    $pacienteSolicitud = $solicitud->paciente
                                        ?: ($solicitud->agenda ? $solicitud->agenda->paciente : null);
                                    $formulariosSolicitud = isset($formulariosDisponibles[$solicitud->id])
                                        ? $formulariosDisponibles[$solicitud->id]
                                        : array('001' => false, '005' => false, '008' => false);
                                @endphp
                                <tr>
                                    <td>{{ substr($solicitud->fechaini, 0, 10) }}</td>
                                    <td>{{ substr($solicitud->fecha_ingreso, 0, 10) }}</td>
                                    <td>{{ $solicitud->id_paciente ?: 'No disponible' }}</td>
                                    <td>
                                        @if($pacienteSolicitud)
                                            {{ $pacienteSolicitud->apellido1 }} {{ $pacienteSolicitud->apellido2 }} {{ $pacienteSolicitud->nombre1 }} {{ $pacienteSolicitud->nombre2 }}
                                        @else
                                            Informaci&oacute;n del paciente no disponible
                                        @endif
                                    </td>
                                    <td>
                                        @if($id_paso == 4 or $id_paso == 5) Dr. @endif
                                        @php
                                            $surgeryAgenda = $id_paso == 5 && isset($surgeryAgendas)
                                                ? $surgeryAgendas->get($solicitud->encounter_id) : null;
                                            $displayDoctor = $surgeryAgenda && $surgeryAgenda->doctor1
                                                ? $surgeryAgenda->doctor1 : ($solicitud->agenda ? $solicitud->agenda->doctor1 : null);
                                        @endphp
                                        @if($displayDoctor)
                                            {{ mb_strtoupper($displayDoctor->apellido1.' '.$displayDoctor->nombre1, 'UTF-8') }}
                                        @else
                                            No asignado
                                        @endif
                                    </td>

                                    @if($id_paso == 2)
                                        <td>
                                            @if(!is_null($triaje) && isset($triaje->ho_prioridad))
                                                <span class="module-priority priority-{{ $triaje->prioridad }}">
                                                    {{ $triaje->ho_prioridad->nombre }}
                                                </span>
                                            @else
                                                <span class="text-muted">Sin prioridad</span>
                                            @endif
                                        </td>
                                    @endif

                                    @if($id_paso == 5)
                                        @php
                                            $surgeryTransfer = $surgeryTransfers->get($solicitud->id);
                                            $surgeryState = isset($surgeryStates[$solicitud->id]) ? $surgeryStates[$solicitud->id] : 'RECEPCION QUIRURGICA';
                                        @endphp
                                        <td>
                                            <strong>{{ $surgeryTransfer && $surgeryTransfer->sala ? mb_strtoupper($surgeryTransfer->sala->nombre_sala, 'UTF-8') : 'SALA NO REGISTRADA' }}</strong>
                                            <div class="text-muted small">{{ $surgeryState }}</div>
                                        </td>
                                    @endif

                                    <td>
                                        <div class="module-action-list">
                                            @if(in_array((int) $solicitud->estado_paso, [2, 3, 5, 6, 7]))
                                            <a href="{{route('hospital.documents.index', ['id_solicitud' => $solicitud->id])}}" class="btn btn-info btn-sm"><i class="fa fa-folder-open"></i> Documentos</a>
                                            @if((int) $solicitud->estado_paso === 5 && isset($surgeryState) && $surgeryState === 'RECUPERACION COMPLETADA')
                                                <a href="{{ route('hospital.traspaso_modulo', ['id_solicitud' => $solicitud->id]) }}" class="btn btn-warning btn-sm"><i class="fa fa-bed"></i> Trasladar a destino</a>
                                            @endif
                                            @if(in_array((int) $solicitud->estado_paso, [3, 6, 7], true))
                                                <a href="{{route('epicrisis.mostrar_final', ['id_solicitud' => $solicitud->id])}}" class="btn btn-success btn-sm"><i class="fa fa-sign-out"></i> Alta y salida</a>
                                            @endif
                                            @endif
                                            @if (!is_null($paso->ruta))
                                                @if($id_paso == 2)
                                                    @if(!is_null($triaje))
                                                        <a href="{{ route('hospital.resultadomanchester', $triaje->id) }}" class="btn btn-sm btn-success">
                                                            <i class="far fa-file-alt" aria-hidden="true"></i>{{ trans('emergencia.DatosTriaje') }}
                                                        </a>
                                                    @endif
                                                    @if($formulariosSolicitud['008'])
                                                        <a href="{{ route('hospital.nuevo.formulario008_pdf', ['id' => $solicitud->id]) }}" class="btn btn-info btn-xs js-pdf-preview" data-pdf-title="Formulario 008">
                                                            <i class="fa fa-eye" aria-hidden="true"></i>{{ trans('emergencia.Formulario008') }}
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('hospital.emergencia', ['solicitud' => $solicitud->id, 'accion' => 'destino']) }}" class="btn btn-warning btn-xs" title="Definir ubicación, destino asistencial o solicitar una cama">
                                                        <i class="fa fa-map-marker" aria-hidden="true"></i>Ubicar / destino
                                                    </a>
                                                @endif

                                                @if ($id_paso == 4)
                                                    <a href="{{ route($paso->ruta, ['tipo' => 0, 'id_solicitud' => $solicitud->id]) }}" class="btn btn-info">
                                                        <i class="fa fa-file" aria-hidden="true"></i>{{ trans('hospitalizacion.VerDetalle') }}
                                                    </a>
                                                @elseif($id_paso == 5)
                                                    <a href="{{ route($paso->ruta, ['tipo' => 1, 'id_solicitud' => $solicitud->id]) }}" class="btn btn-info">
                                                        <i class="fa fa-file" aria-hidden="true"></i>{{ trans('hospitalizacion.VerDetalle') }}
                                                    </a>
                                                @else
                                                    <a href="{{ route($paso->ruta, ['id_solicitud' => $solicitud->id]) }}" class="btn btn-info">
                                                        <i class="fa fa-file" aria-hidden="true"></i>{{ trans('hospitalizacion.VerDetalle') }}
                                                    </a>
                                                    @if(in_array((int) $id_paso, [3, 6, 7], true))
                                                        @if($formulariosSolicitud['005'])
                                                            <a href="{{ route('habitacion.formulario005_pdf', ['id' => $solicitud->id]) }}" class="btn btn-info btn-xs js-pdf-preview" data-pdf-title="Formulario 005">
                                                                <i class="fa fa-eye" aria-hidden="true"></i>{{ trans('emergencia.Formualrio005') }}
                                                            </a>
                                                        @endif
                                                        @if($formulariosSolicitud['008'])
                                                            <a href="{{ route('hospital.nuevo.formulario008_pdf', ['id' => $solicitud->id]) }}" class="btn btn-info btn-xs js-pdf-preview" data-pdf-title="Formulario 008">
                                                                <i class="fa fa-eye" aria-hidden="true"></i>{{ trans('emergencia.Formulario008') }}
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
                                            @endif

                                            @if($formulariosSolicitud['001'])
                                                <a href="{{ route('formulario.001', ['id' => $solicitud->id]) }}" class="btn btn-info btn-xs js-pdf-preview" data-pdf-title="Formulario 001">
                                                    <i class="fa fa-eye" aria-hidden="true"></i>{{ trans('emergencia.formulario') }} 001
                                                </a>
                                            @endif
                                            <button type="button" class="btn btn-info btn-xs" onclick="cargar_planilla('{{ $solicitud->id }}')">
                                                <i class="fa fa-calculator" aria-hidden="true"></i>{{ trans('hospitalizacion.planillar') }}
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $id_paso == 2 || $id_paso == 5 ? 7 : 6 }}" class="module-empty-state">
                                        <span class="module-empty-icon"><i class="fa fa-clipboard" aria-hidden="true"></i></span>
                                        <strong>No se encontraron pacientes</strong>
                                        <span class="module-empty-copy">Prueba con otro periodo o modifica los criterios de búsqueda.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="module-pagination">
                        {{ $solicitudes->appends(Request::except('page'))->links() }}
                    </div>
                </div>
            </div>
        </section>
    </main>

    <div class="modal fade pdf-preview-modal" id="pdfPreviewModal" tabindex="-1" role="dialog" aria-labelledby="pdfPreviewTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="pdfPreviewTitle">
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        <span>Vista previa del formulario</span>
                    </h4>
                    <button type="button" class="pdf-preview-close" data-dismiss="modal" aria-label="Cerrar visor PDF" title="Cerrar">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="pdfPreviewBody" aria-busy="true">
                    <div class="pdf-preview-loading" id="pdfPreviewLoading" role="status" aria-live="polite">
                        <div class="pdf-preview-loading-content">
                            <div class="pdf-preview-spinner" aria-hidden="true"></div>
                            <strong>Cargando documento…</strong>
                            <span>Espere un momento mientras preparamos el visor.</span>
                        </div>
                    </div>
                    <iframe id="pdfPreviewFrame" class="pdf-preview-frame" src="about:blank" title="Vista previa del PDF" aria-hidden="true"></iframe>
                </div>
                <div class="modal-footer">
                    <a id="pdfPreviewDownload" class="btn module-primary-btn" href="#" download>
                        <i class="fa fa-download" aria-hidden="true"></i>
                        Descargar PDF
                    </a>
                    <button type="button" class="btn module-secondary-btn" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var pdfPreviewReadyTimer = null;
        var pdfPreviewSafetyTimer = null;

        function prepararCargaPdf() {
            clearTimeout(pdfPreviewReadyTimer);
            clearTimeout(pdfPreviewSafetyTimer);
            $('#pdfPreviewBody').attr('aria-busy', 'true');
            $('#pdfPreviewLoading').removeClass('is-hidden').attr('aria-hidden', 'false');
            $('#pdfPreviewFrame').removeClass('is-ready').attr('aria-hidden', 'true');
        }

        function mostrarPdfCargado() {
            var frame = $('#pdfPreviewFrame');
            var source = frame.attr('src');
            var modal = $('#pdfPreviewModal');
            var modalAbierto = modal.hasClass('show') || modal.hasClass('in') || modal.is(':visible');
            if (!source || source === 'about:blank' || !modalAbierto) {
                return;
            }

            clearTimeout(pdfPreviewReadyTimer);
            clearTimeout(pdfPreviewSafetyTimer);
            $('#pdfPreviewBody').attr('aria-busy', 'false');
            $('#pdfPreviewLoading').addClass('is-hidden').attr('aria-hidden', 'true');
            frame.addClass('is-ready').attr('aria-hidden', 'false');
        }

        $('#pdfPreviewFrame').on('load', function() {
            var source = $(this).attr('src');
            if (!source || source === 'about:blank') {
                return;
            }

            clearTimeout(pdfPreviewReadyTimer);
            pdfPreviewReadyTimer = setTimeout(mostrarPdfCargado, 180);
        });

        $(document).on('click', '.js-pdf-preview', function(event) {
            event.preventDefault();

            var pdfUrl = $(this).attr('href');
            var pdfTitle = $(this).data('pdf-title') || 'Vista previa del formulario';

            prepararCargaPdf();
            $('#pdfPreviewTitle span').text(pdfTitle);
            $('#pdfPreviewDownload').attr('href', pdfUrl).attr('download', pdfTitle + '.pdf');
            $('#pdfPreviewModal').modal('show');
            $('#pdfPreviewFrame').attr('src', pdfUrl);
            pdfPreviewSafetyTimer = setTimeout(mostrarPdfCargado, 2500);
        });

        $('#pdfPreviewModal').on('hidden.bs.modal', function() {
            clearTimeout(pdfPreviewReadyTimer);
            clearTimeout(pdfPreviewSafetyTimer);
            $('#pdfPreviewFrame').removeClass('is-ready').attr('aria-hidden', 'true').attr('src', 'about:blank');
            $('#pdfPreviewLoading').removeClass('is-hidden').attr('aria-hidden', 'false');
            $('#pdfPreviewBody').attr('aria-busy', 'true');
            $('#pdfPreviewDownload').attr('href', '#').removeAttr('download');
        });

        function buscador() {
            $.ajax({
                url: "{{ route('buscador.ajax_emergencia') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#form_buscar').serialize(),
                type: 'POST',
                dataType: 'html',
                beforeSend: function() {
                    //$('#loading').show();
                },
                success: function(datahtml, data) {
                    //$('#loading').hide();
                    $("#tablaajax").empty().html(datahtml).fadeIn();
                },
                error: function(xhr, status) {
                    alert("{{ trans('hospitalizacion.ErrorTabla') }}");
                },
            });
        }

        function cargar_planilla(id) {
            $.ajax({
                url: "{{ url('planillar_hospital') }}/" + id,
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#form_buscar').serialize(),
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    //$('#loading').show();
                },
                success: function(datahtml, data) {
                    location.href = "{{ url('planillar_hospital/detalle') }}/" + id;
                },
                error: function(xhr, status) {
                    alert("{{ trans('hospitalizacion.ErrorTabla') }}");
                },
            });
        }
    </script>
@endsection
