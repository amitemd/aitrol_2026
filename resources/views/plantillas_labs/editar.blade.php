@extends('plantillas_labs.base')
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
    .contenido{
        font-family: 'Roboto', sans-serif;
    }
    th , tfoot>td{
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
                    <h3 class="box-title" style="margin-top: 7px">Editar Plantillas LABS</h3>
                </div>
                <div class="col-sm-6 text-right">
                    <a href="{{route('plantillacontrollabs.index')}}" class="btn btn-danger btn-sm">
                        {{trans('winsumos.regresar')}}
                    </a>
                </div>
                <hr>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="content">
            <form method="POST" id="formulario" action="{{route('plantilla_procedimiento.update')}}">
                {{ csrf_field() }}
                <div class="row">
                    <input type="hidden" name="id_plantilla" class="form-control" value="{{$id}}">
                    <div class="col-md-3">
                        <label>{{trans('winsumos.codigo')}}</label><br>
                        <input type="text" name="codigo" placeholder="{{trans('winsumos.codigo')}}" id ="codigo"class="form-control" value="{{$plantilla->codigo}}">
                    </div>
                    <div class="col-md-3">
                        <label>{{trans('winsumos.nombre')}}</label><br>
                        <input type="text" name="nombre" placeholder="{{trans('winsumos.nombre')}}" id= "nombre" class="form-control" value="{{$plantilla->nombre}}">
                    </div>
                    <div class="col-md-2">
                        <label>{{trans('winsumos.estado')}}</label><br>
                        <select name="estado" class="form-control">
                            <option @if($plantilla->estado == 1) selected @endif value="1">{{trans('winsumos.activo')}}</option>
                            <option @if($plantilla->estado == 0) selected @endif value="0">{{trans('winsumos.inactivo')}}</option>
                        </select>
                    </div>
                    <br>
                    <div class="col-md-12"><br>
                        <h4 align="center" style="color:#a6004c"><i class="fa fa-list"></i>{{trans('winsumos.items_plantilla')}}</h4><br>
                        <br />

                        
                        <div class="col-md-12 table-responsive">
                            <table id="items" class="table  table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                <thead class="thead-dark">
                                    <tr class='well-darks'>
                                        <th width="40%" tabindex="0">{{trans('winsumos.descripcion')}}</th>
                                        <th width="20%" tabindex="0">{{trans('winsumos.tipo_plantilla')}}</th>
                                        <th width="10%" tabindex="0">{{trans('winsumos.cantidad')}}</th>
                                        <th width="10%" tabindex="0">{{trans('winsumos.precio_unitario')}}</th>
                                        <!--<th width="10%" tabindex="0">IVA</th>-->
                                        <th width="10%" tabindex="0">{{trans('winsumos.total')}}</th>
                                        <th width="10%" tabindex="0">
                                            <button type="button" class="btn btn-success btn-gray agregar_items">
                                                <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $id =1; $selectd = ""; $total=0; //dd($plantillas_items) @endphp
                                    
                                    @foreach($plantillas_items as $value)
                                    <tr class="columnas">
                                        <td>
                                            <input type="hidden"  name="producto[]" id="id_item{{$id}}" class="iditem" value="@if(isset($value->producto)) {{$value->producto->id}} @endif" >
                                            <input  onkeydown="busca_autocomple({{$id}})" required  id="item_id"  class="form-control buscador_items{{$id}}" value="@if(isset($value->producto)) {{$value->producto->nombre}} @endif" style="height:25px;width:90%;">
                                        </td>
                                        <td>
                                            <select style="width:80%; height:25px;" class="form-control form-select  tipo_plantillas"  name="tipo_plantilla[]">
                                                <option value="0" >{{trans('winsumos.seleccione')}}</option>
                                                @foreach ($tipo_plantilla as $tipo)
                                                    <option  @if($value->id_tipo_labs == $tipo->id) selected @endif value="{{$tipo->id}}"> {{$tipo->nombre}} </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        
                                        <td>
                                            <input value="{{$value->cantidad}}" onchange="calcular_item({{$id}})" id="cantidad{{$id}}" value="0" type="number" name="cantidad[]" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="form-control cantidad_producto" style="height:25px;width:75%;">
                                        </td>

                                        <td>
                                            <input onchange="calcular_item({{$id}})" id="valor_unitario{{$id}}" value="{{$value->valor_uni}}" type="text" name="valor_unitario[]" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="form-control valor_unitario" style="height:25px;width:75%;">
                                        </td>
                                      
                                        @php $total += $value->total;  @endphp

                                        <td>
                                            <input id="total_producto{{$id}}" value="{{$value->total}}" readonly type="text" name="total[]" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" class="form-control total_producto" style="height:25px;width:75%;">
                                        </td>
                                        <td>
                                            <button onclick="deleteRow(this); calcular_todo();" type="button"  class="btn btn-danger btn-gray" >
                                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @php $id++; @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" style="text-align: right;">{{trans('winsumos.total')}}</td>
                                        <td id="total_item">{{number_format((float)$total, 2, '.', '')}} </td>
                                    </tr>
                                </tfoot>
                            </table>
                            <input name='contador_items' id='contador_items' type='hidden' value="{{$id}}">
                        </div>

                    </div>
                    <br>

                    <div class="col-md-6"><br></div>
                    <div id="proced_list"></div>
                    <!-- <input type="submit" name="" value="Guardar" class="btn btn-success">-->
                    <div class="col-md-12"  style="text-align: center">
                        <button style="margin-top: 5%;" onclick="validar(event)" class="btn btn-success">
                            {{trans('winsumos.actualizar')}}
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
    function calcular_item(id){
        //console.log("Hola");
        let cantidad = document.getElementById("cantidad"+id);
        let valor_unitario = document.getElementById("valor_unitario"+id);
        let iva = document.getElementById("iva"+id);
        let total_producto = document.getElementById("total_producto"+id);
        let total = 0.00;
        total = parseFloat(cantidad.value * valor_unitario.value);
        total_producto.value = total.toFixed(2); 

        calcular_todo();

    }

    function calcular_todo(){

        let total_producto = document.querySelectorAll(".total_producto");

        let valor_total = 0.00;
        for(let i = 0; i < total_producto.length; i++){
            valor_total = parseFloat(valor_total) + parseFloat(total_producto[i].value);
        }
        console.log(valor_total);
        document.getElementById("total_item").innerHTML = valor_total.toFixed(2);

    }


    function validar(e) {
        e.preventDefault();
        //alert("hola")
        let codigo = document.getElementById("codigo").value;
        let nombre = document.getElementById("nombre").value;
        

        let total = document.querySelectorAll('.total_producto');

        let mensaje = "";

        if (codigo == null || codigo == "") {
            //alertas('error', 'Error', 'Llene el campo de Código')
            mensaje += "<br> {{trans('winsumos.ingrese_codigo')}}";
        }
        if (nombre == null || nombre == "") {
            //alertas('error', 'Error', 'Llene el campo de Nombre')
            mensaje += "<br> {{trans('winsumos.ingrese_nombre')}}";
        }

        if (mensaje != "") {
            alertas('error', "{{trans('winsumos.error')}}", mensaje)
        } else {
            if (total.length == 0) {
                alertas('error', "{{trans('winsumos.error')}}", "{{trans('winsumos.error')}}")
            } else {
                mensaje ="";
                
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
                        mensaje_item = "{{trans('winsumos.ingrese_producto')}} <br>";
                    }
                    if(tipo_plantilla[i].value == "" || tipo_plantilla[i].value == "0"){
                        mensaje_plantilla = "{{trans('winsumos.seleccione_tipo_plantilla')}} <br>";
                    }
                    if(cantidad[i].value =="" || cantidad[i].value <= 0){
                        mensaje_cantidad = "{{trans('winsumos.seleccione_tipo_plantilla')}} <br>";
                    }
                    if(valor_unitario[i].value == "" || parseFloat(valor_unitario[i].value) <= 0){
                        mensaje_v_unitario = "{{trans('winsumos.valores_incorrectos')}}";
                    }
                }

                mensaje = mensaje_item + mensaje_plantilla + mensaje_cantidad + mensaje_v_unitario;
               // mensaje = mensaje_producto + mensaje_total + mensaje_cantidad + mensaje_total_producto;

                if (mensaje != "") {
                    alertas('error', "{{trans('winsumos.error')}}", mensaje)
                } else {
                    //document.getElementById("formulario").submit();
                    enviar_formulario();
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
      
        var id = parseInt(document.getElementById('contador_items').value);
        var tr = `<tr class="columnas">
                        <td>
                            <input type="hidden"  name="producto[]" id="id_item${id}" class="iditem" >
                            <input required  id="item_id"  class="form-control buscador_items${id}" style="height:25px;width:90%;">
                                       
                        </td>

                        <td>
                            <select style="width:80%; height:25px;" class="form-control tipo_plantillas" name="tipo_plantilla[]">
                                <option value="0" >{{trans('winsumos.seleccione')}}</option>
                                @foreach ($tipo_plantilla as $tipo)
                                    <option value="{{$tipo->id}}"> {{$tipo->nombre}} </option>
                                @endforeach
                            </select>
                        </td>
                        
                        <td>
                            <input onchange="calcular_item(${id})"  onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" id="cantidad${id}" value="0" type="number" name="cantidad[]" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="form-control cantidad_producto" style="height:25px;width:75%;">
                        </td>

                        <td>
                            <input onchange="calcular_item(${id})" id="valor_unitario${id}" value="0.00" type="text" name="valor_unitario[]" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="form-control valor_unitario" style="height:25px;width:75%;">
                        </td>

                        <!--<td>
                            <input id="iva${id}" value="0.00" readonly type="text" name="iva[]" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  class="form-control iva" style="height:25px;width:75%;">
                        </td>-->

                        <td>
                            <input id="total_producto${id}" value="0.00" readonly type="text" name="total[]" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" class="form-control total_producto" style="height:25px;width:75%;">
                        </td>
                        <td>
                            <button onclick="deleteRow(this); calcular_todo();" type="button"  class="btn btn-danger btn-gray" >
                                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
                            </button>
                        </td>
                    </tr>`;
        $('#items').append(tr);
        let ids = id;
        $(".buscador_items"+ids).autocomplete({
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
                            console.log("hola"+ids)
                        }
                    });
                },

                minLength: 2,
                select: function(data, ui){
                    //alert("asd");
                    console.log("select",ui.item.iva);
                    //document.getElementById("id_item"+id).value = ui.item.value1;
                    $('#id_item'+ids).val(ui.item.value1);
                    
                }
        });
        id++;
        document.getElementById('contador_items').value= id;
       // select();
    }

    function _agregar_items() {
        var id = document.getElementById('contador_items').value;
        var tr = '<tr class="columnas">' + '<td> <select class="form-control select2_desc producto" style="width:100%"   name="productos_array[]" > <option value="">{{trans("winsumos.seleccione")}}</option> @foreach($producto as $value) <option value="{{$value->id}}">{{$value->nombre}}</option> @endforeach </select></td>' + '<td><input  type="text"  name="cantidad[]" class="form-control total_producto  " style="height:25px;width:75%;"></td>' + '<td class="remover" ><button  type="button"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
        '</tr>' +
        $('#items').append(tr);
        select();
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
                    url: "{{route('planilla.find_item')}}",
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
    function deleteRow(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        //calcular();
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
    function select() {
        $('.select2_desc').select2({
            tags: false
        });
    }


    $('.select2').select2({
        tags: false
    });

    $('.select2_desc').select2({
        tags: false
    });

    function regresar() {
        window.history.back();
    }

    function enviar_formulario(){
        $.ajax({
            type: 'post',
            url: "{{route('plantillacontrollabs.update')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#formulario").serialize(),
            success: function(data) {
                console.log(data);
                if(data.respuesta== "exito"){
                    alertas('success', "{{trans('winsumos.exito')}}", "{{trans('winsumos.guardado_exito')}}");
                }else{
                    alertas('error', "{{trans('winsumos.error')}}", "{{trans('winsumos.error')}}");
                }
            },error: function(data) {
                alertas('error',  "{{trans('winsumos.error')}}", "{{trans('winsumos.error')}}");
                console.log(data);
            }
        });

    }

    function busca_autocomple(ids){
        $(".buscador_items"+ids).autocomplete({
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
                            console.log("hola"+ids)
                        }
                    });
                },

                minLength: 2,
                select: function(data, ui){
                    //alert("asd");
                    console.log("select",ui.item.iva);
                    //document.getElementById("id_item"+id).value = ui.item.value1;
                    $('#id_item'+ids).val(ui.item.value1);
                    
                }
        });
    }
    
    
</script>

@endsection