@extends('insumos.transito.base')
@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<style type="text/css">
    .ui-corner-all {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget {
        font-family: Verdana, Arial, sans-serif;
        font-size: 15px;
    }

    .ui-menu {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
    }

    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .ui-menu .ui-menu-item {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .ui-menu .ui-menu-item a {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }

    .ui-menu .ui-menu-item a:hover {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }

    .ui-widget-content a {
        color: #222222;
    }

    .colorB {
        background-color: #1DE9B6;
        border-radius: 10px;
        margin-right: 10px !important;
        width: 20%;
        text-align: center;
        color: white;
    }

    .colorA {
        background-color: #82B1FF;
        border-radius: 10px;
        width: 20%;
        margin-right: 10px !important;
        text-align: center;
        color: white;
    }

    .colorC {
        background-color: #A5D6A7;
        border-radius: 10px;
        text-align: center;
        margin-right: 10px !important;
        color: white;
        width: 20%;
    }

    .colorE {
        background-color: #DD2C00;
        border-radius: 10px;
        text-align: center;
        margin-right: 10px !important;
        color: white;
        width: 20%;
    }

    .colorD {
        background-color: #80CBC4;
        border-radius: 10px;
        text-align: center;
        margin-right: 10px !important;
        color: white;
        width: 20%;
    }

    .colorB1 {
        background-color: #1DE9B6;

        margin-right: 10px !important;
        font-weight: bold;
        text-align: center;
        color: white;
    }

    .colorA1 {
        background-color: #82B1FF;

        font-weight: bold;
        margin-right: 10px !important;
        text-align: center;
        color: white;
    }

    .colorC1 {
        background-color: #A5D6A7;
        font-weight: bold;
        text-align: center;
        margin-right: 10px !important;
        color: white;

    }

    .colorE1 {
        background-color: #DD2C00;
        font-weight: bold;
        text-align: center;
        margin-right: 10px !important;
        color: white;

    }

    .colorD1 {
        background-color: #80CBC4;
        font-weight: bold;
        text-align: center;
        margin-right: 10px !important;
        color: white;

    }
</style>
<!-- Ventana modal editar -->
<div class="modal fade" id="agregarproductos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>
<div class="modal fade" id="detalles" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>

<div class="modal fade" id="seguimiento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>


<div class="modal fade" id="visualizar_estado" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>


<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<!-- Main content -->

<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-sm-12">
                <h3 class="box-title">{{trans('winsumos.ingreso')}} / {{trans('winsumos.egreso')}} </h3>
            </div>
            <form action="{{route('inventario.ingresos.egresos.varios')}}" method="POST">
                {{ csrf_field() }}
            <div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group col-md-12 col-xs-12">
                            <label class="texto" for="fecha">{{trans('winsumos.fecha_desde')}}:</label>
                        </div>
                        <div  class="col-md-12">
                            <div class="form-group col-md-12 col-xs-10 container-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" name="fecha_desde" class="form-control fecha" autocomplete="off" id="fecha_desde" value="
                                    @if(isset($busq))
                                        @if($busq['fecha_desde'] != '')
                                            {{$busq['fecha_desde']}}
                                        @endif
                                    @endif
                                    ">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group col-md-12 col-xs-12">
                            <label class="texto" for="fecha">{{trans('winsumos.fecha_hasta')}}:</label>
                        </div>
                        <div  class="col-md-12">
                            <div class="form-group col-md-12 col-xs-10 container-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" name="fecha_hasta" class="form-control fecha" autocomplete="off" id="fecha_hasta" value="
                                    @if(isset($busq))
                                        @if($busq['fecha_hasta'] != '')
                                            {{$busq['fecha_hasta']}}
                                        @endif
                                    @endif
                                    ">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-sm-6">
                            <div >
                                <button style="margin: 15px 0px 15px 0px" class="btn btn-success">{{trans('winsumos.Buscar')}}</button>
                            </div>
                        </div>
                    
                        <div class="col-sm-6" style="text-align: right">
                            {{-- <button style="margin: 15px 0px 15px 0px" href="{{ route('inventario.ingresos.egresos.crear') }}"  class="btn btn-success">Crear</button> --}}
                            <a class="btn btn-success" href="{{ route('inventario.ingresos.egresos.crear') }}" > <i class="fa fa-shopping-cart"></i> </a>
                        </div>    
                    </div>
               </div>
            </div>
             
                
            </form>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="box">
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row">
                        <div class="table-responsive col-md-12">
                            <table id="tbl_detalles" class="display compact responsive"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                                <thead>
                                    <tr>
                                        {{-- <th>ID</th> --}}
                                        <th>{{trans('winsumos.detalle')}}</th>
                                        <th>{{trans('winsumos.Numero')}}</th>
                                        <th> Tipo </th>
                                        <th>{{trans('winsumos.Fecha')}} / {{trans('winsumos.hora')}}</th>
                                        <th>Asiento</th>
                                        <th>{{trans('winsumos.bodegas')}}</th>
                                        <th>{{trans('winsumos.observacion')}}</th> 
                                        <th>{{trans('winsumos.usuario_crea')}}</th> 
                                        <th>{{trans('winsumos.accion')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($movimientos as $x)
                                        @php 
                                            $usuario_creacion = Sis_medico\User::find($x->id_usuariocrea);
                                            $bodega_crea = Sis_medico\Bodega::find($x->id_bodega_origen);
                                            $tipo = Sis_medico\InvDocumentosBodegas::find($x->id_documento_bodega);
                                        @endphp
                                        <tr id="section{{$x->id}}">
                                            {{-- <td>{{$x->id}}</td> --}}
                                            <td style="text-align:center; font-size: 19px;color: cyan;"><button style="background:none; border:none;" id="btn{{$x->id}}" onclick="buscarDetalle({{$x->id}})" ><i id="details{{$x->id}}" class="fa fa-chevron-circle-down" aria-hidden="true"></i></button></td>
                                            <td>{{$x->numero_documento}}</td>
                                            <td> {{$tipo->documento}} </td>
                                            <td>{{date('d/m/Y H:i', strtotime($x->created_at))}}</td>
                                            <td>
                                                @if(!is_null($x->id_asiento))
                                                <a class="label label-info" style="font-size: 10px;"  data-toggle="modal" data-target="#visualizar_estado"
                                                    href="{{route('compras.modal_estado', ['id'=>$x->id_asiento])}}">{{$x->id_asiento}}</a>
                                                @endif
                                            </td>
                                            <td> @if(!is_null($bodega_crea)) {{$bodega_crea->nombre}} @endif</td>
                                            <td>@if(isset($x->observacion)){{$x->observacion}}@endif</td> 
                                            <td>@if(!is_null($usuario_creacion)){{$usuario_creacion->nombre1}} {{$usuario_creacion->apellido1}}@endif</td> 
                                            <td> <a class="btn btn-warning btn-xs" href="{{route('inventario.ingresos.egresos.editar',['id'=>$x->id])}}" type="button" title="Editar">  <i class="fa fa-edit"></i> </a> &nbsp;
                                            {{-- <a class="btn btn-danger btn-xs" href="{{route('transito.eliminar',['id'=>$x->id])}}" type="button" title="Eliminar">  <i class="fa fa-trash"></i> </a> </td> --}}
                                            {{-- <a class="btn btn-danger btn-xs" href="{{ route('ingreso.eliminar.conglomerada', ['id' => $x->id_pedido]) }}" type="button" title="Eliminar" data-toggle="modal" data-target="#seguimiento" >  <i class="fa fa-trash"></i> </a> --}} 
                                            <a class="btn btn-info btn-xs" href="{{route('InvIngresoEgreso.pdf_ingreso_egreso',['id'=>$x->id])}}" type="button" title="Editar" target="_blank">  <i class="fa fa-download"></i> </a>
                                            </td> 
                                        </tr>
                                       
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</section>
<!-- /.content -->
<!--script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script-->
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script> 
<script type="text/javascript">
    
  $('#fecha_desde').datetimepicker({
    format: 'YYYY-MM-DD',
  });
  
  $('#fecha_hasta').datetimepicker({
    format: 'YYYY-MM-DD',
  });
    function eliminar (id) {
        Swal.fire({
        title: "{{trans('winsumos.seguro_desea_eliminar_registro')}} ?",
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: "{{trans('winsumos.si')}}",
        denyButtonText: "{{trans('winsumos.cancelar')}}",
        }).then((result) => {
        /* Read more about isConfirmed, isDenied below */
        if (result.isConfirmed) {
            $.ajax({
                type: "get",
                url:  "{{route('transito.eliminar')}}",
                data: {
                    'id': id
                },
                success: function(data) {  
                },
                error: function() { 
                    Swal.fire("{{trans('winsumos.error')}}","{{trans('winsumos.error_eliminar')}}","error");
                }
            });
            // location.reload();
            // Swal.fire('Registro eliminado con éxito!', '', 'success')
        } 
        })
    }
    $('#agregarproductos').on('hidden.bs.modal', function() {
        $(this).removeData('  bs.modal');
    });
    $('#detalles').on('hidden.bs.modal', function() {
        $(this).removeData('  bs.modal');
    });
    $(document).ready(function() {
        $('#fecha').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
            //Important! See issue #1075

        });
        $('#vencimiento').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
            //Important! See issue #1075

        });
        //$('#tbl_detalles_wrapper').removeClass('dataTables_wrapper');
        $('.dataTables_wrapper').each(function(){
            $(this).removeClass('dataTables_wrapper');
        })
    });


    function confirmarSalida() {
        return "{{trans('winsumos.abandonar_pagina')}}";
    }

    function beforeVoid() {}

    function valida(e) {
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros
        patron = /[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function eliminardato(valor) {
        var nombre1 = "dato" + valor;
        var nombre2 = 'visibilidad' + valor;
        document.getElementById(nombre1).style.display = 'none';
        document.getElementById(nombre2).value = 0;
    }
    window.onload = ()=>{
        setTimeout(()=>{
            console.log('hola');
            actualiza();
        }, 1500)
    }
    function actualiza(){
        $('#tbl_detalles').DataTable({ 
            dom: 'Bfrtip',
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            'responsive': true,
            'info': false,
            'autoWidth': true,
            'paging': true, 
            language: {
                "decimal": "",
                "emptyTable": "{{trans('winsumos.no_informacion')}}",
                "info": "{{trans('winsumos.mostrando')}} _START_ {{trans('winsumos.a')}} _END_ {{trans('winsumos.de')}} _TOTAL_ {{trans('winsumos.registros')}}",
                "infoEmpty": "{{trans('winsumos.mostrando')}} 0 {{trans('winsumos.a')}} 0 {{trans('winsumos.de')}} 0 {{trans('winsumos.registros')}}",
                "infoFiltered": "({{trans('winsumos.filtrado')}} {{trans('winsumos.de')}} _MAX_ {{trans('winsumos.total')}} {{trans('winsumos.registros')}})",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "{{trans('winsumos.mostrando')}} _MENU_ {{trans('winsumos.registros')}}",
                "loadingRecords": "{{trans('winsumos.procesando')}}...",
                "processing": "{{trans('winsumos.procesando')}}...",
                "search": "{{trans('winsumos.Buscar')}}:",
                "zeroRecords": "{{trans('winsumos.sin_resultados_encontrados')}}",
                "paginate": {
                    "first": "{{trans('winsumos.primero')}}",
                    "last": "{{trans('winsumos.ultimo')}}",
                    "next": "{{trans('winsumos.siguiente')}}",
                    "previous": "{{trans('winsumos.anterior')}}"
                }
            },
            buttons: [{
            extend: 'copyHtml5',
            footer: true
            },
            
            {
            extend: 'excelHtml5',
            footer: true,
            title: "{{trans('winsumos.transito')}}"
            },
            {
            extend: 'csvHtml5',
            footer: true
            },
            {
            extend: 'pdfHtml5',
            orientation: 'landscape',
            pageSize: 'LEGAL',
            footer: true,
            title: "{{trans('winsumos.transito')}}",
            customize: function(doc) {
                doc.styles.title = {
                color: 'black',
                fontSize: '14',
                alignment: 'center'
                }
            }
            }
        ],
        });
    }

    $(function() {
        $('#fecha_desde').datetimepicker({
            // format: 'YYYY/MM/DD',
            format: 'DD/MM/YYYY',
        });
        $('#fecha_hasta').datetimepicker({
            // format: 'YYYY/MM/DD',
            format: 'DD/MM/YYYY',

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
                title: "{{trans('winsumos.error')}}",
                text: "{{trans('winsumos.verifique_rango_fechas')}}"
            });
        }
    }
    
</script>

<script>
    function format (d, id) {
    var cabecera=` 
                    <td colspan="8" id="contenedor_sub${id}">
                    <table style="width: 92%!important; border: 2px solid black;" class='container table table-hover table-bordered sub_tabla${id}'> 
                        <thead> 
                            <tr> 
                                <th>Serie</th> 
                                <th>Nombre </th> 
                                <th>Lote</th> 
                                <th>Cantidad</th> 
                                <th>Cantidad de Uso</th> 
                                <th>Total</th>
                        </thead>
                        <tbody>`;
    var body="";
    for(i=0; i<d.length; i++){
        body+=` <tr>
                    <td>${d[i].serie}</td> 
                    <td>${d[i].nombre}</td>
                    <td>${d[i].lote}</td>
                    <td>${d[i].cantidad}</td>
                    <td>${d[i].cant_uso}</td>
                    <td>${d[i].total}</td>
                </tr>`;

    }
    var final="</tbody> </table></td>";
    var f= cabecera+body+final;
    return f;
    
}

function buscarDetalle(id){
    let ubicacion = document.getElementById('section'+id).closest('tr');

    $.ajax({
                type: "get",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                url: "{{route('transito.detalle_transito')}}",
                data: {
                    'id': id
                },
                success: function(data){
                    //actualiza();
                    console.log(data);
                    var tr = $("section"+id);

                    let sub_tabla = document.querySelector('.sub_tabla'+id);

                    let sub_tabla2 = $('.sub_tabla'+id);
                    
                     if(sub_tabla != null){
                        sub_tabla2.parent()[0].removeChild(sub_tabla);
                        $('#details'+id).removeClass("fa-chevron-circle-up").addClass("fa-chevron-circle-down");
                        let sub_tabla_2 = document.getElementById('contenedor_sub'+id);
                        sub_tabla_2.style.display="none";
                     }else{
                        $(ubicacion).after( format(data,id) )
                        $('#details'+id).removeClass("fa-chevron-circle-down").addClass("fa-chevron-circle-up");
                     }
                    
                    
                    
                },error:  function(){
                  alert("{{trans('winsumos.error')}}");
                }
    });
}




</script>
@endsection