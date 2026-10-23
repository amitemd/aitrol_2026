@extends('layouts.app-template-h')
@section('content')

<style type="text/css">
    .ui-autocomplete {
      z-index:2147483647;
    }
</style>

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="content">
    <div class="col-md-12">
        <div class="card card-primary">

            <div class="card-header">
                <div class="row">
                    <div class="col-10">
                        <h3 class="card-title">{{trans('hospital.AgregarnuevoPaciente1')}}</h3>
                    </div>
                    <div class="col-md-2 my-2">
                        <button type = "button" class = "btn btn-primary btn-sm" onclick = "location.href= '{{route('hospital.emergencia')}}'">{{trans('hospital.Regresar')}}</button>
                    </div>
                </div>    
            </div>
                
            <div class="card-body">                
                <form method="POST" id="form">
                    <div class="col-md-12">  
                        <div class="row">
                            {{ csrf_field() }}
                            <!--nombre1-->

                            <div class="col-md-3 cl_cedula">
                                <label for="cedula" class="col-md-6 control-label">{{trans('hospital.Cedula')}}</label>
                                
                                    <input id="cedula" type="text" maxlength="10" class="form-control"  name="cedula" value="{{old('cedula')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required >
                                    <span class="help-block">
                                        <strong id="str_cedula"></strong>
                                    </span>
                                
                            </div>

                            <div class="col-md-3 cl_nombre1">
                                <label for="nombre1" class="col-md-6 control-label" >{{trans('hospital.PrimerNombre')}}</label>
                               
                                    <input id="nombre1" type="text" class="form-control"  name="nombre1" value="{{old('nombre1')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" maxlength="60" >
                                    <span class="help-block">
                                        <strong id="str_nombre1"></strong>
                                    </span>
                              
                            </div>
                            <div class="col-md-3 cl_nombre2">
                                <label for="nombre2" class="col-md-6 control-label">{{trans('hospital.SegundoNombre')}}</label>
                                <div class="input-group dropdown">
                                    <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value="{{ old('nombre2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required maxlength="60">
                                    <ul class="dropdown-menu usuario3" id="dnombre22">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                <span class="help-block">
                                    <strong id="str_nombre2" style="padding-left: 15px;"></strong>
                                </span>
                            </div>

                            <div class="col-md-3 cl_apellido1">
                                <label for="apellido1" class="col-md-6 control-label">{{trans('hospital.PrimerApellido')}}</label>
                        
                                    <input id="apellido1" type="text" class="form-control"  name="apellido1" value="{{old('apellido1')}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required maxlength="60">
                                    <span class="help-block">
                                        <strong id="str_apellido1"></strong>
                                    </span>
                       
                            </div>

                            <div class="col-md-3 cl_apellido2">
                                <label for="apellido2" class="col-md-6 control-label">{{trans('hospital.SegundoApellido')}}</label>
                            
                                <div class="input-group dropdown">
                                    <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value="{{ old('apellido2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required maxlength="60">
                                    <ul class="dropdown-menu usuario3" id="dnombre22">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                <span class="help-block">
                                    <strong id="str_apellido2" style="padding-left: 15px;"></strong>
                                </span>

                            </div>

                            

                            <div class="col-md-3 cl_email">
                                <label for="email" class="col-md-4 control-label">{{trans('hospital.Correo')}}</label>
                               
                                    <input id="email" type="email"  class="form-control"  name="email" value="{{old('email')}}" >
                                    <span class="help-block">
                                        <strong id="str_email"></strong>
                                    </span>
                                
                            </div>
                                        
                            <input type="hidden" class="form-control" id="id_paciente" name="id_paciente" value="" >


                            <div id="cambio15a" class="col-md-3">
                                <label for="cortesia" class="col-md-4 control-label">{{trans('hospital.Cortesia')}}</label>
                              
                                    <select id="cortesia" class="form-control" name="cortesia">
                                        <option value="NO" @if(old('cortesia')=="NO"){{"selected"}}@endif>{{trans('hospital.No')}}</option>
                                        <option value="SI" @if(old('cortesia')=="SI"){{"selected"}}@endif>{{trans('hospital.Si')}}</option>        
                                    </select>
                              
                                
                                <span class="help-block">
                                    <strong id="str_cortesia"></strong>
                                </span>
                            </div>

                            <div class="col-md-3 cl_fecha_nacimiento">
                                <label for="fecha_nacimiento" class="col-md-6 control-label">{{trans('hospital.FechaNacimiento')}}</label>
                                <input type="text"  data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_nacimiento" id="fecha_nacimiento" value="{{old('fecha_nacimiento')}}" autocomplete="off">
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
                            <button class="btn btn-primary"  onclick="guardar();"><span class="fa fa-save"> {{trans('hospital.Guardar')}}</span></button>
                        </div>
                    </center>
                </div>
            </div>
            
            <div class="modal-footer">
            
            </div>

        </div>
    </div>
</div>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
//Function para agregar el nuevo paciente
    function cargar_nuevopaciente(){
      $.ajax({
        type: "GET",
        url: "{{route('hospital.registropac')}}",
        data: "",
        datatype: "html",
        success: function(datahtml){
          $("#area_trabajo").html(datahtml);
        },
        error:  function(){
          alert('error al cargar');
        }
      });
    }
    
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

        //alert("funciona");
        
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
            // location.href = '{{url('inicio/buscador')}}/'+data;
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


</script>

@endsection