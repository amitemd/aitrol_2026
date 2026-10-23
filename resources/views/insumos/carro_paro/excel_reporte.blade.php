<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte coche de paro</title>
</head>
<body>
@php
    use Carbon\Carbon;

    $now = Carbon::now();
    $diasMes = $now->daysInMonth;
    $hoy = date('Y-m-d');
    $unaSemana = date('Y-m-d', strtotime('+7 days'));

    $nombreResponsable = trim(
        ($responsable['nombre1'] ?? '') . ' ' .
        ($responsable['nombre2'] ?? '') . ' ' .
        ($responsable['apellido1'] ?? '') . ' ' .
        ($responsable['apellido2'] ?? '')
    );

    $nombreServicio = '';
    if (isset($servicio) && $servicio == 1) {
        $nombreServicio = 'GASTROENTEROLOGÍA';
    } else {
        $nombreServicio = $servicio ?? '';
    }

    function buscarPresentacionExcel($presentaciones, $idPresentacion) {
        foreach ($presentaciones as $p) {
            $id = is_array($p) ? ($p['id'] ?? null) : ($p->id ?? null);
            $unidad = is_array($p) ? ($p['unidad'] ?? '') : ($p->unidad ?? '');
            if ($id == $idPresentacion) {
                return $unidad;
            }
        }
        return '';
    }

    function buscarConcentracionExcel($concentracion, $idConcentracion) {
        foreach ($concentracion as $c) {
            $id = is_array($c) ? ($c['id'] ?? null) : ($c->id ?? null);
            $nombre = is_array($c) ? ($c['nombre'] ?? '') : ($c->nombre ?? '');
            if ($id == $idConcentracion) {
                return $nombre;
            }
        }
        return '';
    }

    function estiloFilaExcel($fechaVence, $hoy, $unaSemana) {
        if (empty($fechaVence)) {
            return '';
        }
        if ($fechaVence < $hoy) {
            return 'background-color: #fee2e2;';
        }
        if ($fechaVence <= $unaSemana) {
            return 'background-color: #fef3c7;';
        }
        return '';
    }
@endphp

{{-- ============================ INSUMOS ============================ --}}
<table>
    <tr>
        <td colspan="{{ 5 + $diasMes }}" style="text-align:center; font-weight:bold; font-size:16px; color:#1e3a8a;">
            CONTROL DE INSUMOS DE COCHE DE PARO
        </td>
    </tr>
    <tr>
        <td colspan="{{ 5 + $diasMes }}" style="text-align:center; font-size:11px; color:#475569;">
            Reporte mensual de control y verificación
        </td>
    </tr>
</table>

<table border="1" style="border-collapse: collapse; margin-top: 5px;">
    <tr>
        <td style="font-weight:bold; background:#e2e8f0;">Responsable</td>
        <td>{{ $nombreResponsable }}</td>
        <td style="font-weight:bold; background:#e2e8f0;">Servicio</td>
        <td>{{ $nombreServicio }}</td>
        <td style="font-weight:bold; background:#e2e8f0;">Periodo</td>
        <td>{{ $mesNombre }} {{ $anio }}</td>
    </tr>
</table>

<table style="margin-top: 5px;">
    <tr>
        <td style="background:#fee2e2; border:1px solid #ef4444;">&nbsp;&nbsp;&nbsp;</td>
        <td>Caducado</td>
        <td>&nbsp;&nbsp;</td>
        <td style="background:#fef3c7; border:1px solid #f59e0b;">&nbsp;&nbsp;&nbsp;</td>
        <td>Próximo a caducar (7 días)</td>
    </tr>
</table>

