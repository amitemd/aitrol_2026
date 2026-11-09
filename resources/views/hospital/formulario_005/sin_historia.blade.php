@extends('layouts.app-template-h')

@section('content')
<style>
    .f005-empty-page { max-width: 1360px; margin: 0 auto; padding: 20px 10px 50px; color: #123b5d; }
    .f005-empty-hero { display: flex; align-items: center; justify-content: space-between; gap: 24px; padding: 25px 30px; border-radius: 8px; color: #fff; background: #104c70; box-shadow: 0 8px 22px rgba(15, 52, 78, .14); }
    .f005-empty-hero__eyebrow { margin: 0 0 6px; color: #aee6ee; font-size: 11px; font-weight: 700; letter-spacing: .8px; text-transform: uppercase; }
    .f005-empty-hero h3 { margin: 0 0 7px; color: #fff; font-size: 25px; font-weight: 700; }
    .f005-empty-hero p { margin: 0; color: rgba(255,255,255,.84); font-size: 14px; }
    .f005-empty-status { min-width: 185px; padding: 13px 16px; border: 1px solid rgba(255,255,255,.3); border-radius: 9px; background: rgba(255,255,255,.12); text-align: center; }
    .f005-empty-status span { display: block; color: rgba(255,255,255,.75); font-size: 11px; text-transform: uppercase; }
    .f005-empty-status strong { display: block; margin-top: 5px; color: #fff; font-size: 13px; }
    .f005-empty-panel { margin-top: 18px; padding: 25px 28px; border: 1px solid #dbe8f2; border-radius: 8px; background: #fff; box-shadow: 0 5px 18px rgba(15, 52, 78, .05); }
    .f005-empty-panel h4 { margin: 0 0 8px; color: #123b5d; font-size: 19px; font-weight: 700; }
    .f005-empty-panel p { color: #60798b; font-size: 14px; }
    .f005-empty-note { margin: 18px 0; padding: 14px 16px; border-left: 4px solid #f0a229; background: #fff8e9; color: #72501a; font-size: 13px; }
    .f005-empty-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 22px; }
    .f005-empty-actions .btn { border-radius: 6px; font-weight: 700; }
    @media (max-width: 767px) { .f005-empty-page { padding: 0 4px 28px; } .f005-empty-hero { display: block; padding: 21px; } .f005-empty-status { margin-top: 16px; } .f005-empty-panel { padding: 20px; } .f005-empty-actions .btn { width: 100%; } }
</style>

<section class="f005-empty-page">
    <div class="f005-empty-hero">
        <div>
            <p class="f005-empty-hero__eyebrow">Módulo hospitalario · Formulario 005</p>
            <h3><i class="fa fa-file-text-o mr-2"></i>Evolución clínica</h3>
            <p>Este episodio todavía no tiene una historia clínica vinculada.</p>
        </div>
        <div class="f005-empty-status"><span>Estado</span><strong>HISTORIA PENDIENTE</strong></div>
    </div>
    <div class="f005-empty-panel">
        <h4><i class="fa fa-info-circle mr-2"></i>¿Por qué no se abre el formulario?</h4>
        @if($solicitud->id_agenda)
            <p>El episodio {{ $solicitud->id }} tiene un ingreso registrado, pero no se encontró una historia clínica vinculada a este episodio. Solicite a Admisión revisar el vínculo antes de documentar la evolución.</p>
        @elseif($paciente->emergency_identity_pending)
            <p>El episodio {{ $solicitud->id }} tiene una identificación provisional. Confirme los datos del paciente y después complete «Vincular Historia 008» desde Emergencia.</p>
        @else
            <p>El paciente está identificado, pero aún falta completar el ingreso de Emergencia y vincular la Historia 008 del episodio {{ $solicitud->id }}. Ese paso crea la agenda, la historia clínica y el registro inicial de evolución.</p>
        @endif
        <div class="f005-empty-note"><i class="fa fa-exclamation-triangle mr-1"></i>La evolución solo puede registrarse en una historia vinculada a este episodio y paciente.</div>
        <div class="f005-empty-actions">
            @if(\Illuminate\Support\Facades\Route::has('hospital.documents.index'))
                <a class="btn btn-primary" href="{{ route('hospital.documents.index', ['id_solicitud' => $solicitud->id]) }}"><i class="fa fa-arrow-left mr-1"></i>Volver a documentos</a>
            @else
                <a class="btn btn-primary" href="{{ URL::previous() }}"><i class="fa fa-arrow-left mr-1"></i>Volver</a>
            @endif
            @if(!$solicitud->id_agenda)
                <a class="btn btn-success" href="{{ route('hospital.ingreso008', ['id_solicitud' => $solicitud->id]) }}" target="_top"><i class="fa fa-link mr-1"></i>Vincular Historia 008</a>
            @endif
            <a class="btn btn-default" href="{{ route('hospital.emergencia') }}"><i class="fa fa-heartbeat mr-1"></i>Ir a Emergencia</a>
        </div>
    </div>
</section>
@endsection
