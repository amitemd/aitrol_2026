<style type="text/css">
    .ui-autocomplete {
      z-index:2147483647;
    }
</style>

<link href="{{ asset("/bower_components/AdminLTE/dist/css/AdminLTE.min.css")}}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">



<!-- Ventana modal editar -->
<div class="modal fade" id="bucar_nombre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal-header" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004ac1;"
>
    <h4 class="modal-title" id="myModalLabel">DATOS DEL PACIENTE</h4>
</div>

<div class="modal-body" style="border: 2px solid #004AC1;border-radius: 3px;"> 
    
    <div class="panel-body">
        <div class="alert alert-warning m1 oculto">
            <strong>Atencion!</strong> <span id="alertms"></span>
        </div>
        
        <form method="POST" id="form" >
          <div class="col-12" >  
            <div class="row">         
            {{ csrf_field() }}
            <!--nombre1-->
            <div class="form-group col-md-6 cl_nombre1">
                <label for="nombre1" class="col-md-4 control-label">Primer Nombre</label>
                <div class="col-md-8">
                    <input id="nombre1" type="text" class="form-control"  name="nombre1" value="{{old('nombre1')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="60"  onchange="busca_usuario_nombre();">
                    <span class="help-block">
                        <strong id="str_nombre1"></strong>
                    </span>
                </div>
            </div>
           
            <div class="form-group col-md-6 cl_nombre2 ">
                <label for="nombre2" class="col-md-4 control-label">Segundo Nombre</label>
                <div class="input-group col-md-8">
                  <input id="nombre2" type="text" class="form-control"  name="nombre2" value="{{old('nombre2')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required maxlength="60" onchange="busca_usuario_nombre();">
                  <div class="input-group-append">
                    <button class="btn btn-default" onclick="sin_nombre();" type="button">(N/A)</button> 
                  </div>
                </div>
                <span class="help-block">
                    <strong id="str_nombre2" style="padding-left: 15px;"></strong>
                </span>
            </div>

            <div class="form-group col-md-6 cl_apellido1 ">
                <label for="apellido1" class="col-md-4 control-label">Primer Apellido</label>
                <div class="col-md-8">
                    <input id="apellido1" type="text" class="form-control"  name="apellido1" value="{{old('apellido1')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required maxlength="60" onchange="busca_usuario_nombre();">
                    <span class="help-block">
                        <strong id="str_apellido1"></strong>
                    </span>
                </div>
            </div>

            <div class="form-group col-md-6 cl_apellido2 ">
                <label for="apellido2" class="col-md-4 control-label">Segundo Apellido</label>
                <div class="input-group col-md-8">
                  <input id="apellido2" type="text" class="form-control"  name="apellido2" value="{{old('apellido2')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required maxlength="60" onchange="busca_usuario_nombre();">
                  <div class="input-group-append">
                    <button class="btn btn-default" onclick="sin_apellido();" type="button">(N/A)</button> 
                  </div>
                </div>
                <span class="help-block">
                    <strong id="str_apellido2" style="padding-left: 15px;"></strong>
                </span>
            </div>

            <div class="form-group col-md-6 cl_cedula ">
                <label for="cedula" class="col-md-4 control-label">Cedula</label>
                <div class="col-md-8">
                    <input id="cedula" type="text" maxlength="10" class="form-control"  name="cedula" value="{{old('cedula')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required >
                    <span class="help-block">
                        <strong id="str_cedula"></strong>
                    </span>
                </div>
            </div>

            <div class="form-group col-md-6 cl_email">
                <label for="email" class="col-md-4 control-label">Correo</label>
                <div class="col-md-8">
                    <input id="email" type="email"  class="form-control"  name="email" value="{{old('email')}}" >
                    <span class="help-block">
                        <strong id="str_email"></strong>
                    </span>
                </div>
            </div>
                           
            <input type="hidden" class="form-control" id="id_paciente" name="id_paciente" value="" >


            <div id="cambio15a" class="form-group col-md-6 cl_cortesia">
                <label for="cortesia" class="col-md-4 control-label">Cortesia</label>
                <div class="col-md-7">
                    <select id="cortesia" class="form-control" name="cortesia">
                        <option value="NO" @if(old('cortesia')=="NO"){{"selected"}}@endif>NO</option>
                        <option value="SI" @if(old('cortesia')=="SI"){{"selected"}}@endif>SI</option>        
                    </select>
                </div>
                <span class="help-block">
                        <strong id="str_cortesia"></strong>
                    </span>
            </div>


            <div class="form-group col-md-6 cl_fecha_nacimiento">
                <label for="fecha_nacimiento" class="col-md-4 control-label">Fecha Nacimiento</label>
                <div class="input-group col-md-8">
                    <div class="input-group-prepend" id="dt1">
                      <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                    </div>
                    <input type="text" class="form-control pull-right"  name="fecha_nacimiento" id="fecha_nacimiento" value="{{old('fecha_nacimiento')}}" required autocomplete="off">
                </div>
                <span class="help-block">
                    <strong id="str_fecha_nacimiento" style="padding-left: 15px;"></strong>
                </span>
            </div>  
          </div>
        </div>
      </form>  

      <div class="form-group">
      <center>
            <div class="col-md-12">
                <button class="btn btn-danger" style="color:white; background-color:#004AC1; border-radius: 5px; border: 2px solid white;" onclick="guardar();"><span class="fa fa-save"> Guardar</span></button>
            </div>
        </center>
    </div>  

    </div>
