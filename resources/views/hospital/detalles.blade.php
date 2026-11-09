@extends('layouts.app-template-h')

@section('content')
<style>
    .emergency-record-page {
        --clinical-navy: #0b3a5b;
        --clinical-blue: #087fb6;
        --clinical-border: #d8e6ee;
        padding: 24px 18px 42px;
        background: #f3f7fa;
        min-height: calc(100vh - 80px);
    }
    .emergency-record-page .emergency-record-shell {
        max-width: 1800px;
        margin: 0 auto;
    }
    .emergency-record-page .hospital-episode-links {
        margin-bottom: 20px;
    }
    .emergency-record-page .emergency-record-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }
    .emergency-record-page #content,
    .emergency-record-page .emergency-record-summary {
        min-width: 0;
    }
    .emergency-record-page #content > .card {
        width: 100%;
        margin: 0;
        overflow: hidden;
        border: 1px solid var(--clinical-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(30, 79, 107, .08);
    }
    .emergency-record-page #content > .card > .card-header {
        max-height: none;
        padding: 14px 18px;
        border: 0;
        background-color: #0b4f7d !important;
        background-image: linear-gradient(105deg, #0b4f7d 0%, #0877a9 58%, #19aec0 100%) !important;
    }
    .emergency-record-page #content > .card > .card-header .card-title,
    .emergency-record-page #content > .card > .card-header .colorbasic {
        margin: 0;
        color: #fff !important;
        font-size: 17px !important;
        font-weight: 800 !important;
    }
    .emergency-record-page #content > .card > .card-body {
        padding: 16px 18px 20px;
        color: #294b60;
    }
    .emergency-record-page #content .card-body b {
        color: #24485f !important;
        font-size: 12px !important;
        font-weight: 800 !important;
    }
    .emergency-record-page #content .card-body span,
    .emergency-record-page #content .card-body td,
    .emergency-record-page #content .card-body div {
        color: #405f72;
        font-size: 13px;
        font-weight: 500;
    }
    .emergency-record-page #content .form-control {
        min-height: 38px;
        border-color: #cbdce6;
        border-radius: 8px;
        box-shadow: none;
    }
    .emergency-record-page #content label {
        color: #284c63;
        font-size: 12px !important;
        font-weight: 700;
    }
    .emergency-record-page #content .btn-primary {
        border: 0;
        border-radius: 9px;
        background: var(--clinical-blue);
        box-shadow: none;
    }
    .emergency-record-page .emergency-record-summary > .emergency-step-grid {
        width: 100%;
        padding: 0;
    }
    .emergency-record-page .emergency-step-grid > .row {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        width: 100%;
        max-width: none;
        margin: 0;
    }
    .emergency-record-page .emergency-step-grid > .row > [class*="col-md-6"] {
        display: block;
        width: auto;
        max-width: none;
        padding: 0;
        margin: 0;
    }
    .emergency-record-page .emergency-step-grid .card {
        width: 100%;
        height: auto !important;
        aspect-ratio: 1 / 1;
        display: flex;
        flex-direction: column;
        margin: 0;
        overflow: hidden;
        border: 1px solid var(--clinical-border);
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 5px 16px rgba(30, 79, 107, .07);
    }
    .emergency-record-page .emergency-step-grid .card-header {
        min-height: 72px !important;
        max-height: none !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 11px 13px;
        border: 0;
        background-color: #0b4f7d !important;
        background-image: linear-gradient(105deg, #0b4f7d 0%, #0877a9 58%, #19aec0 100%) !important;
        color: #fff !important;
    }
    .emergency-record-page .emergency-step-grid .card-header .row {
        display: flex;
        flex: 1 1 auto;
        justify-content: space-between;
        width: 100%;
        margin: 0;
        align-items: center;
    }
    .emergency-record-page .emergency-step-grid .card-header > .d-flex,
    .emergency-record-page .emergency-step-grid .card-header .row > .d-flex {
        flex: 1 1 auto;
        width: auto;
        max-width: calc(100% - 42px);
        padding: 0;
    }
    .emergency-record-page .emergency-step-grid .card-header > [class*="col-md-"]:last-child,
    .emergency-record-page .emergency-step-grid .card-header .row > [class*="col-md-"]:last-child {
        flex: 0 0 32px;
        width: 32px;
        max-width: 32px;
        padding: 0;
        margin: 0 !important;
    }
    .emergency-record-page .emergency-step-grid .card-header .card-title,
    .emergency-record-page .emergency-step-grid .card-header .colorbasic:not(.sradio) {
        margin: 0 !important;
        color: #fff !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        line-height: 1.25;
    }
    .emergency-record-page .emergency-step-grid .sradio {
        display: none;
    }
    .emergency-record-page .emergency-step-grid .card-header .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        float: right;
        width: 32px;
        height: 32px;
        padding: 0;
        border: 1px solid rgba(255, 255, 255, .2);
        border-radius: 9px;
        background: rgba(0, 78, 126, .55) !important;
        color: #fff !important;
        box-shadow: none;
    }
    .emergency-record-page .emergency-step-grid .card-body {
        min-height: 0 !important;
        flex: 1 1 auto;
        padding: 14px 16px;
        overflow: auto;
        color: #294b60;
        line-height: 1.35;
        scrollbar-color: #b8c4cc transparent;
        scrollbar-width: thin;
    }
    .emergency-record-page .emergency-step-grid .card-body::-webkit-scrollbar {
        width: 5px;
    }
    .emergency-record-page .emergency-step-grid .card-body::-webkit-scrollbar-thumb {
        border-radius: 6px;
        background: #b8c4cc;
    }
    .emergency-record-page .emergency-step-grid .card-body > br:first-child {
        display: none;
    }
    .emergency-record-page .emergency-step-grid .card-body .row {
        margin-right: 0;
        margin-left: 0;
    }
    .emergency-record-page .emergency-step-grid .card-body [class*="col-md-"] {
        min-width: 0;
        padding-right: 6px;
        padding-left: 0;
        margin-bottom: 4px;
    }
    .emergency-record-page .emergency-step-grid .card-body b {
        color: #24485f !important;
        font-size: 12px !important;
        font-weight: 800 !important;
    }
    .emergency-record-page .emergency-step-grid .card-body span,
    .emergency-record-page .emergency-step-grid .card-body td,
    .emergency-record-page .emergency-step-grid .card-body div {
        color: #587083 !important;
        font-size: 12px;
        font-weight: 500 !important;
        overflow-wrap: break-word;
        word-break: normal;
    }
    .emergency-record-page .emergency-step-grid .card-body .btn,
    .emergency-record-page .emergency-step-grid .card-body .btn span {
        color: #fff !important;
        font-weight: 800 !important;
    }
    .emergency-record-page .clinical-card-summary,
    .emergency-record-page .clinical-value-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .emergency-record-page .clinical-card-summary__title {
        display: block;
        margin-bottom: 2px;
        font-size: 12px !important;
        line-height: 1.3;
    }
    .emergency-record-page .clinical-card-summary__row,
    .emergency-record-page .clinical-value-list > div {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 1.12fr);
        align-items: start;
        gap: 8px;
        padding-bottom: 7px;
        border-bottom: 1px solid #e4edf2;
    }
    .emergency-record-page .clinical-card-summary__row:last-child,
    .emergency-record-page .clinical-value-list > div:last-child {
        padding-bottom: 0;
        border-bottom: 0;
    }
    .emergency-record-page .clinical-card-summary__row > span,
    .emergency-record-page .clinical-value-list > div > span {
        text-align: right;
    }
    .emergency-record-page .clinical-card-summary__row .badge-danger {
        display: inline-block;
        justify-self: end;
        max-width: 100%;
        padding: 5px 8px;
        color: #fff !important;
        white-space: normal;
        text-align: center;
    }
    .emergency-record-page .emergency-step-grid .card-body .clinical-card-summary__row .badge-danger {
        color: #fff !important;
    }
    .emergency-record-page .history-summary-card .card-header {
        position: relative;
        padding-right: 58px;
    }
    .emergency-record-page .history-summary-card .card-header > .row {
        display: block;
    }
    .emergency-record-page .history-summary-card .card-header .row > .d-flex {
        width: 100%;
        max-width: 100%;
    }
    .emergency-record-page .history-summary-card .card-header .row > [class*="col-md-"]:last-child {
        position: absolute;
        top: 50%;
        right: 13px;
        transform: translateY(-50%);
    }
    .emergency-record-page .history-summary-card .clinical-card-summary__title {
        margin-bottom: 8px;
    }
    .emergency-record-page .exam-request-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px;
        height: 100%;
        align-content: center;
    }
    .emergency-record-page .exam-request-grid .btn {
        min-width: 0;
        min-height: 42px;
        padding: 7px 4px;
        border: 0;
        border-radius: 9px;
        color: #fff !important;
        background: #087fb6 !important;
        font-size: 9px;
        font-weight: 800;
        line-height: 1.15;
        white-space: normal;
        overflow-wrap: normal;
        word-break: normal;
    }
    .emergency-record-page .emergency-step-grid .table {
        margin-bottom: 0;
        font-size: 11px;
    }
    .emergency-record-page .emergency-step-grid .table th,
    .emergency-record-page .emergency-step-grid .table td {
        padding: 5px 4px;
    }
    .emergency-record-page .emergency-primary-tabs {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
        margin-bottom: 14px;
    }
    .emergency-record-page .emergency-primary-tab {
        min-width: 0;
        overflow: hidden;
        border: 1px solid var(--clinical-border);
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 5px 16px rgba(30, 79, 107, .07);
    }
    .emergency-record-page .emergency-primary-tab summary,
    .emergency-record-page .emergency-primary-tab__header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        min-height: 52px;
        padding: 12px 15px;
        color: #fff;
        background: linear-gradient(105deg, #0b4f7d 0%, #0877a9 58%, #19aec0 100%);
        font-size: 13px;
        font-weight: 800;
        cursor: pointer;
        list-style: none;
    }
    .emergency-record-page .emergency-primary-tab summary::-webkit-details-marker {
        display: none;
    }
    .emergency-record-page .emergency-primary-tab__toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 34px;
        width: 34px;
        height: 34px;
        border-radius: 9px;
        color: #fff;
        background: rgba(0, 92, 151, .72);
        font-size: 18px;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, .08);
    }
    .emergency-record-page .emergency-primary-tab__toggle .fa-minus,
    .emergency-record-page .emergency-primary-tab[open] .emergency-primary-tab__toggle .fa-plus {
        display: none;
    }
    .emergency-record-page .emergency-primary-tab[open] .emergency-primary-tab__toggle .fa-minus {
        display: inline-block;
    }
    .emergency-record-page .emergency-primary-tab__body {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        padding: 13px 15px 15px;
    }
    .emergency-record-page .emergency-primary-tab__data {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 9px 14px;
        flex: 1 1 auto;
        min-width: 0;
    }
    .emergency-record-page .emergency-primary-tab__data > div {
        min-width: 0;
    }
    .emergency-record-page .emergency-primary-tab__data b,
    .emergency-record-page .emergency-primary-tab__data span {
        display: block;
    }
    .emergency-record-page .emergency-primary-tab__data b {
        margin-bottom: 2px;
        padding-top: 0 !important;
        color: #24485f;
        font-size: 11px;
    }
    .emergency-record-page .emergency-primary-tab__data span {
        overflow: hidden;
        color: #526d7d;
        font-size: 11px;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .emergency-record-page .emergency-primary-tab__data--attention {
        grid-template-columns: minmax(130px, .45fr) minmax(0, 1fr);
    }
    .emergency-record-page .emergency-primary-tab__data--admission {
        grid-template-columns: repeat(6, minmax(0, 1fr));
        gap: 12px 20px;
    }
    .emergency-record-page .emergency-primary-tab__data--admission .emergency-admission-name {
        grid-column: span 2;
    }
    .emergency-record-page .emergency-primary-tab__action {
        flex: 0 0 auto;
        min-height: 34px;
        padding: 7px 11px;
        color: #fff !important;
        font-size: 11px;
        font-weight: 800;
    }
    @media (max-width: 1279px) {
        .emergency-record-page .emergency-record-grid {
            grid-template-columns: 1fr;
        }
        .emergency-record-page .emergency-primary-tab__data--admission {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
    @media (max-width: 767px) {
        .emergency-record-page {
            padding: 16px;
        }
        .emergency-record-page .emergency-step-grid > .row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .emergency-record-page .emergency-primary-tabs {
            grid-template-columns: 1fr;
        }
        .emergency-record-page .emergency-primary-tab__data--admission {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 520px) {
        .emergency-record-page .emergency-step-grid > .row {
            grid-template-columns: 1fr;
        }
        .emergency-record-page .emergency-primary-tab__body {
            align-items: stretch;
            flex-direction: column;
        }
        .emergency-record-page .emergency-primary-tab__data,
        .emergency-record-page .emergency-primary-tab__data--attention,
        .emergency-record-page .emergency-primary-tab__data--admission {
            grid-template-columns: 1fr;
        }
        .emergency-record-page .emergency-primary-tab__data--admission .emergency-admission-name {
            grid-column: auto;
        }
        .emergency-record-page .emergency-primary-tab__action {
            width: 100%;
        }
    }
</style>

<div class="content emergency-record-page">
    <div class="emergency-record-shell">
        <div class="hospital-episode-links">
            @include('hospital.workflow.episode-links')
        </div>

        @php
            $primaryForm008 = $solicitud->form008->first();
            $admissionExtra = optional($solicitud->paciente->ho_datos_paciente);
            $civilStatuses = [
                1 => trans('emergencia.Soltero'),
                2 => trans('emergencia.Casado'),
                3 => trans('emergencia.Viudo'),
                4 => trans('emergencia.Divorciado'),
                5 => trans('emergencia.UnionLibre'),
            ];
            $admissionAge = $solicitud->paciente->fecha_nacimiento
                ? (int) \Carbon\Carbon::parse($solicitud->paciente->fecha_nacimiento)->diffInYears(\Carbon\Carbon::parse($solicitud->created_at))
                : 'No registrado';
        @endphp
        <div class="emergency-primary-tabs" aria-label="Datos iniciales del episodio">
            <details class="emergency-primary-tab emergency-primary-tab--admission" open>
                <summary class="emergency-primary-tab__header">
                    <span>{{trans('boxesh.RegistrodeAdmision')}}</span>
                    <span class="emergency-primary-tab__toggle" aria-hidden="true">
                        <i class="fa fa-plus"></i><i class="fa fa-minus"></i>
                    </span>
                </summary>
                <div class="emergency-primary-tab__body">
                    <div class="emergency-primary-tab__data emergency-primary-tab__data--admission">
                        <div class="emergency-admission-name"><b>{{trans('pasos.apellidosynombres')}}</b><span>{{ $solicitud->paciente->apellido1}} {{ $solicitud->paciente->apellido2}} {{ $solicitud->paciente->nombre1}} {{ $solicitud->paciente->nombre2}}</span></div>
                        <div><b>{{trans('pasos.cedula')}}</b><span>{{ $solicitud->id_paciente }}</span></div>
                        <div><b>{{trans('pasos.tipodocumentodeidentificacion')}}</b><span>{{ $solicitud->paciente->tipo_documento ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.ciudad')}}</b><span>{{ $solicitud->paciente->ciudad ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.telefono')}}</b><span>{{ $solicitud->paciente->telefono1 ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.Seguro')}}</b><span>{{ optional($solicitud->seguro)->nombre ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.SubSeguro')}}</b><span>{{ $solicitud->id_subseguro ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.celular')}}</b><span>{{ $solicitud->paciente->telefono2 ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.barrio')}}</b><span>{{ $admissionExtra->barrio ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.parroquia')}}</b><span>{{ $admissionExtra->parroquia ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.direccion')}}</b><span>{{ $solicitud->paciente->direccion ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.canton')}}</b><span>{{ $admissionExtra->canton ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.provincia')}}</b><span>{{ $admissionExtra->provincia ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.fechaNacimiento')}}</b><span>{{ $solicitud->paciente->fecha_nacimiento ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.nacionalidad')}}</b><span>{{ $admissionExtra->nacionalidad ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.CallePrincipal')}}</b><span>{{ $admissionExtra->calle_principal ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.CalleSecundaria')}}</b><span>{{ $admissionExtra->calle_secundaria ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.Referencia')}}</b><span>{{ $admissionExtra->referencia ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.edad')}}</b><span>{{ $admissionAge }}</span></div>
                        <div><b>{{trans('pasos.sexo')}}</b><span>{{ $solicitud->paciente->sexo == '1' ? 'HOMBRE' : 'MUJER' }}</span></div>
                        <div><b>{{trans('pasos.estadoCivil')}}</b><span>{{ $civilStatuses[$solicitud->paciente->estadocivil] ?? 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.ocupacion')}}</b><span>{{ $solicitud->paciente->ocupacion ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.LugarNacimiento')}}</b><span>{{ $solicitud->paciente->lugar_nacimiento ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.GrupoPrioritario')}}</b><span>{{ $admissionExtra->grupo_prioritario ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.EspecifiqueGrupoPrioritario')}}</b><span>{{ $admissionExtra->especifique_grupo ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.AutoidentificacionEtnica')}}</b><span>{{ $admissionExtra->autoidentificacion_etnica ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.NacionalidadEtnica')}}</b><span>{{ $admissionExtra->nacionalidad_etnica ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.Pueblos')}}</b><span>{{ $admissionExtra->pueblos ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.NivelEducacion')}}</b><span>{{ $admissionExtra->nivel_educacion ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.EstadoNivelEducacion')}}</b><span>{{ $admissionExtra->estado_nivel_educacion ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.TipoDeEmpresaDeTrabajo')}}</b><span>{{ $admissionExtra->empresa_trabajo ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.referido')}}</b><span>{{ $solicitud->paciente->referido ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.llamara')}}</b><span>{{ $admissionExtra->llamar_a ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.parentesco')}}</b><span>{{ $admissionExtra->parentesco_afinidad ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.telefono')}}</b><span>{{ $admissionExtra->telefono_inst_per_paci ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.familiar')}}</b><span>{{ $admissionExtra->direccion_familiar ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.llegada')}}</b><span>{{ $admissionExtra->forma_llegada ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.informacion')}}</b><span>{{ $admissionExtra->fuente_informacion ?: 'No registrado' }}</span></div>
                        <div><b>{{trans('pasos.telefono')}}</b><span>{{ $solicitud->paciente->telefono3 ?: 'No registrado' }}</span></div>
                        <div class="emergency-admission-name"><b>{{trans('pasos.InstitucionOPersonaQueEntregaAlPaciente')}}</b><span>{{ $admissionExtra->inst_per_paci_entrega ?: 'No registrado' }}</span></div>
                    </div>
                </div>
            </details>
            <details class="emergency-primary-tab" open>
                <summary class="emergency-primary-tab__header">
                    <span>{{trans('boxesh.IniciodeAtencionyMotivo')}}</span>
                    <span class="emergency-primary-tab__toggle" aria-hidden="true">
                        <i class="fa fa-plus"></i><i class="fa fa-minus"></i>
                    </span>
                </summary>
                <div class="emergency-primary-tab__body">
                    <div class="emergency-primary-tab__data emergency-primary-tab__data--attention">
                        <div><b>{{trans('boxesh.Fecha')}}</b><span>{{ $solicitud->fecha_ingreso }}</span></div>
                        <div><b>{{trans('boxesh.Motivo')}}</b><span>{{ !is_null($primaryForm008) && !empty($primaryForm008->motivo) ? $primaryForm008->motivo : 'No registrado' }}</span></div>
                    </div>
                    <button class="btn btn-primary emergency-primary-tab__action" type="button" onclick="segundo_paso()"><i class="fa fa-pencil"></i> Abrir</button>
                </div>
            </details>
        </div>

        <div class="emergency-record-grid">
            <main id="content" aria-live="polite"></main>
            <aside class="emergency-record-summary" aria-label="Secciones de la Historia 008">
                @include('layouts.boxesh')
            </aside>
        </div>
    </div>
</div>
@endsection
