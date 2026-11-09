@extends('layouts.app-template')

@section('content')
@php
    $bloques = isset($payload['bloques']) && is_array($payload['bloques']) ? $payload['bloques'] : array();
    $vitales = isset($payload['signos_vitales']) && is_array($payload['signos_vitales']) ? $payload['signos_vitales'] : array();
    $get = function($array, $key, $default = '') { return is_array($array) && array_key_exists($key, $array) ? $array[$key] : $default; };
    $sexo = !is_null($paciente) ? ($paciente->sexo == 1 ? 'Hombre' : ($paciente->sexo == 2 ? 'Mujer' : '')) : '';
    $fechaAtencion = $get($bloques, 'fecha_atencion', $agenda ? substr($agenda->fechaini, 0, 10) : date('Y-m-d'));
    $horaAtencion = $get($bloques, 'hora_atencion', $agenda ? substr($agenda->fechaini, 11, 5) : date('H:i'));
    $establecimiento = $get($bloques, 'establecimiento', $empresa ? ($empresa->nombre_form ? $empresa->nombre_form : $empresa->nombrecomercial) : '');
    $antecedentesBase = array('Alergia a antibiotico', 'Alergia a anestesia', 'Hemorragias', 'VIH/SIDA', 'Tuberculosis', 'Asma', 'Diabetes', 'Hipertension arterial', 'Enfermedad cardiaca', 'Otros');
    $regiones = array('Labios', 'Mejillas', 'Maxilar superior', 'Maxilar inferior', 'Lengua', 'Paladar', 'Piso de boca', 'Carrillos', 'Glandulas salivales', 'Oro-faringe', 'ATM', 'Ganglios');
    $ihosPiezas = array('16', '11', '26', '36', '31', '46');
    $indicadoresGuardados = isset($payload['indicadores']) && is_array($payload['indicadores']) ? $payload['indicadores'] : array();
    $ihosGuardado = isset($indicadoresGuardados['ihos']) && is_array($indicadoresGuardados['ihos']) ? $indicadoresGuardados['ihos'] : array();
    $ihosDetallesGuardados = isset($ihosGuardado['piezas']) && is_array($ihosGuardado['piezas']) ? $ihosGuardado['piezas'] : array();
    $ihosPorFdi = array();
    foreach ($ihosDetallesGuardados as $ihosDetalle) {
        if (is_array($ihosDetalle) && isset($ihosDetalle['fdi'])) {
            $ihosPorFdi[(string) $ihosDetalle['fdi']] = $ihosDetalle;
        }
    }
    $periodontalValor = trim((string) $get($indicadoresGuardados, 'periodontal', '')) ?: 'No aplica';
    $oclusionValor = trim((string) $get($indicadoresGuardados, 'oclusion', '')) ?: 'No aplica';
    $fluorosisValor = trim((string) $get($indicadoresGuardados, 'fluorosis', '')) ?: 'No aplica';
    $diagnosticos = isset($payload['diagnosticos']) && is_array($payload['diagnosticos']) && count($payload['diagnosticos']) ? $payload['diagnosticos'] : array(array('cie10' => '', 'tipo' => 'PRE'));
    $nombrePaciente = $paciente ? trim($paciente->apellido1.' '.$paciente->apellido2.' '.$paciente->nombre1.' '.$paciente->nombre2) : '';
    $nombreDoctor = $doctor ? trim($doctor->nombre1.' '.$doctor->nombre2.' '.$doctor->apellido1.' '.$doctor->apellido2) : '';
    $embebido = isset($embebido) ? (bool) $embebido : false;
    $contextoFormulario = isset($contextoFormulario) && is_array($contextoFormulario) ? $contextoFormulario : array();
    $moduloFormulario = isset($contextoFormulario['modulo']) ? $contextoFormulario['modulo'] : 'Odontologia';
    $modoPeriodontal = !empty($contextoFormulario['modo_periodontal']);
    $periodontogramaUrl = isset($contextoFormulario['periodontograma_url']) ? $contextoFormulario['periodontograma_url'] : '';
    $formularioSaveUrl = isset($contextoFormulario['save_url']) ? $contextoFormulario['save_url'] : route('odontologia.formulario033.guardar');
    $formularioPrintUrl = isset($contextoFormulario['print_url'])
        ? $contextoFormulario['print_url']
        : ($formulario ? route('odontologia.formulario033.imprimir', ['id' => $formulario->id]) : '');
    $cotizacionUrl = isset($contextoFormulario['cotizacion_url'])
        ? $contextoFormulario['cotizacion_url']
        : url('odontologia/cotizaciones');
    $denticion = isset($denticion) && $denticion == 'temporal' ? 'temporal' : 'permanente';
    $denticionTexto = $denticion == 'permanente' ? 'Denticion permanente' : 'Denticion temporal';
    $odontogramaHerramientas = array(
        'caries' => array('label' => 'Caries', 'icon' => 'fa-circle'),
        'obturado' => array('label' => 'Obturado', 'icon' => 'fa-circle'),
        'sellante_necesario' => array('label' => 'Sellante necesario', 'icon' => 'fa-shield'),
        'sellante_realizado' => array('label' => 'Sellante realizado', 'icon' => 'fa-shield'),
        'extraccion_indicada' => array('label' => 'Extraccion indicada', 'icon' => 'fa-long-arrow-down'),
        'perdida_caries' => array('label' => 'Perdida por caries', 'icon' => 'fa-circle-o'),
        'perdida_otra' => array('label' => 'Perdida por otra causa', 'icon' => 'fa-circle-o'),
        'endodoncia_realizar' => array('label' => 'Endodoncia por realizar', 'icon' => 'fa-registered'),
        'endodoncia_realizada' => array('label' => 'Endodoncia realizada', 'icon' => 'fa-check-circle-o'),
        'corona_indicada' => array('label' => 'Corona indicada', 'icon' => 'fa-square-o'),
        'corona_realizada' => array('label' => 'Corona realizada', 'icon' => 'fa-check-square-o'),
        'protesis_fija' => array('label' => 'Protesis fija', 'icon' => 'fa-link'),
        'protesis_removible_indicada' => array('label' => 'Protesis removible indicada', 'icon' => 'fa-link'),
        'protesis_removible_realizada' => array('label' => 'Protesis removible realizada', 'icon' => 'fa-link'),
        'protesis_total_indicada' => array('label' => 'Protesis total indicada', 'icon' => 'fa-bars'),
        'protesis_total_realizada' => array('label' => 'Protesis total realizada', 'icon' => 'fa-bars'),
        'ausente' => array('label' => 'Ausente', 'icon' => 'fa-ban'),
        'limpiar' => array('label' => 'Limpiar pieza', 'icon' => 'fa-eraser'),
    );
