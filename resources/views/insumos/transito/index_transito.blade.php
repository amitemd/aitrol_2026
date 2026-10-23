@extends('insumos.transito.base')
@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">

<style type="text/css">
    .btn-danger{
        background-color: #dd4b39!important;
    }
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

<div class="modal fade bd-example-modal-lg" id="guia_remision" aria-labelledby="myModalLabelGuia" style="width: 90%; margin-left: 5%;">
    <div class="modal-dialog modal-lg" style="width: 100%;">
        <div class="modal-content" id="cargar_div" style="width: 100%;">

        </div>
    </div>
</div>
<div class="modal fade" id="crear_transportista" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
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
                <h3 class="box-title">TRANSITO</h3>
            </div>
            <form action="{{route('transito.index_transito')}}" method="POST">
                {{ csrf_field() }}
            <div>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="form-group col-md-12 col-xs-12">
                            <label class="texto" for="fecha">Fecha Desde: </label>
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
                            <label class="texto" for="fecha">Fecha Hasta: </label>
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
                      <input type="hidden" name="excel" id="excel" value="0">
                        <div class="col-sm-6">
                            <div >
                                <button type="submit" onclick="changeExcel('b')" style="margin: 15px 0px 15px 0px" class="btn btn-success">Buscar</button>
                            </div>
                        </div>
                        <div class="col-sm-2" style="text-align: right">
                            <button type="submit" onclick="changeExcel('e')" class="btn btn-success" href="{{ route('transito.excel_transproducc') }}">Excel</button>
                        </div>
                            <div class="col-sm-2">
                                <a class="btn btn-success" target="_blank" href="{{route('insumos.proformaInsumos.index')}}">
                                    <i class="fa fa-arrow-right" aria-hidden="true"></i>
                                    Ir a Proformas
                                </a>
                            </div>

                            <div class="col-sm-2" style="text-align: right">
                                <a class="btn btn-danger" href="{{ route('transito.showSource') }}"> <i class="fa fa-shopping-cart"></i> </a>
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
                                        <th>Det.</th>
                                        <th>N°</th>
                                        <th>Fecha / Hora</th>
                                        <th>Bodega Origen</th>
                                        <th>Bodega Destino</th>
                                        <th>Observación</th>
                                        <th>Usuario Crea</th>
                                        <th>
                                            Accion
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($traslados as $x)
                                        <tr id="section{{$x->id}}">
                                            {{-- <td>{{$x->id}}</td> --}}
                                            <td style="text-align:center; font-size: 19px;color: cyan;"><button style="background:none; border:none;" id="btn{{$x->id}}" onclick="buscarDetalle({{$x->id}})" ><i id="details{{$x->id}}" class="fa fa-chevron-circle-down" aria-hidden="true"></i></button></td>
                                            <td>{{$x->numero_documento}}</td>
                                            <td>{{date('d/m/Y H:i', strtotime($x->created_at))}}</td>
                                            <td> @if(isset($x->bodega_origen)) {{$x->bodega_origen->nombre}} @endif</td>
                                            <td> @if(isset($x->bodega_destino)) {{$x->bodega_destino->nombre}} @endif</td>
                                            <td>{{$x->observacion}}</td>
                                            <td>@if(isset($x->usuariocrea)){{$x->usuariocrea->nombre1}} {{$x->usuariocrea->apellido1}}@endif</td>
                                        <td id="colAccion"> {{--<a class="btn btn-warning btn-xs" href="{{route('transito.editnew',['id'=>$x->id])}}" type="button" title="Editar"> <i class="fa fa-edit"></i> </a>--}} &nbsp;
                                            {{-- <a class="btn btn-danger btn-xs" href="{{route('transito.eliminar',['id'=>$x->id])}}" type="button" title="Eliminar"> <i class="fa fa-trash"></i> </a>
                                        </td> --}}
                                            <a class="btn btn-danger btn-xs" onclick="eliminar({{ $x->id }})" type="button" title="Eliminar">  <i class="fa fa-trash"></i> </a> 
                                            <a target='_blank' href="{{url('transito/index/pdf')}}/{{$x->id}}" class="btn btn-danger btn-xs" onclick="">PDF</a>
                                            
                                            <a target='_blank' href="{{url('transito/index/pdf')}}/{{$x->id}}?est=1" class="btn btn-warning btn-xs" onclick="">PDF </a>
                                        @if($x->bodega_destino->tipo == 1)
                                        @php
                                            $proforma = Sis_medico\Transito_Proforma::where('estado',1)->where('id_transito',$x->id)->first();
                                        @endphp
                                        @if(isset($proforma->proforma) and $proforma->proforma->estado == 1)
                                        <a class="btn btn-danger btn-xs" href="{{ route('insumos.proformaInsumos.pdf_proforma', ['id'=> $proforma->proforma->id]) }}">Pdf Nota Entrega</a>
                                        @else
                                        <a class="btn btn-success btn-xs" href="{{ route('transito.proformaInsumos.editarTransito', ['id'=>$x->id]) }}">Nota Entrega</a>
                                        @endif
                                        @endif
                                            <button class="btn btn-success btn-xs" onclick="mostrarModalProforma('{{$x->id}}')" >Prof. Excel</button>
                                            @if($x->bodega_origen->tipo==0 && $x->bodega_destino->tipo==1)
                                                @if(!is_null($proforma))
                                                    @php $guia_remision = Sis_medico\Ct_Guia_Remision_Cabecera::where('id_inv_cab_movimiento',$x->id)->where('estado','!=',-2)->first(); @endphp
                                                    @if(!is_null($guia_remision))
                                                        @if($guia_remision->estado == -1)
                                                            <a id="btnEnviar{{$guia_remision->id}}" type="button" style="color: white;" class="btn btn-primary btn-xs text-center" onclick="confirmar('<?= $guia_remision->id ?>')"><i id="changeIcon" class="fa fa-send" aria-hidden="true"></i></a>
                                                        @elseif($guia_remision->estado == 0)
                                                            <a style="color: white;" class="btn btn-primary btn-xs text-center"><i class="fa fa-spinner" aria-hidden="true" title="En proceso"></i></a>
                                                        @elseif($guia_remision->estado == 5)
                                                            <a style="color: white;" href='{{asset("/")}}api_doc_electronico?opcion=generarPdf&clave={{$guia_remision->clave_acceso}}' class="btn-xs btn-primary" type="button"><i class="fa fa-file-pdf-o"></i></a>
                                                        @elseif($guia_remision->estado == 7)
                                                            <a style="color: white;" type="button" class="btn btn-xs btn-danger text-center" onclick="verErrorXml('<?= $guia_remision->id; ?>')"><i class="fa fa-close" aria-hidden="true" title="Error"></i></a>
                                                        @elseif($guia_remision->estado == 9)
                                                            <a style="color: white;" type="button" class="btn btn-xs btn-danger text-center" onclick="verIErrorTributaria('<?= $guia_remision->clave_acceso; ?>')" title="Error"><i class="fa fa-close" aria-hidden="true"></i></a>
                                                        @endif

                                                        @if($proforma->estado != 1)
                                                            <a id="btnAnular{{$guia_remision->id}}" style="color: white;" type="button" class="btn btn-xs btn-primary text-center" href="javascript:anular({{$guia_remision->id}});" title="Anular Guia"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                        @endif
                                                    @else
                                                        <button onclick="precargarGuia('{{$x->id}}');" class="btn btn-primary btn-xs">Guia</button>
                                                    @endif
                                                @endif
                                            @endif
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

