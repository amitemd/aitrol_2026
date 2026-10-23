@extends('contable.ultimo_precio.base')
@section('action-content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">

<style>
    .lds-ring {
        display: inline-block;
        position: relative;
        width: 100px;
        height: 100px;
    }

    .lds-ring div {
        box-sizing: border-box;
        display: block;
        position: absolute;
        width: 100px;
        height: 100px;
        margin: 8px;
        border: 15px solid #3c8dbc;
        border-radius: 50%;
        animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        border-color: #3c8dbc transparent transparent transparent;
    }

    .lds-ring div:nth-child(1) {
        animation-delay: -0.45s;
    }

    .lds-ring div:nth-child(2) {
        animation-delay: -0.3s;
    }

    .lds-ring div:nth-child(3) {
        animation-delay: -0.15s;
    }

    @keyframes lds-ring {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    table {
        font-family: arial, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    td,
    th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        background-color: #dddddd;
    }

    .btn-buscar {
        width: 50%;
        border-radius: 20px;
        padding: 5px 30px;
        border: 1px solid #068DA9;
        transition: .3s ease-in-out all;
        background-color: #068DA9;
        color: white;
    }

    .btn-excel {
        width: 50%;
        border-radius: 20px;
        padding: 5px 30px;
        border: 1px solid #1F8A70;
        transition: .3s ease-in-out all;
        background-color: #1F8A70;
        color: white;
    }



    .select2-selection {
        overflow: hidden;
    }

    .select2-selection__rendered {
        white-space: normal;
        word-break: break-all;
    }
</style>


<div style="background: white; padding: 20px;">

    <div class="row">
        <div class="col-md-6">
            <label for="producto">Producto</label>
            <select name="producto" id="producto" class="form-control select2" multiple="multiple" data-width="100%">
                <option value=""> Seleccione</option>
                @foreach($producto as $item)
                <option value="{{$item->id}}">{{$item->codigo}} | {{$item->nombre}}</option>
                @endforeach

                </select>
            </select>
        </div>


        <div class="col-md-6">
            <label for="clientes">Clientes</label>
            <select name="clientes" id="cliente" class="form-control select2" multiple="multiple">
                <option value=""> Seleccione</option>
                @for($i=0;$i<count($clientes);$i++) <option value="{{$clientes[$i]->identificacion}}">{{$clientes[$i]->nombre}}</option>
                    @endfor

                </select>
            </select>
        </div>

        <div class="col-xl-10">
            <form action="{{route('insumos.precioProducto.buscador')}}">
                <div class="form-row">

                </div>
                <div class="rol-auto">
                    <div class="col-md-3" style="padding: 20px;">
                        <button type="button" onclick="buscador()" class="btn-buscar"> <i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
                    </div>
                </div>
            </form>

        </div>


        <div class="col-md-2">
            @php
            $dia = "01";
            $date = date("Y-m");
            $fecha = $date."-". $dia;
            //dd($fecha);
            @endphp
            <label for="producto">Fecha Desde</label>
            <input type="date" value="{{$fecha}}" name="fecha_desde" id="fecha_desde" class="form-control">
        </div>

        <div class="col-md-2">
            <label for="producto">Fecha Hasta</label>
            <input type="date" value="{{date('Y-m-d')}}" name="fecha_desde" id="fecha_desde" class="form-control">
        </div>

        

        
    </div>

    <div style="margin-top:20px; overflow-x: scroll;max-width: 100%; max-height: 75vh;" id="table-data">

    </div>
</div>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script>
    // window.onload = function() {
    //     buscador();
    // }
    const buscador = () => {

        let total = $("#cliente").val();

      
        if (total != "") {

            $.ajax({
                url: `{{route('insumos.precioProducto.buscador')}}`,
                type: 'GET',
                data: {
                    fecha_desde: $('#fecha_desde').val(),
                    fecha_hasta: $('#fecha_hasta').val(),
                    producto: $('#producto').val(),
                    cliente: $('#cliente').val()
                },
                beforeSend: function() {
                    $('#table-data').empty().append(`<div style="padding:97px; text-align:center;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div> </div>`)
                },
                success: function(data) {
                    console.log(data);
                    $('#table-data').empty().append(`
                <table class="table">
                    <thead>
                        <tr style="white-space: nowrap;font-weight: bold;">
                            ${data.thead}
                        </tr>
                    </thead>

                    <tbody>
                        ${data.tbody}
                    </tbody>
                </table>`)

                setTimeout(() => {
                    actualiza()
                }, 500);

            },
            error: function(data) {

                }
            })

        }else{
            alert("Ingrese un cliente a buscar");
        }
    }
    // window.onload = function() {



    $('.select2_productos').select2({
        placeholder: "{{trans('contableM.selecproducto')}}...",
        allowClear: true,
        minimumInputLength: 3,
        cache: true,
        ajax: {
            url: '{{route("importaciones.productos")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                return {
                    results: data
                };
            }
        }
    });
    // }
</script>
<script>
	$('.select2').select2({
        	tags: false
    	});
       function actualiza(){
        $('#tbl_detalles').DataTable({
            dom: 'Bfrtip',
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            'responsive': false,
            'info': false,
            'autoWidth': false,
            'paging': false,
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
            title:'REPORTE DE PRECIO'
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
            title: 'REPORTE DE PRECIO',
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
</script>

@endsection