</div>

<div class="modal-footer">
  
</div>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

    function sin_nombre(){

        $('#nombre2').val('(N/A)');

    }
    function sin_apellido(){

        $('#apellido2').val('(N/A)');

    }

    $(function () {

        $('#fecha_nacimiento').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD ',
             //Important! See issue #1075
            
        });

    });   

    $('#dt1').on('click', function(){
            $('#fecha_nacimiento').datetimepicker('show');
    });

    function guardar(){
        
        $('.cl_apellido1').removeClass('has-error');
        $('#str_apellido1').text('');

        $('.cl_nombre1').removeClass('has-error');
        $('#str_nombre1').text('');

        $('.cl_apellido2').removeClass('has-error');
        $('#str_apellido2').text('');

        $('.cl_nombre2').removeClass('has-error');
        $('#str_nombre2').text(''); 
                
        $('.cl_cedula').removeClass('has-error');
        $('#str_cedula').text('');  
                
        $('.cl_email').removeClass('has-error');
                $('#str_email').text('');
                
        $('.cl_fecha_nacimiento').removeClass('has-error');
        $('#str_fecha_nacimiento').text('');                              


        $.ajax({
          type: 'post',
          url:"{{route('hc4_paciente.crear_paciente')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          
          datatype: 'json',
          data: $("#form").serialize(),
          success: function(data){
            //console.log(data);
            //alert(data);
            location.href = '{{url('inicio/buscador')}}/'+data;
          },
          error: function(data){
            console.log(data);
            if(data.responseJSON.apellido1!=null){
                $('.cl_apellido1').addClass('has-error');
                $('#str_apellido1').text(data.responseJSON.apellido1);
            }
            if(data.responseJSON.nombre1!=null){
                $('.cl_nombre1').addClass('has-error');
                $('#str_nombre1').text(data.responseJSON.nombre1);
            }
            if(data.responseJSON.apellido2!=null){
                $('.cl_apellido2').addClass('has-error');
                $('#str_apellido2').text(data.responseJSON.apellido2);
            }
            if(data.responseJSON.nombre2!=null){
                $('.cl_nombre2').addClass('has-error');
                $('#str_nombre2').text(data.responseJSON.nombre2);
            }
            if(data.responseJSON.cedula!=null){
                $('.cl_cedula').addClass('has-error');
                $('#str_cedula').text(data.responseJSON.cedula);
            }
            if(data.responseJSON.email!=null){
                $('.cl_email').addClass('has-error');
                $('#str_email').text(data.responseJSON.email);
            }
            if(data.responseJSON.fecha_nacimiento!=null){
                $('.cl_fecha_nacimiento').addClass('has-error');
                $('#str_fecha_nacimiento').text(data.responseJSON.fecha_nacimiento);
            }
          }
        });
    }

var busca_usuario_nombre = function ()
{
    
    var jnombre1 = document.getElementById('nombre1').value;
    var jnombre2 = document.getElementById('nombre2').value;
    var japellido1 = document.getElementById('apellido1').value;
    var japellido2 = document.getElementById('apellido2').value;

    if(jnombre1!='' && jnombre2!='' && japellido1!='' && japellido2!='' )
    {
        
        $('#alertms').empty().html('');
        $(".m1").addClass("oculto"); 
        $.ajax({
        type: 'get',
        url:'{{ route('busca.pacientexnombre')}}',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        
        datatype: 'json',
        data: {nombre1 : jnombre1, nombre2 : jnombre2, apellido1 : japellido1, apellido2 : japellido2,},
        success: function(data){
            
                if(data!='0'){
                    $('#alertms').empty().html('El Paciente '+jnombre1+' '+jnombre2+' '+japellido1+' '+japellido2+' ya existe con C.I: '+data);
                    $(".m1").removeClass("oculto");    
                }    
                
            },
        
        })
    
    }    
    
}
           


</script>





 


