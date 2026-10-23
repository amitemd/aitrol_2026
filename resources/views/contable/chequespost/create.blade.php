@extends('contable.chequespost.base')
@section('action-content')

<style type="text/css">
    .ui-autocomplete
        {
             overflow-x: hidden;
              max-height: 200px;
              width:1px;
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
        .disableds{
            display: none;
        }
        .disableds2{
            display: none;
        }
        .disableds3{
            display: none;
        }
        .has-cc span img{
            width:2.775rem;
        }
        .has-cc .form-control-cc {
            z-index: 2;
            display: block;
            width: 2.375rem;
            height: 2.375rem;
            line-height: 1.8rem;
            text-align: center;
            pointer-events: none;
            color: #444;
            font-size: 1.5em;
            float: right;
            margin-right: 1px;

        }
        .has-cc .form-control-cc2{
            z-index: 2;
            display: block;
            width: 2.375rem;
            height: 2.375rem;
            line-height: 1.8rem;
            text-align: center;
            pointer-events: none;
            color: #444;
            font-size: 1.5em;
            float: right;
            margin-right: 1px;
        }
        .cvc_help{
            cursor: pointer;
        }
        .card {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            padding: 16px;
            text-align: center;
            background-color: white;
        }
        .card2 {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            padding: 16px;
            text-align: center;
            background-color: #f1f1f1;
        }
        .swal-title {
            margin: 0px;
            font-size: 16px;
            box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
            margin-bottom: 28px;
        }
        .cabecera{
            background-color: #9E9E9E;
            border-radius: 2px;
            color: white;
        }
        .borde{
            border:2px solid #9E9E9E;
        }
        .hde{
            background-color: #888;
            width: 100%;
            height: 25px;
            margin: 0 auto;
            line-height: 25px;
            color: #FFF;
            text-align: center;
        }
        .cien{
            width: 98%;
            
        }
        .cien2{
            width: 95%;
        }
        .cien3{
            width: 95%;
        }



</style>

<script type="text/javascript">
    function check(e){
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
      location.href="{{route('chequespost.index')}}";
    }

</script>

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="modal fade bd-example-modal-lg" id="calendarModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" id="content">

        </div>
    </div>
</div>
<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="{{route('clientes.index')}}">Clientes</a></li>
        <li class="breadcrumb-item"><a href="{{route('chequespost.index')}}">Recibo de Cheques PostGirados</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nuevo Recibo de Cheques PostGirados</li>
      </ol>
    </nav>
    <form class="form-vertical " id="crear_factura" role="form" method="post">
        {{ csrf_field() }}
            <div class="box box-solid">
                <div class="box-header header_new">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-9 col-sm-9 col-6">
                                <div class="box-title" ><b>RECIBO DE CHEQUES POSTFECHADOS CLIENTES</b></div>
                            </div>
                            <div class="col-3">
                                <div class="row">
                                    <a class="btn btn-success btn-gray btn-xs" href="javascript:guardar()" id="boton_guardar" ><i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                    </a>
                                    <button type="button" class="btn btn-success btn-xs btn-gray" onclick="nuevo_comprobante()" style="margin-left: 10px;">
                                        <i class="glyphicon glyphicon-file" aria-hidden="true"></i>&nbsp;&nbsp;Nuevo
                                    </button>
                                    <a class="btn btn-success btn-gray btn-xs" style="margin-left: 3px;" href="javascript:goBack()" >
                                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-body dobra">
                    
                        <div class="row header">
                            <div class="col-md-12">
                                <div class="row  ">
                                    <div class="col-md-12">
                                        &nbsp;
                                    </div>

                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="label_header" style="padding-left: 0px">Estado</label>
                                        <input class="col-md-12 col-xs-12" style="background-color: green;" readonly>
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Id</label>
                                        <input id="idx" type="text" class="form-control" name="idx" readonly>
                                           
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Número</label>
                                        <input class="form-control " type="text" name="numero" id="numero" readonly>
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Tipo</label>
                                        <input class="form-control " type="text" name="tipo" id="tipo" readonly value="CLI-CH">
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Fecha</label>
                                        <div class="input-group col-md-12" >
                                            <input class="col-md-12 col-xs-12 form-control " id="fecha" type="date" name="fecha" value="{{date('Y-m-d')}}">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Asiento</label>
                                        <input class="form-control " type="text" name="asiento" id="asiento" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row  ">
                                    
                                    <div class="col-md-2 col-xs-2 px-1" >
                                            <label class="col-md-12 label_header" for="acreedor">Cliente: </label>
                                            <input type="text" id = "id_cliente" name="id_cliente" placeholder="Cédula" class= "form-control form-control-sm id_cliente col-md-12"  >
                                    </div>
                                    <div class="col-md-4 col-xs-4 px-1" >
                                        <label class="col-md-12 label_header" for="valor">&nbsp;</label>
                                            <input type="text" id = "nombre_proveedor" name="nombre_proveedor" placeholder="Nombre Cliente" class= "form-control form-control-sm nombre_proveedor  col-md-12" >
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="col-md-12 label_header" for="valor">Valor:</label>
                                        <input class="form-control" type="text" id="valor_total" placeholder="$ 0.00" autocomplete="off" name="valor_total" onblur="setNumber(this.value)" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                                    </div>
                                    <div class="col-md-2 col-xs-2 px-1">
                                        <label class="control-label label_header">Caja</label>
                                        <select class="form-control select2_cuentas" style="width: 100%;" name="id_caja" id="id_caja">
                                            <option value="">Seleccione...</option>
                                            @foreach($caja as $value)
                                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2 col-xs-2" style="top: 10px;">
                                        <button type="button" class="btn btn-primary btn-gray" onclick="boton_deuda();">Aplicar Deudas</button>
                                    </div>  
                                </div>
                            </div>
                            <div class="col-md-12 px-1">
                                <input type="hidden" name="total_suma_a" id="total_suma_a">
                                <input type="hidden" name="saldoax" id="saldoax">
                                <label class="control-label  label_header" for="">DETALLE DE CHEQUES RECIBIDOS</label>
                           </div>
                           <div class="table-responsive col-md-12 px-1">
                            <input type="hidden" name="contador_a" id="contador_a" value="0">
                            <table id="example3" class="table-responsive col-md-12" role="grid" aria-describedby="example2_info">
                                <thead style="background-color: #9E9E9E; color: white;" >
                                <tr style="position: relative;">
                                    <th style="width: 10%; text-align: center;">Tipo</th>
                                    <th style="width: 5%; text-align: center;">Fecha</th>
                                    <th style="width: 5%; text-align: center;">Número</th>
                                    <th style="width: 12%; text-align: center;">Banco</th>
                                    <th style="width: 5%; text-align: center;">Cuenta</th>
                                    <th style="width: 18%; text-align: center;">Girador</th>
                                    <th style="width: 8%; text-align: center;">Valor</th>
                                    <th style="width: 3%; text-align: center;">Valor B</th>
                                    <th style="width: 3%; text-align: center;">
                                        <button onclick="crea_td()" type="button" class="btn btn-success btn-gray btn-xs" >
                                            <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                        </button>
                                    </th>
                                </tr>
                                </thead>
                                <tbody id="det_recibido">
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                          </div>
                         
                            
                                <div class="col-md-1 px-1">
                                    &nbsp;
                                </div>
                                <div class="col-md-1 px-1">
                                &nbsp;
                                </div>
                                <div class="col-md-2 px-1">
                                &nbsp;
                                </div>
                                <div class="col-md-2 px-1">
                                &nbsp;
                                </div>
                                <div class="col-md-2 px-1">
                                &nbsp;

                                </div>
                                <div class="col-md-2 px-1">
                                &nbsp;

                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header col-md-12"> TOTAL INGRESOS:</label>
                                    <input class="form-control col-md-3" type="text" name="total_ingresos" id="total_ingresos" readonly>
                                </div>
                            
                         

                        <input type="text" name="contador" id="contador" value="0" class="hidden">
                        <input type="hidden" name="total_suma" id="total_suma">
                                         
                    <div class="col-md-12 px-1">
                        <label class="label_header" for="detalle_deuda">DETALLE DE DEUDAS DEL CLIENTE</label>
                    </div>
                   
                        <div class="table-responsive col-md-12 px-1" style="min-height: 250px; max-height: 250px;">               
                            <table id="example2" class="table-responsive col-md-12" role="grid" aria-describedby="example2_info">
                                <thead style="background-color: #9E9E9E; color: white;" >
                                    <tr style="position: relative;">
                                        <th style="width: 10%; text-align: center;">DI</th>
                                        <th style="width: 8%; text-align: center;">Emisión</th>
                                        <th style="width: 5%; text-align: center;">Tipo</th>
                                        <th style="width: 2%; text-align: center;">Número</th>
                                        <th style="width: 20%; text-align: center;">Concepto</th>
                                        <th style="width: 5%; text-align: center;">Div</th>
                                        <th style="width: 5%; text-align: center;">Saldo</th>
                                        <th style="width: 5%; text-align: center;">Abono</th>
                                        <th style="width: 5%; text-align: center;">Nuevo Saldo</th>
                                        
                                    </tr>
                                </thead>
                                <tbody id="crear">
                                
                                @php $cont=0; @endphp
                                @foreach (range(1, 6) as $i)
                                    <tr>
                                        <td> <input class="form-control" type="text" name="vence{{$cont}}" id="vence{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" type="text" name="tipo{{$cont}}" id="tipo{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" type="text" name="numero{{$cont}}" id="numero{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" type="text" name="concepto{{$cont}}" id="concepto{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" style="background-color: #c9ffe5;" type="text" name="div{{$cont}}" id="div{{$cont}}" value="$" readonly> </td>
                                        <td> <input class="form-control" style="background-color: #c9ffe5; width: 150% " type="text" name="saldo{{$cont}}" id="saldo{{$cont}}" readonly> </td>
                                        <td> <input class="form-control" style="background-color: #c9ffe5; text-align: center;" type="text" name="abono{{$cont}}" id="abono{{$cont}}" readonly></td>
                                        <td> <input class="form-control" style="width: 100%; text-align: left;" type="text" name="nuevo_saldo{{$cont}}" id="nuevo_saldo{{$cont}}" readonly></td>
                                        <td> <input class="form-control" style="width: 100%; text-align: center;" type="text" name="abono_base{{$cont}}" id="abono_base{{$cont}}" readonly> </td>
                                    </tr>
                                    @php $cont = $cont +1; @endphp
                                @endforeach
                                    
                                </tbody>
                                <tfoot>
                                </tfoot>
                            </table>
                        </div>
                            
                                <div class="col-md-1 px-1">
                                    &nbsp;
                                </div>
                                <div class="col-md-1 px-1">
                                &nbsp;
                                </div>
                                <!--
                                <div class="col-md-2 px-1">
                                    <label class="label_header col-md-12"> TOTAL CRÉDITO:</label>
                                    <input class="form-control col-md-3" type="text" name="total_ingreso" id="total_ingreso" class="col-md-12" readonly>
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header col-md-12"> TOTAL DE ABONOS:</label>
                                    <input class="form-control col-md-3" type="text" name="total_abono" id="total_abono" class="col-md-12" readonly>
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header col-md-12"> NUEVO SALDO:</label>
                                    <input class="form-control col-md-3" type="text" name="nuevo_saldo" id="nuevo_saldo" class="col-md-12" readonly>
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header col-md-12"> DEFICIT DE INGRESO:</label>
                                    <input class="form-control col-md-3" type="text" name="deficit" id="deficit" class="col-md-12" readonly>
                                </div>
                                <div class="col-md-2 px-1">
                                    <label class="label_header col-md-12"> SUPERAVIT:</label>
                                    <input class="form-control col-md-3" type="text" name="total_ingresos21" id="total_ingresos21" class="col-md-12" readonly>
                                </div>-->
                            
                         
                        
                         
                                <div class="form-group col-md-12">
                                    
                                    <input class="form-control" type="text" name="autollenar" id="autollenar" autocomplete="off" >
                                </div>
                                <div class="form-group col-md-12">
                                    <label class="label_header" for="">Observaciones</label>
                                    <textarea class="form-control" name="observaciones2" id="observaciones2" cols="30" rows="5"></textarea>
                                </div>
                                <div class="form-group col-md-2">
                                    <div class="input-group">
                                        
                                        <input type="hidden" name="proveedor" id="proveedor"> 
                                        <input type="hidden" name="sobrante" id="sobrante">
                                        <input type="hidden" name="egreso_retenciones" id="egreso_retenciones">
                                    </div>
                                </div>
                            
                        
                  
                        </div>


                </div>
    </form>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/icheck.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>

<script type="text/javascript">

    $(document).ready(function(){

        //$('#myform')[0].reset(); PARA LIMPIAR TODOS LOS INPUTS DENTRO DEL FORM
        $('.select2_cuentas').select2({
            tags: false
        });
        $('#fact_contable_check').iCheck({
        checkboxClass: 'icheckbox_flat-blue',
        increaseArea: '20%' // optional
        });

    });
    function setNumber(e){
       // return parseFloat(e).toFixed(2);
       //if(e.length)
       if(e==""){
       e=0;
       }
        $("#valor_total").val(parseFloat(e).toFixed(2))
        $("#total_ingresos").val(parseFloat(e).toFixed(2))
    }
    function crea_td(contador){
        id= document.getElementById('contador').value;
        var midiv = document.createElement("tr");
        midiv.setAttribute("id","dato"+id);
        midiv.innerHTML = '<td><select class="cien form-control" onchange="cambio_banco('+id+')" name="tipo'+id+'" style="width: 100%; height: 75%;" id="tipo'+id+'"> <option value="2"> CHEQUE </option> </select></td> <td><input class="visibilidad" type="hidden" id="visibilidad'+id+'" name="visibilidad'+id+'" value="1"><input name="fecha'+id+'" class="cien2 " style="width: 100%; height: 75%;" value="{{date("Y-m-d")}}" type="date" id="fecha'+id+'" ></td><td> <input style=" width: 100%; height: 75%;" class="cien3 form-control" name="numero_a'+id+'" id="numero_a'+id+'" autocomplete="off"> </td><td> <select style=" width: 100%; height: 75%;" class="cien form-control select2_cuentas2" name="banco'+id+'" id="banco'+id+'"> @foreach($bancos as $value) <option value="{{$value->id}}">{{$value->nombre}}</option>  @endforeach </select></td><td><input style="height: 75%; width: 100%;" class="cien3 form-control" name="cuenta'+id+'" autocomplete="off" id="cuenta'+id+'" ></td><td><input class="cien3 form-control" style="width: 100%; height: 75%;" autocomplete="off"  type="text" id="girador'+id+'" name="girador'+id+'"></td><td> <input style="width: 100%; height: 75%;" class="cien3 form-control" type="text" name="valor'+id+'" onchange="validar_td('+id+')"  id="valor'+id+'" autocomplete="off" ><td> <input style="width: 100%; height: 75%;" class="cien3 form-control" type="text" name="valor_base'+id+'" id="valor_base'+id+'" value="0.00" disabled> </td><td style="text-align:center;"><button id="eliminar'+id+'" type="button" onclick="javascript:eliminar_registro('+id+')" class="btn btn-danger btn-gray delete btn-xs"> <i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button></td>';
        document.getElementById('det_recibido').appendChild(midiv);
        llenar_girador(id);
        id = parseInt(id);
        id = id+1;
        document.getElementById('contador').value = id;
        $('.select2_cuentas2').select2({
                tags: false
            });

    }

    function llenar_girador(id){
        var girador= $('#nombre_proveedor').val();  
        //console.log(girador);
        if((girador)!=null){
            $('#girador'+id).val(girador);
        }else{
            console.log('error NAN');
        }
    }
    function buscarAsiento(id_asiento){
        $.ajax({
            type: 'get',
            url: "{{route('buscar_asiento.diario')}}",
            datatype: 'json',
            data: {
                'id_asiento': id_asiento,
                'validacion': '8'
            },
            success: function(data) {

                if (data.value != 'No se encontraron resultados') {
                    $('#asiento').val(data);
                }


            },
            error: function(data) {
                console.log(data);
            }
        })
    }
    function cambio_banco(id){
        if(id!=null){
            $("#banco"+id).empty();
            var valor= $("#tipo"+id).val();
            var eleman = document.getElementById("banco"+id);
            var validacion=0;
            //console.log(valor);
            switch(valor){
                case '1': 
                    validacion=3;
                    break;
                case '4':
                    validacion= 1;
                    break;
                case '6': 
                     validacion=2;
                    break;
            }
            if(validacion!=3){
                $.ajax({
                type: 'post',
                url:"{{route('comp_ingreso.tarjeta')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: {'opcion': validacion},
                success: function(data){
                    //alert(data[0].nombre);
                    if(data.value!='no'){
                        if(valor!=0){
                            $("#banco"+id).empty();
                            
                            //remove it
                            eleman.disabled = false;
                            $.each(data,function(key, registro) {
                                $("#banco"+id).append('<option value='+registro.id+'>'+registro.nombre+'</option>');
                            }); 
                        }else{
                            $("#banco"+id).empty();
                        }
    
                    }
                },
                error: function(data){
                    console.log(data);
                }
              })
            }else{
                eleman.disabled = true;
            }
            
        }else{
            console.log("Error id null");
        }
    }
    function boton_deuda(){
        var valor= parseFloat($("#valor_total").val());
        var valor_saldo= parseFloat($("#saldo_a0").val());
        var total=0;
        if(!isNaN(valor) && !isNaN(valor_saldo)){

            var valor= parseFloat($("#valor_total").val());
            var valor2= parseFloat($("#valor_total").val());
            var valor_saldo= parseFloat($("#saldo0").val());
            var contador=parseInt($("#contador_a").val());
            var saldo=0
            var abono=0;
            var total=0;
            var nuevo_s=0;
            for(i=0; i<=contador; i++){
                saldo+= parseFloat($("#saldo_a"+i).val());
                valor_saldo= parseFloat($("#saldo_a"+i).val());
                valor-=valor_saldo;
                var cont= parseFloat($("#abono_a"+i).val());
                if(isNaN(cont)){
                    cont=0;
                }
                abono+= cont;
                console.log(valor);
                if(valor>valor_saldo){
                    $("#abono_a"+i).val(valor_saldo.toFixed(2,2));
                    //suma_totales2();
                }else{
                    total= valor+valor_saldo;
                    console.log(abono+" anthony");
                    if(total<=valor2 ){
                        if(total>0){
                            console.log("entra");
                            if(total>valor_saldo){
                                total=valor_saldo;
                                $("#abono_a"+i).val(total.toFixed(2,2));
                                //suma_totales2();
                            }else{
                                $("#abono_a"+i).val(total.toFixed(2,2));
                                //suma_totales2();
                            }
                    
                        }
                    }
                    
                    
                
                }
                console.log("veces");
            }
            
        }else{
            swal("Error!","Ingrese valor","error");
        }
    }

    function eliminar_registro(valor)
    {
        var dato1 = "dato"+valor;
        var nombre2 = 'visibilidad'+valor;
        document.getElementById(nombre2).value = 0;
        document.getElementById(dato1).style.display='none';
        suma_totales();
    }
    function guardar(){
        //$('#crear_factura').submit();
        //validaciones
          let boton = document.getElementById('boton_guardar');

            boton.style.display = "none"

        var formulario = document.forms["crear_factura"];
        var proveedor= formulario.id_cliente.value;
        var nombre_proveedor= formulario.nombre_proveedor.value;
        var fecha= formulario.fecha.value;  
        var msj = "";
        var contador_a= formulario.contador_a.value;
        var contador= formulario.contador.value;
        var valor_total= formulario.valor_total.value;
        var autollenar= formulario.autollenar.value;
        var validacion= validar_tabla1();
        if(proveedor==""){
            msj+="Por favor, Llene el campo id cliente <br/>";
        }
        if(nombre_proveedor==""){
            msj+="Por favor, Llene el campo de cliente<br/>";
        }
        if(fecha==""){
            msj+="Por favor, Llene la fecha <br/>";
        }
        if(contador_a==""){
            msj+="Por favor, Llene los campos faltantes de la tabla <br/>";
        }
        if(contador==0){
            msj+="Por favor, Llene los campos faltantes de la tabla deuda <br/>";
        }
        if(valor_total==""){
            msj+="Por favor, Llene el campo valor <br/>";
        }
        if(autollenar==""){
            msj+="Por favor llenar el campo de observaciones <br/>";
        }
        if(msj==""){
            //alert("entras");
            $.ajax({
                    type: 'post',
                    url:"{{route('chequespost.store')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data: $('#crear_factura').serialize(),
                    success: function(data){
                        swal("Correcto!","Se creo el comprobante correctamente","success");
                        $('#crear_factura input').attr('readonly', 'readonly');
                        $("#boton_guardar").attr("disabled", true);
                        $("#idx").val(data);
                        buscarAsiento(data);
                        $("#asiento").val(data);
                        
                    },
                    error: function(data){
                         boton.style.display = "block"
                        console.log(data);
                    }
            })
        }else{
            $('#boton_guardar').removeAttr("disabled");
                swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
        }

    }


    $("#id_cliente").autocomplete({
    source: function( request, response ) {
        $.ajax( {
        type: 'GET',
        url: "{{route('ventas.buscarclientexid')}}",
        dataType: "json",
        data: {
            term: request.term
        },
        success: function( data ) {
            response(data);
        
            //console.log("identificacion_cliente", data.id);
            if(data.id==""){
                //swal("el cliente no existe");
                existeCliente= false; 
            }else{
                existeCliente = true;
            }
        }
        } );
        },
        change:function(event, ui){
            $("#crear").empty();
            $("#nombre_proveedor").val(ui.item.nombre);
            buscar_vendedor();
        },
    selectFirst: true,
    minLength: 1,
    } );
    function validar_tabla1(){
        var contador = parseInt($('#contador').val());
        var validacion=0;
        if(!isNaN(contador)){
            for(i=0; i<=contador; i++){
                var tipo= $("#tipo"+i).val();
                if(tipo == undefined){
                    validacion ++ ;
                }
               
            }  
           
            if(validacion>0){
                return 'error';
            }else{
                return 'ok';
            }
             
        }else{
            console.log("Error contador");
        }
    }

    $("#nombre_proveedor").autocomplete({

        source: function( request, response ) {
            $.ajax( {
            type: 'GET',
            url: "{{route('ventas.buscarcliente')}}",
            dataType: "json",
            data: {
                term: request.term
            },
            success: function( data ) {
                response(data);
            }
            } );
        },
        change:function(event, ui){
            $("#crear").empty();
            $("#id_cliente").val(ui.item.id);
            buscar_vendedor();

        },
        selectFirst: true,
        minLength: 1,
    } );

    function buscar_vendedor(){
        var proveedor= $("#id_cliente").val();
        var tipo= parseInt($("#esfac_contable").val());
        $.ajax({
            type: "post",
            url: "{{route('clientes.deudas')}}", 
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data:{'id_cliente':proveedor,'tipo':tipo},
            success: function(data){
                $("#crear").empty();
                //console.log("entra");
                if(data.value!="no"){
                    $("#crear").empty();
                    var fila = 0;
                    //console.log(data);
                    for(i=0; i<data[4].length;i++){
                        if(tipo!=1){
                            var row =addNewRow(i, data[4][i].fecha, data[4][i].valor_contable, 'VEN-FA', data[4][i].numero, data[4][i].nro_comprobante, data[4][i].valor_contable, data[4][i].id);
                            $('#example2').append(row);
                            fila = i;
                        }else{
                            var row =addNewRow(i, data[4][i].fecha, data[4][i].valor_contable, 'VEN-FA', data[4][i].numero, data[4][i].nro_comprobante, data[4][i].valor_contable, data[4][i].id);
                            $('#example2').append(row);
                            fila = i;
                        }
                    }
                    //console.log("total es:",fila);
                    $("#contador_a").val(fila);
                    //console.log(data[5]);
                }
                //swal("Correcto!", "Superavit creado correctamente", "success");                  
            },
            error:  function(data){
               //console.log(data);             
               $("#crear").empty();
            }
        });

        

    }
    function validar_td(id){
        if((id)!=null){
            var valor= parseFloat($("#valor_total").val());
            var abono= parseFloat($("#valor"+id).val());
            suma_totales();
            var cantidad= parseFloat($("#total_suma").val());
            if(!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)){
                if(cantidad<=valor){
                    $("#valor"+id).val(abono.toFixed(2,2));
                }else{
                        valor=0;
                        $("#valor"+id).val(valor.toFixed(2,2));
                        swal("¡Error!","Error no puede superar al valor","error")
                    }
            }else{
                abono=0;
                valor=0;
                $("valor"+id).val(valor.toFixed(2,2));
            }
        }else{
            alert("error");
        }
    }
    function validar_td2(id){
        if((id)!=null){
            var valor= parseFloat($("#valor_total").val());
            var abono= parseFloat($("#abono_a"+id).val());
            var saldo= parseFloat($("#saldo_a"+id).val());
            suma_totales2();
            var cantidad= parseFloat($("#total_suma_a").val());
            if(!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)){
                if(cantidad<=valor){
                    var uno=1;
                    $("#verificar_superavit").val(uno);
                    if(abono>saldo){
                        abono=saldo;
                    }
                    $("#abono_a"+id).val(abono.toFixed(2,2));
                }else{
                    valor=0;
                    $("#abono_a"+id).val(valor.toFixed(2,2));
                    swal("¡Error!","Error no puede superar al valor del cheque","error")
                }
            }else{
                abono=0;
                valor=0;
                $("#abono_a"+id).val(valor.toFixed(2,2));
            }
        }else{
            alert("error");
        }
    }
    function suma_totales2(){
      contador  =  0;
      iva = 0;
      total = 0;
      sub = 0;
      descu1 = 0;
      total_fin = 0;
      descu = 0;
      cantidad = 0;
     
      $("#crear tr").each(function(){
        $(this).find('td')[0];
                cantidad= parseFloat($("#abono_a"+contador).val());
                if(!isNaN(cantidad)){
                    total+=cantidad;
                }
            contador = contador+1;
        });
        if(isNaN(total)){ total=0;}
        $("#total_suma_a").val(total.toFixed(2,2));

        //alert(total_fin);

    }
    function suma_totales3(){
      contador  =  0;
      iva = 0;
      total = 0;
      sub = 0;
      descu1 = 0;
      total_fin = 0;
      descu = 0;
      cantidad = 0;
     
      $("#crear_a tr").each(function(){
        $(this).find('td')[0];
                cantidad= parseFloat($("#saldo_a"+contador).val());
                if(!isNaN(cantidad)){
                    total+=cantidad;
                }
            contador = contador+1;
        });
        if(isNaN(total)){ total=0;}
        $("#saldoax").val(total.toFixed(2,2));

        //alert(total_fin);

    }
    function suma_totales(){
      contador  =  0;
      iva = 0;
      total = 0;
      sub = 0;
      descu1 = 0;
      total_fin = 0;
      descu = 0;
      cantidad = 0;
     
      $("#det_recibido tr").each(function(){
        $(this).find('td')[0];
                cantidad= parseFloat($("#valor"+contador).val());
                if(!isNaN(cantidad)){
                    total+=cantidad;
                }
            contador = contador+1;
        });
        if(isNaN(total)){ total=0;}
        $("#total_suma").val(total.toFixed(2,2));
        //alert(total_fin);

    }
    function addNewRow(pos,fecha, valor, factura, fact_numero, observacion, valor_nuevo,ids){
        var markup = "";
        var num= parseInt(pos)+1;
        markup = "<tr>"+
                "<td> <input class='form-control' type='text' name='vence"+pos+"' id='vence"+pos+"' readonly='' value='"+ids+"'> </td>"+
                "<td> <input class='form-control' type='text' name='emision"+pos+"' id='emision"+pos+"' value='"+fecha+"' readonly=''> </td>"+ "<td> <input class='form-control' type='text' name='tipo_a"+pos+"' id='tipo_a"+pos+"' value='VEN-FA' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' name='numero"+pos+"' id='numero"+pos+"' value='"+fact_numero+"' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' name='observacion"+pos+"' id='observacion"+pos+"' value='Fact:"+fact_numero+" Ref: "+observacion+"' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' name='div"+pos+"' id='div"+pos+"' value='$' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' name='saldo_a"+pos+"' value='"+valor+"' id='saldo_a"+pos+"' readonly=''> </td>"+
                "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; text-align: center;' name='abono_a"+pos+"' id='abono_a"+pos+"' onchange='validar_td2("+pos+")'></td>"+
                "<td> <input style='width: 100%;' class='form-control' type='text' style=' text-align: left;' name='nuevo_saldo"+pos+"' value='"+valor+"' id='nuevo_saldo"+pos+"' readonly=''></td>"+
            "</tr>";
        return markup;

    }

    function addNewRow2(pos,fecha, tipo, fact_numero, observacion, valor){
        var markup = "";
        var num= parseInt(pos)+1;
        markup = "<tr>"+
                "<td> <input type='text' name='emision"+pos+"' id='emision"+pos+"' readonly='' value='"+fecha+"'> </td>"+
                "<td> <input type='text'  style='width: 100%;' name='tipo"+pos+"' id='tipo"+pos+"' value='"+tipo+"' readonly=''> </td>"+
                "<td> <input type='text' name='numero_a"+pos+"' id='numero_a"+pos+"' value='"+fact_numero+"' readonly=''> </td>"+
                "<td> <input type='text' style='width: 100%;' name='concepto"+pos+"' id='concepto"+pos+"' value='"+observacion+"' readonly=''> </td>"+
                "<td> <input type='text' style='background-color: #c9ffe5; ' name='div"+pos+"' id='div"+pos+"' value='$' readonly=''> </td>"+
                "<td> <input type='text' style='background-color: #c9ffe5; ' name='saldo_a"+pos+"' value='"+valor+"' id='saldo_a"+pos+"' readonly=''> </td>"+
                "<td> <input type='text' style='background-color: #c9ffe5; text-align: center;' name='abono_a"+pos+"' id='abono_a"+pos+"' onchange='validar_saldos("+pos+")'></td>"+
                "<td> <input type='text' style=' text-align: left;' name='nuevo_saldo"+pos+"' value='0.00'  id='nuevo_saldo_a"+pos+"' readonly=''><input type='hidden' name='visibilidad"+pos+"' id='visibilidad"+pos+"' value='0'></td>"+
            "</tr>";
        return markup;

    }
    function buscador_anticipos(){
        var proveedor= $("#id_proveedor").val();
        var tipo= parseInt($("#esfac_contable").val());
        $.ajax({
            type: "post",
            url: "{{route('cruce.anticipos')}}", 
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "json",
            data:{'proveedor':proveedor,'tipo':tipo},
            success: function(data){    
                //console.log(data);
                if(data.value!="no"){
                    $("#crear_a").empty();
                    var fila = 0;

                    for(i=0; i<data.length;i++){
                            var row =addNewRow2(i, data[i].fecha_asiento, 'ACR-EG',data[i].secuencia,data[i].observacion, data[i].valor_abono);
                            $('#example3').append(row);
                            fila = i;
                    }
                    //console.log("total es:",fila);
                    $("#contador_a").val(fila);
                    //console.log(data[5]);
                }
                
            },
            error:  function(data){
               console.log(data);             

            }
        });

        

    }
    function nuevo_comprobante(){
        location.href="{{route('chequespost.create')}}";
    }

</script>
</section>
@endsection
