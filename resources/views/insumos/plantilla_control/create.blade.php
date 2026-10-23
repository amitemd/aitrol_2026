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

    .select2-container {
        width: 100% !important;
    }

    .select2-selection {
        height: 39% !important;
    }

    .contenido {
        font-family: 'Roboto', sans-serif;
    }

    th,
    tfoot>td {
        font-weight: bold;
    }
</style>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

<section class="content contenido">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="box-title" style="margin-top: 7px">Crear Plantillas Control</h3>
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
        <div class="content">
            <form id="formulario" method="POST">
                {{ csrf_field() }}
                <div class="row">
                    <div style="margin-top:22px" class="form-group col-md-3">
                        <label>Procedimientos</label>

                        <select class="form-control" name="procedimientos[]" id="procedimientos" multiple="multiple">
                            <!--<option value="">Seleccione ...</option>-->
                            @foreach($procedimiento as $procedimientos2)
                            <option value="{{$procedimientos2->id}}">{{$procedimientos2->nombre}}</option>
                            @endforeach
                        </select>
                    </div>

                    <br>

                    <div class="form-group col-md-3">
                        <label>Código</label><br>
                        <input id="codigo" type="text" name="codigo" placeholder="Código" class="form-control">
                    </div>
                    <div class="form-group col-md-4">
                        <label>Nombre</label><br>
                        <input id="nombre" type="text" name="nombre" placeholder="Nombre" class="form-control">
                    </div>
                    <div class="form-group col-md-2">
                        <label>Estado</label><br>
                        <select name="estado" class="form-control">
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <br>
                    <div class="col-md-12"><br>
                        <h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i> Items de Plantilla</h4><br>
                        <br/>

                        <input name='contador_items' id='contador_items' type='hidden' value="1">
                        <div class="col-md-12 table-responsive">
                            <table id="items" class="table table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                <thead class="thead-dark">
                                    <tr class='well-darks'>
                                        <th width="50%" tabindex="0">Descripción</th>
                                        <th width="20%" tabindex="0">Tipo</th>
                                        <th width="10%" tabindex="0">Cantidad</th>
                                        <th width="10%" tabindex="0">Valor Unitario</th>
                                        <th width="10%" tabindex="0">Total</th>
                                        <th width="10%" tabindex="0">
                                            <button type="button" class="btn btn-success btn-gray agregar_items">
                                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="fila-fija">
                                    </tr>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" style="text-align: right;">Total</td>
                                        <td id="total_item">0.00</td>
                                    </tr>
                                </tfoot>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <br>

                    <div class="col-md-6"><br></div>
                    <div id="proced_list"></div>
                    <div class="col-md-12" style="text-align: center;">
                        <button onclick="validar(event)" class="btn btn-success" id="agregar_btn">
                            Agregar
                        </button>
                    </div>

                </div>
            </form>

        </div>
        <!-- /.box-body -->
    </div>
