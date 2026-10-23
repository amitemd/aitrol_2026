<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
    <h4 class="modal-title" id="myModalLabel" style="text-align: center;">EDITAR PRINCIPAL</h4> 
    <div class="form-inline col-md-12{{ $errors->has('id_prin_b') || $errors->has('cita')? ' has-error' : '' }}">
        <label for="id_prin_b" class="col-md-4 control-label">Seleccionar Principal:</label>
        <input id="id_prin_b" maxlength="10" type="text" class="form-control input-sm col-md-6" onchange="buscar_usuario();" name="id_prin_b" value=@if(old('id_prin_b')!='')"{{old('id_prin_b')}}"@else"{{$paciente->id_usuario}}"@endif required autofocus onkeyup="validarCedula(this.value);" >
        <button type="button" class="btn btn-primary btn-sm" onclick="buscar_usuario();" ><span class="glyphicon glyphicon-search"></span> Buscar</button>
    </div> 
</div>
 
<div class="modal-body"> 
    
    <form method="POST" action="#" id="form" >

        {{csrf_field()}}
       
        
        <div class="row">

            
                <b><span class="col-md-12" style="color: red; text-decoration: bold;" id="mensaje"></span></b>

            <input type="hidden" name="id" value="{{$paciente->id}}">   
            <input type="hidden" name="id_usuario" value="{{$paciente->id_usuario}}">  
            <input type="hidden" name="id_buscar" id="id_buscar" value=""> 

            <div class="form-group cl_id_prin col-md-4{{ $errors->has('id_prin') ? ' has-error' : '' }}">
                <label for="id_prin" class="col-md-8 control-label">Cédula</label>
                <input id="id_prin" maxlength="10" type="text" class="form-control input-sm" name="id_prin" value=@if(old('id_prin')!='')"{{old('id_prin')}}"@else"{{$paciente->id_usuario}}"@endif required="required" autofocus >
                <span class="help-block">
                    <strong id="str_id_prin"></strong>
                </span>  
            </div>
                        
            <!--primer nombre-->
            <div class="form-group cl_nombre1_prin col-md-4{{ $errors->has('nombre1_prin') ? ' has-error' : '' }}">
                <label for="nombre1_prin" class="col-md-12 control-label">Primer Nombre</label>
                <input id="nombre1_prin" type="text" class="form-control input-sm" name="nombre1_prin" value=@if(old('nombre1_prin')!='')"{{old('nombre1_prin')}}"@else"{{$user_aso->nombre1}}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus  >
                <span class="help-block">
                    <strong id="str_nombre1_prin"></strong>
                </span>                
            </div>

                                            
            <!--segundo nombre-->
            <div class="form-group cl_nombre2_prin col-md-4{{ $errors->has('nombre2_prin') ? ' has-error' : '' }}">
                <label for="nombre2_prin" class="col-md-12 control-label">Segundo Nombre</label>
                <div class="input-group dropdown col-md-12">
                    <input id="nombre2_prin" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2_prin" value=@if(old('nombre2_prin')!='')"{{old('nombre2_prin')}}"@else"{{ $user_aso->nombre2 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
                    <ul class="dropdown-menu usuario1">
                        <li><a data-value="N/A">N/A</a></li>
                    </ul>
                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                </div>     
                <span class="help-block">
                    <strong id="str_nombre2_prin"></strong>
                </span>  
            </div>
                        
            <!--primer apellido-->
            <div class="form-group cl_apellido1_prin col-md-4{{ $errors->has('apellido1_prin') ? ' has-error' : '' }}">
                <label for="apellido1_prin" class="col-md-12 control-label">Primer Apellido</label>
                <input id="apellido1_prin" type="text" class="form-control input-sm" name="apellido1_prin" value=@if(old('apellido1_prin')!='')"{{old('apellido1_prin')}}"@else"{{ $user_aso->apellido1 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus >
               <span class="help-block">
                    <strong id="str_apellido1_prin"></strong>
                </span>  
            </div>
                        
            <!--segundo apellido-->
            <div class="form-group cl_apellido2_prin col-md-4{{ $errors->has('apellido2_prin') ? ' has-error' : '' }}">
                <label for="apellido2_prin" class="col-md-12 control-label">Segundo Apellido</label>
                <div class="input-group dropdown col-md-12">
                    <input id="apellido2_prin" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2_prin" value=@if(old('apellido2_prin')!='')"{{old('apellido2_prin')}}"@else"{{ $user_aso->apellido2 }}" @endif style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus  >
                    <ul class="dropdown-menu usuario2">
                        <li><a data-value="N/A">N/A</a></li>
                    </ul>
                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                </div>    
                <span class="help-block">
                    <strong id="str_apellido2_prin"></strong>
                </span>  
            </div>

            <!--fecha_nacimiento-->
            <div class="form-group cl_fecha_nacimiento_prin col-md-4{{ $errors->has('fecha_nacimiento_prin') ? ' has-error' : '' }}">
                <label for="fecha_nacimiento_prin" class="col-md-12 control-label">Fecha Nacimiento</label>
                <div class="input-group date col-md-12">
                    <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input id="fecha_nacimiento_prin" onchange="edad2_prin();" type="text" class="form-control input-sm" name="fecha_nacimiento_prin" value=@if(old('fecha_nacimiento_prin')!='')"{{old('fecha_nacimiento_prin')}}"@else"{{ $user_aso->fecha_nacimiento }}" @endif required autofocus >
                </div>    
                <span class="help-block">
                    <strong id="str_fecha_nacimiento_prin"></strong>
                </span>  
            </div>
 
            <!-- Div de edad -->
            <div class="form-group cl_Xedad_prin col-md-2{{ $errors->has('Xedad_prin') ? ' has-error' : '' }}">
                <label for="Xedadp" class="col-md-8 control-label">Edad</label>
                <input id="Xedadp" type="text" class="form-control input-sm" name="Xedad_prin" readonly="readonly">
                <span class="help-block">
                    <strong id="str_Xedad_prin"></strong>
                </span>
            </div>
                        
            <!--menor edad-->
            <div class="form-group cl_menoredad_prin col-md-2{{ $errors->has('menoredad_prin') ? ' has-error' : '' }}">
                <label for="menoredadp" class="col-md-12 control-label">M.Edad</label>
                <input id="tmenoredadp" type="text" class="form-control input-sm" name="tmenoredad_prin" value="" required autofocus readonly="readonly">
                <input id="menoredadp" type="hidden" class="form-control input-sm" name="menoredad_prin" value="" >
                <span class="help-block">
                    <strong id="str_menoredad_prin"></strong>
                </span>  
            </div>

            <!--email-->                        
            <div class="form-group cl_email col-md-8{{ $errors->has('email') ? ' has-error' : '' }}">
                <label for="email" class="col-md-8 control-label">E-Mail</label>
                <input id="email" type="email" class="form-control input-sm" name="email" value=@if(old('email')!='')"{{old('email')}}"@else"{{ $user_aso->email }}" @endif required autofocus  >
               <span class="help-block">
                    <strong id="str_email"></strong>
                </span>                    
            </div>  
        

        </div>    
        
         
            
            <!--Botón para abrir la ventana modal de editar -->
            <a href="#" id="confirme" class="btn btn-primary col-md-offset-10"><span class="glyphicon glyphicon-floppy-disk"></span> Actualizar</a>
         
    </div>
  </form>

<div class="modal-footer">
  <button type="button" id="bcerrar" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script type="text/javascript">
$(document).ready(function() 
{
        edad2_prin();
        buscar_usuario();

        $('.usuario1 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');

        });

        $('.usuario2 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');

        });

        $('#fecha_nacimiento_prin').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });
        

});



