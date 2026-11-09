@extends('layouts.app-template-h')
@section('content')
<section class="content">
    <h3>{{ $document->title }}</h3>
    <a class="btn btn-default" href="{{ route('hospital.documents.index', ['id_solicitud' => $solicitud->id]) }}"><i class="fa fa-arrow-left"></i> Documentos</a>
    <p>{{ $document->signed_at ? 'Firmado' : 'Sin firma' }}: {{ $document->signed_by }} {{ $document->signed_at }}</p>
    <div class="table-responsive"><table class="table table-bordered table-condensed">
        <tbody>@foreach($fields as $key => $value)<tr><th>{{ $key }}</th><td>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</td></tr>@endforeach</tbody>
    </table></div>
    @if($document->amendments->count())
        <h4>Adendas</h4>
        <table class="table table-bordered table-condensed">
            <thead><tr><th>Version</th><th>Autor</th><th>Fecha</th><th>Motivo</th></tr></thead>
            <tbody>@foreach($document->amendments->sortBy('version') as $amendment)<tr><td>{{ $amendment->version }}</td><td>{{ $amendment->authored_by }}</td><td>{{ $amendment->authored_at }}</td><td>{{ $amendment->reason }}</td></tr>@endforeach</tbody>
        </table>
        <details><summary>Documento firmado original</summary><pre>{{ $document->content_snapshot }}</pre></details>
    @endif
</section>
@endsection