@endphp
<link rel="stylesheet" href="{{ asset('odontologia/css/formulario033.css') }}">
@if($modoPeriodontal)
<style>
    html,body{background:#eef3f8;margin:0;min-height:0!important;padding:0}
    .odontologia-033-embebida.content-wrapper{margin-left:0!important;min-height:0!important}
    .periodontologia-033.content{padding:0}
    .periodontologia-033 .odo-context,.periodontologia-033 .odo-page{max-width:none}
    .periodontologia-033__chart-frame{background:#fff;border:0;display:block;height:900px;width:100%}
</style>
@endif
<div class="content-wrapper @if($embebido) odontologia-033-embebida @endif">
    @if(!$embebido)
    <section class="content-header odo-content-header">
        <h1>{{ $moduloFormulario }} <small>Formulario 033 MSP 2021</small></h1>
        <ol class="breadcrumb">
            <li><a href="{{ asset('/') }}"><i class="fa fa-home"></i> Inicio</a></li>
            @if($agenda)<li><a href="{{ route('agenda.detalle', ['id' => $agenda->id]) }}">Agenda #{{ $agenda->id }}</a></li>@endif
            <li class="active">Formulario 033</li>
        </ol>
    </section>
    @endif

    <section class="content odontologia-033 @if($modoPeriodontal) periodontologia-033 @endif">
        <div class="odo-context">
            <div class="odo-context-grid">
                <div class="odo-context-item"><strong>Paciente</strong>{{ $nombrePaciente }}<br>{{ $paciente ? $paciente->id : '' }}</div>
                <div class="odo-context-item"><strong>Agenda</strong>{{ $agenda ? '#'.$agenda->id.' | '.$agenda->fechaini : 'Sin agenda' }}<br>{{ $sala ? $sala->nombre_sala : '' }}</div>
                <div class="odo-context-item"><strong>Historia clinica</strong>HCID {{ $historia->hcid }}<br>{{ $seguro ? $seguro->nombre : 'Sin seguro' }}</div>
                <div class="odo-context-item"><strong>Profesional</strong>{{ $nombreDoctor }}<br>{{ $procedimiento ? $procedimiento->nombre : '' }}</div>
            </div>
        </div>

        <form id="formulario033" class="odo-page">
            {{ csrf_field() }}
            <header class="odo-head"><div><h1>Historia Clinica Unica de {{ $modoPeriodontal ? 'Periodontologia' : 'Odontologia' }}</h1><small>Ministerio de Salud Publica del Ecuador - Sistema Nacional de Salud</small></div><span class="odo-code">SNS-MSP / HCU-form.033 / 2021</span></header>
            <section class="odo-block"><div class="odo-title"><span class="letter">A</span><h2>Datos del establecimiento y usuario/paciente</h2>@if($formulario)<span class="odo-note">Ultima version: {{ $formulario->version }} | vigente hasta {{ $formulario->vigente_hasta }}</span>@endif</div><div class="odo-row odo-c4"><label class="odo-field">Institucion<input type="text" data-field="institucion_sistema" value="{{ $seguro ? $seguro->nombre : $get($bloques, 'institucion_sistema', '') }}" readonly></label><label class="odo-field">Unicodigo<input type="text" data-field="unicodigo" value="{{ $get($bloques, 'unicodigo', $empresa ? $empresa->unicodigo : '') }}" readonly></label><label class="odo-field">Establecimiento<input type="text" data-field="establecimiento" value="{{ $establecimiento }}"></label><label class="odo-field">N. historia clinica<input type="text" value="{{ $paciente ? $paciente->id : '' }}" readonly></label></div><div class="odo-row odo-c6"><label class="odo-field">Primer apellido<input value="{{ $paciente ? $paciente->apellido1 : '' }}" readonly></label><label class="odo-field">Segundo apellido<input value="{{ $paciente ? $paciente->apellido2 : '' }}" readonly></label><label class="odo-field">Primer nombre<input value="{{ $paciente ? $paciente->nombre1 : '' }}" readonly></label><label class="odo-field">Segundo nombre<input value="{{ $paciente ? $paciente->nombre2 : '' }}" readonly></label><label class="odo-field">Sexo<input value="{{ $sexo }}" readonly></label><label class="odo-field">Edad<input value="{{ $edad }}" readonly></label></div><div class="odo-row odo-c5"><label class="odo-field">Fecha<input type="date" data-field="fecha_atencion" value="{{ $fechaAtencion }}"></label><label class="odo-field">Hora<input type="time" data-field="hora_atencion" value="{{ $horaAtencion }}"></label><label class="odo-field">N. hoja<input type="number" id="nro_hoja" data-field="nro_hoja" value="{{ $get($bloques, 'nro_hoja', 1) }}"></label><label class="odo-field">Tipo<select data-field="tipo_atencion"><option>Primera</option><option>Subsecuente</option></select></label><label class="odo-field">Servicio<select data-field="servicio"><option @if($agenda && $agenda->est_amb_hos != 1) selected @endif>Ambulatorio</option><option @if($agenda && $agenda->est_amb_hos == 1) selected @endif>Intensivo</option></select></label></div></section>
            <section class="odo-block"><div class="odo-title"><span class="letter">B</span><h2>Motivo de consulta</h2></div><label class="odo-field">Motivo<textarea data-field="motivo_consulta">{{ $get($bloques, 'motivo_consulta', $agenda ? $agenda->observaciones : '') }}</textarea></label></section>
            <section class="odo-block"><div class="odo-title"><span class="letter">C</span><h2>Enfermedad o problema actual</h2></div><label class="odo-field">Enfermedad actual<textarea data-field="enfermedad_actual">{{ $get($bloques, 'enfermedad_actual', $historia->evolucion) }}</textarea></label><div class="odo-inline"><strong>Embarazo:</strong><label><input type="radio" name="embarazo" data-field="embarazo" value="SI"> Si</label><label><input type="radio" name="embarazo" data-field="embarazo" value="NO"> No</label><label><input type="radio" name="embarazo" data-field="embarazo" value="NA" checked> No aplica</label></div></section>
            @foreach(array('D' => 'personales', 'E' => 'familiares') as $letra => $tipo)<section class="odo-block"><div class="odo-title"><span class="letter">{{ $letra }}</span><h2>Antecedentes {{ $tipo }}</h2></div><div class="odo-check-grid">@foreach($antecedentesBase as $item)<label class="odo-field" data-antecedente="{{ $tipo }}" data-nombre="{{ $item }}"><span class="odo-check"><input type="checkbox"> {{ $item }}</span><input type="text" placeholder="Descripcion" @if($tipo == 'personales' && $item == 'Otros') value="{{ $paciente ? $paciente->antecedentes_pat : '' }}" @elseif($tipo == 'familiares' && $item == 'Otros') value="{{ $paciente ? $paciente->antecedentes_fam : '' }}" @endif></label>@endforeach</div></section>@endforeach
            <section class="odo-block"><div class="odo-title"><span class="letter">F</span><h2>Signos vitales</h2></div><div class="odo-row odo-c4"><label class="odo-field">Presion arterial<input id="presion_arterial" data-vital="pa" placeholder="120/80" value="{{ $get($vitales, 'presion_arterial', $historia->presion ?: ($paciente ? $paciente->presion : '')) }}"></label><label class="odo-field">Frecuencia cardiaca<input id="frecuencia_cardiaca" data-vital="fc" type="number" value="{{ $get($vitales, 'frecuencia_cardiaca', $historia->pulso) }}"></label><label class="odo-field">Temperatura<input id="temperatura" data-vital="temp" type="number" step="0.1" value="{{ $get($vitales, 'temperatura', $historia->temperatura ?: ($paciente ? $paciente->temperatura : '')) }}"></label><label class="odo-field">Frecuencia respiratoria<input id="frecuencia_respiratoria" data-vital="fr" type="number" value="{{ $get($vitales, 'frecuencia_respiratoria') }}"></label></div><div id="vital-alert" class="odo-alert"></div></section>
            <section class="odo-block"><div class="odo-title"><span class="letter">G</span><h2>Examen del sistema estomatognatico</h2></div><table class="odo-table"><thead><tr><th>Region</th><th class="center">Patologia</th><th>Descripcion</th></tr></thead><tbody>@foreach($regiones as $region)<tr data-region="{{ $region }}"><td>{{ $region }}</td><td class="center"><input type="checkbox" data-region-check></td><td><input data-region-desc value="Sin Patologia Aparente"></td></tr>@endforeach</tbody></table></section>
@if($modoPeriodontal)
<section class="odo-block odo-periodontogram-block">
    <iframe class="periodontologia-033__chart-frame" src="{{ $periodontogramaUrl }}" title="Periodontograma de cuatro cuadrantes"></iframe>
                <div hidden aria-hidden="true">
                    <svg id="odontograma-svg" width="1040" height="440"></svg>
                    <div id="mr-container"></div>
                </div>
            </section>
            @else
            <section class="odo-block odo-odontogram-block" data-denticion="{{ $denticion }}">
                <div class="odo-title odo-odontogram-title">
                    <span class="letter">H</span>
                    <span class="odo-title-tooth" aria-hidden="true"><i class="fa fa-stethoscope"></i></span>
                    <h2>Odontograma</h2>
                    <span class="odo-dentition-badge"><i class="fa fa-user-md" aria-hidden="true"></i>{{ $denticionTexto }} <small>{{ $edad }} anos</small></span>
                </div>
                <div class="odo-toolbar odo-no-print" role="toolbar" aria-label="Condiciones del odontograma">
                    @foreach($odontogramaHerramientas as $tool => $config)
                        <button type="button" class="odo-button @if($tool == 'caries') active @endif" data-tool="{{ $tool }}" title="{{ $config['label'] }}" aria-label="{{ $config['label'] }}">
                            <span class="odo-tool-icon" aria-hidden="true"><i class="fa {{ $config['icon'] }}"></i></span>
                            <span>{{ $config['label'] }}</span>
                        </button>
                    @endforeach
                </div>
                <div class="odo-wrap odo-chart-wrap">
                    <svg id="odontograma-svg" width="1040" height="440" role="img" aria-label="Odontograma {{ strtolower($denticionTexto) }}"></svg>
                </div>
                <div id="mr-container" class="odo-mr-container" aria-hidden="{{ $denticion == 'temporal' ? 'true' : 'false' }}"></div>
                <template id="odontograma-cie10-options">
                    @foreach($cie10 as $cie)<option value="{{ $cie->id }}">{{ $cie->id }} - {{ $cie->descripcion }}</option>@endforeach
                </template>
                <template id="odontograma-procedimiento-options">
                    @foreach($procedimientosCatalogo as $procedimientoCatalogo)<option value="{{ $procedimientoCatalogo->id }}">{{ $procedimientoCatalogo->nombre }}</option>@endforeach
                </template>
                <template id="odontograma-doctor-options">
                    @foreach($doctoresCatalogo as $doctorCatalogo)<option value="{{ $doctorCatalogo->id }}">{{ trim($doctorCatalogo->apellido1.' '.$doctorCatalogo->apellido2.' '.$doctorCatalogo->nombre1.' '.$doctorCatalogo->nombre2) }}</option>@endforeach
                </template>
            </section>
            <section class="odo-block odo-surface-treatment-block">
                <div class="odo-title">
                    <span class="letter">N</span>
                    <h2>Diagnostico y tratamiento por pieza</h2>
                    <span class="odo-note">Maximo 2 diagnosticos CIE-10 por pieza</span>
                </div>
                <div id="odontograma-detalles-panel" class="odo-face-details odo-face-details-in-section" hidden aria-hidden="true">
                    <div class="odo-face-details-scroll">
                        <table class="odo-table odo-face-details-table">
                            <thead><tr><th>#</th><th>Fecha</th><th>Pieza</th><th>Caras</th><th>Diagnostico (CIE-10)</th><th>Procedimiento asociado</th><th>Doctor</th></tr></thead>
                            <tbody id="odontograma-detalles-body"></tbody>
                        </table>
                    </div>
                </div>
                @include('odontologia.cotizacion_editor')
            </section>
            @endif
            <section class="odo-block">
                <div class="odo-title"><span class="letter">I</span><h2>Indicadores de salud bucal</h2></div>
                <div class="odo-two-col">
                    <div class="odo-index">
                        <h3>IHOS</h3>
                        <table class="odo-table">
                            <thead><tr><th>Pieza</th><th>Placa</th><th>Calculo</th><th>Gingivitis</th><th>Ausente</th></tr></thead>
                            <tbody>
                                @foreach($ihosPiezas as $fdi)
                                    @php $ihosFila = isset($ihosPorFdi[$fdi]) ? $ihosPorFdi[$fdi] : array(); @endphp
                                    <tr data-ihos-row>
                                        <td><input data-ihos="fdi" value="{{ $fdi }}"></td>
                                        <td><input data-ihos="placa" type="number" min="0" max="3" value="{{ $get($ihosFila, 'placa', 0) }}"></td>
                                        <td><input data-ihos="calculo" type="number" min="0" max="3" value="{{ $get($ihosFila, 'calculo', 0) }}"></td>
                                        <td><input data-ihos="gingivitis" type="number" min="0" max="1" value="{{ $get($ihosFila, 'gingivitis', 0) }}"></td>
                                        <td class="center"><input data-ihos="ausente" type="checkbox" @if($get($ihosFila, 'ausente', 0)) checked @endif></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="odo-cpo-grid" style="margin-top:8px"><span class="rowlab">Totales</span><span class="hdr">Placa</span><span class="hdr">Calculo</span><span class="hdr">Gingivitis</span><span class="hdr">IHOS</span><span></span><output id="ihos_placa">0.00</output><output id="ihos_calculo">0.00</output><output id="ihos_gingivitis">0.00</output><output id="ihos_total">0.00</output></div>
                    </div>
                    <div class="odo-index">
                        <h3>Otros</h3>
                        <label class="odo-field">Periodontal<select id="periodontal"><option value="No aplica" @if($periodontalValor == 'No aplica') selected @endif>No aplica</option><option @if($periodontalValor == 'Leve') selected @endif>Leve</option><option @if($periodontalValor == 'Moderada') selected @endif>Moderada</option><option @if($periodontalValor == 'Severa') selected @endif>Severa</option></select></label>
                        <label class="odo-field">Oclusion<select id="oclusion"><option value="No aplica" @if($oclusionValor == 'No aplica') selected @endif>No aplica</option><option @if($oclusionValor == 'Angle I') selected @endif>Angle I</option><option @if($oclusionValor == 'Angle II') selected @endif>Angle II</option><option @if($oclusionValor == 'Angle III') selected @endif>Angle III</option></select></label>
                        <label class="odo-field">Fluorosis<select id="fluorosis"><option value="No aplica" @if($fluorosisValor == 'No aplica') selected @endif>No aplica</option><option @if($fluorosisValor == 'Leve') selected @endif>Leve</option><option @if($fluorosisValor == 'Moderada') selected @endif>Moderada</option><option @if($fluorosisValor == 'Severa') selected @endif>Severa</option></select></label>
                    </div>
                </div>
            </section>
            <section class="odo-block"><div class="odo-title"><span class="letter">J</span><h2>Indices CPO-ceo</h2></div><div class="odo-cpo-grid"><span></span><span class="hdr">Cariados</span><span class="hdr">Perdidos/extraidos</span><span class="hdr">Obturados</span><span class="hdr">Total</span><span class="rowlab @if($denticion == 'temporal') denticion-oculta @endif">CPOD</span><output id="cpod_c" class="@if($denticion == 'temporal') denticion-oculta @endif">0</output><output id="cpod_p" class="@if($denticion == 'temporal') denticion-oculta @endif">0</output><output id="cpod_o" class="@if($denticion == 'temporal') denticion-oculta @endif">0</output><output id="cpod_total" class="@if($denticion == 'temporal') denticion-oculta @endif">0</output><span class="rowlab @if($denticion == 'permanente') denticion-oculta @endif">ceod</span><output id="ceod_c" class="@if($denticion == 'permanente') denticion-oculta @endif">0</output><output id="ceod_e" class="@if($denticion == 'permanente') denticion-oculta @endif">0</output><output id="ceod_o" class="@if($denticion == 'permanente') denticion-oculta @endif">0</output><output id="ceod_total" class="@if($denticion == 'permanente') denticion-oculta @endif">0</output></div></section>
            <section class="odo-block"><div class="odo-title"><span class="letter">K</span><h2>Planes</h2></div><div class="odo-row odo-c3"><label class="odo-field">Plan diagnostico<textarea data-field="plan_diagnostico"></textarea></label><label class="odo-field">Plan terapeutico<textarea data-field="plan_terapeutico">{{ $historia->receta }}</textarea></label><label class="odo-field">Plan educacional<textarea data-field="plan_educacional">{{ $historia->observaciones }}</textarea></label></div></section>
            <section class="odo-block"><div class="odo-title"><span class="letter">L</span><h2>Examenes complementarios</h2></div><table class="odo-table"><thead><tr><th>Examen</th><th>Solicitado</th><th>Resultado</th><th>Informe</th></tr></thead><tbody>@foreach(array('Biometria','Quimica sanguinea','Rayos X','Otros') as $ex)<tr data-examen-complementario="{{ $ex }}"><td>{{ $ex }}</td><td class="center"><input type="checkbox" data-examen-solicitado></td><td class="center"><input type="checkbox" data-examen-resultado></td><td><input data-examen-informe></td></tr>@endforeach</tbody></table></section>
            <section class="odo-block odo-legacy-diagnoses-hidden" aria-hidden="true"><div class="odo-title"><span class="letter">M</span><h2>Diagnosticos CIE-10</h2><button type="button" class="odo-mini odo-no-print" data-add-row="diagnosticos">Agregar</button></div><table class="odo-table"><thead><tr><th>CIE-10</th><th>PRE/DEF</th><th></th></tr></thead><tbody data-rows="diagnosticos">@foreach($diagnosticos as $dx)<tr data-diagnostico-row><td><select data-dx="cie10"><option value="">Seleccione</option>@foreach($cie10 as $cie)<option value="{{ $cie->id }}" @if($historia->id_cie_10 == $cie->id) selected @endif>{{ $cie->id }} - {{ $cie->descripcion }}</option>@endforeach</select></td><td><select data-dx="tipo"><option value="PRE">PRE</option><option value="DEF">DEF</option></select></td><td class="center"><button type="button" class="odo-mini odo-no-print" data-remove-row="1">Quitar</button></td></tr>@endforeach</tbody></table></section>
            <section class="odo-block"><div class="odo-title"><span class="letter">O</span><h2>Datos del profesional</h2></div><div class="odo-row odo-c6"><label class="odo-field">Profesional<input id="profesional_nombre" value="{{ $nombreDoctor }}" readonly></label><label class="odo-field">Registro<input id="registro_doctor" value="{{ $doctor ? $doctor->registro_doctor : '' }}"></label><label class="odo-field">Libro<input id="libro" value="{{ $doctor ? $doctor->libro : '' }}"></label><label class="odo-field">Folio<input id="folio" value="{{ $doctor ? $doctor->folio : '' }}"></label><label class="odo-field">Numero<input id="numero" value="{{ $doctor ? $doctor->numero : '' }}"></label><label class="odo-field">Codigo MSP/ACESS<input id="codigo_msp_acess" value="{{ $doctor && isset($doctor->codigo_msp_acess) ? $doctor->codigo_msp_acess : '' }}"></label></div></section>
            <footer class="odo-footer-actions odo-no-print"><span id="odo-status" class="odo-status" style="display:none" aria-hidden="true">@if($formulario)Version {{ $formulario->version }} cargada. @endif Autoguardado activo.</span><button type="button" id="btn-export-json" class="odo-secondary" style="display:none" aria-hidden="true" tabindex="-1">Exportar JSON</button><button type="button" id="btn-guardar-033" class="odo-primary">Guardar</button><button type="button" id="btn-print-033" class="odo-secondary">PDF FORMULARIO 033</button></footer>
        </form>
    </section>
</div>
@php
    $anatomiasDentalesJson = @file_get_contents(public_path('odontologia/data/anatomias_dentales.json'));
    if ($anatomiasDentalesJson === false) {
        $anatomiasDentalesJson = '{}';
    }
@endphp
<script>window.FORM033_DENTAL_ANATOMIES={!! $anatomiasDentalesJson !!};</script>
<script>window.FORM033_DATA={cotizacionUrl:{!! json_encode($cotizacionUrl) !!},csrf:{!! json_encode(csrf_token()) !!},saveUrl:{!! json_encode($formularioSaveUrl) !!},hcid:{!! json_encode((string) $historia->hcid) !!},idAgenda:{!! json_encode((string) $historia->id_agenda) !!},idPaciente:{!! json_encode((string) $historia->id_paciente) !!},idDoctor:{!! json_encode((string) $historia->id_doctor1) !!},edad:{{ is_numeric($edad) ? (int) $edad : 'null' }},denticion:{!! json_encode($denticion) !!},modoPeriodontal:{{ $modoPeriodontal ? 'true' : 'false' }},printMode:{{ $imprimir ? 'true' : 'false' }},printUrl:{!! json_encode($formularioPrintUrl) !!},payload:{!! $payloadJson ? $payloadJson : '{}' !!}};</script>
<script src="{{ asset('odontologia/js/formulario033.js') }}"></script>
@if($modoPeriodontal)<script src="{{ asset('periodontologia/js/formulario033_periodontologia.js') }}?v={{ filemtime(public_path('periodontologia/js/formulario033_periodontologia.js')) }}"></script>@endif
<script src="{{ asset('odontologia/js/formulario033_denticion.js') }}?v={{ filemtime(public_path('odontologia/js/formulario033_denticion.js')) }}"></script>
<script src="{{ asset('odontologia/js/formulario033_detalles.js') }}?v={{ filemtime(public_path('odontologia/js/formulario033_detalles.js')) }}"></script>
<script src="{{ asset('odontologia/js/formulario033_autoguardado.js') }}?v={{ filemtime(public_path('odontologia/js/formulario033_autoguardado.js')) }}"></script>
<script src="{{ asset('odontologia/js/formulario033_cotizacion.js') }}?v={{ filemtime(public_path('odontologia/js/formulario033_cotizacion.js')) }}"></script>
@endsection
