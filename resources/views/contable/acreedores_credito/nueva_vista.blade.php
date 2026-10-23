@extends('contable.acreedores_credito.basevista')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
            <li class="breadcrumb-item"><a href="#">Credito Acreedores</a></li>
        </ol>
    </nav>
    <div class="box" style=" background-color: white;">
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">BUSCADOR</label>
            </div>
        </div>
        <div class="box-body dobra">
            <form method="POST" id="formulario" action="{{route('acreedores.informenc')}}">
                {{ csrf_field() }}
                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="texto col-md-3 control-label">Fecha desde:</label>
                    <div class="col-md-9">
                        <input type="text" id="fecha_desde" name="desde" value="{{$fecha_desde}}" class="form-control">
                    </div>
                </div>

                <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha hasta:</label>
                    <div class="col-md-9">
                        <input type="text" id="fecha_hasta" name="hasta" value="{{$fecha_hasta}}" class="form-control">
                    </div>
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="nombre_proveedor">Proveedor:</label>
                </div>
                <div class="form-group col-md-4 col-xs-4 container-4" style="margin-left:4% !important;">
                    <select class="form-control select2" name="id_proveedor" id="id_proveedor">
                        <option value="">Seleccione...</option>
                        @foreach($proveedores as $value)
                        <option @if($value->id==$id_proveedor) selected="selected" @endif value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
                    <button type="button" onclick="buscar()" class="btn btn-primary buscar" id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                    </button>
                    <button type="button" onclick="buscar(1);" class="btn btn-primary exportar" id="btn_exportar">
                        <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> Exportar
                    </button>
                    <input type="hidden" name="excel" id="excel" value="0">
                </div>
            </form>
            @if(count($credito_acreedores)>0)
            <div class="form-group col-md-12">
                <div class="row">
                    <div class="col-md-1">
                        <dl>
                            <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>

                        </dl>
                    </div>
                    <div class="col-md-3">
                        <dl>
                            <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                            <dd>&nbsp; {{$empresa->id}}</dd>
                        </dl>
                    </div>
                    <div class="col-md-4">
                        <h4 style="text-align: center;">INFORME DE NOTA DE CREDITO ACREEDORES</h4>
                        <h5 style="text-align: center;"> @if(($fecha_desde!=null)) Desde {{date("d-m-Y", strtotime($fecha_desde))}} - Hasta {{date("d-m-Y", strtotime($fecha_hasta))}} @elseif($fecha_hasta!=null) AL - {{date("d-m-Y", strtotime($fecha_hasta))}} @endif</h5>
                    </div>
                    <table id="example2" class="display compact" role="grid" aria-describedby="example2_info" style="font-size: 12px; text-align:center; width:100%;">
                        <thead>
                            <tr>
                                <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Fecha</th>
                                <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Secuencia</th>
                                <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Proveedor</th>
                                <th style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1">Factura</th>
                                <th style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1">Detalle</th>
                                <th style="text-align:left;width:7%;" tabindex="0" aria-controls="example2" colspan="1">Subtotal 0</th>
                                <th style="text-align:left;width:7%;" tabindex="0" aria-controls="example2" colspan="1">Subtotal 12</th>
                                <th style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1">Subtotal</th>
                                <th style="text-align:left;" tabindex="0" aria-controls="example2" colspan="1">Impuesto </th>
                                <th style="text-align:center;" tabindex="0" aria-controls="example2" colspan="1">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $acumulador=0; $total_sub0=0; $total_sub12=0; $total_subtotal=0; $total_impuesto=0;         
                            @endphp
                            @foreach($credito_acreedores as $value)
                            @php
                            $acumulador+=$value->valor_contable;
                            $total_sub0+=$value->subtotal_0;
                            $total_sub12+=$value->subtotal_12;
                            $total_subtotal+=$value->subtotal;
                            $total_impuesto+=$value->impuesto;
                            @endphp
                            <tr>
                                <td>{{$value->fecha}}</td>
                                <td>{{$value->serie}}-{{$value->secuencia}}</td>
                                <td>@if(isset($value->compra)) @if(isset($value->compra->proveedorf)) {{$value->compra->proveedorf->nombrecomercial}} @endif @endif</td>
                                <td>@if(isset($value->compra)){{$value->compra->numero}} @endif</td>
                                <td style="text-align: left;">{{$value->concepto}}</td>
                                <td style="text-align: right;">@if(!is_null($value->subtotal_0)) ${{number_format($value->subtotal_0, 2,'.',',')}} @else 0.00 @endif </td>
                                <td style="text-align: right;">@if(!is_null($value->subtotal_12)) ${{number_format($value->subtotal_12, 2,'.',',')}} @else 0.00 @endif </td>
                                <td style="text-align: right;">@if(!is_null($value->subtotal)) ${{number_format($value->subtotal, 2,'.',',')}} @else 0.00 @endif </td>
                                <td style="text-align: right;">@if(!is_null($value->impuesto)) ${{number_format($value->impuesto, 2,'.',',')}} @else 0.00 @endif </td>
                                <td style="text-align: right;">${{$value->valor_contable}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="font-weight: bold;">
                            <tr>
                                <td colspan="4">&nbsp;</td>
                                <td style="text-align: right;">{{trans('contableM.total')}}</td>
                                <td style="text-align: right;">${{number_format($total_sub0,2)}}</td>
                                <td style="text-align: right;">${{number_format($total_sub12,2)}}</td>
                                <td style="text-align: right;">${{number_format($total_subtotal,2)}}</td>
                                <td style="text-align: right;">${{number_format($total_impuesto,2)}}</td>
                                <td style="text-align: right;">${{number_format($acumulador,2)}}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @endif
        </div>
    </div>

    </div>
</section>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            tags: false
        });
    });

    function buscar(s) {
        if (s == 1) {
            //excel
            $('#excel').val(1);
            $('#formulario').submit();
        } else {
            $('#excel').val(0);
            $('#formulario').submit();

        }
    }
    $(function() {
        $('#fecha_desde').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_desde}}',
        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_hasta}}',

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
    $('#example2').DataTable({
        'paging': false,
         dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
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
          title: 'INFORME NOTA DE CREDITO ACREEDORES'
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
          title: 'INFORME NOTA DE CREDITO ACREEDORES',
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '16',
              alignment: 'center'
            }
          }
        }
      ],
    });
</script>


@endsection