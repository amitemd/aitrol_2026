



<div class="modal-content" style="width: 100%;">
    <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px; background-color: #3d7ba8;">
        <div class="col-md-5">
             <!--<h4 class="modal-title size_text">INGRESO DE RETENCION CLIENTE</h4>-->
             <label style="color: white">INGRESO DE RETENCION CLIENTE</label>
        </div>
        <div class="col-md-7">
            <button type="button" id="cerrar" onclick="cerrar()" class="close" data-dismiss="modal">&times;</button>
        </div>
    </div>
    <div class="col-md-12" style="padding-top: 5px"></div>
    <div class="modal-body size_text">
        <form action="" id="form_retencion" method="post">
            <div class="col-md-12 size_text" style="background-color: #3d7ba8;">
                <label style="color: white">DATOS DE LA FACTURA</label>
            </div> 
            <div class="col-md-12">
                <div class="form-row">
                    <div class="col-md-12" style="padding-top: 5px"></div>
                    @if(!is_null($cliente->nombre))
                        <div class="col-md-10 col-xs-6">
                            <label for="nomb_cliente" class="col-md-2 control-label">Cliente:</label>
                            <div class="input-group col-md-9">
                                {{$cliente->nombre}}
                            </div>
                        </div>
                    @endif
                    @if(!is_null($cliente->identificacion))
                        <div class="col-md-10 col-xs-6">
                            <label for="ident_cliente" class="col-md-2 control-label">RUC/CI</label>
                            <div class="input-group col-md-9">
                                {{$cliente->identificacion}}
                            </div>
                        </div>
                    @endif
                    @if(!is_null($cliente->direccion_representante))
                        <div class="col-md-10 col-xs-6">
                            <label for="dir_cliente" class="col-md-2 control-label">Dirección:</label>
                            <div class="input-group col-md-9">
                                 {{$cliente->direccion_representante}}
                            </div>
                        </div>
                    @endif
                    <div class="col-md-10 col-xs-6">
                        <label for="tip_comprobante" class="col-md-2 control-label">Tipo:</label>
                        <div class="input-group col-md-9">
                            Factura Venta
                        </div>
                    </div>
                    @if(!is_null($ct_vent->nro_comprobante))
                        <div class="col-md-10 col-xs-6">
                            <label for="num_comprobante" class="col-md-2 control-label">No. Comprobante:</label>
                            <div class="input-group col-md-9">
                                {{$ct_vent->nro_comprobante}}
                            </div>
                        </div>
                    @endif
                    @if(!is_null($ct_vent->fecha))
                        <div class="col-md-10 col-xs-6">
                            <label for="fech_fact" class="col-md-2 control-label">F. Emision:</label>
                            <div class="input-group col-md-9">
                                {{$ct_vent->fecha}}
                            </div>
                        </div>
                    @endif
                    @if(!is_null($ct_vent->base_imponible))
                        <div class="col-md-10 col-xs-6">
                            <label for="ba_imponib" class="col-md-2 control-label">Base Imp:</label>
                            <div class="input-group col-md-9">
                                {{$ct_vent->base_imponible}}
                            </div>
                        </div>
                    @endif
                    @if(!is_null($ct_vent->impuesto))
                        <div class="col-md-10 col-xs-6">
                            <label for="impuest" class="col-md-2 control-label">Iva:</label>
                            <div class="input-group col-md-9">
                                {{$ct_vent->impuesto}}
                            </div>
                        </div>
                    @endif
                    @if(!is_null($ct_vent->total_final))
                        <div class="col-md-10 col-xs-6">
                            <label for="tot" class="col-md-2 control-label">Total:</label>
                            <div class="input-group col-md-9">
                                {{$ct_vent->total_final}}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-md-12" style="padding-top: 5px"></div>
            <div class="col-md-12 size_text" style="background-color: #3d7ba8;">
                <label style="color: white">DATOS DE LA RETENCIÓN</label>
            </div>
            <div class="col-md-12">
                <div class="col-md-3 col-xs-3" style="padding-left: 2px;">
                    <label for="secuencia" class="control-label" class="control-label">Secuencial:</label>
                    <div class="input-group">
                        <input id="secuencia"  name="secuencia" type="text" class="form-control" value="">
                    </div>
                </div>
                <div class="col-md-3 col-xs-3" style="padding-left: 2px;">
                    <label for="num_retencion" class="control-label" class="control-label">N#. Retención:</label>
                    <div class="input-group">
                        <input id="num_retencion"  name="num_retencion" type="text" class="form-control" value="">
                    </div>
                </div>
                <div class="col-md-3 col-xs-3" style="padding-left: 2px;">
                    <label for="num_autorizacion" class="control-label" class="control-label">N#: Autorización</label>
                    <div class="input-group">
                        <input id="num_autorizacion"  name="num_autorizacion" type="text" class="form-control" value="">
                    </div>
                </div>
                
            </div>
            <div class="col-md-12">
                <div class="col-md-3 col-xs-3" style="padding-left: 2px;">
                    <label class="control-label" class="control-label">TIPO RETENCIÓN</label>
                    <select id="tip_retencion" name="tip_retencion" onchange="obtener_porcent_retencion()" class="form-control input-sm" required>
                        <option value="">Seleccione...</option>
                        <option value="1">IVA</option>
                        <option value="2">RENTA</option>
                    </select>
                </div>
                <div class="col-md-6 col-xs-3" style="padding-left: 2px;">
                    <label class="control-label" class="control-label">PORCENTAJE RETENCIÓN</label>
                    <select id="porcent_retencion" name="porcent_retencion" onchange="obtener_codigo()" class="form-control input-sm" required>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3 col-xs-3" style="padding-left: 2px;">
                    <label for="cod_porc" class="control-label" class="control-label">CODIGO % (OPCIONAL)</label>
                    <div class="input-group">
                        <input id="cod_porc"  name="cod_porc" type="text" class="form-control" value="">
                    </div>
                </div>
                <div class="col-md-3 col-xs-3" style="padding-left: 2px;">
                    <label for="base_ret" class="control-label" class="control-label">BASE RET</label>
                    <div class="input-group">
                        <input id="base_ret"  name="base_ret" type="text" class="form-control" value="">
                    </div>
                </div>
                <div class="col-md-3 col-xs-3" style="padding-left: 2px;">
                    <label for="monto_ret" class="control-label" class="control-label">MONTO RETENIDO</label>
                    <div class="input-group">
                        <input id="monto_ret"  name="monto_ret" type="text" class="form-control" value="">
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="modal-footer">
             <button type="button" class="btn btn-primary" style="margin-top: 40px;" id="guardar" data-dismiss="modal">GUARDAR</button>
    </div>
