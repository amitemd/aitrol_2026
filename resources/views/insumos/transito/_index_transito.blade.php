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
                        <div class="col-sm-6">
                            <div >
                                <button style="margin: 15px 0px 15px 0px" class="btn btn-success">Buscar</button>
                            </div>
                        </div>
                    
                        <div class="col-sm-6" style="text-align: right">
                            <a class="btn btn-danger" href="{{ route('transito.showSource') }}" > <i class="fa fa-shopping-cart"></i> </a>
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
                                        <tr>
                                            {{-- <td>{{$x->id}}</td> --}}
                                            <td>{{$x->numero_documento}}</td>
                                            <td>{{date('d/m/Y H:i', strtotime($x->created_at))}}</td>
                                            <td> @if(isset($x->bodega_origen)) {{$x->bodega_origen->nombre}} @endif</td>
                                            <td> @if(isset($x->bodega_destino)) {{$x->bodega_destino->nombre}} @endif</td> 
                                            <td>{{$x->observacion}}</td> 
                                            <td>{{$x->usuariocrea->nombre1}} {{$x->usuariocrea->apellido1}}</td> 
                                            <td> {{-- a class="btn btn-warning btn-xs" href="{{route('transito.editnew',['id'=>$x->id])}}" type="button" title="Editar">  <i class="fa fa-edit"></i> </a> &nbsp;--}}
                                            {{-- <a class="btn btn-danger btn-xs" href="{{route('transito.eliminar',['id'=>$x->id])}}" type="button" title="Eliminar">  <i class="fa fa-trash"></i> </a> </td> --}}
                                            <a class="btn btn-danger btn-xs" onclick="eliminar({{ $x->id }})" type="button" title="Eliminar">  <i class="fa fa-trash"></i> </a> </td>
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
    function format ( d ) {
    var cabecera=`<table class='table table-hover table-bordered'> 
                        <thead> 
                            <tr> 
                                <th>#</th> 
                                <th>FECHA</th> 
                                <th>FECHA VENCE</th> 
                                <th>OBSERVACION</th> 
                                <th>SALDO</th> 
                        </thead>
                        <tbody>`;
    var body="";
    for(i=0; i<d.details.length; i++){
        body+=` <tr>
                    <td>${d.details[i].id}</td> 
                    <td>${d.details[i].fecha}</td>
                    <td>${d.details[i].fecha_termino}</td>
                    <td>${d.details[i].observacion}</td>
                    <td>${d.details[i].valor_contable}</td>
                </tr>`;

    }
    var final="</tbody> </table>";
    var f= cabecera+body+final;
    return f;
    
}
$(document).ready(function() {
    var table = $('#example').DataTable( {
        "ajax":{
              "url": '{{route("compras.cartera_pagar")}}?fecha_desde='+$('#fecha_desde').val(),
              "type": 'POST',
              "headers":{
                'X-CSRF-TOKEN': $('input[name=_token]').val()
              },
              "data": function ( d ) {
                  d.fecha_desde = $('#fecha_desde').val();
                  d.fecha_hasta = $('#fecha_hasta').val();
              }
        },
        dom: 'lBrtip',
      paging: false,
      buttons: [{
          extend: 'copyHtml5',
          footer: true
        },
        {
          extend: 'excelHtml5',
          footer: true,
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
          customize: function(doc) {
            doc.styles.title = {
              color: 'black',
              fontSize: '17',
              alignment: 'center'
            }
          }
        }
      ],
        "columns": [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            { "data": "id", },
            { "data": "proveedor" },
            { "data": "vencido",render: $.fn.dataTable.render.number(',', '.', 2, ''), "className": "table-cell-edit" },
            { "data": "periodo",render: $.fn.dataTable.render.number(',', '.', 2, ''), },
            { "data": "porvencer",render: $.fn.dataTable.render.number(',', '.', 2, ''), }
        ]
    } );
    $('#example tbody')
        .on( 'mouseenter', 'td', function () {
            var colIdx = table.cell(this).index().column;
 
            $( table.cells().nodes() ).removeClass( 'highlight' );
            $( table.column( colIdx ).nodes() ).addClass( 'highlight' );
    } );
    // Add event listener for opening and closing details
    $('#example tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( format(row.data()) ).show();
            tr.addClass('shown');
        }
    } );
} );

</script>
@endsection