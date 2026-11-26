<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Lista de verificación de cirugía segura</title>
    <style>
        @page { margin: 24mm 17mm; }
        body { color: #122f45; font: 10pt "DejaVu Sans", sans-serif; }
        img { max-width: 48mm; max-height: 17mm; }
        h1 { font-size: 17pt; margin: 8pt 0 4pt; }
        h2 { font-size: 12pt; margin-top: 16pt; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; vertical-align: top; border: 1px solid #b9cbd7; padding: 7pt; }
        th { width: 34%; background: #edf4f7; }
        tr { page-break-inside: avoid; }
        .notes { min-height: 21pt; white-space: pre-wrap; }
    </style>
</head>
<body>
    @if($logo)
        <img src="{{ $logo }}" alt="">
    @endif
    <h1>Lista de verificación de cirugía segura</h1>
    <p>{{ $context['institution'] }}</p>
    <table>
        <tr><th>Establecimiento</th><td>{{ $context['establishment'] }}</td></tr>
        <tr><th>Unicódigo</th><td>{{ $context['unicodigo'] }}</td></tr>
        <tr><th>Paciente</th><td>{{ $context['patient_name'] }}</td></tr>
        <tr><th>Historia clínica</th><td>{{ $context['history_number'] }}</td></tr>
        <tr><th>Edad</th><td>{{ $context['age'] }}</td></tr>
        <tr><th>Fecha</th><td>{{ $context['date'] }}</td></tr>
        <tr><th>Quirófano</th><td>{{ $context['operating_room'] }}</td></tr>
        <tr><th>Procedimiento</th><td>{{ $context['procedure'] }}</td></tr>
    </table>
    <h2>Registro de verificación</h2>
    <table>
        @foreach($fields as $label)
            <tr><th>{{ $label }}</th><td class="notes">{{ $notes[$label] ?? '' }}</td></tr>
        @endforeach
    </table>
</body>
</html>
