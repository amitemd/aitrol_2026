@extends('contable.importaciones.base')
@section('action-content')
<?php
$rolUsuario = Auth::user()->id_tipo_usuario;
?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/dataTables.bootstrap4.min.css" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.bootstrap4.min.css" />
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="box">
                <div class="box-header " style="border-top: 3px solid #3c8dbc;">
                    <div class="col-md-12" style="padding-left: 0px;">
                        <h3 class="box-title">
                            <b style="color:#3c8dbc">Stock Minimo </b>
                        </h3>
                    </div>
                </div>
                <div class="box-body">
                    <!-- {{ rand(1, 4)}} -->
                    @if(in_array($rolUsuario, array(1, 7)) == true)
                    <div class="form-group col-md-12">
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                            <div class="row">


                                <div class="form-group col-md-12">
                                    @if($stock != '[]' )
                                    <div class="table-responsive col-md-12">
                                        <h2>Insumos con Stock Minimo</h2>
                                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                            <thead>
                                                <tr>
                                                    <th>Codigo</th>
                                                    <th>Nombre</th>
                                                    <th>Descripción</th>
                                                    <th>Stock Minimo</th>
                                                    <th>Cantidad Total</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                {{-- @foreach ($stock as $value)
                                            <tr role="row" class="odd">
                                                <td class="sorting_1">{{$value->codigo}}</td>
                                                <td class="sorting_1">{{$value->nombre}}</td>
                                                <td class="sorting_1">{{$value->descripcion}}</td>
                                                <td class="sorting_1">{{$value->minimo}}</td>
                                                <td class="sorting_1" style="background-color: yellow">{{$value->cantidad}}</td>

                                                </tr>
                                                @endforeach --}}


                                                @foreach ($inventario as $value)
                                                <tr role="row" class="odd">
                                                    <td class="sorting_1">{{$value->codigo}}</td>
                                                    <td class="sorting_1">{{$value->nombre}}</td>
                                                    <td class="sorting_1">{{$value->descripcion}}</td>
                                                    <td class="sorting_1">{{$value->existencia_min}}</td>
                                                    <td class="sorting_1" style="background-color: yellow">{{$value->existencia}}</td>

                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.25/js/dataTables.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.bootstrap4.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {

        $('#example2').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': true,
            'info': false,
            'autoWidth': false,
            "scrollY": 300,
            "scrollX": true,
            'scrollCollapse': true,
            "order": [
                [4, "asc"]
            ],
            responsive: 'true',
            dom: 'Bfrtilp',
            buttons: [{
              extend: 'excelHtml5',
              text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i>',
              titleAttr: 'Exportar a Excel',
              className: 'btn btn-success'
            },
            {
              extend: 'pdfHtml5',
              text: '<i class="fa fa-file-pdf-o" aria-hidden="true"></i>',
              titleAttr: 'Exportar a PDF',
              className: 'btn btn-danger'
            },
            {
              extend: 'print',
              text: '<i class="fa fa-print"></i> ',
              titleAttr: 'Imprimir',
              className: 'btn btn-info'
            },
          ]
        });

        tinymce.init({
            selector: '#hc'
        });


    });
</script>    
</section>


@endsection