</div>
<script type="text/javascript">
        
    function cerrar(){
        
        if (confirm('¿Desea salir sin guardar las retenciones?')){
            location.href ="{{route('venta_index')}}";
        }
    
    }

   
    /*************************************
    *********OBTENER % RETENCION**********
    /*************************************/
    function obtener_porcent_retencion(){

        var id_porcent = $("#tip_retencion").val();

        $.ajax({
            type: 'post',
            url:"{{route('iva_fuente.porcentaje')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'id_por': id_porcent},
            success: function(data){
              

                if(data.value!='no'){
                    if(id_porcent != 0){
                        $("#porcent_retencion").empty();
                        
                        $.each(data,function(key, registro) {
                            $("#porcent_retencion").append('<option value='+registro.id+'>'+registro.nombre+'</option>');
                            
                        }); 
                    }else{
                        $("#porcent_retencion").empty();
                       
                    }
 
                }
            },
            error: function(data){
                console.log(data);
            }
        })

    }

    /*************************************
    ********OBTENER CODIGO RETENIDO*******
    /*************************************/
    function obtener_codigo(){

        var cod_ret = $("#porcent_retencion").val();

        $.ajax({
            type: 'post',
            url:"{{route('codigo.porcentaje')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: {'codig_ret': cod_ret},
            success: function(data){

                //console.log(data);
              
                if(data.value!='no'){
                    if(cod_ret != 0){

                       $('#cod_porc').val(data);
                        
                    }
 
                }
            },
            error: function(data){
                console.log(data);
            }
        })

    }




   
</script>