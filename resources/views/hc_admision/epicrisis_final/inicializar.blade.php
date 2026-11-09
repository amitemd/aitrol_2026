@extends(request()->boolean('embedded') ? 'layouts.epicrisis-embedded' : 'layouts.app-template-h')

@section('content')
<style>
    .epicrisis-start-page { max-width: 1440px; margin: 0 auto; padding: 20px 10px 50px; color: #123b5d; }
    .epicrisis-start-page > * { max-width: 1360px; margin-left: auto; margin-right: auto; }
    .epicrisis-start-hero { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 26px 30px; border-radius: 8px; color: #fff; background: #104c70; box-shadow: 0 8px 22px rgba(15, 52, 78, .14); }
    .epicrisis-start-hero__eyebrow { margin: 0 0 7px; color: #aee6ee; font-size: 11px; font-weight: 700; letter-spacing: .9px; text-transform: uppercase; }
    .epicrisis-start-hero h3 { margin: 0 0 7px; color: #fff; font-size: 26px; font-weight: 700; }
    .epicrisis-start-hero p { margin: 0; color: rgba(255,255,255,.84); font-size: 14px; }
    .epicrisis-start-patient { display: block; margin-top: 15px; color: #bdeaf1; font-size: 14px; font-weight: 700; }
    .epicrisis-start-status { min-width: 190px; padding: 13px 16px; border: 1px solid rgba(255,255,255,.3); border-radius: 9px; background: rgba(255,255,255,.12); text-align: center; }
    .epicrisis-start-status span { display: block; color: rgba(255,255,255,.76); font-size: 11px; letter-spacing: .6px; text-transform: uppercase; }
    .epicrisis-start-status strong { display: block; margin-top: 5px; color: #fff; font-size: 14px; }
    .epicrisis-start-back { display: inline-flex; align-items: center; gap: 7px; margin: 0 0 15px; color: #17618b; font-size: 13px; font-weight: 700; text-decoration: none; }
    .epicrisis-start-back:hover { color: #0877be; text-decoration: none; }
    .epicrisis-start-summary { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; margin-top: 18px; }
    .epicrisis-start-summary__item { display: flex; align-items: center; gap: 12px; min-height: 70px; padding: 12px 15px; border: 1px solid #dbe8f2; border-radius: 8px; background: #fff; box-shadow: 0 3px 12px rgba(15, 52, 78, .04); }
    .epicrisis-start-summary__icon { display: grid; place-items: center; width: 36px; height: 36px; flex: 0 0 36px; border-radius: 50%; background: #e6f5fa; color: #0877be; font-size: 16px; }
    .epicrisis-start-summary__label { display: block; color: #718596; font-size: 11px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; }
    .epicrisis-start-summary__value { display: block; margin-top: 3px; color: #123b5d; font-size: 14px; font-weight: 700; }
    .epicrisis-start-panel { margin-top: 18px; padding: 24px 26px; border: 1px solid #dbe8f2; border-radius: 8px; background: #fff; box-shadow: 0 5px 18px rgba(15, 52, 78, .05); }
    .epicrisis-start-panel h4 { margin: 0 0 7px; color: #123b5d; font-size: 19px; font-weight: 700; }
    .epicrisis-start-panel > p { margin: 0; color: #60798b; font-size: 14px; }
    .epicrisis-start-steps { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px; margin: 22px 0; padding: 0; list-style: none; }
    .epicrisis-start-steps li { min-height: 82px; padding: 13px 14px; border-radius: 6px; background: #eef8fc; color: #315875; font-size: 13px; }
    .epicrisis-start-steps strong { display: block; margin-bottom: 5px; color: #0877be; }
    .epicrisis-start-note { padding: 13px 15px; border-left: 4px solid #20a7b7; background: #f1fbfd; color: #456b7e; font-size: 13px; }
    .epicrisis-start-actions { display: flex; align-items: center; gap: 10px; margin-top: 22px; }
    .epicrisis-start-actions .btn { min-height: 42px; border-radius: 6px; font-weight: 700; }
    .epicrisis-start-actions .btn-primary { padding: 10px 18px; background: #0877be; border-color: #0877be; }
    @if(request()->boolean('embedded'))
        .epicrisis-start-page { padding: 0 12px 12px; }
        .epicrisis-start-page > * { max-width: none; }
        .epicrisis-start-title { display:flex; min-height:64px; align-items:center; gap:12px; margin-right:-12px; margin-bottom:12px; margin-left:-12px; padding:12px 18px; color:#fff; background:linear-gradient(105deg,#064b7a 0%,#087db5 60%,#1db8c8 100%); }
        .epicrisis-start-title__number { display:inline-flex; min-width:48px; height:30px; align-items:center; justify-content:center; padding:0 10px; border:1px solid rgba(255,255,255,.78); border-radius:999px; font-size:12px; font-weight:800; }
        .epicrisis-start-title h2 { margin:0; color:#fff; font-size:17px; font-weight:800; }
        .epicrisis-start-hero { display: block; padding: 20px; border-radius: 11px; background: linear-gradient(135deg, #073f69, #108eaa); }
        .epicrisis-start-hero h3 { font-size: 21px; }
        .epicrisis-start-status { min-width: 0; margin-top: 16px; }
        .epicrisis-start-summary, .epicrisis-start-steps { grid-template-columns: 1fr; }
        .epicrisis-start-panel { padding: 19px; border-radius: 11px; }
        .epicrisis-start-actions, .epicrisis-start-actions form, .epicrisis-start-actions .btn { width: 100%; }
    @endif
    @media (max-width: 767px) {
        .epicrisis-start-page { padding: 0 4px 28px; }
        .epicrisis-start-hero { display: block; padding: 21px; }
        .epicrisis-start-status { margin-top: 16px; }
        .epicrisis-start-summary, .epicrisis-start-steps { grid-template-columns: 1fr; }
        .epicrisis-start-panel { padding: 20px; }
        .epicrisis-start-actions { align-items: stretch; flex-direction: column; }
        .epicrisis-start-actions .btn { width: 100%; }
    }
</style>

<section class="epicrisis-start-page">
    @if(request()->boolean('embedded'))
        <header class="epicrisis-start-title"><span class="epicrisis-start-title__number">006</span><h2>Epicrisis</h2></header>
    @elseif(\Illuminate\Support\Facades\Route::has('hospital.documents.index'))
        <a class="epicrisis-start-back" href="{{ route('hospital.documents.index', ['id_solicitud' => $solicitud->id]) }}"><i class="fa fa-arrow-left"></i> Volver a documentos del episodio</a>
    @else
        <a class="epicrisis-start-back" href="{{ URL::previous() }}"><i class="fa fa-arrow-left"></i> Volver</a>
    @endif

    @php
        $patientName = trim(($paciente->apellido1 ?: '').' '.($paciente->apellido2 ?: '').' '.($paciente->nombre1 ?: '').' '.($paciente->nombre2 ?: ''));
    @endphp
    <div class="epicrisis-start-hero">
        <div>
            <p class="epicrisis-start-hero__eyebrow">Módulo hospitalario · Cierre clínico</p>
            <h3><i class="fa fa-file-text-o mr-2"></i>Preparar cierre clínico</h3>
            <p>Completa la epicrisis final antes de cerrar el episodio del paciente.</p>
            <span class="epicrisis-start-patient"><i class="fa fa-user mr-1"></i>{{ mb_strtoupper($patientName ?: 'Paciente sin nombre', 'UTF-8') }}</span>
        </div>
        <div class="epicrisis-start-status"><span>Estado del documento</span><strong>PENDIENTE DE INICIALIZAR</strong></div>
    </div>

    <div class="epicrisis-start-summary">
        <div class="epicrisis-start-summary__item"><span class="epicrisis-start-summary__icon"><i class="fa fa-file-text-o"></i></span><div><span class="epicrisis-start-summary__label">Documento</span><strong class="epicrisis-start-summary__value">Epicrisis final</strong></div></div>
        <div class="epicrisis-start-summary__item"><span class="epicrisis-start-summary__icon"><i class="fa fa-user-md"></i></span><div><span class="epicrisis-start-summary__label">Paciente</span><strong class="epicrisis-start-summary__value">{{ $solicitud->id_paciente }}</strong></div></div>
        <div class="epicrisis-start-summary__item"><span class="epicrisis-start-summary__icon"><i class="fa fa-shield"></i></span><div><span class="epicrisis-start-summary__label">Cierre</span><strong class="epicrisis-start-summary__value">Aún no iniciado</strong></div></div>
    </div>

    <div class="epicrisis-start-panel">
        <h4><i class="fa fa-list-ol mr-2"></i>¿Qué debes hacer?</h4>
        <p>Al iniciar se creará el documento de trabajo. Tus datos quedarán guardados y podrás completarlos paso a paso.</p>
        <ol class="epicrisis-start-steps">
            <li><strong>1. Iniciar epicrisis</strong>Abre el formulario de cierre clínico.</li>
            <li><strong>2. Completar información</strong>Registra evolución, diagnóstico, indicaciones y destino.</li>
            <li><strong>3. Validar y firmar</strong>Revisa el checklist antes de cerrar el episodio.</li>
        </ol>
        <div class="epicrisis-start-note"><i class="fa fa-info-circle mr-1"></i>Inicializar no cierra el episodio. Solo prepara el documento para que puedas completarlo.</div>
        <div class="epicrisis-start-actions">
            <form method="POST" action="{{ route('epicrisis.inicializar_final', ['id_solicitud' => $solicitud->id]) }}">
                {{ csrf_field() }}
                @if(request()->boolean('embedded'))<input type="hidden" name="embedded" value="1">@endif
                <button type="submit" class="btn btn-primary"><i class="fa fa-arrow-right mr-1"></i> Iniciar epicrisis y continuar</button>
            </form>
        </div>
    </div>
</section>
@endsection