</section>
<!-- /.content -->
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script>
    $('.agregar_items').on('click', function() {
        agregar_items();
    });

    function validar(e) {
        e.preventDefault();
        let codigo = document.getElementById("codigo").value;
        let nombre = document.getElementById("nombre").value;
        let procedimiento = document.getElementById("procedimientos").value;
        let total = document.querySelectorAll('.total_producto');

        let mensaje = "";

        if (procedimiento == null || procedimiento == "") {
            mensaje += "Seleccione el Procedimiento";
        }
        if (codigo == null || codigo == "") {
            mensaje += "<br>Llene el campo de Código";
        }
        if (nombre == null || nombre == "") {
            mensaje += "<br>Llene el campo de Nombre";
        }

        if (mensaje != "") {
            alertas('error', 'Error', mensaje)
        } else {
            if (total.length == 0) {
                alertas('error', 'Error', 'Debe agregar productos a la Plantilla')
            } else {
                mensaje = "";

                let iditem = document.querySelectorAll(".iditem");
                let tipo_plantilla = document.querySelectorAll(".tipo_plantillas");
                let cantidad = document.querySelectorAll(".cantidad_producto");
                let valor_unitario = document.querySelectorAll(".valor_unitario");


                let mensaje_item ="";
                let mensaje_plantilla ="";
                let mensaje_cantidad="";
                let mensaje_v_unitario = "";

                for(let i = 0; i < total.length; i++) {
                    if(iditem[i].value == "" || iditem[i].value == null){
                        mensaje_item = "Falta agregar productos<br>";
                    }
                    if(tipo_plantilla[i].value == "" || tipo_plantilla[i].value == "0"){
                        mensaje_plantilla = "Seleccione el tipo de plantilla<br>";
                    }
                    if(cantidad[i].value =="" || cantidad[i].value <= 0){
                        mensaje_cantidad = "Cantidades Incorrectas<br>";
                    }
                    if(valor_unitario[i].value == "" || parseFloat(valor_unitario[i].value) <= 0){
                        mensaje_v_unitario = "Valores incorrectos";
                    }
                }

                mensaje = mensaje_item + mensaje_plantilla + mensaje_cantidad + mensaje_v_unitario;
                

               // mensaje = mensaje_producto + mensaje_total + mensaje_v_unitario + mensaje_cantidad + mensaje_plantilla;

                if (mensaje != "") {
                    alertas('error', 'Error', mensaje)
                } else {
                    $.ajax({
                        type: "get",
                        url: "{{route('plantilla.comprobar')}}",
                        data: $('#formulario').serialize(),
                        datatype: "json",
                        success: function(data) {
                            console.log(data);
                            if (data.validar == "si") {
                                alertas('error', 'Error', `${data.mensaje}`)
                            } else {
                                enviar_formulario();
                            }
                        },
                        error: function() {
                            alert('error al cargar');
                        }
                    });

                }
            }
        }
        console.log(mensaje)
    }

    function alertas(icon, title, text) {
        Swal.fire({
            icon: `${icon}`,
            title: `${title}`,
            html: `${text}`,
        })
    }



    function agregar_items() {
        var id = document.getElementById('contador_items').value;
        var tr = `<tr class="columnas">
                        <td>
                            <input type="hidden"  name="producto[]" id="id_item${id}" class="iditem">
                            <input  required  id="item_id"  class="form-control buscador_items${id}" style="height:25px;width:90%;">
                        </td>

                        <td>
                          <select  style="width:80%; height:25px;" class="form-control tipo_plantillas" name="tipo_plantilla[]">
                            <option value="0">Seleccione</option>
                            @foreach ($tipo_plantilla as $tipo)
                            <option value="{{$tipo->id}}"> {{$tipo->nombre}} </option>
                            @endforeach
                          </select>
                        </td>
                        
                        <td>
                            <input onchange="calcular_item(${id})" id="cantidad${id}" value="0" type="number" name="cantidad[]" onKeypress="if (event.keyCode < 45 || event.keyCode > 57 || event.key == '-') event.returnValue = false;"  class="form-control cantidad_producto" style="height:25px;width:75%;">
                        </td>

                        <td>
                            <input onchange="calcular_item(${id})" id="valor_unitario${id}" value="0.00" type="text" name="valor_unitario[]" onKeypress="if (event.keyCode < 45 || event.keyCode > 57 || event.key == '-') event.returnValue = false; "  class="form-control valor_unitario" style="height:25px;width:75%;">
                        </td>
                      
                        <td>
                            <input id="total_producto${id}" value="0.00" readonly type="text" name="total[]" onKeypress="if (event.keyCode < 45 || event.keyCode > 57 || event.key == '-') event.returnValue = false;" class="form-control total_producto" style="height:25px;width:75%;">
                        </td>
                        <td>
                            <button onclick="deleteRow(this)" type="button"  class="btn btn-danger btn-gray" >
                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>`;
        $('#items').append(tr);

        //select();
        var ids = document.getElementById('contador_items').value;

        $(".buscador_items" + id).autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('planilla.buscar_item_producto')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                        console.log(data);
                        console.log("hola" + ids)
                    }
                });
            },

            minLength: 2,
            select: function(data, ui) {
                //alert("asd");
                console.log("select", ui.item.iva);
                //document.getElementById("id_item"+id).value = ui.item.value1;
                $('#id_item' + ids).val(ui.item.value1);

            }
        });

        id++;
        document.getElementById('contador_items').value = id;
    }

    function calcular_item(id) {
        let cantidad = document.getElementById("cantidad" + id);
        let valor_unitario = document.getElementById("valor_unitario" + id);
        let iva = document.getElementById("iva" + id);
        let total_producto = document.getElementById("total_producto" + id);

        let total = 0.00;
        total = parseFloat(cantidad.value * valor_unitario.value);
        total_producto.value = total.toFixed(2);


        calcular_todo();

    }

    function calcular_todo() {

        let total_producto = document.querySelectorAll(".total_producto");

        let valor_total = 0.00;
        for (let i = 0; i < total_producto.length; i++) {
            valor_total = parseFloat(valor_total) + parseFloat(total_producto[i].value);
        }
        console.log(valor_total);
        document.getElementById("total_item").innerHTML = valor_total.toFixed(2);

    }


    function select() {
        $('.select2_desc').select2({
            tags: false
        });
    }

    function deleteRow(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        calcular_todo();
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



    function enviar_formulario() {
        $.ajax({
            type: 'post',
            url: "{{route('plantilla_procedimiento.save')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data) {
                console.log(data);
                if (data.respuesta == "exito") {
                    alertas('success', 'Exito', 'Guardado Correctamente');
                    document.getElementById('agregar_btn').disabled = true;
                    document.getElementById('procedimientos').disabled = true;
                } else {
                    alertas('error', 'Error', 'Ocurrio un problema');
                }
            },
            error: function(data) {
                alertas('error', 'Error', 'Ocurrio un problema');
                console.log(data);
            }
        });

    }

    function agregar_item(e) {

        $.ajax({
            type: 'post',
            url: "{{route('planilla.find_id_item')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'nombre': e.value
            },
            success: function(data) {
                if (data[0] != "") {
                    $(e).parent().parent().find('.iditem').val(data[0].id);
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

    function busca_autocomple(ids) {
        $(".buscador_items" + ids).autocomplete({
            source: function(request, response) {
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('planilla.buscar_item_producto')}}",
                    dataType: "json",
                    data: {
                        term: request.term
                    },
                    success: function(data) {
                        response(data);
                        console.log(data);
                        console.log("hola" + ids)
                    }
                });
            },

            minLength: 2,
            select: function(data, ui) {
                //alert("asd");
                console.log("select", ui.item.iva);
                //document.getElementById("id_item"+id).value = ui.item.value1;
                $('#id_item' + ids).val(ui.item.value1);
                let iva = document.getElementById("iva" + ids);
                iva.setAttribute("data-iva", ui.item.iva)

            }
        });
    }
</script>

@endsection