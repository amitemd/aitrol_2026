<style type="text/css">
    .texto_derecha{
        text-align: right;
    }
</style>
@php
 $query1= DB::table('ct_asientos_cabecera')->where('fact_numero',$fact_numero)->first();
 $query2= DB::table('ct_compras')->where('id_asiento_cabecera',$query1->id)->first();
 $query3= DB::table('proveedor')->where('id',$query2->proveedor)->first();
@endphp
<div class="modal-content">
        <div class="modal-header">
                <label for="title" class="control-label col-md-11">ANTICIPO A PROVEEDORES</label>
                <button type="button" class="close texto_derecha" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
        </div>
        <form id="guardar_datos()" enctype="multipart/form-data" method="POST">
            {{ csrf_field() }} 
            <div class="modal-body">
                <div class="form-group col-md-12">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="input-factura" class="control-label">Factura N°</label>
                            <input type="text" class="form-control" name="no_factura" id="no_factura" onlyread value="{{$fact_numero}}" readonly>
                            <input type="hidden" name="proveedor" id="proveedor" value="{{$query2->proveedor}}">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="input-proveedor">Proveedor</label>
                            <input class="form-control" type="text" name="proveedor" id="proveedor" readonly value="{{$query3->nombrecomercial}}">
                        </div>
                        <div class="form-group">
                             <label class="control-label" for="formas_pago">Formas de pago</label>
                             <select name="formas_pago" class="form-control" id="formas_pago" onchange="validacion_pago()">
                                <option value="0">Seleccione...</option>
                                @foreach($formas_pago as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                             </select>
                        </div>
                        <div class="form-group" id="para_desabilitar" style="display: none;">
                            <label for="input_banco">Banco</label>
                            <select name="banco" class="form-control" id="banco">
                                @foreach($banco as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group" id="para_caja" style="display: none;">
                            <label class="control-label" for="input_caja">Caja</label>
                            <select class="form-control" name="caja" id="caja">
                            @foreach($caja as $value)
                                <option value="{{$value->id}}">{{$value->nombre}}</option>
                            @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="input-monto" class="control-label">Monto</label>
                            <input type="text" class="form-control" name="monto" id="monto" readonly value="{{$valor}}">
                        </div>
                        <div class="form-group">
                            <label for="input_monto_usar" class="control-label">Monto Usar</label>
                            <input type="text" class="form-control" name="monto_usar" id="monto_usar" onchange="llenar_campo()">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
            <a href="javascript:guardar_anticipos()" class="button">Crear</a>
            </div>
        </form>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/hospital/cleave/dist/cleave.min.js")}}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">

    function validacion_pago(){
        var formas_pago= $("#formas_pago").val();
        var deshabilitar= $("#para_desabilitar").val();
        if(formas_pago!=1){
            $("#para_desabilitar").show('slow');
            $("#para_caja").hide('slow');
        }else{
            $("#para_desabilitar").hide('slow');
            $("#para_caja").show('slow');
            console.log("funciona");
        }
    }
    function guardar_anticipos(){
    //aqui va la funcion en ajax del guardado con el hidden respectivo del input DE ANTICIPOS 
        var proveedor= $("#proveedor").val();
        var fact_numero=$("#no_factura").val();
        var valor_nuevo=$("#monto_usar").val();
        var formas_pago= $("#formas_pago").val();
        var caja= $("#caja").val();
        var valor_ant= $("#monto").val();
        var banco= $("#banco").val();

        if(proveedor!=""&& fact_numero !=""&& valor_nuevo!=""&&valor_ant!=""&&banco!=""){
            $.ajax({
                type: 'post',
                url:"{{route('acreedores_guardar_anticipos')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: {'proveedor':proveedor,'fact_numero': fact_numero,'valor_nuevo': valor_nuevo,'valor_ant':valor_ant,'banco':banco,'formas_pago':formas_pago,'caja':caja},
                success: function(data){
                //console.log(data);
                    if(data=='ok'){
                        swal("Correcto!","Se creo el anticipo correctamente al provedoor n°: "+proveedor,"success");
                        location.href="{{route('acreedores_cegreso')}}";
                    }
                },
                error: function(data){
                    console.log(data);
                }
            })
        }else{
            swal("Error!","Por favor llena todos los campos del formulario","warning");
        }


  }
    function llenar_campo(){
        var llena= parseFloat($("#monto").val());
        var totales= parseFloat($("#monto_usar").val());  
        if(totales>llena){
        swal("Error!","No puedes usar un monto igual o superior a la de la factura","warning");
        $("#monto_usar").val('');
        }else if(totales<0){
        swal("Error!","Ingrese valores correctos en el anticipo","warning");
        $("#monto_usar").val('');
        
        }
    }
</script>