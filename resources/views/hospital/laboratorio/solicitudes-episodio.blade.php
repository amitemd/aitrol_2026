@extends(Request::boolean('embedded') ? 'layouts.hospital-embedded-module' : 'layouts.app-template-h')
@section('title', 'Solicitud de laboratorio · Episodio '.$solicitud->id)
@section('content')
<div style="padding:24px 18px">
    <h3>Solicitud de laboratorio · Episodio #{{ $solicitud->id }}</h3>
    <p>Seleccione la cobertura, agregue los exámenes y confirme la orden. Al volver, el formulario 010A mostrará los datos guardados.</p>
    <a class="btn btn-primary" style="color:#fff;margin-bottom:18px" href="{{ route('hospital.laboratory.index', ['id_solicitud' => $solicitud->id] + (Request::boolean('embedded') ? ['embedded' => 1] : [])) }}">Volver a laboratorio y ver formularios</a>
    <div id="content">@include('hospital.emergencia.orden_laboratorio.index', $nativeData)</div>
</div>
@if(Request::boolean('embedded'))
<script>
document.addEventListener('submit', function (event) {
    var form = event.target;
    if (!form || !form.matches('form[action*="/laboratorio/"][action$="/confirmar"]')) return;
    var action = new URL(form.action, window.location.href);
    action.searchParams.set('embedded', '1');
    form.action = action.href;
}, true);
</script>
@endif
@endsection
