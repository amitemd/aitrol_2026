@extends('layouts.app-template-h')
@section('content')
<style>
    .clinical-doc-page { max-width: 1880px; margin: 0 auto; padding: 20px 14px 45px; }
    .clinical-doc-hero { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 25px 30px; margin-bottom: 16px; border-radius: 8px; color: #fff; background: #104c70; box-shadow: 0 8px 22px rgba(15, 52, 78, .14); }
    .clinical-doc-hero__eyebrow { margin: 0 0 6px; color: #aee6ee; font-size: 11px; font-weight: 700; letter-spacing: .9px; text-transform: uppercase; }
    .clinical-doc-hero h3 { margin: 0 0 6px; color: #fff; font-size: 25px; font-weight: 700; }
    .clinical-doc-hero p { margin: 0; color: rgba(255,255,255,.82); font-size: 13px; }
    .clinical-doc-status { min-width: 190px; padding: 12px 16px; border: 1px solid rgba(255,255,255,.28); border-radius: 9px; background: rgba(255,255,255,.12); text-align: center; }
    .clinical-doc-status span { display: block; color: rgba(255,255,255,.75); font-size: 11px; text-transform: uppercase; letter-spacing: .6px; }
    .clinical-doc-status strong { display: block; margin-top: 4px; color: #fff; font-size: 14px; }
    .clinical-doc-nav { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; padding: 0 0 20px; }
    .clinical-doc-nav__group { display: flex; align-items: center; flex-wrap: wrap; gap: 10px; width: 100%; }
    .clinical-doc-nav .btn { min-height: 40px; border-radius: 6px; border-color: #c8dce8; color: #17486a; background: #fff; font-weight: 600; }
    .clinical-doc-nav .btn:hover { color: #0877be; border-color: #0877be; background: #f1f9fd; }
    .clinical-doc-section-title { display: flex; align-items: center; gap: 8px; margin: 22px 0 10px; color: #123b5d; font-size: 19px; font-weight: 700; }
    .clinical-doc-section-title i { color: #0877be; }
    .clinical-doc-table { overflow: hidden; border: 1px solid #dbe8f2; border-radius: 10px; background: #fff; box-shadow: 0 4px 14px rgba(15, 52, 78, .04); }
    .clinical-doc-table table { margin: 0; }
    .clinical-doc-table thead th { border: 0; background: #edf6fb; color: #315875; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; }
    .clinical-doc-table tbody td { vertical-align: middle; border-color: #edf2f6; color: #35536a; }
    .clinical-doc-empty { padding: 20px; color: #718596; text-align: center; }
    .clinical-doc-pending { padding: 15px 18px; margin-bottom: 20px; border: 1px solid #f2d69a; border-left: 4px solid #f0a229; border-radius: 8px; background: #fff8e9; color: #72501a; }
    .clinical-doc-pending h4 { margin: 0 0 7px; color: #855b12; font-size: 16px; }
    .clinical-doc-pending ul { margin: 0; padding-left: 18px; }
    .clinical-doc-link-history { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; }
    .clinical-doc-link-history h4 { margin-bottom: 4px; }
    @media (max-width: 767px) { .clinical-doc-page { padding: 0 4px 25px; } .clinical-doc-hero { display: block; padding: 20px; } .clinical-doc-status { margin-top: 16px; } }
    .surgery-planner { margin: 18px 0 28px; padding: 22px; border: 1px solid #dbe8f2; border-radius: 8px; background: #f8fbfd; box-shadow: 0 5px 18px rgba(15, 52, 78, .06); }
    .surgery-planner__header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 18px; }
    .surgery-planner__header h4 { margin: 0 0 5px; color: #123b5d; font-weight: 700; }
    .surgery-planner__header p { margin: 0; color: #647b8d; font-size: 13px; }
    .surgery-planner__step { display: inline-flex; align-items: center; gap: 7px; padding: 7px 11px; border-radius: 18px; background: #e4f3fb; color: #0877be; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .surgery-planner__field { padding: 16px; border: 1px solid #dbe8f2; border-radius: 9px; background: #fff; }
    .surgery-planner__field label { color: #1c4868; font-weight: 700; }
    .surgery-planner__field .help-block { margin: 6px 0 0; color: #718596; font-size: 12px; }
    .surgery-planner .select2-container { width: 100% !important; }
    .surgery-planner .select2-container--default .select2-selection--multiple { min-height: 46px; border: 1px solid #b9d3e3; border-radius: 7px; padding: 5px 7px; }
    .surgery-planner .select2-container--default.select2-container--focus .select2-selection--multiple { border-color: #0789c9; box-shadow: 0 0 0 3px rgba(8, 137, 201, .12); }
    .surgery-planner .select2-selection__choice { margin-top: 3px !important; border: 0 !important; border-radius: 14px !important; background: #dff2fb !important; color: #075985 !important; padding: 4px 8px !important; }
    .surgery-planner .select2-selection__choice__remove { color: #075985 !important; margin-right: 5px !important; }
    .surgery-planner .select2-selection--multiple .select2-selection__rendered { display: flex; flex-wrap: wrap; align-items: flex-start; gap: 3px; min-width: 0; }
    .surgery-planner .select2-selection__choice { display: inline-flex !important; align-items: flex-start; max-width: 100%; min-width: 0; white-space: normal !important; overflow-wrap: anywhere; word-break: break-word; }
    .surgery-planner .select2-selection__choice__remove { flex: 0 0 auto; line-height: 1.35; }
    .surgery-planner .select2-dropdown { border-color: #b9d3e3; }
    .surgery-planner__actions { display: flex; justify-content: flex-end; margin-top: 18px; }
    .surgery-planner__steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 8px; margin: 0 0 18px; padding: 0; list-style: none; }
    .surgery-planner__steps li { padding: 10px 12px; border-radius: 6px; background: #eef8fc; color: #315875; font-size: 12px; }
    .surgery-planner__steps strong { display: block; margin-bottom: 3px; color: #0877be; }
    .surgery-advanced { margin: 15px 0 22px; border: 1px solid #dbe8f2; border-radius: 7px; background: #fff; }
    .surgery-advanced summary { padding: 12px 15px; cursor: pointer; color: #527086; font-size: 13px; font-weight: 700; }
    .surgery-advanced summary i { margin-right: 6px; color: #0877be; }
    .surgery-advanced__body { padding: 0 15px 15px; }
    .clinical-doc-nav__caption { flex: 1 0 100%; margin: 0 0 2px; color: #61798c; font-size: 12px; font-weight: 600; }
    .clinical-doc-patient { display: block; margin-top: 12px; color: #bdeaf1; font-size: 14px; font-weight: 700; }
    .clinical-doc-summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin: 0 auto 18px; }
    .clinical-doc-summary__item { display: flex; align-items: center; gap: 12px; min-height: 68px; padding: 12px 15px; border: 1px solid #dbe8f2; border-radius: 8px; background: #fff; box-shadow: 0 3px 12px rgba(15, 52, 78, .04); }
    .clinical-doc-summary__icon { display: grid; place-items: center; width: 35px; height: 35px; flex: 0 0 35px; border-radius: 50%; background: #e6f5fa; color: #0877be; font-size: 16px; }
    .clinical-doc-summary__label { display: block; color: #718596; font-size: 11px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; }
    .clinical-doc-summary__value { display: block; margin-top: 3px; color: #123b5d; font-size: 14px; font-weight: 700; }
    .clinical-doc-block { margin-bottom: 24px; }
    .clinical-doc-block__heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 10px; }
    .clinical-doc-block__heading .clinical-doc-section-title { margin: 0; }
    .clinical-doc-badge { padding: 5px 9px; border-radius: 14px; background: #edf6fb; color: #0877be; font-size: 11px; font-weight: 700; }
    .clinical-form-help { margin: 0 0 11px; color: #61798c; font-size: 13px; }
    .clinical-document-search { margin-bottom: 17px; padding: 14px; border: 1px solid #c8e0eb; border-radius: 10px; background: #f7fcfe; box-shadow: 0 4px 14px rgba(15,52,78,.05); }
    .clinical-document-search label { display: block; margin-bottom: 7px; color: #123b5d; font-size: 13px; font-weight: 700; }
    .clinical-document-search__field { position: relative; display: flex; align-items: center; }
    .clinical-document-search__field > i { position: absolute; left: 13px; color: #0b78ad; pointer-events: none; }
    .clinical-document-search input { width: 100%; height: 43px; padding: 9px 118px 9px 37px; border: 1px solid #b9d5e3; border-radius: 7px; background: #fff; color: #173f5d; }
    .clinical-document-search input:focus { border-color: #1686bb; outline: 0; box-shadow: 0 0 0 3px rgba(22,134,187,.12); }
    .clinical-document-search__clear { position: absolute; right: 7px; min-width: 82px; height: 31px; padding: 0 9px; border: 0; border-radius: 5px; background: #e7f3f8; color: #315875; font-size: 11px; font-weight: 700; cursor: pointer; }
    .clinical-document-search__clear:hover, .clinical-document-search__clear:focus { background: #d7ebf3; color: #0b628c; outline: 0; }
    .clinical-document-search__meta { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-top: 7px; color: #61798c; font-size: 11px; }
    .clinical-document-search__count { font-weight: 700; }
    .clinical-document-search__empty { margin: 0 0 18px; padding: 17px; border: 1px dashed #b9d5e3; border-radius: 9px; background: #fff; color: #61798c; text-align: center; }
    .clinical-doc-page [hidden] { display: none !important; }
    .clinical-form-status { display: inline-block; padding: 4px 8px; border-radius: 999px; font-size: 11px; font-weight: 700; white-space: nowrap; }
    .clinical-form-status.is-available { background: #e8f7ee; color: #176b3f; }
    .clinical-form-status.is-unavailable { background: #f1f4f7; color: #65798a; }
    .clinical-doc-table tbody tr:hover { background: #f8fcfe; }
    .surgery-cycle { margin: 20px 0; padding: 20px 22px; border: 1px solid #dbe8f2; border-radius: 8px; background: #fff; box-shadow: 0 5px 18px rgba(15, 52, 78, .05); }
    .surgery-cycle__header { display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; padding-bottom: 13px; border-bottom: 1px solid #e8f0f5; }
    .surgery-cycle__header h4 { margin: 0 0 5px; color: #123b5d; font-weight: 700; }
    .surgery-cycle__meta { margin: 0; color: #688094; font-size: 13px; }
    .surgery-cycle__state { padding: 7px 11px; border-radius: 16px; background: #e7f5fb; color: #0877be; font-size: 12px; font-weight: 700; white-space: nowrap; }
    .surgery-cycle__actions { display: flex; flex-wrap: nowrap; align-items: center; gap: 8px; margin-top: 16px; overflow-x: auto; padding-bottom: 3px; }
    .surgery-cycle__actions form { display: inline-flex; flex: 0 0 auto; margin: 0; }
    .surgery-cycle__actions .btn { flex: 0 0 auto; white-space: nowrap; }
    .surgery-cycle__checklist { margin-top: 15px; padding: 12px 14px; border-radius: 6px; background: #f5f9fb; color: #587083; font-size: 13px; }
    .clinical-doc-next-step { margin-top: 22px; padding: 18px 20px; border: 1px solid #b9e4ef; border-radius: 8px; background: #effafe; }
    .clinical-doc-next-step strong { display: block; margin-bottom: 5px; color: #104c70; font-size: 16px; }
    .clinical-doc-workspace { display: grid; grid-template-columns: minmax(390px, 43%) minmax(0, 1fr); align-items: stretch; gap: 18px; }
    .clinical-doc-workspace > * { min-width: 0; }
    .clinical-doc-sidebar .clinical-doc-section-title { font-size: 16px; }
    .clinical-doc-sidebar .clinical-doc-block { margin-bottom: 18px; }
    .clinical-form-list { max-height: 610px; overflow-y: auto; border: 1px solid #dbe8f2; border-radius: 9px; background: #fff; }
    .clinical-report-list { max-height: 380px; }
    .clinical-report-group { padding: 8px 12px; border-bottom: 1px solid #dbe8f2; background: #eef7fb; color: #315875; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
    .clinical-form-group { position: sticky; top: 0; z-index: 1; padding: 8px 12px; border-bottom: 1px solid #d4e5ee; background: #e8f4f9; color: #315875; font-size: 11px; font-weight: 700; letter-spacing: .45px; text-transform: uppercase; }
    .clinical-form-item { padding: 10px 12px; border-bottom: 1px solid #e8f0f5; }
    .clinical-form-item:last-child { border-bottom: 0; }
    .clinical-form-item__top, .clinical-form-item__bottom { display: flex; align-items: center; gap: 8px; }
    .clinical-form-item__top { margin-bottom: 5px; }
    .clinical-form-item__name { flex: 1; min-width: 0; color: #123b5d; font-size: 13px; font-weight: 700; }
    .clinical-form-code { display: inline-block; min-width: 40px; padding: 3px 5px; border-radius: 5px; background: #eaf6fb; color: #0b628c; font-size: 12px; font-weight: 700; text-align: center; }
    .clinical-form-edition { color: #6b7f8e; font-size: 10px; font-weight: 700; white-space: nowrap; }
    .clinical-form-item__bottom { padding-left: 48px; flex-wrap: wrap; }
    .clinical-form-item__context { flex: 1; min-width: 95px; color: #61798c; font-size: 11px; }
    .clinical-form-item__unavailable { color: #718596; font-size: 11px; }
    .clinical-form-item__hint { margin: 5px 0 0 48px; color: #61798c; font-size: 11px; line-height: 1.35; }
    .clinical-form-item .btn { flex: 0 0 auto; }
    .clinical-form-item .btn-primary.js-document-preview { color: #fff !important; }
    .clinical-doc-records { overflow: hidden; border: 1px solid #dbe8f2; border-radius: 9px; background: #fff; }
    .clinical-doc-record { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px; border-bottom: 1px solid #e8f0f5; }
    .clinical-doc-record:last-child { border-bottom: 0; }
    .clinical-doc-record__title { display: block; color: #123b5d; font-size: 13px; }
    .clinical-doc-record__meta { display: block; margin-top: 3px; color: #61798c; font-size: 11px; }
    .clinical-doc-record__actions { display: flex; flex: 0 0 auto; gap: 5px; }
    .clinical-doc-viewer { position: sticky; top: 14px; display: flex; flex-direction: column; align-self: stretch; overflow: hidden; border: 1px solid #c6dfed; border-radius: 10px; background: #fff; box-shadow: 0 7px 24px rgba(15, 52, 78, .08); }
    .clinical-doc-viewer__header { display: flex; align-items: center; justify-content: space-between; gap: 12px; min-height: 58px; padding: 12px 16px; border-bottom: 1px solid #dbe8f2; background: #f4faff; }
    .clinical-doc-viewer__header h4 { margin: 0; color: #123b5d; font-size: 16px; font-weight: 700; overflow-wrap: anywhere; }
    .clinical-doc-viewer__actions { display: flex; flex: 0 0 auto; gap: 6px; }
    .clinical-doc-viewer__placeholder { display: grid; flex: 1 1 auto; place-content: center; min-height: min(72vh, 760px); padding: 30px; color: #61798c; text-align: center; }
    .clinical-doc-viewer__placeholder i { margin-bottom: 10px; color: #a6cadb; font-size: 42px; }
    .clinical-doc-viewer__placeholder strong { display: block; margin-bottom: 4px; color: #315875; font-size: 16px; }
    .clinical-doc-viewer__frames { position: relative; display: none; flex: 1 1 auto; min-height: 550px; background: #fff; }
    .clinical-doc-viewer.is-open .clinical-doc-viewer__frames { display: flex; flex-direction: column; }
    .clinical-doc-viewer__frames iframe { display: none; flex: 1 1 auto; width: 100%; height: 100%; min-height: 550px; border: 0; background: #fff; }
    .clinical-doc-viewer__frames iframe.is-active { display: block; }
    .clinical-doc-viewer__loading { position: absolute; inset: 0; z-index: 3; display: flex; align-items: center; justify-content: center; gap: 10px; background: rgba(255,255,255,.93); color: #315875; font-size: 13px; font-weight: 700; }
    .clinical-doc-viewer__spinner { width: 24px; height: 24px; border: 3px solid #cce4ef; border-top-color: #0877be; border-radius: 50%; animation: clinical-doc-spin .75s linear infinite; }
    @keyframes clinical-doc-spin { to { transform: rotate(360deg); } }
    .clinical-doc-viewer.is-open .clinical-doc-viewer__placeholder { display: none; }
    .clinical-doc-viewer [hidden] { display: none !important; }
    .clinical-doc-preview-help { margin: 0; padding: 8px 14px; border-top: 1px solid #e8f0f5; color: #718596; font-size: 11px; }
    .clinical-doc-record .js-document-preview.is-selected, .clinical-form-item .js-document-preview.is-selected, .clinical-form-item .js-document-editor.is-selected { color: #fff !important; background: #0b6f8d; border-color: #0b6f8d; }
    @media (max-width: 1100px) { .clinical-doc-workspace { grid-template-columns: 1fr; } .clinical-doc-viewer { position: static; } .clinical-doc-viewer__frames iframe { height: min(76vh, 820px); } .clinical-form-list { max-height: 460px; } }
    @media (max-width: 767px) { .clinical-doc-record { align-items: flex-start; flex-direction: column; } .clinical-doc-viewer__header { align-items: flex-start; flex-direction: column; } .clinical-doc-viewer__placeholder { min-height: 300px; } .clinical-doc-viewer__frames, .clinical-doc-viewer__frames iframe { min-height: 540px; } .clinical-form-item__top { flex-wrap: wrap; } .clinical-form-status { margin-left: 48px; } .clinical-document-search input { padding-right: 96px; } .clinical-document-search__meta { align-items: flex-start; flex-direction: column; } }
    @media (max-width: 767px) { .surgery-cycle { padding: 16px; } .surgery-cycle__header { display: block; } .surgery-cycle__state { display: inline-block; margin-top: 10px; } .surgery-cycle__actions .btn, .surgery-cycle__actions form { width: 100%; } .surgery-cycle__actions form .btn { width: 100%; } }
    @media (max-width: 767px) { .surgery-planner { padding: 15px; } .surgery-planner__header { display: block; } .surgery-planner__step { margin-top: 12px; } .surgery-planner__actions { justify-content: stretch; } .surgery-planner__actions .btn { width: 100%; } }
    @media (max-width: 767px) { .surgery-planner__steps { grid-template-columns: 1fr; } }
    @media (max-width: 767px) { .clinical-doc-summary { grid-template-columns: 1fr; } .clinical-doc-block__heading { align-items: flex-start; flex-direction: column; } }
</style>
<section class="content clinical-doc-page">
    @php $patient = $solicitud->paciente; @endphp
    <div class="clinical-doc-hero">
        <div><p class="clinical-doc-hero__eyebrow">Módulo hospitalario · Registro clínico</p><h3><i class="fa fa-folder-open mr-2"></i>Formularios y documentos del episodio {{ $solicitud->id }}</h3><p>Consulta y completa la documentación clínica del episodio.</p><span class="clinical-doc-patient"><i class="fa fa-user mr-1"></i>{{ mb_strtoupper(trim((optional($patient)->apellido1 ?? '').' '.(optional($patient)->apellido2 ?? '').' '.(optional($patient)->nombre1 ?? '').' '.(optional($patient)->nombre2 ?? '')), 'UTF-8') }} · ID {{ $solicitud->id_paciente }}</span></div>
        <div class="clinical-doc-status"><span>Estado clínico</span><strong>{{ mb_strtoupper($encounter->clinical_state ?: $encounter->status, 'UTF-8') }}</strong>@if($readOnly)<span class="mt-1">Solo lectura</span>@endif</div>
    </div>
    @if(session('status'))<div class="alert alert-success">{{ session('status') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    @if((int) $solicitud->estado_paso === 3 && !$solicitud->id_agenda && !$readOnly)
        <div class="clinical-doc-pending clinical-doc-link-history">
            <div><h4>Historia 008 pendiente de vincular</h4><span>Complete el ingreso original de Emergencia para habilitar la evolución clínica. La cama asignada se conserva.</span></div>
            <a class="btn btn-primary" href="{{ route('hospital.ingreso008', ['id_solicitud' => $solicitud->id]) }}">Vincular Historia 008</a>
        </div>
    @endif
    <div class="clinical-doc-summary">
        <div class="clinical-doc-summary__item"><span class="clinical-doc-summary__icon"><i class="fa fa-file-pdf-o"></i></span><div><span class="clinical-doc-summary__label">Reportes PDF</span><strong class="clinical-doc-summary__value">{{ count($reportCatalog) }} disponibles</strong></div></div>
        <div class="clinical-doc-summary__item"><span class="clinical-doc-summary__icon"><i class="fa fa-medkit"></i></span><div><span class="clinical-doc-summary__label">Procedimientos</span><strong class="clinical-doc-summary__value">{{ count($cycles) }} quirúrgico(s)</strong></div></div>
        <div class="clinical-doc-summary__item"><span class="clinical-doc-summary__icon"><i class="fa fa-tasks"></i></span><div><span class="clinical-doc-summary__label">Acceso</span><strong class="clinical-doc-summary__value">{{ $readOnly ? 'Solo lectura' : 'Edición disponible' }}</strong></div></div>
    </div>
    <div class="clinical-doc-nav">
        <div class="clinical-doc-nav__caption">Acciones principales del episodio</div>
        <div class="clinical-doc-nav__group">
        @if(Route::has('hospital.index_modulos'))
            <a class="btn btn-default" href="{{ route('hospital.index_modulos', ['id_paso' => $solicitud->estado_paso]) }}"><i class="fa fa-arrow-left"></i> Hospital</a>
        @endif
        @foreach($links as $link)
            @if($link['key'] === 'evolution')
                <a class="btn btn-primary" href="{{ route('formulario005.index_005', ['id_solicitud' => $solicitud->id]) }}"><i class="fa fa-file-text-o"></i> {{ mb_strtoupper($link['label'], 'UTF-8') }}</a>
            @else
                <a class="btn btn-default" href="{{ route('hospital.documents.link', ['id_solicitud' => $solicitud->id, 'key' => $link['key']]) }}"><i class="fa fa-file-text-o"></i> {{ $link['label'] }}</a>
            @endif
        @endforeach
        </div>
    </div>
    @if(count($pending))
        <div class="clinical-doc-pending"><h4><i class="fa fa-exclamation-triangle mr-1"></i>Documentación quirúrgica pendiente</h4><ul>@foreach($pending as $message)<li>{{ $message }}</li>@endforeach</ul></div>
    @endif
    <div class="clinical-doc-workspace">
    <div class="clinical-doc-sidebar">
    <div class="clinical-document-search" role="search" aria-labelledby="episode-document-search-label">
        <label id="episode-document-search-label" for="episode-document-search"><i class="fa fa-search mr-1" aria-hidden="true"></i>Buscar reportes y formularios</label>
        <div class="clinical-document-search__field">
            <i class="fa fa-search" aria-hidden="true"></i>
            <input id="episode-document-search" type="search" placeholder="Código, nombre, especialidad o categoría" autocomplete="off" aria-describedby="episode-document-search-help episode-document-search-count">
            <button id="episode-document-search-clear" class="clinical-document-search__clear" type="button" hidden>Limpiar</button>
        </div>
        <div class="clinical-document-search__meta">
            <span id="episode-document-search-help">Ejemplos: 113, laboratorio, UCI o epicrisis.</span>
            <span id="episode-document-search-count" class="clinical-document-search__count" aria-live="polite">{{ count($reportCatalog) + count($formCatalog) }} disponibles</span>
        </div>
    </div>
    <div id="episode-document-search-empty" class="clinical-document-search__empty" role="status" hidden>
        <i class="fa fa-search mr-1" aria-hidden="true"></i>No hay reportes ni formularios que coincidan con la búsqueda.
    </div>
    @include('hospital.documentos.reportes', ['reportCatalog' => $reportCatalog])
    @include('hospital.documentos.catalogo', ['formCatalog' => $formCatalog])
    <div class="clinical-doc-block" id="registro-documental"><div class="clinical-doc-block__heading"><h4 class="clinical-doc-section-title"><i class="fa fa-files-o"></i>Registro documental</h4><span class="clinical-doc-badge">{{ $documents->count() }} documento(s)</span></div>
    <div class="clinical-doc-records">
        @forelse($documents as $document)
            <div class="clinical-doc-record">
                <div><strong class="clinical-doc-record__title">{{ $document->title }}</strong><span class="clinical-doc-record__meta">{{ $document->signed_at ? 'Firmado' : ($document->status === 'PLACEHOLDER' ? 'Sin contenido clínico' : 'Sin firma') }}@if($document->signed_at) · {{ $document->signed_by }} · {{ $document->signed_at }}@endif · {{ $document->amendments->count() }} adenda(s)</span></div>
                <div class="clinical-doc-record__actions">
                    <button type="button" class="btn btn-primary btn-xs js-document-preview" data-preview-url="{{ route('hospital.documents.preview', ['id_solicitud' => $solicitud->id, 'document' => $document->id]) }}" data-full-url="{{ route('hospital.documents.document', ['id_solicitud' => $solicitud->id, 'document' => $document->id]) }}" data-preview-title="Registro: {{ $document->title }}">Ver registro</button>
                    <a class="btn btn-default btn-xs" href="{{ route('hospital.documents.document', ['id_solicitud' => $solicitud->id, 'document' => $document->id]) }}" target="_blank" rel="noopener" title="Abrir documento aparte"><i class="fa fa-external-link"></i></a>
                </div>
            </div>
        @empty
            <div class="clinical-doc-empty"><i class="fa fa-inbox fa-2x d-block mb-2"></i>Sin documentos incorporados al registro canónico.</div>
        @endforelse
    </div></div>
    </div>
    <aside class="clinical-doc-viewer" id="episodeDocumentViewer" aria-label="Visor de documentos">
        <div class="clinical-doc-viewer__header"><h4 id="episodeDocumentTitle"><i class="fa fa-file-text-o mr-1"></i>Vista del documento</h4><div class="clinical-doc-viewer__actions"><a id="episodeDocumentOpen" class="btn btn-default btn-xs" href="#" target="_blank" rel="noopener" hidden><i class="fa fa-external-link"></i> Abrir aparte</a><button id="episodeDocumentClose" class="btn btn-default btn-xs" type="button" hidden>Cerrar</button></div></div>
        <div class="clinical-doc-viewer__placeholder"><div><i class="fa fa-file-text-o"></i><strong>Seleccione un formulario o documento</strong>Pulse «Completar», «Continuar», «Ver» o «Ver aquí» para abrirlo sin salir de esta pantalla.</div></div>
        <div id="episodeDocumentFrames" class="clinical-doc-viewer__frames">
            <iframe id="episodeDocumentFrame" title="Contenido del documento" src="about:blank"></iframe>
            <div id="episodeDocumentLoading" class="clinical-doc-viewer__loading" role="status" aria-live="polite" hidden><span class="clinical-doc-viewer__spinner" aria-hidden="true"></span><span>Preparando documento…</span></div>
        </div>
        <p class="clinical-doc-preview-help">Las acciones disponibles se abren dentro de este panel. Si alguna pantalla no permite vista integrada, use «Abrir aparte».</p>
    </aside>
    </div>
    <div class="clinical-doc-block" id="formularios-quirurgicos"><div class="clinical-doc-block__heading"><h4 class="clinical-doc-section-title"><i class="fa fa-medkit"></i>Procedimientos quirúrgicos</h4><span class="clinical-doc-badge">{{ count($cycles) }} registrado(s)</span></div>
    @php $canOperateSurgery = Auth::user()->hasAnyUserTypeName(['SISTEMAS', 'DOCTORES', 'DOCTOR', 'ANESTESIÓLOGO', 'ANESTESIOLOGO']); @endphp
    @if($operational && $canOperateSurgery && (int) $solicitud->estado_paso === 5)
        @if($surgicalReceipt)
            @php
                $plannedStartDefault = date('Y-m-d\\TH:i');
                $plannedEndDefault = date('Y-m-d\\TH:i', strtotime('+1 hour'));
            @endphp
            <div class="surgery-planner">
                <div class="surgery-planner__header">
                    <div><h4><i class="fa fa-calendar-plus-o mr-1"></i> Paso 1: Planificar la cirugía</h4><p>Completa estos datos y pulsa guardar.</p></div>
                    <span class="surgery-planner__step"><i class="fa fa-map-marker"></i> {{ $surgicalReceipt->sala ? mb_strtoupper($surgicalReceipt->sala->nombre_sala, 'UTF-8') : 'SALA NO REGISTRADA' }}</span>
                </div>
                @if($surgicalReceipt->observaciones)<div class="alert alert-info"><i class="fa fa-info-circle mr-1"></i>{{ $surgicalReceipt->observaciones }}</div>@endif
                <ol class="surgery-planner__steps">
                    <li><strong>1. Elige la cirugía</strong>Selecciona uno o varios procedimientos.</li>
                    <li><strong>2. Elige el médico</strong>Selecciona quién será responsable.</li>
                    <li><strong>3. Define el horario</strong>Revisa las fechas y guarda.</li>
                </ol>
                <form method="POST" action="{{ route('hospital.documents.create', ['id_solicitud' => $solicitud->id]) }}" id="surgeryPlannerForm">
                {{ csrf_field() }}
                <input type="hidden" name="sala" value="{{ $surgicalReceipt->id_sala }}">
                <div class="row">
                    <div class="form-group col-md-7"><div class="surgery-planner__field"><label for="planned_procedures">Procedimiento pospuesto o propuesto *</label>
                        <select class="form-control" id="planned_procedures" name="procedimiento[]" multiple required data-placeholder="Busque por nombre y seleccione uno o varios">
                            @foreach($catalog as $item)<option value="{{ $item->id }}" @if(in_array($item->id, $plannedProcedureIds ?: [])) selected @endif>{{ mb_strtoupper($item->nombre, 'UTF-8') }}</option>@endforeach
                        </select><div class="help-block">Escriba para buscar. Las opciones seleccionadas aparecerán como etiquetas.</div>
                    </div></div>
                    <div class="form-group col-md-5"><div class="surgery-planner__field"><label for="planned_doctor">Médico responsable *</label>
                        <select class="form-control" id="planned_doctor" name="doctor" required><option value="">Seleccione el médico</option>
                            @foreach($doctors as $doctor)<option value="{{ $doctor->id }}" @if((string) $plannedDoctorId === (string) $doctor->id) selected @endif>{{ mb_strtoupper(trim($doctor->apellido1.' '.$doctor->apellido2.' '.$doctor->nombre1), 'UTF-8') }}</option>@endforeach
                        </select>
                    </div></div>
                    <div class="form-group col-md-6"><div class="surgery-planner__field"><label for="planned_start">Inicio previsto *</label><input id="planned_start" class="form-control" type="datetime-local" name="fecha" value="{{ $plannedStart ?: $plannedStartDefault }}" required></div></div>
                    <div class="form-group col-md-6"><div class="surgery-planner__field"><label for="planned_end">Fin previsto *</label><input id="planned_end" class="form-control" type="datetime-local" name="fechafin" value="{{ $plannedEnd ?: $plannedEndDefault }}" required></div></div>
                </div>
                <div class="surgery-planner__actions"><button class="btn btn-primary" type="submit"><i class="fa fa-check mr-1"></i> Guardar planificación quirúrgica</button></div>
                </form>
            </div>
        @endif
        <details class="surgery-advanced">
            <summary><i class="fa fa-link"></i> Opciones avanzadas: usar una cirugía ya existente</summary>
            <div class="surgery-advanced__body">
                <p class="text-muted">Úsalo solo si la cirugía ya fue creada antes y no aparece en el paso anterior.</p>
        <form method="POST" action="{{ route('hospital.documents.bind', ['id_solicitud' => $solicitud->id]) }}">
            {{ csrf_field() }}
            <div class="row">
                <div class="form-group col-md-6"><label for="procedure_id">Procedimiento del episodio</label>
                    <select id="procedure_id" name="procedure_id" class="form-control" required>
                        <option value="">Seleccione</option>
                        @foreach($procedures as $procedure)<option value="{{ $procedure->id }}">{{ $procedure->id }} - Historia {{ $procedure->id_hc }}</option>@endforeach
                    </select>
                </div>
                <div class="form-group col-md-6"><button type="submit" class="btn btn-primary"><i class="fa fa-link"></i> Vincular a recepcion actual</button></div>
            </div>
        </form>
            </div>
        </details>
    @endif
    @foreach($cycles as $cycle)
        <?php $procedureId = $cycle['binding']->to_value; $params = ['id_solicitud' => $solicitud->id, 'procedure' => $procedureId]; ?>
        @php
            $procedureStatus = !$cycle['started'] ? 'Pendiente de preparacion'
                : (!$cycle['finished'] ? 'En quirofano / operacion en curso'
                : (!$cycle['recovered'] ? 'Cirugia finalizada / recuperacion pendiente' : 'Recuperacion completada'));
        @endphp
        <div class="surgery-cycle">
        <div class="surgery-cycle__header"><div><h4><i class="fa fa-stethoscope mr-1"></i>Procedimiento {{ $procedureId }}</h4><p class="surgery-cycle__meta">Inicio: {{ $cycle['started'] ? $cycle['started']->created_at : 'Pendiente' }} · Protocolo: {{ $cycle['finished'] ? 'Finalizado' : 'Pendiente' }} · Recuperación: {{ $cycle['recovered'] ? 'Firmada' : 'Pendiente' }}</p></div><span class="surgery-cycle__state">{{ mb_strtoupper($procedureStatus, 'UTF-8') }}</span></div>
        <div class="surgery-cycle__actions">
            @foreach(['protocol_pdf' => 'PDF protocolo', 'anesthesia_pdf' => '018 - Anestesia'] as $key => $label)
                <a class="btn btn-default" href="{{ route('hospital.documents.link', ['id_solicitud' => $solicitud->id, 'key' => $key, 'procedure_id' => $procedureId]) }}"><i class="fa fa-file-pdf-o"></i> {{ $label }}</a>
            @endforeach
            <a class="btn btn-default" href="{{ route('hospital.documents.recovery', $params) }}"><i class="fa fa-file-text-o"></i> Postanestesico</a>
            @if($cycle['current'] && !$readOnly && $canOperateSurgery)
            @if($operational)
                @if(!$cycle['started'])
                    <form method="POST" action="{{ route('hospital.documents.prepare', $params) }}">
                        {{ csrf_field() }}<button class="btn btn-default" type="submit"><i class="fa fa-plus"></i> Preparar procedimiento</button>
                    </form>
                @endif
                <a class="btn btn-default" href="{{ route('hospital.documents.link', ['id_solicitud' => $solicitud->id, 'key' => 'preop', 'procedure_id' => $procedureId]) }}"><i class="fa fa-edit"></i> Preanestesico existente</a>
                @if($cycle['started'])<a class="btn btn-default" href="{{ route('hospital.documents.link', ['id_solicitud' => $solicitud->id, 'key' => 'protocol', 'procedure_id' => $procedureId]) }}"><i class="fa fa-edit"></i> Protocolo existente</a>@endif
            @endif
            @endif
        </div>
            <form method="POST" action="{{ route('hospital.documents.sign', $params) }}">
                {{ csrf_field() }}
                <div class="row">
                    <div class="form-group col-md-4"><label>Documento</label><select class="form-control" name="kind"><option value="preop">Valoracion preanestesica</option>@if($canOperateSurgery && $cycle['started'])<option value="protocol">Protocolo</option>@endif</select></div>
                    <div class="form-group col-md-6"><label>Motivo de adenda, si esta firmado</label><input class="form-control" name="amendment_reason" maxlength="2000"></div>
                    <div class="form-group col-md-2"><button class="btn btn-primary" type="submit"><i class="fa fa-pencil"></i> Firmar</button></div>
                </div>
            </form>
            @if($canOperateSurgery && $operational && !$cycle['started'])
                <div class="surgery-cycle__checklist"><strong>Antes de iniciar:</strong> firme la valoración preanestésica y complete identidad, indicación, consentimiento y checklist.</div>
                <form method="POST" action="{{ route('hospital.documents.start', $params) }}">
                    {{ csrf_field() }}
                    @include('hospital.habitacion._procedure_safety')
                    <button class="btn btn-primary" type="submit"><i class="fa fa-play"></i> Iniciar procedimiento</button>
                </form>
            @endif
            @if($canOperateSurgery && $operational && $cycle['started'] && !$cycle['finished'])
                <div class="surgery-cycle__checklist"><strong>Operación en curso:</strong> registre y firme el protocolo antes de finalizar la cirugía.</div>
                <form method="POST" action="{{ route('hospital.documents.finish', $params) }}">
                    {{ csrf_field() }}<button class="btn btn-primary" type="submit"><i class="fa fa-check"></i> Finalizar e ingresar a recuperacion</button>
                </form>
            @endif
        </div>
    @endforeach
    @php
        $allSurgicalCyclesRecovered = count($cycles) > 0 && collect($cycles)->every(function ($cycle) {
            return $cycle['recovered'];
        });
    @endphp
    @if($operational && $canOperateSurgery && $surgicalReceipt && $allSurgicalCyclesRecovered)
        <hr>
        <div class="clinical-doc-next-step">
            <strong><i class="fa fa-check-circle mr-1"></i>Cirugía y recuperación completadas</strong>
            El siguiente paso es definir el destino postoperatorio y solicitar la recepción del paciente.
            <div style="margin-top:10px;">
                <a class="btn btn-primary" href="{{ route('hospital.traspaso_modulo', ['id_solicitud' => $solicitud->id]) }}">
                    <i class="fa fa-bed"></i> Solicitar hospitalización / UCI
                </a>
            </div>
        </div>
    @elseif($operational && $canOperateSurgery && $surgicalReceipt)
        <hr>
        <div class="alert alert-warning">
            Complete y firme el protocolo quirúrgico y la recuperación postanestésica antes de solicitar el traslado.
        </div>
    @endif
    </div>
</section>
<script>
    (function () {
        var input = document.getElementById('episode-document-search');
        var clearButton = document.getElementById('episode-document-search-clear');
        var count = document.getElementById('episode-document-search-count');
        var empty = document.getElementById('episode-document-search-empty');
        if (!input) return;

        var items = Array.prototype.slice.call(document.querySelectorAll('[data-document-search]'));
        var sections = Array.prototype.slice.call(document.querySelectorAll('[data-document-search-section]'));
        var normalize = function (value) {
            var normalized = (value || '').toLocaleLowerCase('es');
            return typeof normalized.normalize === 'function'
                ? normalized.normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                : normalized;
        };
        var update = function () {
            var terms = normalize(input.value.trim()).split(/\s+/).filter(Boolean);
            var visible = 0;

            items.forEach(function (item) {
                var searchable = normalize(item.getAttribute('data-document-search'));
                var show = terms.every(function (term) { return searchable.indexOf(term) !== -1; });
                item.hidden = !show;
                if (show) visible++;
            });

            sections.forEach(function (section) {
                var groups = Array.prototype.slice.call(section.querySelectorAll('[data-document-search-group]'));
                groups.forEach(function (group) {
                    var next = group.nextElementSibling;
                    var hasVisible = false;
                    while (next && !next.hasAttribute('data-document-search-group')) {
                        if (next.hasAttribute('data-document-search') && !next.hidden) hasVisible = true;
                        next = next.nextElementSibling;
                    }
                    group.hidden = !hasVisible;
                });
                section.hidden = !section.querySelector('[data-document-search]:not([hidden])');
            });

            clearButton.hidden = terms.length === 0;
            empty.hidden = visible !== 0;
            count.textContent = terms.length
                ? visible + (visible === 1 ? ' resultado' : ' resultados')
                : items.length + ' disponibles';
        };

        input.addEventListener('input', update);
        input.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && input.value) {
                input.value = '';
                update();
            }
        });
        clearButton.addEventListener('click', function () {
            input.value = '';
            update();
            input.focus();
        });
        update();
    })();

    (function () {
        var viewer = document.getElementById('episodeDocumentViewer');
        var frameStack = document.getElementById('episodeDocumentFrames');
        var editorFrame = document.getElementById('episodeDocumentFrame');
        var loading = document.getElementById('episodeDocumentLoading');
        var title = document.getElementById('episodeDocumentTitle');
        var openLink = document.getElementById('episodeDocumentOpen');
        var closeButton = document.getElementById('episodeDocumentClose');
        var selectedButton = null;
        var activeFrame = null;
        var activeUrl = '';
        var pdfFrames = new Map();
        var cacheLimit = 6;
        var prefetchLimit = 3;
        var prefetchQueue = [];
        var prefetchStarted = false;
        var prefetchRunning = false;

        function setLoading(visible) {
            loading.hidden = !visible;
        }

        function deactivateFrames() {
            Array.prototype.forEach.call(frameStack.querySelectorAll('iframe.is-active'), function (item) {
                item.classList.remove('is-active');
                item.setAttribute('aria-hidden', 'true');
            });
        }

        function activateFrame(frame, url, loaded) {
            deactivateFrames();
            activeFrame = frame;
            activeUrl = url;
            frame.classList.add('is-active');
            frame.setAttribute('aria-hidden', 'false');
            setLoading(!loaded);
        }

        function trimPdfCache() {
            while (pdfFrames.size > cacheLimit) {
                var candidateUrl = '';
                var candidate = null;
                pdfFrames.forEach(function (entry, url) {
                    if (entry.frame === activeFrame || !entry.loaded) return;
                    if (!candidate || entry.lastUsed < candidate.lastUsed) {
                        candidate = entry;
                        candidateUrl = url;
                    }
                });
                if (!candidate) return;
                candidate.frame.remove();
                pdfFrames.delete(candidateUrl);
            }
        }

        function schedulePrefetch() {
            if (!prefetchQueue.length || prefetchRunning) return;
            var run = function () { prefetchNext(); };
            if ('requestIdleCallback' in window) window.requestIdleCallback(run, { timeout: 1800 });
            else window.setTimeout(run, 350);
        }

        function createPdfFrame(url, prefetch) {
            var frame = document.createElement('iframe');
            var entry = { frame: frame, loaded: false, lastUsed: Date.now(), prefetch: !!prefetch };
            frame.title = prefetch ? 'Documento PDF precargado' : 'Contenido del documento';
            frame.loading = 'eager';
            frame.setAttribute('aria-hidden', 'true');
            frame.addEventListener('load', function () {
                entry.loaded = true;
                entry.lastUsed = Date.now();
                if (activeFrame === frame && activeUrl === url) setLoading(false);
                if (entry.prefetch) {
                    prefetchRunning = false;
                    schedulePrefetch();
                } else if (!prefetchStarted) {
                    startPrefetch();
                }
                trimPdfCache();
            });
            pdfFrames.set(url, entry);
            frame.src = url;
            frameStack.insertBefore(frame, loading);
            return entry;
        }

        function showPdf(url) {
            var entry = pdfFrames.get(url);
            if (!entry) entry = createPdfFrame(url, false);
            entry.lastUsed = Date.now();
            activateFrame(entry.frame, url, entry.loaded);
            trimPdfCache();
        }

        function showEditor(url) {
            var alreadyLoaded = editorFrame.getAttribute('data-loaded-url') === url;
            activateFrame(editorFrame, url, alreadyLoaded);
            if (alreadyLoaded) return;
            editorFrame.setAttribute('data-loaded-url', url);
            editorFrame.onload = function () {
                if (activeFrame === editorFrame && activeUrl === url) setLoading(false);
            };
            editorFrame.src = url;
        }

        function startPrefetch() {
            prefetchStarted = true;
            var seen = new Set();
            document.querySelectorAll('.js-document-preview[data-prefetch-pdf="1"]').forEach(function (button) {
                try {
                    var url = new URL(button.getAttribute('data-preview-url'), window.location.href);
                    if (url.origin !== window.location.origin || seen.has(url.href) || pdfFrames.has(url.href)) return;
                    if (prefetchQueue.length >= prefetchLimit) return;
                    seen.add(url.href);
                    prefetchQueue.push(url.href);
                } catch (error) {}
            });
            schedulePrefetch();
        }

        function prefetchNext() {
            if (!prefetchQueue.length) {
                prefetchRunning = false;
                return;
            }
            var url = prefetchQueue.shift();
            if (pdfFrames.has(url)) {
                schedulePrefetch();
                return;
            }
            prefetchRunning = true;
            createPdfFrame(url, true);
        }

        function openPreview(button, scrollToViewer) {
            var url;
            var fullUrl;
            try {
                url = new URL(button.getAttribute('data-preview-url') || button.getAttribute('href'), window.location.href);
                fullUrl = new URL(button.getAttribute('data-full-url') || url.href, window.location.href);
            } catch (error) { return; }
            if (url.origin !== window.location.origin || fullUrl.origin !== window.location.origin) { return; }

            if (selectedButton) {
                selectedButton.classList.remove('is-selected');
                selectedButton.setAttribute('aria-pressed', 'false');
            }
            selectedButton = button;
            button.classList.add('is-selected');
            button.setAttribute('aria-pressed', 'true');
            title.textContent = button.getAttribute('data-preview-title') || 'Vista del documento';
            openLink.href = fullUrl.href;
            openLink.hidden = false;
            closeButton.hidden = false;
            viewer.classList.add('is-open');
            if (button.matches('[data-form-pdf="1"], [data-form-template="1"]')) showPdf(url.href);
            else showEditor(url.href);
            if (scrollToViewer && window.innerWidth <= 1100) { viewer.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        }

        document.querySelectorAll('.js-document-preview, .js-document-editor').forEach(function (button) {
            button.setAttribute('aria-pressed', 'false');
            button.addEventListener('click', function (event) {
                event.preventDefault();
                openPreview(button, true);
            });
        });
        closeButton.addEventListener('click', function () {
            deactivateFrames();
            activeFrame = null;
            activeUrl = '';
            setLoading(false);
            viewer.classList.remove('is-open');
            title.textContent = 'Vista del documento';
            openLink.hidden = true;
            closeButton.hidden = true;
            if (selectedButton) {
                selectedButton.classList.remove('is-selected');
                selectedButton.setAttribute('aria-pressed', 'false');
                selectedButton = null;
            }
        });

        var firstDocument = document.querySelector('.js-document-preview[data-form-code="006"][data-official-pdf="1"]')
            || document.querySelector('.js-document-preview[data-form-pdf="1"][data-official-pdf="1"]')
            || document.querySelector('.js-document-preview[data-form-pdf="1"]');
        if (firstDocument) { openPreview(firstDocument, false); }
    })();
    $(function () {
        var procedureSelect = $('#planned_procedures');
        if (procedureSelect.length && $.fn.select2) {
            procedureSelect.select2({
                width: '100%',
                placeholder: procedureSelect.data('placeholder'),
                closeOnSelect: false,
                language: { noResults: function () { return 'No se encontraron procedimientos'; }, searching: function () { return 'Buscando...'; } }
            });
        }
        $('#surgeryPlannerForm').on('submit', function (event) {
            var start = $('#planned_start').val();
            var end = $('#planned_end').val();
            if (start && end && new Date(end) <= new Date(start)) {
                event.preventDefault();
                alert('La fecha de fin debe ser posterior a la fecha de inicio.');
            }
        });
    });
</script>
@endsection
