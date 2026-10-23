<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte coche de paro</title>

    <style>
        @page { margin: 10px 12px 10px 12px; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 8px;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .page-container {
            height: 190mm;
            position: relative;
            page-break-inside: avoid;
        }

        .page-break { page-break-after: always; }

        .header {
            width: 100%;
            margin-bottom: 6px;
            border-bottom: 2px solid #1e40af;
            padding-bottom: 5px;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0;
            letter-spacing: 0.3px;
        }

        .subtitle {
            text-align: center;
            font-size: 10px;
            color: #475569;
            margin: 2px 0 0 0;
        }

        .meta { width: 100%; border-collapse: collapse; margin-top: 6px; margin-bottom: 6px; }
        .meta td { border: 1px solid #cbd5e1; padding: 5px 6px; font-size: 8px; background: #f8fafc; }
        .meta-label { font-weight: bold; background: #e2e8f0 !important; width: 80px; }

        .legend { margin-bottom: 6px; font-size: 8px; color: #475569; }
        .legend-item { display: inline-block; margin-right: 14px; }
        .legend-box-danger, .legend-box-warning {
            display: inline-block; width: 8px; height: 8px;
            margin-right: 4px; vertical-align: middle;
        }
        .legend-box-danger  { background: #fee2e2; border: 1px solid #ef4444; }
        .legend-box-warning { background: #fef3c7; border: 1px solid #f59e0b; }

        .table-wrapper { height: 150mm; }

        table.report { width: 100%; border-collapse: collapse; }
        table.report thead { display: table-header-group; }

        table.report th,
        table.report td {
            border: 1px solid #cbd5e1;
            text-align: center;
            vertical-align: middle;
            padding: 3px 2px;
            height: 22px;
        }

        table.report th {
            background: #1e293b;
            color: #ffffff;
            font-size: 7.5px;
            font-weight: bold;
            line-height: 1.1;
            white-space: nowrap;
        }

        table.report td { font-size: 7px; line-height: 1.15; }

        table.report tfoot td {
            border: 1px solid #cbd5e1;
            text-align: center;
            vertical-align: middle;
            padding: 0;
            height: 20px;
        }

        tbody tr:nth-child(even) { background: #f8fafc; }
        .danger-row  { background: #fee2e2 !important; }
        .warning-row { background: #fef3c7 !important; }

        .col-num    { width: 18px; }
        .col-nombre {
            width: 70px !important;
            text-align: center !important;
            padding-left: 5px !important;
            white-space: normal;
            word-break: normal;
            overflow-wrap: break-word;
        }
        .col-pres { width: 42px; }
        .col-cant { width: 28px; }
        .col-cad  { width: 50px; }
        .col-dia  { width: 15px; padding: 1px !important; font-size: 6.5px; }

        .footer-fixed { position: absolute; bottom: 0; left: 0; right: 0; }
        .footer { margin-top: 6px; text-align: right; font-size: 7px; color: #64748b; }
        .no-data { text-align: center; font-weight: bold; padding: 10px; }
        .firma-strip { display: block; width: 100%; height: 20px; }
    </style>
</head>

<body>

@php
    use Carbon\Carbon;

    // =========================================================================
    // 1. DEFAULTS
    // =========================================================================

    if (!isset($diasMes) || !$diasMes) {
        $diasMes = Carbon::now()->daysInMonth;
    }

    $hoy       = date('Y-m-d');
    $unaSemana = date('Y-m-d', strtotime('+7 days'));

    if (!isset($mesNombre))      { $mesNombre      = ''; }
    if (!isset($anio))           { $anio           = date('Y'); }
    if (!isset($stock))          { $stock          = []; }
    if (!isset($presentaciones)) { $presentaciones = []; }

    // =========================================================================
    // 2. NOMBRE RESPONSABLE
    // =========================================================================

    $nombreResponsable = '';
    if (isset($responsable)) {
        $nombreResponsable = trim(
            ($responsable->nombre1   ?? '') . ' ' .
            ($responsable->nombre2   ?? '') . ' ' .
            ($responsable->apellido1 ?? '') . ' ' .
            ($responsable->apellido2 ?? '')
        );
        if ($nombreResponsable === '') {
            $nombreResponsable = $responsable->name ?? '';
        }
    }

    // =========================================================================
    // 3. NOMBRE SERVICIO
    // =========================================================================

    if (!isset($servicio))  { $nombreServicio = ''; }
    elseif ($servicio == 1) { $nombreServicio = 'GASTROENTEROLOGÍA'; }
    else                    { $nombreServicio = $servicio; }

    // =========================================================================
    // 4. HELPER CLASE FILA
    // =========================================================================

    if (!function_exists('_cocheParo_claseFila')) {
        function _cocheParo_claseFila($fecha, $hoy, $unaSemana) {
            if (!$fecha)              return '';
            if ($fecha < $hoy)        return 'danger-row';
            if ($fecha <= $unaSemana) return 'warning-row';
            return '';
        }
    }

    // =========================================================================
    // 5. MAPA PRESENTACIONES O(1)
    // =========================================================================

    $mapPres = [];
    foreach ($presentaciones as $p) {
        $id     = is_array($p) ? ($p['id']     ?? null) : ($p->id     ?? null);
        $unidad = is_array($p) ? ($p['unidad'] ?? '')   : ($p->unidad ?? '');
        if ($id !== null) { $mapPres[$id] = $unidad; }
    }

    // =========================================================================
    // 6. IDs ÚNICOS DE RESPONSABLES
    // =========================================================================

    $idsResp = [];
    foreach ([0, 1] as $tipo) {
        if (!empty($stock[$tipo])) {
            foreach ($stock[$tipo] as $item) {
                if (!empty($item->responsable_id)) {
                    $idsResp[$item->responsable_id] = true;
                }
            }
        }
    }
    $idsResp = array_keys($idsResp);

    // =========================================================================
    // 7. QUERY ÚNICA DE FIRMAS
    // =========================================================================

    $firmasPorUsuario = [];
    if (!empty($idsResp)) {
        foreach (\Sis_medico\Firma_Usuario::whereIn('id_usuario', $idsResp)->get() as $f) {
            if (!empty($f->id_usuario)) {
                $firmasPorUsuario[$f->id_usuario] = $f;
            }
        }
    }

    // =========================================================================
    // 8. CARGAR IMAGEN GD UNA VEZ POR USUARIO
    //    Normalizar siempre a PNG con fondo BLANCO para que dompdf
    //    no muestre el fondo negro del canvas truecolor.
    // =========================================================================

    // Convierte cualquier imagen a un GD truecolor con fondo blanco
    if (!function_exists('_cocheParo_gdConFondoBlanco')) {
        function _cocheParo_gdConFondoBlanco($ruta) {
            $ext = strtolower(pathinfo($ruta, PATHINFO_EXTENSION));

            if (in_array($ext, ['jpg', 'jpeg'])) {
                $src = @imagecreatefromjpeg($ruta);
            } elseif ($ext === 'gif') {
                $src = @imagecreatefromgif($ruta);
            } else {
                $src = @imagecreatefrompng($ruta);
            }

            if (!$src) { return null; }

            $w = imagesx($src);
            $h = imagesy($src);

            // Canvas truecolor con fondo BLANCO puro
            $dst = imagecreatetruecolor($w, $h);
            $blanco = imagecolorallocate($dst, 255, 255, 255);
            imagefilledrectangle($dst, 0, 0, $w - 1, $h - 1, $blanco);

            // Copiar sobre fondo blanco — aplana el canal alpha
            imagecopy($dst, $src, 0, 0, 0, 0, $w, $h);
            imagedestroy($src);

            return $dst;
        }
    }

    $gdPorUsuario = []; // id_usuario => resource GD (fondo blanco) | null

    foreach ($firmasPorUsuario as $idU => $firmaObj) {
        if (empty($firmaObj->nombre)) {
            $gdPorUsuario[$idU] = null;
            continue;
        }
        $ruta = storage_path('app/avatars/' . $firmaObj->nombre);
        if (!file_exists($ruta)) {
            $gdPorUsuario[$idU] = null;
            continue;
        }
        $gdPorUsuario[$idU] = _cocheParo_gdConFondoBlanco($ruta);
    }

    // =========================================================================
    // 9. MAPA  día => id_responsable
    // =========================================================================

    $buildMapaDia = function ($items) use ($diasMes) {
        $mapa = [];
        foreach ($items as $item) {
            if (empty($item->responsable_id)) { continue; }

            if (isset($item->dias) && is_array($item->dias)) {
                foreach ($item->dias as $dia => $valor) {
                    if ($valor !== '' && $valor !== null) {
                        $d = (int) $dia;
                        if ($d >= 1 && $d <= $diasMes && !isset($mapa[$d])) {
                            $mapa[$d] = $item->responsable_id;
                        }
                    }
                }
                continue;
            }

            if (!empty($item->fecha)) {
                $d = (int) date('d', strtotime($item->fecha));
                if ($d >= 1 && $d <= $diasMes && !isset($mapa[$d])) {
                    $mapa[$d] = $item->responsable_id;
                }
            }
        }
        return $mapa;
    };

    // =========================================================================
    // 10. GENERAR LA "TIRA" — 1 PNG con todas las firmas del mes
    //
    //     - Fondo blanco en todo el canvas
    //     - Cada firma se copia en su columna (dia-1)*$pxPorDia
    //     - 1 solo <img> en el HTML → dompdf lo procesa 1 sola vez
    // =========================================================================

    $pxPorDia = 15;
    $altoPx   = 20;

    $buildTira = function ($items) use ($buildMapaDia, $gdPorUsuario, $diasMes, $pxPorDia, $altoPx) {

        $mapaDia = $buildMapaDia($items);
        if (empty($mapaDia)) { return null; }

        $anchoPx = $diasMes * $pxPorDia;

        // Canvas con fondo BLANCO explícito
        $tira   = imagecreatetruecolor($anchoPx, $altoPx);
        $blanco = imagecolorallocate($tira, 255, 255, 255);
        imagefilledrectangle($tira, 0, 0, $anchoPx - 1, $altoPx - 1, $blanco);

        foreach ($mapaDia as $dia => $respId) {
            $gdFirma = $gdPorUsuario[$respId] ?? null;
            if (!$gdFirma) { continue; }

            $xDest = ($dia - 1) * $pxPorDia;
            $wOrig = imagesx($gdFirma);
            $hOrig = imagesy($gdFirma);

            // Copiar la firma escalada a la celda del día correspondiente
            imagecopyresampled(
                $tira,     $gdFirma,
                $xDest,    0,
                0,         0,
                $pxPorDia, $altoPx,
                $wOrig,    $hOrig
            );
        }

        // Exportar como PNG en base64
        ob_start();
        imagepng($tira);
        $png = ob_get_clean();
        imagedestroy($tira);

        return 'data:image/png;base64,' . base64_encode($png);
    };

    $tiraInsumos = !empty($stock[1]) ? $buildTira($stock[1]) : null;
    $tiraMedics  = !empty($stock[0]) ? $buildTira($stock[0]) : null;

    // Liberar GD
    foreach ($gdPorUsuario as $gd) {
        if ($gd) { imagedestroy($gd); }
    }
    unset($gdPorUsuario);

    $fechaGeneracion = date('d/m/Y H:i');
@endphp


{{-- =========================================================================
     PÁGINA 1 — INSUMOS
     ========================================================================= --}}
<div class="page-container">

    <div class="header">
        <p class="title">CONTROL DE INSUMOS DE COCHE DE PARO</p>
        <p class="subtitle">Reporte mensual de control y verificación</p>
    </div>

    <table class="meta">
        <tr>
            <td class="meta-label">Responsable</td>
            <td>{{ $nombreResponsable }}</td>
            <td class="meta-label">Servicio</td>
            <td>{{ $nombreServicio }}</td>
            <td class="meta-label">Periodo</td>
            <td>{{ $mesNombre }} {{ $anio }}</td>
        </tr>
    </table>

    <div class="legend">
        <span class="legend-item"><span class="legend-box-danger"></span>Caducado</span>
        <span class="legend-item"><span class="legend-box-warning"></span>Próximo a caducar (7 días)</span>
    </div>

    <div class="table-wrapper">
        <table class="report">
            <thead>
                <tr>
                    <th class="col-num"    rowspan="2">#</th>
                    <th class="col-nombre" rowspan="2">Insumo</th>
                    <th class="col-nombre" rowspan="2">Lote</th>
                    <th class="col-pres"   rowspan="2">Present.</th>
                    <th class="col-cant"   rowspan="2">Cant.</th>
                    <th class="col-cad"    rowspan="2">Caduca</th>
                    <th colspan="{{ $diasMes }}">DÍAS DEL MES</th>
                </tr>
                <tr>
                    @for ($i = 1; $i <= $diasMes; $i++)
                        <th class="col-dia">{{ $i }}</th>
                    @endfor
                </tr>
            </thead>

            <tbody>
                @forelse ($stock[1] ?? [] as $index => $val)
                    @php
                        $diasVal   = $val->dias ?? [];
                        $caduca    = $val->fecha_caducidad ?? '';
                        $filaClass = _cocheParo_claseFila($caduca, $hoy, $unaSemana);
                        $presTexto = $mapPres[$val->idPresentacion ?? null] ?? '';
                    @endphp
                    <tr class="{{ $filaClass }}">
                        <td class="col-num">{{ $index + 1 }}</td>
                        <td class="col-nombre">{{ $val->producto ?? '' }}</td>
                        <td>{{ $val->lote ?? '' }}</td>
                        <td class="col-pres">{{ $presTexto }}</td>
                        <td class="col-cant">{{ $val->stock ?? '' }}</td>
                        <td class="col-cad">{{ $caduca }}</td>
                        @for ($i = 1; $i <= $diasMes; $i++)
                            <td class="col-dia">{{ $diasVal[$i] ?? '' }}</td>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 6 + $diasMes }}" class="no-data">
                            No existen registros de insumos para mostrar.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="6"
                        style="text-align:center;font-weight:bold;font-size:6px;background:#f8fafc;padding:3px 2px;">
                        Firma Responsable
                    </td>
                    <td colspan="{{ $diasMes }}" style="background:#ffffff;padding:0;">
                        @if ($tiraInsumos)
                            <img src="{{ $tiraInsumos }}" class="firma-strip" alt="firmas">
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer-fixed">
        <div class="footer">Generado el {{ $fechaGeneracion }}</div>
    </div>

</div>

<div class="page-break"></div>

{{-- =========================================================================
     PÁGINA 2 — MEDICAMENTOS
     ========================================================================= --}}
<div class="page-container">

    <div class="header">
        <p class="title">CONTROL DE MEDICAMENTOS DE COCHE DE PARO</p>
        <p class="subtitle">Reporte mensual de control y verificación</p>
    </div>

    <table class="meta">
        <tr>
            <td class="meta-label">Responsable</td>
            <td>{{ $nombreResponsable }}</td>
            <td class="meta-label">Servicio</td>
            <td>{{ $nombreServicio }}</td>
            <td class="meta-label">Periodo</td>
            <td>{{ $mesNombre }} {{ $anio }}</td>
        </tr>
    </table>

    <div class="legend">
        <span class="legend-item"><span class="legend-box-danger"></span>Caducado</span>
        <span class="legend-item"><span class="legend-box-warning"></span>Próximo a caducar (7 días)</span>
    </div>

    <div class="table-wrapper">
        <table class="report">
            <thead>
                <tr>
                    <th class="col-num"    rowspan="2">#</th>
                    <th class="col-nombre" rowspan="2">Medicamento</th>
                    <th class="col-nombre" rowspan="2">Lote</th>
                    <th class="col-pres"   rowspan="2">Present.</th>
                    <th class="col-cant"   rowspan="2">Cant.</th>
                    <th class="col-cad"    rowspan="2">Caduca</th>
                    <th colspan="{{ $diasMes }}">DÍAS DEL MES</th>
                </tr>
                <tr>
                    @for ($i = 1; $i <= $diasMes; $i++)
                        <th class="col-dia">{{ $i }}</th>
                    @endfor
                </tr>
            </thead>

            <tbody>
                @forelse ($stock[0] ?? [] as $index => $val)
                    @php
                        $diasVal   = $val->dias ?? [];
                        $caduca    = $val->fecha_caducidad ?? '';
                        $filaClass = _cocheParo_claseFila($caduca, $hoy, $unaSemana);
                        $presTexto = $mapPres[$val->idPresentacion ?? null] ?? '';
                    @endphp
                    <tr class="{{ $filaClass }}">
                        <td class="col-num">{{ $index + 1 }}</td>
                        <td class="col-nombre">{{ $val->producto ?? '' }}</td>
                        <td>{{ $val->lote ?? '' }}</td>
                        <td class="col-pres">{{ $presTexto }}</td>
                        <td class="col-cant">{{ $val->stock ?? '' }}</td>
                        <td class="col-cad">{{ $caduca }}</td>
                        @for ($i = 1; $i <= $diasMes; $i++)
                            <td class="col-dia">{{ $diasVal[$i] ?? '' }}</td>
                        @endfor
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ 6 + $diasMes }}" class="no-data">
                            No existen registros de medicamentos para mostrar.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="6"
                        style="text-align:center;font-weight:bold;font-size:6px;background:#f8fafc;padding:3px 2px;">
                        Firma Responsable
                    </td>
                    <td colspan="{{ $diasMes }}" style="background:#ffffff;padding:0;">
                        @if ($tiraMedics)
                            <img src="{{ $tiraMedics }}" class="firma-strip" alt="firmas">
                        @endif
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="footer-fixed">
        <div class="footer">Generado el {{ $fechaGeneracion }}</div>
    </div>

</div>

</body>
</html>
