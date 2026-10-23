@extends('layouts.app-template')
@section('content')
<style>
    .table-coche-paro {
        width: 100%;
        border-collapse: collapse;
        font-family: Arial, sans-serif;
        font-size: 11px;
    }

    .table-coche-paro th,
    .table-coche-paro td {
        border: 1px solid #000;
        padding: 4px;
        text-align: center;
    }

    .table-coche-paro th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .header-title {
        font-size: 18px;
        font-weight: bold;
        text-align: center;
        background-color: #d9e1f2 !important;
    }

    .month-header {
        font-weight: bold;
        text-align: left;
    }

    .table-responsive {
        overflow-x: auto;
    }

    .custom-tabs {
        padding: 0 10px;
        gap: 5px;
        border-bottom: none !important;
    }

    .custom-tabs .nav-link {
        color: #718096;
        font-weight: 500;
        font-size: 0.95rem;
        padding: 15px 25px;
        border: none;
        background: transparent;
        transition: all 0.2s ease-in-out;
        position: relative;
        border-radius: 0;
    }

    .custom-tabs .nav-link:hover {
        color: #2d3748;
        background-color: #f7fafc;
    }

    .custom-tabs .nav-link.active {
        color: #3182ce !important;
        background-color: transparent !important;
        font-weight: 700;
    }

    .custom-tabs .nav-link.active::after {
        content: "";
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: #3182ce;
        border-radius: 10px 10px 0 0;

    }

    .custom-tab-content {
        padding: 40px;
        border-top: none;
        border-radius: 0 0 12px 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);

    }

    /* Ajuste para iconos si los usas */
    .custom-tabs .nav-link i {
        margin-right: 8px;
        font-size: 1.1rem;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Control de Insumos de Coche de Paro
        </h1>
    </section>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Registros</h3>
            </div>
            <div class="box-header">
                <button type="button" class="btn btn-warning" onclick="history.back()">
                    <i class="fa fa-arrow-left" aria-hidden="true"></i>
                    Regresar
                </button>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <nav>
                        <div class="nav nav-tabs nav-fill custom-tabs" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" data-toggle="tab" href="#nav-insumos">
                                <i class="fas fa-box"></i> Insumos
                            </a>
                            <a class="nav-item nav-link" data-toggle="tab" href="#nav-medicamentos">
                                <i class="fas fa-pills"></i> Medicamentos
                            </a>

                        </div>
                    </nav>

                    <div class="tab-content custom-tab-content" id="nav-tabContent">
                        <div class="tab-pane fade in active" id="nav-insumos" role="tabpanel">
                            <table class="table-coche-paro">
                                <thead>
                                    <tr>
                                        <th colspan="37" class="header-title">
                                            CONTROL DE INSUMOS DE COCHE DE PARO
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" style="text-align: left;">LCDO. {{$responsable["nombre1"]}} {{$responsable["nombre2"]}} {{$responsable["apellido1"]}} {{$responsable["apellido2"]}}<br>SERVICIO:
                                            {{ isset($servicio) && $servicio == 1 ? 'GASTROENTEROLOGIA' : '' }}
                                        </th>
                                        <th colspan="32" style="text-align: center;" class="month-header">{{$mesNombre}} {{$anio}}</th>
                                    </tr>
                                    <tr>
                                        <th>ITEM</th>
                                        <th>INSUMO</th>
                                        <th>PRESENTACIÓN</th>
                                        <th>CANTIDAD</th>
                                        <th>FECHA DE CADUCIDAD</th>
                                        <th>OBSERVACION</th>
                                        @for($i = 1; $i <= 31; $i++)
                                            <th>{{ $i }}</th>
                                            @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $now = Carbon\Carbon::now();
                                        $hoy = date('Y-m-d');
                                        $unaSemana = date('Y-m-d', strtotime('+7 days'));
                                    ?>
                                    @foreach($stock[1] as $cont=>$val)
                                    <?php
                                    $style = '';
                                    if ($val->fecha_caducidad < $hoy) {
                                        $style = 'style="background-color:#f8d7da;"';
                                    } elseif ($val->fecha_caducidad <= $unaSemana) {
                                        $style = 'style="background-color:#fff3cd;"';
                                    }
                                    ?>
                                    <tr {!! $style !!}>
                                        <td>
                                            {{$cont+1}}
                                        </td>
                                        <td>
                                            {{$val->producto}}
                                        </td>
                                        <td>
                                            <select name="presentacion[]" class="form-control select2" readonly>
                                                <option value="">--Seleccione--</option>';
                                                @foreach ($presentaciones as $value)

                                                <?php

                                                $selected = ($value["id"] === $val->idPresentacion) ? 'selected' : ''
                                                ?>
                                                <option {{$selected}} value='{{$value["id"]}}'>{{$value["unidad"]}}</option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td>
                                            {{$val->stock_disponible}}
                                        </td>
                                        <td>
                                            {{$val->fecha_caducidad}}
                                        </td>
                                        <td>
                                            {{$val->observacion}}
                                        </td>

                                        @for ($i = 1; $i <= $now->daysInMonth; $i++)
                                            @php
                                            $detalleDia = $insumos->first(function ($item) use ($now, $i,$opc,$val) {
                                            return \Carbon\Carbon::parse($item->created_at)->day == 
                                            $i && $item->cabecera->tipo == 2 && $val->producto_id == $item->producto_lote->producto_id;
                                            });
                                            @endphp

                                            <td class="text-center">
                                                <input
                                                    disabled
                                                    type="number"
                                                    value="{{ $detalleDia ? $detalleDia->stock : '' }}"
                                                    class="form-control text-center p-0"
                                                    style="width:80px; height:30px;">
                                            </td>
                                            @endfor

                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <td colspan="6">
                                        <span style="font-weight:bold;font-size:medium">Firma Responsable</span>
                                    </td>
                                    @for ($i = 1; $i <= $now->daysInMonth; $i++)
                                        @php

                                        $detalleDia = $insumos->first(function ($item) use ($now, $i,$opc) {
                                        return \Carbon\Carbon::parse($item->cabecera->created_at)->day == $i && 
                                        $item->cabecera->tipo == 2; 
                                        });
                                        @endphp

                                        <td class="text-center">

                                            @if($detalleDia && $detalleDia->cabecera && $detalleDia->cabecera->firma)
                                            <img src="{{ 'https://ieced.siaam.ec/sis_medico/storage/app/avatars/' . $detalleDia->cabecera->firma->nombre }}"
                                                alt="firma"
                                                style="max-width:90px; max-height:60px;">
                                            @endif
                                        </td>
                                        @endfor
                                </tfoot>
                            </table>
                        </div>
                        <div class="tab-pane" id="nav-medicamentos" role="tabpanel">
                            <table class="table-coche-paro">
                                <thead>
                                    <tr>
                                        <th colspan="37" class="header-title">
                                            CONTROL DE MEDICAMENTOS DE COCHE DE PARO
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="5" style="text-align: left;">LCDO. {{$responsable["nombre1"]}} {{$responsable["nombre2"]}} {{$responsable["apellido1"]}} {{$responsable["apellido2"]}}<br>SERVICIO:
                                            {{ isset($servicio) && $servicio == 1 ? 'GASTROENTEROLOGIA' : '' }}
                                        </th>
                                        <th colspan="32" style="text-align: center;" class="month-header">{{$mesNombre}} {{$anio}}</th>
                                    </tr>
                                    <tr>
                                        <th>ITEM</th>
                                        <th>INSUMO</th>
                                        <th>PRESENTACIÓN</th>
                                        <th>CONCETRACIÓN</th>
                                        <th>CANTIDAD</th>
                                        <th>FECHA DE CADUCIDAD</th>
                                        <th>OBSERVACION</th>
                                        @for($i = 1; $i <= 31; $i++)
                                            <th>{{ $i }}</th>
                                            @endfor
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $now = Carbon\Carbon::now(); ?>
                                    @foreach($stock[0] as $cont=>$val)
                                    <?php
                                    $style = '';
                                    if ($val->fecha_caducidad < $hoy) {
                                        $style = 'style="background-color:#f8d7da;"';
                                    } elseif ($val->fecha_caducidad <= $unaSemana) {
                                        $style = 'style="background-color:#fff3cd;"';
                                    }
                                    ?>
                                    <tr {!! $style !!}>
                                        <td>
                                            {{$cont+1}}
                                        </td>
                                        <td>
                                            {{$val->producto}}
                                        </td>
                                        <td>
                                            <select name="presentacion[]" class="form-control select2" readonly>
                                                <option value="">--Seleccione--</option>';
                                                @foreach ($presentaciones as $value)

                                                <?php

                                                $selected = ($value["id"] === $val->idPresentacion) ? 'selected' : ''
                                                ?>
                                                <option {{$selected}} value='{{$value["id"]}}'>{{$value["unidad"]}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <select name="concentracion[]" class="form-control select2">
                                                <option value="">--Seleccione--</option>';

                                                @foreach ($concentracion as $value)
                                                <?php
                                                $selected = ($value["id"] == $val->idConcetracion) ? 'selected' : '';
                                                ?> <option {{$selected}} value='{{$value["id"]}}'>{{$value->nombre}}</option>

                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            {{$val->stock_disponible}}
                                        </td>
                                        <td>
                                            {{$val->fecha_caducidad}}
                                        </td>
                                        <td>
                                            {{$val->observacion}}
                                        </td>

                                        @for ($i = 1; $i <= $now->daysInMonth; $i++)
                                            @php
                                            $detalleDia = $insumos->first(function ($item) use ($now, $i,$opc,$val) {
                                            return \Carbon\Carbon::parse($item->created_at)->day == $i && 
                                            $item->cabecera->tipo == 1 && $val->producto_id == $item->producto_lote->producto_id;
                                            });
                                            @endphp

                                            <td class="text-center">
                                                <input
                                                    disabled
                                                    type="number"
                                                    value="{{ $detalleDia ? $detalleDia->stock : '' }}"
                                                    class="form-control text-center p-0"
                                                    style="width:80px; height:30px;">
                                            </td>
                                            @endfor

                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <td colspan="7">
                                        <span style="font-weight:bold;font-size:medium">Firma Responsable</span>
                                    </td>
                                    @for ($i = 1; $i <= $now->daysInMonth; $i++)
                                        @php


                                        $detalleDia = $insumos->first(function ($item) use ($now, $i,$opc) {
                                        return \Carbon\Carbon::parse($item->cabecera->created_at)->day == $i && $item->cabecera->tipo == 1;
                                        });
                                        @endphp

                                        <td class="text-center">

                                            @if($detalleDia && $detalleDia->cabecera && $detalleDia->cabecera->firma)
                                            <img src="{{ 'https://ieced.siaam.ec/sis_medico/storage/app/avatars/' . $detalleDia->cabecera->firma->nombre }}"
                                                alt="firma"
                                                style="max-width:90px; max-height:60px;">
                                            @endif
                                        </td>
                                        @endfor
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
@endsection