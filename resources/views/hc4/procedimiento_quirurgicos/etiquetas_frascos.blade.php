<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Etiquetas de frascos</title>
    <style>
        @page { margin: 6mm; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #111; }
        .etiqueta { width: 100%; text-align: center; font-size: 10pt; line-height: 1.3; }
        .etiqueta__descripcion { margin-top: 2mm; font-size: 9pt; overflow-wrap: break-word; }
        .salto { page-break-after: always; }
    </style>
</head>
<body>
@foreach($hc4_biopsias as $biopsia)
    <section class="etiqueta">
        <strong>Fecha:</strong> {{ substr($historia->fecha_atencion ?: $biopsia->created_at, 0, 10) }}<br>
        <strong>N.º frasco:</strong> {{ $biopsia->numero_frasco }}<br>
        <strong>Cédula:</strong> {{ $paciente->id }}<br>
        <strong>Paciente:</strong>
        {{ trim($paciente->nombre1.' '.$paciente->nombre2.' '.$paciente->apellido1.' '.$paciente->apellido2) }}
        <div class="etiqueta__descripcion">
            <strong>Descripción:</strong> {{ $biopsia->descripcion_frasco }}
        </div>
    </section>
    @if(!$loop->last)<div class="salto"></div>@endif
@endforeach
</body>
</html>
