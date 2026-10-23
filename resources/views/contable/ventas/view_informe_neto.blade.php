@extends('contable.balance_comprobacion.base')
@section('action-content')
<?php

use Sis_medico\Ct_Clientes;
use Sis_medico\Ct_Nota_Credito_Clientes;
?>
<link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css')}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<link rel="stylesheet" href="{{ asset('/css/icheck/all.css')}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.contable')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.Contabilidad')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.ventas')}}</a></li>
        </ol>
    </nav>
    <div class="box dobra" style=" background-color: white;">
        <div class="row head-title">
            <div class="col-md-12 header">
                <label class="color_texto" for="title">{{trans('contableM.Buscador')}}</label>
            </div>
        </div>
        <div class="box-body ">
            <form method="POST" id="reporte_master" action="{{url('/')}}/ventas/informeNeto">
                {{ csrf_field() }}
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="texto col-md-3 control-label">{{trans('contableM.FechaDesde')}}: </label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_desde').value = ''; buscar();"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.Fechahasta')}}: </label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off" required>
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.cliente')}}: </label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </div>
                            <select name="id_cliente" id="id_cliente" class="form-control select2_cliente" style="width: 100%;">
                                <option value="">Seleccionar...</option>
                                <?php
                                $nombreCliente = isset($ventas[0]->nombre_cliente) ? $ventas[0]->nombre_cliente : '';
                                if ($nombreCliente == '') {
                                    /*if (Auth::user()->id == '0921605895') {
                                        echo $id_cliente;
                                        $cliente = Ct_Clientes::where('identificacion', $id_cliente)->first();
                                        echo '<pre>';
                                        print_r($cliente);
                                        exit;
                                    }*/
                                }
                                if (isset($id_cliente) && $id_cliente != '') {
                                    echo '<option value="' . $id_cliente . '" selected="selected">' . $nombreCliente . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('contableM.vendedor')}}: </label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-user"></i>
                            </div>
                            <select name="id_vendedor" id="id_vendedor" class="form-control select2_vendedor" style="width: 100%;">
                                <option value="">Seleccionar...</option>
                                @foreach ($user_vendedor as $value)
                                <option value="{{ $value->id }}" {{isset($id_vendedor) && $id_vendedor==$value->id?'selected':''}} data-id="{{ $value->id }}" data-name="{{ $value->nombre1 }} {{ $value->apellido1 }}">{{ $value->nombre1 }} {{ $value->apellido1 }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="texto col-md-3 control-label">Estado: </label>
                    <div class="col-md-9">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-thermometer"></i>
                            </div>
                            <select name="estado" id="estado" class="form-control" style="width: 100%;">
                                <option value="">Todos</option>
                                <option value="1" {{isset($estado) && $estado==1?'selected':''}}>Activo</option>
                                <option value="0" {{isset($estado) && $estado==0?'selected':''}}>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="excel" name="excel"> 
                <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
                    <button type="submit" class="btn btn-primary" id="boton_buscar" onclick="document.getElementById('excel').value = 0">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('contableM.buscar')}}
                    </button>
                    <button type="submit" class="btn btn-warning" id="boton_buscar" onclick="document.getElementById('excel').value = 1">
                        <span class="glyphicon glyphicon-excel" aria-hidden="true"></span> {{trans('contableM.excel')}}
                    </button>
                </div>
                <div class="form-group col-md-6 col-xs-9" style="text-align: right;">
                </div>
            </form>
        </div>
        @if(isset($ventas) && count($ventas)>0)
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                        </div>
                        <div class="box-body">
                            <div class="col-md-1">
                                <dl>
                                    <dd>
                                        @if($empresa->logo!=null)
                                        <img src="{{asset('/logo').'/'.$empresa->logo}}" style="width:100px;height: 40px; margin-left: 11px;">
                                        @endif
                                    </dd>
                                </dl>
                            </div>
                            <div id="imprimir">
                                <div class="col-md-3">
                                    <dl>
                                        <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                                        <dd>&nbsp; {{$empresa->id}}</dd>
                                    </dl>
                                </div>
                                <div class="col-md-4">
                                    <h4 style="text-align: center;">INFORME DE VENTAS NETAS</h4>
                                    <h5 style="text-align: center;"> @if(($fecha_desde!=null)) Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}} @else AL - {{date("d-m-Y", strtotime($fecha_hasta))}} @endif</h5>
                                </div>
                                <div class="col-md-4">
                                </div>
                                <table id="example2" class="display compact" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow-y: scroll;" width="100%">
                                    <thead>
                                        <tr class='well-dark'>
                                            <th width="6%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1">No</th>
                                            <th width="6%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1">Fecha</th>
                                            <th width="7%" style="text-align:center;" tabindex="0" aria-controls="example2" rowspan="1">Comprobante</th>
                                            <th width="6%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Identificación</th>
                                            <th width="8%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Cliente</th>
                                            <th width="9%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Vendedor</th>
                                            <th width="6%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Ventas</th>
                                            <th width="6%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Descuento</th>
                                            <th width="6%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Ventas neta</th>
                                            <th width="6%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">NC</th>
                                            <th width="6%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Ventas netas final </th>
                                            <th width="6%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Estado</th>
                                            <th width="6%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Creado</th>
                                            <th width="6%" style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Anulado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($ventas) && count($ventas) > 0) {
                                            $cont = 0;
                                            $subtotal0_ = 0;
                                            $subtotal12_ = 0;
                                            $descuentos_ = 0;
                                            $subtotales_ = 0;
                                            $notaC0_ = 0;
                                            $notaC12_ = 0;
                                            $totalNC_ = 0;
                                            $ventaNetas_ = 0;
                                            $tabla = 0;

                                            // cantidades suma

                                            $ventasI = 0;
                                            $descuentoI = 0;
                                            $ventasNetasI = 0;
                                            $ncI = 0;
                                            $ventasNFI = 0;
                                            foreach ($ventas as $i => $row) {

                                                $cont++;
                                                $subtotal = ($row->subtotal_0 + $row->subtotal_12) - $row->descuento;
                                                $totalNC = $row->subtotal0 + $row->subtotal12;
                                                $ventaNeta = $subtotal - $totalNC;
                                                if ($row->estado != 0) {
                                                    $subtotal0_ += $row->subtotal_0;
                                                    $subtotal12_ += $row->subtotal_12;
                                                    $descuentos_ += $row->descuento;
                                                    $subtotales_ += ($row->subtotal_0 + $row->subtotal_12) - $row->descuento;
                                                    $notaC0_ += $row->subtotal0;
                                                    $notaC12_ += $row->subtotal12;
                                                    $totalNC_ += $row->subtotal0 + $row->subtotal12;
                                                    $ventaNetas_ += $ventaNeta;
                                                }
                                                $estado = 'Inactiva';
                                                if ($row->estado != 0) {
                                                    $estado = 'Activa';
                                                }
                                                $modifi = '';
                                                if ($row->estado == 0) {
                                                    $modifi = $row->modificado_por;
                                                }

                                                if (isset($ventas[$i + 1])) {
                                                    if ($row->id_cliente  != $ventas[$i + 1]->id_cliente) {
                                                        $tabla = 1;
                                                        $ventasI += (float)$row->subtotal_12 + $row->subtotal_0;
                                                        $descuentoI += (float)$row->descuento;
                                                        $ventasNetasI += $subtotal;
                                                        $ncI += $totalNC;
                                                        $ventasNFI += $ventaNeta;
                                                    } else {
                                                        $tabla = 0;
                                                        $ventasI += (float)$row->subtotal_12 + $row->subtotal_0;
                                                        $descuentoI += (float)$row->descuento;
                                                        $ventasNetasI += $subtotal;
                                                        $ncI += $totalNC;
                                                        $ventasNFI += $ventaNeta;
                                                    }
                                                } else {
                                                    $tabla = 1;
                                                    $ventasI += (float)$row->subtotal_12 + $row->subtotal_0;
                                                    $descuentoI += (float)$row->descuento;
                                                    $ventasNetasI += $subtotal;
                                                    $ncI += $totalNC;
                                                    $ventasNFI += $ventaNeta;
                                                }

                                                echo '<tr>
                                                <td>' . $cont . '</td>
                                                <td>' . $row->fecha . '</td>
                                                <td>' . $row->nro_comprobante . '</td>
                                                <td align="right">' . $row->id_cliente . '</td>
                                                <td>' . $row->nombre_cliente . '</td>
                                                <td>' . $row->vendedor . '</td>
                                                <td style="text-align:right;">$' . number_format($row->subtotal_12 + $row->subtotal_0, 2) . '</td>
                                                <td style="text-align:right;">$' . number_format($row->descuento, 2) . '</td>
                                                <td style="text-align:right;">$' . number_format($subtotal, 2) . '</td>
                                                <td style="text-align:right;">$' . number_format($totalNC, 2) . '</td>
                                                <td style="text-align:right;">$' . number_format($ventaNeta, 2) . '</td>
                                                <td>' . $estado . '</td>
                                                <td>' . $row->creado_por . '</td>
                                                <td>' . $modifi . '</td>
                                            </tr>' . ($tabla == 1 ? '<tr style="background-color: #E7F9FC;">
                                                    <td>TOTAL</td> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td></td> 
                                                    <td style="text-align:right;">$' . number_format($ventasI,2) . '</td>
                                                    <td style="text-align:right;">$' . number_format($descuentoI,2) . '</td>
                                                    <td style="text-align:right;">$' . number_format($ventasNetasI,2) . '</td>
                                                    <td style="text-align:right;">$' . number_format($ncI,2) . '</td>
                                                    <td style="text-align:right;">$' . number_format($ventasNFI,2) . '</td>
                                                    <td ></td>
                                                    <td ></td>
                                                    <td ></td>
                                            </tr>' : null);

                                            if (isset($ventas[$i + 1])) {
                                                if ($row->id_cliente  != $ventas[$i + 1]->id_cliente) {
                                                    $ventasI = 0;
                                                    $descuentoI = 0;
                                                    $ventasNetasI = 0;
                                                    $ncI = 0;
                                                    $ventasNFI = 0;
                                                } 
                                            }
                                            }


                                           
                                        }
                                        ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td><label>TOTAL :</label></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align:right;">$<?= number_format($subtotal12_ + $subtotal0_, 2); ?></td>
                                            <td style="text-align:right;">$<?= number_format($descuentos_, 2); ?></td>
                                            <td style="text-align:right;">$<?= number_format($subtotales_, 2); ?></td>
                                            <td style="text-align:right;">$<?= number_format($totalNC_, 2); ?></td>
                                            <td style="text-align:right;">$<?= number_format($ventaNetas_, 2); ?></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