function buscar_usuario(){

    var jsusuario = document.getElementById('id_prin_b').value;
    

    $.ajax({
        type: 'get',
        url:'{{url('buscar_usuario')}}/'+jsusuario,
       
        success: function(data){
            
            if(data!="null"){
                $('#id_prin').val(data.id);
                $('#id_buscar').val(data.id);
                $('#nombre1_prin').val(data.nombre1);
                $('#nombre2_prin').val(data.nombre2);
                $('#apellido1_prin').val(data.apellido1);
                $('#apellido2_prin').val(data.apellido2);
                $('#fecha_nacimiento_prin').val(data.fecha_nacimiento);
                $('#email').val(data.email);
                $('#mensaje').text("");
                edad2_prin(); 
                  
            }else{
                $('#id_prin').val("");
                $('#nombre1_prin').val("");
                $('#nombre2_prin').val("");
                $('#apellido1_prin').val("");
                $('#apellido2_prin').val("");
                $('#fecha_nacimiento_prin').val("");
                $('#email').val("");
                $("#Xedadp").val("");
                $("#tmenoredadp").val("");
                $('#id_buscar').val("");
                 
                $('#mensaje').text("Usuario no existe !!!");
            }
            
        },
        
    })


    
}

function edad2_prin()
{
    
    var jsnacimiento = document.getElementById("fecha_nacimiento_prin").value;
    var jsedad = calcularEdad(jsnacimiento);
    
    if(isNaN(jsedad))
    {
        $("#Xedadp").val('0');
    }
    else
    {

        $("#Xedadp").val(jsedad);
    }


    if (jsedad>=18){

       $("#tmenoredadp").val("NO");
       $("#menoredadp").val("0");
    }
    else{
        $("#tmenoredadp").val("SI");
        $("#menoredadp").val("1");
    }
       
          
}
$('#confirme').click(function(event){

    

    $.ajax({
        type: 'get',
        url:'{{route('admisiones.actualiza_pr')}}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: $("#form").serialize(),
        success: function(data){
            console.log(data);
            alert('Principal actualizado');
            busca_principal(); 
            $('#bcerrar').click();
            
        },
        error: function(data){
           
            $('#str_id_prin').empty().html('');
            $('#str_nombre1_prin').empty().html('');
            $('#str_nombre2_prin').empty().html('');
            $('#str_apellido1_prin').empty().html('');
            $('#str_apellido2_prin').empty().html('');
            $('#str_fecha_nacimiento_prin').empty().html('');
            $('#str_Xedad_prin').empty().html('');
            $('#str_menoredad_prin').empty().html('');
            $('#str_email').empty().html('');
            $(".cl_id_prin").removeClass("has-error");
            $(".cl_nombre1_prin").removeClass("has-error");
            $('.cl_nombre2_prin').removeClass("has-error");
            $('.cl_apellido1_prin').removeClass("has-error");
            $('.cl_apellido2_prin').removeClass("has-error");
            $('.cl_fecha_nacimiento_prin').removeClass("has-error");
            $('.cl_fecha_nacimiento_prin').removeClass("has-error");
            $('.cl_Xedad_prin').removeClass("has-error");
            $('.cl_menoredad_prin').removeClass("has-error");
            $('.cl_email').removeClass("has-error");

            if(data.responseJSON.id_prin!=null){
                $(".cl_id_prin").addClass("has-error");
                $('#str_id_prin').empty().html(data.responseJSON.id_prin);
            }
            if(data.responseJSON.nombre1_prin!=null){
                $(".cl_nombre1_prin").addClass("has-error");
                $('#str_nombre1_prin').empty().html(data.responseJSON.nombre1_prin);
            }
            if(data.responseJSON.nombre2_prin!=null){
                $(".cl_nombre2_prin").addClass("has-error");
                $('#str_nombre2_prin').empty().html(data.responseJSON.nombre2_prin);
            }
            if(data.responseJSON.apellido1_prin!=null){
                $(".cl_apellido1_prin").addClass("has-error");
                $('#str_apellido1_prin').empty().html(data.responseJSON.apellido1_prin);
            }
            if(data.responseJSON.apellido2_prin!=null){
                $(".cl_apellido2_prin").addClass("has-error");
                $('#str_apellido2_prin').empty().html(data.responseJSON.apellido2_prin);
            }
             if(data.responseJSON.fecha_nacimiento_prin!=null){
                $(".cl_fecha_nacimiento_prin").addClass("has-error");
                $('#str_fecha_nacimiento_prin').empty().html(data.responseJSON.fecha_nacimiento_prin);
            }
            if(data.responseJSON.Xedad_prin!=null){
                $(".cl_Xedad_prin").addClass("has-error");
                $('#str_Xedad_prin').empty().html(data.responseJSON.Xedad_prin);
            }
            if(data.responseJSON.menoredad_prin!=null){
                $(".cl_menoredad_prin").addClass("has-error");
                $('#str_menoredad_prin').empty().html(data.responseJSON.menoredad_prin);
            }
             if(data.responseJSON.email!=null){
                $(".cl_email").addClass("has-error");
                $('#str_email').empty().html(data.responseJSON.email);
            }
            
        }
    })

});
    

</script>        