@extends('comercial.producto_tarifario.base')
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
</style>

<div class="modal fade" id="detalle_paquet" tabindex="-1" role="dialog"   aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="width: 50%;">
    </div>
  </div>
</div>

<script type="text/javascript">
    function check(e) {
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
        window.history.back();
    }
</script>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header color_cab with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
               <h5><b>{{trans('contableM.fichdeproducoserv-')}}</h5>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-success btn-gray" >
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" role="form" method="POST" action="{{ route('proforma.store_producto_comercial') }}">
                {{ csrf_field() }}
               
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group col-md-12{{ $errors->has('codigo') ? ' has-error' : '' }}">
                                <label for="codigo" class="col-md-4 control-label">Codigo</label>
                                <div class="col-md-4">
                                    <input id="codigo" type="text" class="form-control" name="codigo" value="{{old('codigo')}}" style="text-transform:uppercase;" maxlength="50" required autofocus>
                                    @if ($errors->has('codigo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('codigo') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group col-md-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                <label for="nombre" class="col-md-4 control-label">Nombre</label>
                                <div class="col-md-8">
                                    <input id="nombre" type="text" class="form-control" name="nombre" value="{{old('nombre')}}" style="text-transform:uppercase;" required autofocus>
                                    @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group col-md-12">
                                <label for="descripcion" class="col-md-4 control-label">Descripcion</label>
                                <div class="col-md-8">
                                    <textarea rows="1" class="form-control" id="descripcion" name="descripcion"></textarea>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="descripcion" class="col-md-4 control-label">Precio PVP</label>
                                <div class="col-md-8">
                                 <input id="valor_total_paq" type="text" class="form-control" name="valor_total_paq" value="" style="text-transform:uppercase;" maxlength="50" onkeypress="return isNumberKey(event)"  placeholder="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);"  required autofocus>
   
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <label for="grupo" class="col-md-4 control-label">Grupo</label>
                                <div class="col-md-8">
                                    <select class="form-control select2_grupo" onchange="cambiar_cuentas()" name="grupo" id="grupo" style="width: 100%;">
                                        <option value="">Seleccione...</option>
                                        <option value="1">Insumos</option>
                                        <option value="2">Medicamentos</option>
                                        <option value="4">Procedimientos</option>
                                        <option value="3">Servicios</option>
                                        <option value="5">Otros</option>
                                        <option value="6">Honorario</option>
                                        <option value="7">Equipo</option>
                                        <option value="8">Honorario Anestesiologico</option>
                                        <option value="9">Membresias</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12" style="padding-left: 0px; padding-right: 0px">
                                
                                <div class="form-group col-md-6">

                                    <label style="padding-left: 15px"> {{trans('contableM.aplicaiva')}} &nbsp; <input type="checkbox" id="iva" name="iva" value="1" > </label>
                                </div>
                            </div>
                            </div>

                    </div>
                    <div class="form-group col-xs-10" style="text-align: center;">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-success btn-gray">
                            {{trans('laboratorio.agregar')}}
                            </button>
                        </div>
                    </div>

            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

    <script type="text/javascript">

        $("#codigo").blur(function(){
            //alert("Valida Codigo");
            buscarCodigoProducto();
        });


        function buscarCodigoProducto(){

            var codig = $("#codigo").val();
         
            $.ajax({
                type: 'post',
                url:"{{route('buscar_codigo.producto')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                type: 'POST',
                datatype: 'json',
                data: {'cod_prod': codig},
                success: function(data){
                    console.log(data.existe);
                    if(data.existe!=null){
                        
                        swal("Error!","{{trans('contableM.elcoddelproducyaseencuenregis')}}", "error");
                        $('#codigo').val("");
                    
                    }
                
                },
                error: function(data){
                  console.log(data);
                }
            });

        
        }

        
        //Retorna con dos Decimales
        function checkformat(entry) { 
                
            var test = entry.value;

            if (!isNaN(test)) {
                entry.value=parseFloat(entry.value).toFixed(2);
            }

            if (isNaN(entry.value) == true){      
                entry.value='0.00';        
            }
        
            if (test < 0) {
                entry.value = '0.00';
            }
            
        }

        function buscar_prod() {
            $.ajax({
                type: 'post',
                url: "{{route('contable_find_insumo')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                type: 'POST',
                datatype: 'json',
                data: $("#codigo_prod_0"),
                success: function(data) {
                    console.log(data);
                    if (data != 'no') {
                        // $('#codigo').val(data[0].codigo);
                        $('#nombre').val(data[0].nombre);
                        $('#descripcion').val(data[0].descripcion)
                        $('#marca').val(data[0].id_marca)
                        $('#stock_minimo').val(data[0].minimo)
                    }
                },
                error: function(data) {
                    console.log(data);
                }
            })
        };
        var fila = $("#mifila").html();
        $('body').on('click', '.delete', function() {
            $(this).parent().parent().remove();
        });
        function cambiar_cuentas(){
            var grupo= parseInt($("#grupo").val());
            switch(grupo){
                case 1:
                    $('#cta_gastos').val('1.01.03.01.02').trigger('change');
                    $('#cta_ventas').val('4.1.01.02').trigger('change');
                    $('#cta_costos').val('5.1.01.05').trigger('change');
                    $('#cta_devolucion').val('4.1.07.01').trigger('change');
                    $('#impuesto_iva_compras').val('1.01.05.01.01').trigger('change');
                    $('#impuesto_iva_ventas').val('2.01.07.01.01').trigger('change');
                    $('#impuesto_servicio').val('1').trigger('change');
                    
                break;
                case 2:
                    $('#cta_gastos').val('1.01.03.01.02').trigger('change');
                    $('#cta_ventas').val('4.1.01.02').trigger('change');
                    $('#cta_costos').val('5.1.01.05').trigger('change');
                    $('#cta_devolucion').val('4.1.07.01').trigger('change');
                    $('#impuesto_iva_compras').val('1.01.05.01.01').trigger('change');
                    $('#impuesto_iva_ventas').val('2.01.07.01.01').trigger('change');
                break;
                case 3:
                    $('#cta_gastos').val('1.01.03.01.03').trigger('change');
                    $('#cta_ventas').val('4.1.01.02').trigger('change');
                    $('#cta_costos').val('5.1.01.07').trigger('change');
                    $('#cta_devolucion').val('4.1.07.01').trigger('change');
                    $('#impuesto_iva_compras').val('1.01.01.1.01').trigger('change');
                    $('#impuesto_iva_ventas').val('1.01.01.1.01').trigger('change');
                    $('#impuesto_servicio').val('1').trigger('change');
                    $('#impuesto_ice').val('1').trigger('change');
                    $('#clasificacion_impuesto_ice').val('1').trigger('change');
                    
                break;
                case 4:
                    $('#cta_gastos').val('1.01.03.01.03').trigger('change');
                    $('#cta_ventas').val('4.1.01.02').trigger('change');
                    $('#cta_costos').val('5.1.01.07').trigger('change');
                    $('#cta_devolucion').val('4.1.07.01').trigger('change');
                    
                break;
                case 5:
                    $('#cta_gastos').val('1.01.03.01.03').trigger('change');
                    $('#cta_ventas').val('4.1.01.02').trigger('change');
                    $('#cta_costos').val('5.1.01.07').trigger('change');
                    $('#cta_devolucion').val('4.1.07.01').trigger('change');
                    $('#impuesto_iva_compras').val('1.01.01.1.01').trigger('change');
                    $('#impuesto_iva_ventas').val('1.01.01.1.01').trigger('change');
                    $('#impuesto_servicio').val('1').trigger('change');
                    $('#impuesto_ice').val('1').trigger('change');
                    $('#clasificacion_impuesto_ice').val('1').trigger('change');
                    
                break;
            }
        }
        function nuevo() {
            var nuevafila = $("#mifila").html();
            var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
            rowk.innerHTML = fila;
            //rowk.className="well";
        }

        function isNumberKey(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode
            if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
                return false;

            return true;
        }

        $('.agregar_td').on('click', function() {
            agregar_td();
        });

        $(document).ready(function() {
            $('.select2_cuentas').select2({
                tags: false
            });
        });


        function agregar_td() {


            var id = document.getElementById('contador_prod').value;

            var tr = '<tr class="fila-fija"><td><input type="hidden" style="width:20%;margin-bottom:2px" readonly name="id_insumo[]" class="id_insumo"/><input onchange="agregar_id(this)" class="buscador form-control"  name="codigo_producto[]" id="codigo_producto" style="width:94%;height:20px"/></td><td class="eliminar"><button  type="button"  class="btn btn-danger btn-gray " ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td></tr>'

          
            $('#example2').append(tr);
            var variable = 1;
            var sum = parseInt(id) + parseInt(variable);
            document.getElementById("contador_prod").value = parseInt(sum);

            $(".buscador").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        url: "{{route('contable_find_producto')}}",
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
        $(document).on("click", ".eliminar", function() {
            var parent = $(this).parents().get(0);
            $(parent).remove();
        });

        $('.agregar').on('click', function() {
            agregar();
        });

        function agregar() {
            var id = document.getElementById('contador_insumo').value;
            var tr = '<tr>' + '<td><input type="hidden" name="id_equipo[]" class="id_equipo"/><input required name="insumos_producto[]" id="insumos_producto" onchange="agregar_equipo(this)" class=" buscador_insumos form-control"  style="height:25px;width:94%;" name="producto[]"/></td>' + '<td class="esconder" ><button  type="button"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
            '</tr>' +
            $('#equipo').append(tr);
            var variable = 1;
            var sum = parseInt(id) + parseInt(variable);
            document.getElementById("contador_insumo").value = parseInt(sum);

            $(".buscador_insumos").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        url: "{{route('contable_find_insumo')}}",
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
        $(document).on("click", ".esconder", function() {
            var parent = $(this).parents().get(0);
            $(parent).remove();
        });

        function agregar_id(e) {

            $.ajax({
                type: 'post',
                url: "{{route('contable_find_id')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'nombre': e.value
                },
                success: function(data) {
                    if (data[0] != "") {
                        $(e).parent().parent().find('.id_insumo').val(data[0].id);
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

        function agregar_equipo(e) {
            $.ajax({
                type: 'post',
                url: "{{route('contable_find_id_equipo')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'nombre': e.value
                },
                success: function(data) {
                    if (data[0] != "") {
                        $(e).parent().parent().find('.id_equipo').val(data[0].id);
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


        $('.agregar_procedimientos').on('click', function() {
            agregar_procedimientos();
        });

        function agregar_procedimientos() {
            var id = document.getElementById('contador_procedimientos').value;
            var tr = '<tr>' + '<td><input type="hidden" name="id_procedimiento[]" class="id_procedimiento" /><input required name="proce_id[]" id="proce_id" onchange="agregar_procedimiento(this)" class=" buscador_procedimientos form-control"  style="height:25px;width:94%;" name="producto[]"/></td>' +'<td><select class="form-control select2" style="height:25px;width:94%;" name="seguro_procedimiento[]" > <option value="">Seleccione ...</option> @foreach($seguros as $value) <option value="{{$value->id}}">{{$value->nombre}}</option> @endforeach </select></td>'+'<td><input type="number" style="height:25px;width:94%;" name="precio_procedimiento[]" placeholder="0.00" class="form-control"></td>'+ '<td class="ocultar" ><button  type="button"  class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'
            '</tr>' +
            $('#procedimientos').append(tr);
            var variable = 1;
            var sum = parseInt(id) + parseInt(variable);
            document.getElementById("contador_procedimientos").value = parseInt(sum);
            $(".buscador_procedimientos").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        url: "{{route('contable_find_procedimientos')}}",
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
        $(document).on("click", ".ocultar", function() {
            var parent = $(this).parents().get(0);
            $(parent).remove();
        });

        function agregar_procedimiento(e) {

            $.ajax({
                type: 'post',
                url: "{{route('contable_find_id_procedimiento')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'nombre': e.value
                },
                success: function(data) {
                    if (data[0] != "") {
                        $(e).parent().parent().find('.id_procedimiento').val(data[0].id);
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

        $('.agregar_paquetes').on('click', function() {
            agregar_paquetes();
        });

        function agregar_paquetes(){

          id= document.getElementById('contador_paquetes').value;
          id_temp_paq = document.getElementById('cont_paq_temp').value;

          //Creamos la Tabla Temporal
          var midiv_item = document.createElement("tr")
          midiv_item.setAttribute("id","dato"+id);

          midiv_item.innerHTML = '<td><input required type="hidden" id="visibilidad_paquete'+id+'" name="visibilidad_paquete'+id+'" value="1"><input type="number" name="paque_cant'+id+'" id="paque_cant'+id+'" class="form-control" style="height:25px;width:75%;"></td><td><input type="hidden" name="id_paquete'+id+'" class="id_paquete"><input type="hidden" name="iva_prod'+id+'" class="iva_prod"><input required name="paque_id'+id+'" id="paque_id'+id+'" onchange="agregar_valores(this)" class=" buscador_paquetes form-control"  style="height:25px;width:90%;"></td><td><input type="text" name="precio_paq'+id+'" id="precio_paq'+id+'" class="form-control" style="height:25px;width:75%;" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="checkformat(this);" autocomplete="off"></td><td style="width: 40px;"><button type="button" onclick="eliminar_item_paquete('+id+')" class="btn btn-danger btn-gray"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>'

            document.getElementById('detalle_paquete').appendChild(midiv_item);
            id = parseInt(id);
            id = id+1;

            document.getElementById('contador_paquetes').value = id;


            $(".buscador_paquetes").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('input[name=_token]').val()
                        },
                        url: "{{route('contable_find_paquete')}}",
                        dataType: "json",
                        data: {term: request.term,'posicion_paq': id_temp_paq},
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                
                change: function(event, ui){
                    
                    //obtener_precio_producto(ui.item.codig_product,ui.item.pos_paq);
                    id_temp_paq = parseInt(id_temp_paq);
                    id_temp_paq = id_temp_paq+1;
                    document.getElementById('cont_paq_temp').value = id_temp_paq;
                
                },

                minLength: 2,
            
            });

        }

        function eliminar_item_paquete(valor)
        {
            var dato_item1 = "dato"+valor;
            var dato_item2 = 'visibilidad_paquete'+valor;
            document.getElementById(dato_item1).style.display='none';
            document.getElementById(dato_item2).value = 0;
        }


        //Obtener Precio del Producto
        function obtener_precio_producto(cod_prod,posi){
        
            $.ajax({
                type:'post',
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                url:"{{route('contable_precio_prod.codigo')}}",
                datatype: 'json',
                data: {'cod_prod': cod_prod,'posicion': posi},
                success: function(data){
                  //console.log(data);
                  //response(data);
                  if(data.no_data!='no'){
                   //alert(data.prec_prod)
                   //alert(data.posic);     
                   $('#precio_paq'+data.posic).val(data.prec_prod);
                  }
                
                },
                error: function(data){
                  console.log(data);
                }
            });

        }

        $(document).on("click", ".remover", function() {
            var parent = $(this).parents().get(0);
            $(parent).remove();
        });

        function agregar_valores(e) {

            $.ajax({
                type: 'post',
                url: "{{route('contable_find_id_paquete')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                datatype: 'json',
                data: {
                    'nombre': e.value
                },
                success: function(data){
                    
                    if (data[0] != "") {
                        
                        $(e).parent().parent().find('.id_paquete').val(data[0].id);
                        $(e).parent().parent().find('.iva_prod').val(data[0].iva);
                    
                    }
                   
                },
                error: function(data) {
                    console.log(data);
                }
            });
        
        }

    </script>

</section>
@endsection