<script src="{{ asset ('/js/bootstrap-datetimepicker.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="{{ asset ('/js/icheck.js') }}"></script>
<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2_vendedor').select2({
            tags: false,
            placeholder: "--Seleccionar--",
        });
        $('#example2').DataTable({
            'paging': false,
            dom: 'lBrtip',
            'lengthChange': false,
            'searching': true,
            "scrollX": true,
            "scrollY": 450,
            'ordering': false,
            responsive: true,
            'info': false,
            'autoWidth': true,
            buttons: [{
                    extend: 'copyHtml5',
                    footer: true
                },
                {
                    extend: 'excelHtml5',
                    footer: true,
                    title: 'INFORME VENTAS NETAS {{$empresa->nombrecomercial}} @if((isset($fecha_desde) && $fecha_desde!=null)) Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{isset($fecha_hasta)? date("d-m-Y", strtotime($fecha_hasta)):""}} @else AL - {{isset($fecha_hasta)?date("d-m-Y", strtotime($fecha_hasta)):""}} @endif'
                },
                {
                    extend: 'csvHtml5',
                    footer: true
                },
                {
                    extend: 'pdfHtml5',
                    orientation: 'landscape',
                    pageSize: 'TABLOID',
                    footer: true,
                    title: 'INFORME VENTAS NETAS {{$empresa->nombrecomercial}} @if(isset($fecha_desde) && ($fecha_desde!=null)) Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{isset($fecha_hasta)?date("d-m-Y", strtotime($fecha_hasta)):""}} @else AL - {{isset($fecha_hasta)?date("d-m-Y", strtotime($fecha_hasta)):""}} @endif',
                    customize: function(doc) {
                        doc.pageMargins = [30, 30, 30, 30];
                        doc.styles.title = {
                            color: 'black',
                            fontSize: '16',
                            alignment: 'center'
                        }
                    }
                }
            ],
        });
        $('.select2_cliente').select2({
            tags: false,
            allowClear: true,
            placeholder: "--Seleccionar--",
            minimumInputLength: 3,
            ajax: {
                url: "ventas?opcion=getCliente",
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(response) {
                    return {
                        results: response.aaData
                    };
                },
                cache: true,
                dropdownCssClass: "bigdrop"
            }
        });
        $('#fecha_desde').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '<?= isset($fecha_desde) ? $fecha_desde : '' ?>',
        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '<?= isset($fecha_hasta) ? $fecha_hasta : '' ?>',

        });
        $("#fecha_desde").on("dp.change", function(e) {
            verifica_fechas();
        });

        $("#fecha_hasta").on("dp.change", function(e) {
            verifica_fechas();
        });
    });

    function verifica_fechas() {
        if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Verifique el rango de fechas y vuelva consultar'
            });
        }
    }
</script>
@endsection