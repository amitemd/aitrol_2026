@extends('insumos.plantilla_control.base')
@section('action-content')
    <style type="text/css">
        .ui-autocomplete {
            overflow-x: hidden;
            max-height: 300px;
            width: 25%;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000px;
            font-size: 11px;
            float: left;
            display: none;
            min-width: 160px;
            _width: 140px;
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
        .columnas td {
            margin: 10px;
        }

    .contenido{
        font-family: 'Roboto', sans-serif;
    }
    th , tfoot>td{
        font-weight: bold;
    }

    @import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

    </style>
    
        
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <section class="content contenido">
        <div class="box">
            <div class="box-header">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="box-title contenido" style="margin-top: 7px">Información de Plantillas</h3>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button onclick="regresar();" class="btn btn-danger btn-sm">
                            Regresar
                        </button>
                    </div>
                    <hr>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="content contenido">
                <div class="row">
                    <input type="hidden" name="id_plantilla" class="form-control" value="{{ $id }}">

                    <div class="form-group col-md-3">
                        <label>Procedimientos</label>
                        <select disabled class="form-control" name="procedimientos[]" id="procedimientos"
                            multiple="multiple">
                            @foreach ($procedimiento as $value)

                                <option selected value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label>Código</label><br>
                        <input type="text" name="codigo" placeholder="Código" readonly class="form-control"
                            value="{{ $plantilla->codigo }}">
                    </div>
                    <div class="col-md-4">
                        <label>Nombre</label><br>
                        <input type="text" name="nombre" placeholder="Nombre" readonly class="form-control"
                            value="{{ $plantilla->nombre }}">
                    </div>
                    <div class="col-md-2">
                        <label>Estado</label><br>
                        <input type="text" name="estado" placeholder="estado" readonly class="form-control"
                            value="@if ($plantilla->estado == 1) Activo @else Inactivo @endif">
                        <!--select name="estado" class="form-control">
                      <option @if ($plantilla->estado == 1) selected @endif value="1">Activo</option>
                      <option @if ($plantilla->estado == 0) selected @endif value="0">Inactivo</option>
                    </select-->
                    </div>
                    <br>
                    <div class="col-md-12"><br>
                        <h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i> Items de Plantilla</h4><br>
                        <br />

                        <input name='contador_items' id='contador_items' type='hidden' value="0">
                        <div class="col-md-12 table-responsive">
                            <table id="items" class="table  table-bordered table-hover dataTable" role="grid"
                                aria-describedby="example2_info" style="width: 100%;">
                                <thead class="thead-dark">
                                    <tr class='well-darks text-center'>
                                        <th width="10%" tabindex="0" class="text-center">Fecha</th>
                                        <th width="40%" tabindex="0">Items</th>
                                        <th width="20%" tabindex="0">Tipo Plantilla</th>
                                        <th width="8%" tabindex="0" class="text-center">Cantidad </th>
                                        <th width="12%" tabindex="0">Valor Unitario </th>
                                        <th width="10%" tabindex="0" class="text-center">Total </th>

                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    @php
                                     $suma=0;

                                    @endphp
                                    @foreach ($plantillas_items as $value)
                                        @php
                                           $fecha = date('d/m/Y', strtotime($value->fecha));
                                            $suma += $value->total;
                                          //  $sum = number_format($sum,2);*/
                                            @endphp
                                        <tr class="fila-fija columnas">
                                            <td>
                                                  
                                                <label for="fechaItems"> {{ $fecha }}</label>
                                            </td>
                                            <td>
                                                <label for="nombres-items" > {{ $value->nom_prod }}</label>
                                            </td>
                                            <td>
                                                @foreach($tipo_plantilla as $tipo)
                                                    @if($tipo->id == $value->tipo_plantilla)
                                                        <label for="valorUnitario" style="font-weight:normal;float: left;"> {{$tipo->nombre}} </label>           
                                                    @endif 
                                                @endforeach 
                                            </td>
                                            <td class="text-center">
                                             <label for="cantidad" style="font-weight:normal;float: center;"> {{ $value->cantidad }}</label> 
                                            </td>
                                            <td class="text-right">
                                              <label for="valorUnitario" style="font-weight:normal;float: right;">{{ $value->valor_uni= number_format($value->valor_uni,2)}}</label> 
                                            </td>
                                          
                                            <td >
                                             <label for="Total" style="font-weight:normal;float: right;"> {{ $value->toral=number_format($value->total,2) }}</label>  
                                            </td>

                                        </tr>
                                    @endforeach
                                    <tr class="fila_fija columnas">
                                        <td colspan="5">
                                            <label for="sumaTotal" style="float: right; margin-right:3px;"
                                                class="text-center">Total: </label>
                                        </td>

                                        <td>
                                            <label for="sumaTotal" style="float: right; margin-right: 8%;"> {{ $suma = number_format($suma,2) }}</label>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <br>

                    <div class="col-md-6"><br></div>
                    <div id="proced_list"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
    </div>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset('/js/jquery-ui.js') }}"></script>

    <script>
        $('.agregar_items').on('click', function() {
            agregar_items();
        });

        function agregar_items() {
            var id = document.getElementById('contador_items').value;
            var tr = '<tr>' +
                '<td><input type="hidden" name="id_item[]" class="id_item"/><input required name="item_id[]" id="item_id" onchange="agregar_item(this)" class=" buscador_items form-control"  style="height:25px;width:90%;" name="producto[]"/></td>' +
                '<td><input type="number" name="item_cant[]" id="item_cant" class="form-control" style="height:25px;width:75%;" value="1"><input type="number" name="orden[]" id="orden" class="form-control" style="height:25px;width:75%;"></td>' +
                '<td class="remover" ><button  type="button"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
            '</tr>' +
            $('#items').append(tr);
            var variable = 1;
            var sum = parseInt(id) + parseInt(variable);
            document.getElementById("contador_items").value = parseInt(sum);


            $(".buscador_items").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        url: "{{ route('planilla.find_item') }}",
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                minLength: 2,
            });

        };
        $(document).on("click", ".remover", function() {
            var parent = $(this).parents().get(0);
            $(parent).remove();
        });

        function agregar_item(e) {

            $.ajax({
                type: 'post',
                url: "{{ route('planilla.find_id_item') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'nombre': e.value
                },
                success: function(data) {
                    if (data[0] != "") {
                        $(e).parent().parent().find('.id_item').val(data[0].id);
                    }
                    if (data.value != 'no resultados') {
                        console.log(data);
                    } else {}
                },
                error: function(data) {
                    console.log(data);
                }
            });
        }

        function regresar() {


            window.history.back();
        }

        $(function() {
            $(document).ready(function() {
                $('#id_seguro').select2();
                $('#procedimientos').select2();
            });
        });
    </script>
@endsection
