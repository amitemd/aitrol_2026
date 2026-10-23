@extends('hospital_iess.hospitalizados.base')

@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Agregar Hospitalizado</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('hospitalizados.store') }}">
                        {{ csrf_field() }}
                    
                        <!--cedula-->
                        <div class="form-group col-md-6{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-md-4 control-label">Cédula</label>
                            <div class="col-md-7">
                                <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value="{{ old('id') }}" required autofocus onkeyup="validarCedula(this.value);" onchange="buscapaciente();">
                                @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--primer nombre-->
                        <div class="form-group col-md-6{{ $errors->has('nombre1') ? ' has-error' : '' }}">
                            <label for="nombre1" class="col-md-4 control-label">Primer Nombre</label>
                            <div class="col-md-7">
                                <input id="nombre1" class="form-control input-sm" type="text" name="nombre1" value="{{ old('nombre1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
               
                        <!--//segundo nombre-->
                        <div class="form-group col-md-6 {{ $errors->has('nombre2') ? ' has-error' : '' }}">
                            <label for="nombre2" class="col-md-4 control-label">Segundo Nombre</label>
                            <div class="col-md-7">
                                <div class="input-group dropdown">
                                    <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="nombre2" value="{{ old('nombre2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required >
                                    <ul class="dropdown-menu usuario1">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('nombre2'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('nombre2') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
           
                        <!--primer apellido-->
                        <div class="form-group col-md-6{{ $errors->has('apellido1') ? ' has-error' : '' }}">
                            <label for="apellido1" class="col-md-4 control-label">Primer Apellido</label>
                            <div class="col-md-7">
                                <input id="apellido1" type="text" class="form-control input-sm" name="apellido1" value="{{ old('apellido1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('apellido1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('apellido1') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                 
                        <!--Segundo apellido-->
                        <div class="form-group col-md-6 {{ $errors->has('apellido2') ? ' has-error' : '' }}">
                            <label for="apellido2" class="col-md-4 control-label">Segundo Apellido</label>
                            <div class="col-md-7">
                                <div class="input-group dropdown">
                                    <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" name="apellido2" value="{{ old('apellido2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required >
                                    <ul class="dropdown-menu usuario2">
                                        <li><a data-value="N/A">N/A</a></li>
                                    </ul>
                                    <span role="button" class="input-group-addon dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></span>
                                </div>
                                    @if ($errors->has('apellido2'))
                                    <span class="help-block">
                                     <strong>{{ $errors->first('apellido2') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>
                       
                        
                    
                        <!--procedencia-->
                        <div class="form-group col-md-6{{ $errors->has('procedencia') ? ' has-error' : '' }}">
                            <label for="procedencia" class="col-md-4 control-label">Ubicación</label>

                            <div class="col-md-7">
                                <input id="procedencia" type="text" class="form-control input-sm" name="procedencia" value="{{ old('procedencia') }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                                @if ($errors->has('procedencia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('procedencia') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--Direccion-->
                        <div class="form-group col-md-6{{ $errors->has('sala_hospital') ? ' has-error' : '' }}">
                            <label for="sala_hospital" class="col-md-4 control-label">Sala</label>

                            <div class="col-md-7">
                                <input id="sala_hospital" type="text" class="form-control input-sm" name="sala_hospital" value="{{ old('sala_hospital') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                                @if ($errors->has('sala_hospital'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('sala_hospital') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--id_tipo_seguro-->
                        <div class="form-group col-md-6{{ $errors->has('id_seguro') ? ' has-error' : '' }}">
                            <label for="id_seguro" class="col-md-4 control-label">Tipo Seguro</label>
                            <div class="col-md-7">
                                <select id="id_tipo_seguro" name="id_seguro" class="form-control input-sm" required>
                                        <option value="">Seleccione ..</option>
                                    @foreach($seguros as $seguro) 
                                        <option @if(old('id_seguro')== $seguro->id) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>   
                                    @endforeach
                                </select>  
                                
                                @if ($errors->has('id_seguro'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_seguro') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-md-6 {{ $errors->has('id_doctor1') ? ' has-error' : '' }}">
                                <label for="id_doctor1" class="col-md-4 control-label">Doctor</label>
                                <div class="col-md-7"> 
                                    <select id="id_doctor1" name="id_doctor1" class="form-control input-sm" >
                                        <option value="">Seleccione ...</option>
                                    @foreach ($usuarios as $usuario)
                                        <option @if(old('id_doctor1') == $usuario->id) selected @endif value="{{$usuario->id}}">{{$usuario->nombre1}} {{$usuario->nombre2}} {{$usuario->apellido1}} {{$usuario->apellido2}}</option>
                                    @endforeach
                                    </select>
                                    @if ($errors->has('id_doctor1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_doctor1') }}</strong>
                                    </span>
                                    @endif 
                                </div>
                            </div>

                        <div id="cambio17" class="form-group col-md-6 {{ $errors->has('fechaini') ? ' has-error' : '' }}" >
                            <label class="col-md-4 control-label">Ingreso</label>
                            <div class="col-md-7">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ old('fechaini') }}" name="fechaini" class="form-control input-sm" id="fechaini"  placeholder="AAAA/MM/DD" required >
                                </div>
                                   @if ($errors->has('fechaini'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('fechaini') }}</strong>
                                    </span>
                                    @endif
                            </div>
                        </div>    


                        <div class="form-group col-md-6{{ $errors->has('observaciones') ? ' has-error' : '' }}">
                            <label for="observaciones" class="col-md-4 control-label">Observaciones</label>

                            <div class="col-md-7">
                                <input id="observaciones" type="text" class="form-control input-sm" name="observaciones" value="{{ old('observaciones') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >

                                @if ($errors->has('observaciones'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('observaciones') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        

                    

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Agregar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            
            </div>
        </div>
    </div>
    
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

    $(document).ready(function() {
        $("#id_tipo_usuario").change(function () {
            
            //var valor = 0;
            var estado = document.getElementById("id_tipo_usuario").value;
            
             
        });

        $('#fecha_nacimiento').datetimepicker({
            format: 'YYYY/MM/DD'


            });
        $(".breadcrumb").append('<li><a href="{{asset('/hospitalizados')}}"></i> Hospitalizados</a></li>');
        $(".breadcrumb").append('<li class="active">Agregar</li>');

        $('.usuario1 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');    
        });

        $('.usuario2 a').click(function() {
            $(this).closest('.dropdown').find('input.nombrecode')
            .val('(' + $(this).attr('data-value') + ')');

        });

        $('#fechaini').datetimepicker({
            useCurrent: false,
            format: 'YYYY/MM/DD',
             //Important! See issue #1075
            
        });
           

    });

    var buscapaciente = function ()
    {
    

        var js_paciente = document.getElementById('id').value;
        
        $.ajax({
            type: 'get',
            url: "{{ url('hospitalizados/buscapaciente')}}/"+js_paciente, //hospitalizados.buscapaciente
                       
            success: function(data){
                if(data=='no'){
                    $('#nombre1').val('');
                    $('#nombre2').val('');
                    $('#apellido1').val('');
                    $('#apellido2').val('');
                 
                }else{
                    //alert('Paciente ya ingresado en el sistema');
                    //console.log(data);
                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#procedencia').focus();
                }
            }    
        });  
    
    }

    

</script>
@endsection
