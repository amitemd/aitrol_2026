<style type="text/css">
    label{
        font-size: 12px;
    }
    .alerta_correcto{
      position: fixed;
      z-index: 9999;
      bottom:  58%;
      left: 41%;
      
    }

</style>
<div class="alert alert-danger alerta_correcto alert-dismissable col-6" role="alert" style="display: none;font-size: 14px;">
    <b>Complete los datos: <span id="err"></span></b>
</div>
<form name="hostingplan" id="guardar_externo" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="promo" value="{{$id}}">  
    <li class="p-name2"><span class="pl-title">Ingreso del Paciente&nbsp;<b></b></span></li>
    
    <li class="p-feat1">
        <!--cedula-->
        <div class="form-group col-md-4">
            <label for="id" class="control-label">Cédula</label>
            <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value="{{ old('id') }}" required autofocus autocomplete="off" onchange="buscapaciente();" >
        </div>
        <!--primer nombre-->
        <div class="form-group col-md-4">
            <label for="nombre1" class="control-label">Primer Nombre</label>
            
            <input id="nombre1" class="form-control input-sm" maxlength="40" type="text" name="nombre1" value="{{ old('nombre1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" readonly>
        </div>
        <!--//segundo nombre-->
        <div class="form-group col-md-4">
            <label for="nombre2" class="control-label">Segundo Nombre</label>
            <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" maxlength="40" name="nombre2" value="{{ old('nombre2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="" readonly>
        </div>
        
    </li>  
    <li class="p-feat1">  
        <!--primer apellido-->
        <div class="form-group col-md-4">
            <label for="apellido1" class="control-label">Primer Apellido</label>
            
            <input id="apellido1" type="text" class="form-control input-sm" maxlength="40" name="apellido1" value="{{ old('apellido1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" readonly>
            
        </div>
                     
        <!--Segundo apellido-->
        <div class="form-group col-md-4">
            <label for="apellido2" class="control-label">Segundo Apellido</label>
            <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" maxlength="40" name="apellido2" value="{{ old('apellido2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="" readonly>
        </div>

        <!--sexo 1=MASCULINO 2=FEMENINO-->
        <div class="form-group col-md-4{{ $errors->has('sexo') ? ' has-error' : '' }}">
            <label for="sexo" class="control-label">Sexo</label>
            <select id="sexo" name="sexo" class="form-control input-sm" required onchange="" readonly>
                <option value="">Seleccionar ..</option>
                <option value="1">MASCULINO</option>
                <option value="2">FEMENINO</option>
                        
            </select>       
        </div>  

    </li>  

    <li class="p-feat1">
        <!--fecha_nacimiento-->
        <div class="form-group col-md-4">
            <label class="control-label">Fecha Nacimiento</label>
            <input type="date" value="{{old('fecha_nacimiento')}}" name="fecha_nacimiento" class="form-control pull-right input-sm" id="fecha_nacimiento" required onchange="" readonly>
        </div>
        <!--Celular-->
        <div class="form-group col-md-4">
            <label for="celular" class="control-label">Celular</label>
            <input id="celular" type="text" class="form-control input-sm" maxlength="10" name="celular" value="{{ old('celular') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" readonly>
            
        </div>
        <!--Direccion-->
        <div class="form-group col-md-4">
            <label for="direccion" class="control-label">Dirección</label>
            
            <input id="direccion" type="text" class="form-control input-sm" name="direccion" maxlength="70" value="{{ old('direccion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" readonly>
            
        </div>

    </li> 

    <li class="p-feat1">
        
        <!--email-->
        <div class="form-group col-md-8">
            <label for="email" class="control-label">E-mail</label>
            
            <input id="email" type="text" class="form-control input-sm" name="email" maxlength="70" value="{{ old('email') }}" required autofocus onchange="" readonly>
            @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
            @endif
            
        </div>
        <!--email-->
        <div class="form-group col-md-4">
            <label for="forma_pago" class="control-label">Forma de Pago</label>
            <select id="forma_pago" name="forma_pago" class="form-control input-sm" required onchange="" >
                <option value="1">EFECTIVO/CHEQUE</option>
                <option value="2">TARJETA DEBITO</option> 
                <option value="2">TARJETA CREDITO</option>      
            </select>   
        </div>

        

    </li>  

    
    <li class="p-feat1"><br></li> 
    <li class="p-feat1"><br></li>     
    <li class="p-feat"><a class="txt-button" onclick="ingreso_orden();return false;">Ingresar</a></li> 

                        

</form>
<script type="text/javascript">
    var buscapaciente = function ()
    {
    
        var js_paciente = document.getElementById('id').value;
        
        $.ajax({
            type: 'get',
            url: "{{ url('laboratorio/externo/web/promo/buscarpaciente')}}/"+js_paciente, //hospitalizados.buscapaciente
                       
            success: function(data){
                if(data=='no'){
                    
                    $('#nombre1').removeAttr("readonly");
                    $('#nombre2').removeAttr("readonly");
                    $('#apellido1').removeAttr("readonly");
                    $('#apellido2').removeAttr("readonly");
                    $('#sexo').removeAttr("readonly");
                    $('#fecha_nacimiento').removeAttr("readonly");
                    $('#celular').removeAttr("readonly");
                    $('#direccion').removeAttr("readonly");
                    $('#email').removeAttr("readonly");
                    
                    
                 
                }else{
                    //alert('Paciente ya ingresado en el sistema');
                    //console.log(data);
                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#sexo').val(data.sexo);
                    $('#fecha_nacimiento').val(data.fecha_nacimiento);
                    $('#celular').val(data.telefono1);
                    $('#direccion').val(data.direccion);
                    $('#email').val(data.email);
                    //$('#procedencia').focus();
                }
            }    
        });  
    
    }

    function ingreso_orden(){
        var cedula = $('#id').val();
        var nombre1 = $('#nombre1').val();
        var apellido1 = $('#apellido1').val();
        var sexo = $('#sexo').val();
        var fecha_nacimiento = $('#fecha_nacimiento').val();
        var celular = $('#celular').val();
        var direccion = $('#direccion').val();
        var email = $('#email').val();
        var texto = '';
        if(cedula!=null){
            texto = texto + " cedula";
        }
        if(nombre1!=null){
            texto = texto + " nombre";
        }
        if(apellido1!=null){
            texto = texto + " apellido";
        }
        if(sexo!=null){
            texto = texto + " sexo";
        }
        if(fecha_nacimiento!=null){
            texto = texto + " fecha de nacimiento";
        }
        if(celular!=null){
            texto = texto + " celular";
        }
        if(direccion!=null){
            texto = texto + " direccion";
        }
        if(email!=null){
            texto = texto + " email";
        }
        if(texto != ''){
            $("#err").text(texto);
            $(".alerta_correcto").fadeIn(1000);
            $(".alerta_correcto").fadeOut(10000);    
        }
        if (texto=='') {
            $.ajax({
                type: "POST",
                url: "{{route('lab_externo.guardar')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: "html",
                data: $("#guardar_externo").serialize(),
                success: function(datahtml){
                  

                },
                error: function(){

                  //console.log(data);

                }
            });

        }


    }
</script>
