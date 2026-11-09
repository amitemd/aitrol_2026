<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $document->title }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 26px; color: #24445c; background: #fff; font: 14px/1.5 Arial, sans-serif; }
        h1 { margin: 0 0 8px; color: #123b5d; font-size: 22px; }
        h2 { margin: 28px 0 10px; color: #123b5d; font-size: 17px; }
        .meta { margin-bottom: 24px; color: #61798c; }
        .meta span { display: inline-block; margin: 0 12px 5px 0; }
        table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        th, td { padding: 11px 12px; border: 1px solid #dce9f1; text-align: left; vertical-align: top; overflow-wrap: anywhere; white-space: pre-wrap; }
        th { width: 30%; background: #f0f7fb; color: #315875; }
        thead th { width: auto; }
        tr:nth-child(even) td { background: #f9fcfe; }
        pre { padding: 14px; overflow: auto; border: 1px solid #dce9f1; border-radius: 6px; background: #f8fbfd; white-space: pre-wrap; }
        @media (max-width: 600px) { body { padding: 16px; } th { width: 38%; } }
    </style>
</head>
<body>
    <h1>{{ $document->title }}</h1>
    <div class="meta">
        <span>Episodio {{ $solicitud->id }}</span>
        <span>{{ $document->signed_at ? 'Firmado' : 'Sin firma' }}</span>
        @if($document->signed_at)<span>{{ $document->signed_by }} · {{ $document->signed_at }}</span>@endif
    </div>
    <table aria-label="Contenido del documento">
        <tbody>
            @forelse($fields as $key => $value)
                <tr><th scope="row">{{ $key }}</th><td>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : $value }}</td></tr>
            @empty
                <tr><td>Este documento todavía no tiene contenido clínico registrado.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($document->amendments->count())
        <h2>Adendas</h2>
        <table aria-label="Adendas del documento">
            <thead><tr><th>Versión</th><th>Autor</th><th>Fecha</th><th>Motivo</th></tr></thead>
            <tbody>@foreach($document->amendments->sortBy('version') as $amendment)
                <tr><td>{{ $amendment->version }}</td><td>{{ $amendment->authored_by }}</td><td>{{ $amendment->authored_at }}</td><td>{{ $amendment->reason }}</td></tr>
            @endforeach</tbody>
        </table>
        <details><summary>Documento firmado original</summary><pre>{{ $document->content_snapshot }}</pre></details>
    @endif
</body>
</html>
