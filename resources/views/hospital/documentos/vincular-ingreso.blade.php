@extends('layouts.app-template-h')

@section('content')
<div class="container py-4">
    <h3>Documentos de la cirugía</h3>
    <p>Esta cirugía fue programada desde Agenda. Vincula el ingreso hospitalario para abrir sus documentos.</p>
    @if($errors->has('documentos'))
        <div class="alert alert-warning" role="alert">{{ $errors->first('documentos') }}</div>
    @endif
    <form method="POST" action="{{ route('hospital.documents.link-scheduled-admission', ['id_solicitud' => $idSolicitud]) }}">
        @csrf
        <button type="submit" class="btn btn-primary">Vincular ingreso y abrir documentos</button>
        <a href="{{ route('hospital.index_modulos', ['id_paso' => 5]) }}" class="btn btn-default">Volver a Cirugía</a>
    </form>
</div>
@endsection