function changeExcel(l) {
       if (l === 'e') {
           document.getElementById("excel").value = 1;
       }else{
           document.getElementById("excel").value = 0;
       }

   }

  $('#fecha_desde').datetimepicker({
    format: 'YYYY-MM-DD',
  });

  $('#fecha_hasta').datetimepicker({
    format: 'YYYY-MM-DD',
  });
    function eliminar (id) {
        Swal.fire({
        title: 'Seguro que desea eliminar el registro?',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Si`,
        denyButtonText: `Cancelar`,
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
                    Swal.fire("Error: ","Error al eliminar","error");
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
        return "Va a abandonar esta página. Cualquier cambio no guardado se perderá";
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

    const mostrarModalProforma = id_transito => {
        $.ajax({
            url: `{{url('new_proforma/compras/modal')}}/${id_transito}`,
            type: 'GET',
            data: {},
            success: function(data) {
                $('#modal_proforma').modal();
                $('#cargar_modal').empty().html(data);
            }, error: function(data) {

            }
        })
    }

    function actualiza() {
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
                "emptyTable": "No hay información",
                "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": "Mostrar _MENU_ Entradas",
                "loadingRecords": "Cargando...",
                "processing": "Procesando...",
                "search": "Buscar:",
                "zeroRecords": "Sin resultados encontrados",
                "paginate": {
                    "first": "Primero",
                    "last": "Ultimo",
                    "next": "Siguiente",
                    "previous": "Anterior"
                }
            },
            buttons: [{
            extend: 'copyHtml5',
            footer: true
            },

            {
            extend: 'excelHtml5',
            footer: true,
            title: 'TRANSITO'
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
            title: 'TRANSITO',
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
                title: 'Oops...',
                text: 'Verifique el rango de fechas y vuelva consultar'
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
                  alert('error al cargar');
                }
    });
}

    $('#guia_remision').on('hidden.bs.modal', function(){
        $(this).removeData('bs.modal');
    });

    function precargarGuia(id_inv_cab_mov) {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: `{{ route('transito.modal_guia_remision')}}`,
            data: {
                'id_inv_cab_mov': id_inv_cab_mov,
                'fecha_desde': $('#fecha_desde').val(),
                'fecha_hasta': $('#fecha_hasta').val(),
            },
            success: function(data) {
                if (data.status == "error") {
                    alertas('error', 'Error', data.msj);
                } else {
                    $('#cargar_div').html(data);
                    $('#guia_remision').modal();
                }
            },
            error: function(data) {
                document.getElementById('boton_guardar').disabled = false;
                alertas('error', 'Error', data.msj);
            }
        });
    }
    function alertas(icon, title, msj) {
        if (msj.length == 1) {
            Swal.fire({
                icon: icon,
                title: title,
                html: msj
            })
        } else {
            Swal.fire({
                icon: icon,
                title: title,
                html: msj.join()
            })
        }
    }
    function verErrorXml(num) {
        $.ajax({
            url: "{{ route('sri_electronico/errorGeneral') }}",
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                id: num
            },
            dataType: 'json',
            success: function(json) {
                var texto = '';
                $.each(JSON.parse(json.descripcion_error), function(i, item) {
                    texto += item + '<br/>';
                });
                Swal.fire({
                    icon: 'error',
                    html: texto,
                    title: 'Error al generar el XML',
                    confirmButtonText: 'Ok',
                });
            }
        });
    }
    function verIErrorTributaria(clave) {
        $.ajax({
            url: "{{ route('sri_electronico/errorTributario') }}",
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                clave: clave
            },
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#estadoErrorT').val(json.xml.estado);
                $('#claveAccesoErrorT').val(json.xml.comprobantes.comprobante.claveAcceso);
                $('#mensajeErrorT').val(json.xml.MensajesDb.mensajeDb);
                $('#modalErrorTributaria').modal('show');
            }
        });
    }
    function verInfoTributaria(clave) {
        $.ajax({
            url: "{{ route('sri_electronico/infoTributario') }}",
            type: 'post',
            data: {
                _token: $('input[name=_token]').val(),
                clave: clave
            },
            dataType: 'json',
            success: function(json) {
                console.log(json);
                $('#estadoInfoT').val(json.estado[0]);
                $('#numAutorizacionInfoT').val(json.claveAccesoConsultada[0]);
                var fechaAut = json.fechaAutorizacion[0].split('T');
                var horaAut = fechaAut[1].split('-');
                $('#fechaAutorizacionInfoT').val(fechaAut[0] + ' ' + horaAut[0]);
                $('#numDocInfoT').val(json.numeroDocumento);
                $('#tipoDocInfoT').val('Guia de Remisón');
                $('#modalInfoTributaria').modal('show');
            }
        });
    }
    const enviar = (id) => {
        $.ajax({
            type: 'post',
            url: "{{ route('send_information_guia') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'id': id
            },
            success: function(data) {
                if (!data.error) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Verificación correcta',
                    }).then(() => {
                        $('#btnEnviar' + id).html('<i class="fa fa-fw fa-spinner" title="En proceso"></i>');
                    })
                }
            },
            error: function(data) {}
        })
    }
    const confirmar = (id) => {
        Swal.fire({
            title: 'Esta seguro de queres enviar al sri?',
            showDenyButton: false,
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            denyButtonText: 'Cerrar',
        }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                enviar(id);
            }
        });
    }

    function anular(id) {
        Swal.fire({
            title: `¿{{ trans('contableM.anularGuia') }}?`,
            text: `{{ trans('contableM.norevertiraccion') }}!`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                test(id);
            }
        })
    }
    async function test(id) {
        $.ajax({
            type: 'post',
            url: "{{url('transito/guia_remision/anular')}}",
            datatype: 'json',
            data: {
                '_token': $('input[name=_token]').val(),
                'id': id,
                'fecha_desde': $('#fecha_desde').val(),
                'fecha_hasta': $('#fecha_hasta').val(),
            },
            success: function(data) {
                if(data.status == 'success'){
                    Swal.fire({
                        title: `Success`,
                        text: data.msj,
                        icon: 'success',
                    })
                    setTimeout(function() {
                        location.href = "{{route('transito.index_transito',['fecha_desde'=>$busq['fecha_desde'],'fecha_hasta'=>$busq['fecha_hasta']])}}";
                    }, 1500)
                }
                else{
                    Swal.fire({
                        title: `Error`,
                        text: data.msj,
                        icon: 'error',
                    });
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
</script>
@endsection