<table border="1" style="border-collapse: collapse; margin-top: 5px;">
    <thead>
        <tr>
            <th rowspan="2" style="background:#1e293b; color:#ffffff;">#</th>
            <th rowspan="2" style="background:#1e293b; color:#ffffff;">Insumo</th>
            <th rowspan="2" style="background:#1e293b; color:#ffffff;">Present.</th>
            <th rowspan="2" style="background:#1e293b; color:#ffffff;">Cant.</th>
            <th rowspan="2" style="background:#1e293b; color:#ffffff;">Caduca</th>
            <th colspan="{{ $diasMes }}" style="background:#1e293b; color:#ffffff;">DÍAS DEL MES</th>
        </tr>
        <tr>
            @for($i = 1; $i <= $diasMes; $i++)
                <th style="background:#1e293b; color:#ffffff;">{{ $i }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @if(isset($stock[1]) && count($stock[1]) > 0)
            @foreach($stock[1] as $index => $val)
                @php
                    $presentacionTexto = buscarPresentacionExcel($presentaciones, $val->idPresentacion ?? null);
                    $estiloFila = estiloFilaExcel($val->fecha_vence ?? '', $hoy, $unaSemana);
                @endphp
                <tr style="{{ $estiloFila }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $val->producto ?? '' }}</td>
                    <td>{{ $presentacionTexto }}</td>
                    <td>{{ $val->stock_disponible ?? '' }}</td>
                    <td>{{ $val->fecha_vence ?? '' }}</td>

                    @for($i = 1; $i <= $diasMes; $i++)
                        @php
                            $detalleDia = $insumos->first(function ($item) use ($i, $val) {
                                return \Carbon\Carbon::parse($item->created_at)->day == $i
                                    && $item->cabecera->tipo == 2
                                    && ($val->producto_id ?? null) == $item->producto_lote->producto_id;
                            });
                        @endphp
                        <td>{{ $detalleDia ? $detalleDia->stock : '' }}</td>
                    @endfor
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="{{ 5 + $diasMes }}" style="text-align:center; font-weight:bold;">
                    No existen registros de insumos para mostrar.
                </td>
            </tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5" style="text-align:center; font-weight:bold; background:#f8fafc;">
                Firma Responsable
            </td>

            @for ($i = 1; $i <= $diasMes; $i++)
                @php
                    $detalleDia = $insumos->first(function ($item) use ($i) {
                        return \Carbon\Carbon::parse($item->cabecera->created_at)->day == $i
                            && $item->cabecera->tipo == 2;
                    });
                @endphp
                <td>
                    @if($detalleDia && $detalleDia->cabecera && $detalleDia->cabecera->firma)
                        {{ $detalleDia->cabecera->firma->nombre }}
                    @endif
                </td>
            @endfor
        </tr>
    </tfoot>
</table>

<table>
    <tr>
        <td colspan="{{ 5 + $diasMes }}" style="text-align:right; color:#64748b;">
            Generado el {{ date('d/m/Y H:i') }}
        </td>
    </tr>
</table>

<br>
<br>

{{-- ============================ MEDICAMENTOS ============================ --}}
<table>
    <tr>
        <td colspan="{{ 6 + $diasMes }}" style="text-align:center; font-weight:bold; font-size:16px; color:#1e3a8a;">
            CONTROL DE MEDICAMENTOS DE COCHE DE PARO
        </td>
    </tr>
    <tr>
        <td colspan="{{ 6 + $diasMes }}" style="text-align:center; font-size:11px; color:#475569;">
            Reporte mensual de control y verificación
        </td>
    </tr>
</table>

<table border="1" style="border-collapse: collapse; margin-top: 5px;">
    <tr>
        <td style="font-weight:bold; background:#e2e8f0;">Responsable</td>
        <td>{{ $nombreResponsable }}</td>
        <td style="font-weight:bold; background:#e2e8f0;">Servicio</td>
        <td>{{ $nombreServicio }}</td>
        <td style="font-weight:bold; background:#e2e8f0;">Periodo</td>
        <td>{{ $mesNombre }} {{ $anio }}</td>
    </tr>
</table>

<table style="margin-top: 5px;">
    <tr>
        <td style="background:#fee2e2; border:1px solid #ef4444;">&nbsp;&nbsp;&nbsp;</td>
        <td>Caducado</td>
        <td>&nbsp;&nbsp;</td>
        <td style="background:#fef3c7; border:1px solid #f59e0b;">&nbsp;&nbsp;&nbsp;</td>
        <td>Próximo a caducar (7 días)</td>
    </tr>
</table>

<table border="1" style="border-collapse: collapse; margin-top: 5px;">
    <thead>
        <tr>
            <th rowspan="2" style="background:#1e293b; color:#ffffff;">#</th>
            <th rowspan="2" style="background:#1e293b; color:#ffffff;">Medicamento</th>
            <th rowspan="2" style="background:#1e293b; color:#ffffff;">Present.</th>
            <th rowspan="2" style="background:#1e293b; color:#ffffff;">Concen.</th>
            <th rowspan="2" style="background:#1e293b; color:#ffffff;">Cant.</th>
            <th rowspan="2" style="background:#1e293b; color:#ffffff;">Caduca</th>
            <th colspan="{{ $diasMes }}" style="background:#1e293b; color:#ffffff;">DÍAS DEL MES</th>
        </tr>
        <tr>
            @for($i = 1; $i <= $diasMes; $i++)
                <th style="background:#1e293b; color:#ffffff;">{{ $i }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @if(isset($stock[0]) && count($stock[0]) > 0)
            @foreach($stock[0] as $index => $val)
                @php
                    $presentacionTexto = buscarPresentacionExcel($presentaciones, $val->idPresentacion ?? null);
                    $concentracionTexto = buscarConcentracionExcel($concentracion, $val->idConcetracion ?? null);
                    $estiloFila = estiloFilaExcel($val->fecha_vence ?? '', $hoy, $unaSemana);
                @endphp
                <tr style="{{ $estiloFila }}">
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $val->producto ?? '' }}</td>
                    <td>{{ $presentacionTexto }}</td>
                    <td>{{ $concentracionTexto }}</td>
                    <td>{{ $val->stock_disponible ?? '' }}</td>
                    <td>{{ $val->fecha_vence ?? '' }}</td>

                    @for($i = 1; $i <= $diasMes; $i++)
                        @php
                            $detalleDia = $insumos->first(function ($item) use ($i, $val) {
                                return \Carbon\Carbon::parse($item->created_at)->day == $i
                                    && $item->cabecera->tipo == 1
                                    && ($val->producto_id ?? null) == $item->producto_lote->producto_id;
                            });
                        @endphp
                        <td>{{ $detalleDia ? $detalleDia->stock : '' }}</td>
                    @endfor
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="{{ 6 + $diasMes }}" style="text-align:center; font-weight:bold;">
                    No existen registros de medicamentos para mostrar.
                </td>
            </tr>
        @endif
    </tbody>
    <tfoot>
        <tr>
            <td colspan="6" style="text-align:center; font-weight:bold; background:#f8fafc;">
                Firma Responsable
            </td>

            @for ($i = 1; $i <= $diasMes; $i++)
                @php
                    $detalleDia = $insumos->first(function ($item) use ($i) {
                        return \Carbon\Carbon::parse($item->cabecera->created_at)->day == $i
                            && $item->cabecera->tipo == 1;
                    });
                @endphp
                <td>
                    @if($detalleDia && $detalleDia->cabecera && $detalleDia->cabecera->firma)
                        {{ $detalleDia->cabecera->firma->nombre }}
                    @endif
                </td>
            @endfor
        </tr>
    </tfoot>
</table>

<table>
    <tr>
        <td colspan="{{ 6 + $diasMes }}" style="text-align:right; color:#64748b;">
            Generado el {{ date('d/m/Y H:i') }}
        </td>
    </tr>
</table>

</body>
</html>