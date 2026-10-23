<div class="box box-warning box-solid">
    <div class="header box-header with-border" >
        <div class="box-title col-md-12" ><b style="font-size: 16px;">CREAR FACTURA</b></div>
    </div>
    
	<div class="box-body">

        <form class="form-vertical" id="crear_form">
        
            <input type="hidden" name="_token" value="{{ csrf_token() }}"> 

            <input type="hidden" name="empresa" value="{{$empresa->id}}"> 

            <div class="alert alert-danger oculto">
                <ul id="ms_error"></ul>
            </div>

            <div class="form-group col-md-8 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="factura" class="col-md-12 col-xs-12 control-label">Factura</label>
                <div class="col-md-3 col-xs-3" style="padding-left: 1px;padding-right: 1px;">
                    <input class="form-control input-sm" type="number" name="suc" id="suc" value="" placeholder="001" maxlength="3" >
                </div>
              
                <div class="col-md-3 col-xs-3" style="padding-left: 1px;padding-right: 1px;">
                    <input class="form-control input-sm" type="number" name="caj" id="caj" value="" placeholder="001" maxlength="3">
                </div>
              
                <div class="col-md-6 col-xs-6 input-group" style="padding-left: 1px;padding-right: 1px;">
                    <input class="form-control input-sm" type="number" name="factura" id="factura" value="" placeholder="17439" onchange="buscar();" maxlength="10">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('suc').value = '';document.getElementById('caj').value = '';document.getElementById('factura').value = '';"></i>
                    </div>
                </div>  
              
            </div>
            <div class="form-group col-md-12 col-xs-12"></div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="cedula" class="control-label">Cédula</label>
             
                <div class="input-group">
                    <input value="" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Cédula" onchange="buscar();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('cedula').value = ''; buscar();"></i>
                    </div>  
                </div>
              
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="nombres" class="control-label">Paciente</label>
              
                <div class="input-group">
                    <input value="" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Apellidos y Nombres" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="buscar();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('nombres').value = '';buscar();"></i>
                    </div>
                </div>  
              
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="id_seguro" class="control-label">Seguro</label>
              
                <select class="form-control input-sm" name="id_seguro" id="id_seguro" onchange="buscar();">
                    <option value="">Seleccione ...</option>
                    @foreach($seguros as $seguro)
                        <option value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                    @endforeach  
                </select>
              
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="cedula" class="control-label">Ruc/Cédula</label>
             
                <div class="input-group">
                    <input value="" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Cédula" onchange="buscar();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('cedula').value = ''; buscar();"></i>
                    </div>  
                </div>
              
            </div>

            <div class="form-group col-md-8 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="razon_social" class="control-label">Cliente</label>
              
                <div class="input-group">
                    <input value="" type="text" class="form-control input-sm" name="razon_social" id="razon_social" placeholder="Razon Social" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" onchange="buscar();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('razon_social').value = '';buscar();"></i>
                    </div>
                </div>  
              
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="ciudad" class="control-label">Ciudad</label>
             
                <div class="input-group">
                    <input value="" type="text" class="form-control input-sm" name="ciudad" id="ciudad" placeholder="Cédula" onchange="buscar();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('ciudad').value = ''; buscar();"></i>
                    </div>  
                </div>
              
            </div>

            <div class="form-group col-md-8 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="direccion" class="control-label">Dirección</label>
             
                <div class="input-group">
                    <input value="" type="text" class="form-control input-sm" name="direccion" id="direccion" placeholder="Cédula" onchange="buscar();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('direccion').value = ''; buscar();"></i>
                    </div>  
                </div>
              
            </div>

            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="telefono1" class="control-label">Teléfono</label>
             
                <div class="input-group">
                    <input value="" type="text" class="form-control input-sm" name="telefono1" id="telefono1" placeholder="Cédula" onchange="buscar();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('telefono1').value = ''; buscar();"></i>
                    </div>  
                </div>
              
            </div>

            <div class="form-group col-md-8 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
                <label for="email" class="control-label">Mail</label>
             
                <div class="input-group">
                    <input value="" type="text" class="form-control input-sm" name="email" id="email" placeholder="Cédula" onchange="buscar();">
                    <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
                        <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('email').value = ''; buscar();"></i>
                    </div>  
                </div>
              
            </div>

        </form>

        <div class="form-group col-xs-6">
            <div class="col-md-6 col-md-offset-4">
                <button class="btn btn-primary" onclick="crear_factura()">
                Crear Factura
                </button>
            </div>
        </div>

    </div>        

                    
</div>

<script type="text/javascript">
    
    $(function () {



    });    
    

    function crear_factura(){
        
        $('#suc').parent().removeClass('has-error');
        $('#caj').parent().removeClass('has-error');
        $('#factura').parent().removeClass('has-error');
        $('#ms_error').parent().hide();
        $('#ms_error').empty().html('');


        $.ajax({
            type: 'post',
            url:"{{route('factura.store')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#crear_form").serialize(),
            success: function(data){
                alert(data);    
            },
            error: function(data){
                console.log(data);
                    if(data.responseJSON!=null){
                        if(data.responseJSON.suc!=null){
                            $('#suc').parent().addClass('has-error');
                            $('#ms_error').parent().show();
                            $('#ms_error').append('<li>'+data.responseJSON.suc[0]+'</li>');
                        }
                        if(data.responseJSON.caj!=null){
                            $('#caj').parent().addClass('has-error');
                            $('#ms_error').parent().show();
                            $('#ms_error').append('<li>'+data.responseJSON.caj[0]+'</li>');
                        }
                        if(data.responseJSON.factura!=null){
                            $('#factura').parent().addClass('has-error');
                            $('#ms_error').parent().show();
                            $('#ms_error').append('<li>'+data.responseJSON.factura[0]+'</li>');
                        }
                    }    
                }
        })
    }

</script>