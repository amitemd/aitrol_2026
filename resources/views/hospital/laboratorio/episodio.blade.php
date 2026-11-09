@extends(Request::boolean('embedded') ? 'layouts.hospital-embedded-module' : 'layouts.app-template-h')
@section('title', 'Laboratorio clínico · Episodio '.$solicitud->id)
@section('content')
<style>
    .episode-lab { max-width:1200px; margin:auto; padding:24px 16px; color:#19405b; }
    .episode-lab h2 { color:#104c70; margin:0 0 8px; }
    .episode-lab .lab-card { background:#fff; border:1px solid #d5e5ee; border-radius:10px; padding:22px; margin:18px 0; }
    .episode-lab .lab-actions { display:flex; flex-wrap:wrap; gap:10px; margin:16px 0 0; align-items:center; }
    .episode-lab .lab-actions form { margin:0; }
    .episode-lab .btn { border-radius:6px; padding:9px 14px; font-weight:600; }
    .episode-lab .btn-primary, .episode-lab .btn-primary:hover { background:#0877a8; color:#fff !important; }
    .episode-lab .btn-default { background:#fff; border:1px solid #9cbfce; color:#174d6c !important; }
    .episode-lab .btn-default:hover, .episode-lab .btn-default:focus { background:#eaf5fa; border-color:#0877a8; }
    .episode-lab .lab-status { background:#edf5f9; color:#27526c; padding:6px 10px; border-radius:6px; display:inline-block; }
    .episode-lab .lab-meta { color:#526d80; }
    .episode-lab .lab-step { border-left:4px solid #0877a8; padding-left:14px; margin:20px 0; }
</style>
<div class="episode-lab">
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    <h2>Laboratorio clínico</h2>
    <p>Episodio #{{ $solicitud->id }} · {{ $solicitud->paciente->apellido1 ?? '' }} {{ $solicitud->paciente->nombre1 ?? '' }} · {{ $solicitud->id_paciente }}</p>
    <div class="lab-actions">
        @unless(Request::boolean('embedded'))<a class="btn btn-default" href="{{ route('hospital.documents.index', ['id_solicitud' => $solicitud->id]) }}#formularios-episodio">Volver a formularios del episodio</a>@endunless
        <a class="btn btn-default" href="{{ route('hospital.laboratory.index', ['id_solicitud' => $solicitud->id, 'form' => $focus] + (Request::boolean('embedded') ? ['embedded' => 1] : [])) }}">Actualizar resultados</a>
    </div>
    <div class="lab-card">
        <div class="lab-step">
            <h4>010A · Solicitud de laboratorio clínico</h4>
            <p>Cree la orden, seleccione los exámenes y confirme la solicitud. Los exámenes guardados llenan automáticamente el formulario 010A.</p>
            @if($canRequest)
                <a class="btn btn-primary" href="{{ route('hospital.laboratory.requests', ['id_solicitud' => $solicitud->id] + (Request::boolean('embedded') ? ['embedded' => 1] : [])) }}">Crear / completar solicitud</a>
            @else
                <p class="lab-meta">Para solicitar exámenes se requiere un episodio abierto y un médico o usuario de Sistemas autorizado.</p>
            @endif
        </div>
        <div class="lab-step" id="informe-laboratorio">
            <h4>010B · Informe de laboratorio clínico</h4>
            <p>Abra el área de una orden confirmada para ingresar y guardar sus resultados. El 010B muestra los valores, unidades y referencias registrados. Cada resultado indica si está pendiente de certificar.</p>
            @unless($canRecord)<p class="lab-meta">El ingreso y la certificación de resultados requieren acceso a Control de laboratorio y un episodio que admita edición.</p>@endunless
        </div>
    </div>
    @forelse($reports as $report)
        @php
            $order = $report['order'];
            $areas = $report['exams']->pluck('maquina')->filter(function ($value) { return in_array((string) $value, ['0', '1', '2'], true); })->unique();
            $areaNames = [0 => 'Manual / especiales', 1 => 'Hematología', 2 => 'Bioquímica'];
        @endphp
        <section class="lab-card" aria-label="Orden {{ $order->id }}">
            <h4>Orden #{{ $order->id }} <small>{{ $order->fecha_orden }}</small></h4>
            <p class="lab-status">{{ (int) $order->estado === 1 ? 'Solicitud confirmada' : 'Solicitud pendiente de confirmar' }} · {{ $report['recorded'] }}/{{ $report['expected'] }} resultados · {{ $report['certified'] }} certificados</p>
            <p>{{ $report['exams']->pluck('nombre')->implode(' · ') ?: 'Todavía no se han seleccionado exámenes.' }}</p>
            <div class="lab-actions">
                @if($canRequest && (int) $order->estado === -1 && $report['exams']->isNotEmpty())
                    <form method="post" action="{{ route('hospital.laboratory.confirm', ['id_solicitud' => $solicitud->id, 'order' => $order->id] + (Request::boolean('embedded') ? ['embedded' => 1] : [])) }}">@csrf<button class="btn btn-primary" type="submit">Confirmar solicitud #{{ $order->id }}</button></form>
                @endif
                @if($report['exams']->isNotEmpty())<a class="btn btn-primary" target="_blank" rel="noopener" href="{{ route('hospital.laboratory.pdf', ['id_solicitud' => $solicitud->id, 'order' => $order->id, 'code' => '010A']) }}">Ver solicitud 010A</a>@endif
                @if($report['recorded'] || $report['details']->isNotEmpty())<a class="btn btn-primary" target="_blank" rel="noopener" href="{{ route('hospital.laboratory.pdf', ['id_solicitud' => $solicitud->id, 'order' => $order->id, 'code' => '010B']) }}">Ver resultados 010B</a>@else<span class="lab-meta">010B: aún no hay resultados guardados.</span>@endif
            </div>
            @if($canRecord && (int) $order->estado === 1)
                <div class="lab-actions">
                    @foreach($areas as $area)
                        <form method="post" target="_blank" action="{{ route('hospital.laboratory.results', ['id_solicitud' => $solicitud->id, 'order' => $order->id, 'machine' => $area]) }}">
                            @csrf<button class="btn btn-default" type="submit">Ingresar resultados · {{ $areaNames[$area] }}</button>
                        </form>
                    @endforeach
                    @if($areas->contains(0))
                        <form method="post" target="_blank" action="{{ route('hospital.laboratory.results', ['id_solicitud' => $solicitud->id, 'order' => $order->id, 'machine' => 'special']) }}">
                            @csrf<button class="btn btn-default" type="submit">Panel de resultados especiales</button>
                        </form>
                    @endif
                </div>
            @elseif((int) $order->estado !== 1)
                <p class="lab-meta" style="margin-top:14px">Complete y confirme esta solicitud antes de ingresar resultados.</p>
            @endif
        </section>
    @empty
        <div class="lab-card">Este episodio todavía no tiene solicitudes de laboratorio. Use «Crear / completar solicitud» para registrar la primera orden.</div>
    @endforelse
</div>
@endsection
