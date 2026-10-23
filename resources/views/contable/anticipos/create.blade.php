<style type="text/css">
.ui-autocomplete {
    z-index:2147483647;
    position: absolute;
    top: 100%;
    left: 0;
   
    float: left;
    display: none;
    min-width: 160px;   
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
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
<div class="modal-content">
        <div class="modal-header">
            <label>Crear Anticipo</label>
        </div>
        <div class="modal-body">
            <form id="guardar_anticipo" method="post">
                <div class="col-md-12 col-xs-6">
                    <label for="id_empresa" class="control-label">Empresa</label>
                    <select class="form-control input-sm" name="id_empresa" id="id_empresa" onchange="obtener_sucursal()" required>
                        @foreach($empresa_general as $value)
                            <option value="{{$value->id}}">{{$value->razonsocial}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 col-xs-6">
                    <label for="sucursal" class="control-label">Sucursal</label>
                    <select class="form-control input-sm" name="sucursal" id="sucursal" onchange="obtener_caja()">
                        @foreach($empresa_sucurs->sucursales as $sucursal_f)
                        <option selected value="{{$sucursal_f->id}}">{{$sucursal_f->codigo_sucursal}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12 col-xs-6">
                    <label for="punto_emision" class="control-label">Punto de Emision</label>
                    <select class="form-control input-sm" name="punto_emision" id="punto_emision">
                        @foreach($sucursal_f->cajas as $value)
                        <option selected value="{{$value->id}}">{{$value->codigo_caja}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="control-label">PROVEEDOR:</label>
                    <input type="text" class="form-control" onchange="cambiar_nombre_proveedor()" id="nombre_proveedor" name="nombre_proveedor">
                    <input type="hidden" name="proveedor" id="proveedor">
                </div>
                <div class="col-md-12">
                    <label class="control-label">TIPO DE PAGO: </label>
                    <select class="form-control" name="tipo_pago" id="tipo_pago" onchange="traer_cuentas_banco()">
                            <option value="0">Seleccione...</option>
                        @foreach($tipo_pago as $value)
                            <option value="{{$value->id}}">{{$value->nombre}}</option>
                        @endforeach
                    </select>
                    <select style="margin-top: 4px; display:none;" class="form-control" name="bancos" id="bancos"></select>
                </div>
                <div class="col-md-6">
                    <label class="control-label">FECHA PAGO:</label>
                    <input type="date" name="fecha_pago" id="fecha_pago" value="{{date('Y-m-d')}}" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="control-label">MONTO ANTICIPO:</label>
                    <input class="form-control" type="text" name="monto_anticipo" onchange="redondea_precio(this,2)"  onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" id="monto_anticipo">
                </div>
                <div class="col-md-12">
                    <label class="control-label">CONCEPTO:</label>
                    <input type="text" name="concepto" id="concepto" class="form-control">
                </div>
            </form>

        </div>
        <div class="modal-footer">
            <button type="button" style="margin-top: 12px;" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
            <button type="button" style="margin-top: 12px;" onclick="guardar();" class="btn btn-primary">CREAR</button>
        </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
    $("#nombre_proveedor").autocomplete({
    source: function( request, response ) {
        $.ajax( {
        url: "{{route('compra_buscar_nombreproveedor')}}",
        dataType: "json",
        data: {
            term: request.term
        },
        success: function( data ) {
            response(data);
        }
        } );
    },
    minLength: 2,
    } );

    function cambiar_nombre_proveedor(){
        $.ajax({
            type: 'post',
            url:"{{route('compra_buscar_proveedornombre')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'nombre':$("#nombre_proveedor").val()},
            success: function(data){
                if(data.value != "no"){
                    $('#proveedor').val(data.value);
                    $('#direccion_proveedor').val(data.direccion);
                }else{
                    $('#proveedor').val("");
                    $('#direccion_proveedor').val("");
                }

            },
            error: function(data){
                console.log(data);
            }
        });
    }
    function traer_cuentas_banco(){
        var opciones= $("#tipo_pago").val();
        $.ajax({
            type: 'post',
            url:"{{route('anticipo.bancos')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'opciones': opciones},
            success: function(data){
                //console.log(data);
                if(data.value!='no'){
                    if(opciones!=0){
                        $("#bancos").empty();
                        $("#bancos").show('slow');
                        $.each(data,function(key, registro) {
                            $("#bancos").append('<option value='+registro.id+'>'+registro.nombre+'</option>');

                        });
                    }else{
                        $("#bancos").empty();

                    }

                }
            },
            error: function(data){
                console.log(data);
            }
        });
    }
    function guardar(){
        var formulario = document.forms["guardar_anticipo"];
        var concepto= formulario.concepto.value;
        var fecha_pago= formulario.fecha_pago.value;
        var tipo_pago= formulario.tipo_pago.value;
        var monto_anticipo= formulario.monto_anticipo.value;

        var msj = "";
        if(concepto==""){
            msj+="Por favor, Llene el campo concepto<br/>";
        }
        if(fecha_pago==""){
            msj+="Ingrese fecha de pago<br/>";
        }
        if(tipo_pago==""){
            msj+="Por favor, Llene el tipo de pago<br/>";
        }
        if(monto_anticipo==""){
            msj+="Por favor, Llene el monto de la retención<br/>";
        }
        if(msj==""){
            $.ajax({
                    type: 'post',
                    url:"{{route('anticipo_store')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: 'json',
                    data: $('#guardar_anticipo').serialize(),
                    success: function(data){
                    //console.log(data);
                        swal("Correcto!","Se creo el anticipo correctamente al provedoor n°: "+proveedor,"success");
                        location.href="{{route('acreedores_cegreso')}}";
                    },
                    error: function(data){
                        console.log(data);
                    }
            })

        }else{
                swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
        }
    }
    function obtener_caja(){

        var id_sucursal = $("#sucursal").val();
        //alert(id_sucursal);
        $("#sucursal_final").val(id_sucursal);
        $.ajax({
            type: 'post',
            url:"{{route('caja.sucursal')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_sucur': id_sucursal},
            success: function(data){
                //console.log(data);

                if(data.value!='no'){
                    if(id_sucursal!=0){
                        $("#punto_emision").empty();

                        $.each(data,function(key, registro) {
                            $("#punto_emision").append('<option value='+registro.codigo_sucursal+'-'+registro.codigo_caja+'>'+registro.codigo_sucursal+'-'+registro.codigo_caja+'</option>');

                        });
                    }else{
                        $("#punto_emision").empty();

                    }

                }
            },
            error: function(data){
                console.log(data);
            }
        })

    }
function obtener_sucursal(){

    var id_seleccionado = $("#id_empresa").val();

    $.ajax({
        type: 'post',
        url:"{{route('sucursal.empresa')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {'id_emp': id_seleccionado},
        success: function(data){
            //console.log(data);

            if(data.value!='no'){
                if(id_seleccionado!=0){
                    $("#sucursal").empty();

                    $.each(data,function(key, registro) {
                        $("#sucursal").append('<option value='+registro.id+'>'+registro.codigo_sucursal+'</option>');

                    });
                }else{
                    $("#sucursal").empty();

                }

            }
        },
        error: function(data){
            console.log(data);
        }
    })

}
function redondea_precio(elemento,nDec){

    var n = parseFloat(elemento.value);
    var s;
    var d= elemento.value;
    var en= String(d);
    arr = en.split(".");  // declaro el array
    entero= arr[0];
    decimal = arr[1];
    //alert(decimal);
        if(decimal!=null){
            if((decimal.length)>2){
                n = Math.round(n * Math.pow(10, nDec)) / Math.pow(10, nDec);
                s = String(n) + "." + String(Math.pow(10, nDec)).substr(1);
                s = s.substr(0, s.indexOf(".") + nDec + 1);
                $('#monto_anticipo').val(s);
            }
        }else{
            $('#monto_anticipo').val(n.toFixed(2,2));
        }
}
    
    

</script>