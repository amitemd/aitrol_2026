@extends('ticket_soporte_tecnico.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Soporte Tecnico</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>
    <form class="form-vertical" method="post" role="form" id="formulario" >
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="id" name="id" value="{{$id}}">       
        <div class="box">
            <div class="box-header">
                <div class="col-md-9">
                    <h5><b>Control de Soporte Tecnico</b></h5>
                </div>
                 <div class="col-md-1 text-right">
                    <button onclick="goBack()" class="btn btn-danger">
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                    </button>
                </div>
            </div>

       
            <div class="separator"></div>
            <div class="box-body">
             <div class="form-group  col-xs-4">
                    <label for="fecha" class="col-md-4 contro-label">Fecha</label>
                    <div class="col-md-8">
                        <input id="fecha" name="fecha" type="date" class="form-control" value="{{date('Y-m-d',strtotime($control_limp->fecha))}}" placeholder="fecha">
                    </div>
                </div>

                <div class="form-group col-md-6 col-xs-6">
                    <label for="responsable" class="col-md-3 control-label">Responsable</label>
                    <div class="col-md-9">
                      <input id="responsable" type="text" class="form-control" name="responsable" value="{{ $control_limp->responsable }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('responsable'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('responsable') }}</strong>
                                    </span>
                                @endif
                    </div>  
              </div>
                
                <div class="form-group col-md-6 col-xs-6">
                    <label for="desinfectante" class="col-md-3 control-label">Desinfectante</label>
                    <div class="col-md-9">
                       <input id="desinfectante" type="text" class="form-control" name="desinfectante" value="{{$control_limp->desinfectante }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('desinfectante'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('desinfectante') }}</strong>
                                    </span>
                                @endif
                    </div>
                </div>
                 <div class="form-group col-md-6 col-xs-6">
                    <label for="nombre_piso" class="col-md-3 control-label">Nombre del Piso</label>
                    <div class="col-md-6">
                       <select id="nombre_piso"  name="nombre_piso" class="form-control" required autofocus>
                            <option value="">Seleccione</option>
                            @foreach($nombre_piso as $val)
                            <option {{$control_limp->nombre_piso == $val->id ? 'selected' : ''  }} value="{{$val->id}}">{{$val->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @if($rolUsuario!='5')
                <div class="form-group col-md-6 col-xs-6">
                    <label for="responsable" class="col-md-3 control-label">Usuario</label>
                    <div class="col-md-9">
                        <input id="responsable" type="text" class="form-control" name="responsable" value="{{ old('responsable') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                        @if ($errors->has('responsable'))
                        <span class="help-block">
                            <strong>{{ $errors->first('responsable') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                @endif                   
                
                  <div class="form-group col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                            <label for="observaciones" id="titulo" class="col-md-2 control-label" >Observaciones</label>
                            <div class="col-md-10" style="padding-left: 10px;padding-right: 5%;">
                                <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{$control_limp->observacion }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('observaciones'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('observaciones') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                          <div class="form-group col-xs-12">
                        <div class="col-md-6 col-md-offset-4">
                            <button  type="submit" class="btn btn-primary">
                            Actualizar
                            </button>
                        </div>
                    </div>
              </div>
           </form>
</section>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
  function editar() {

        $.ajax({
            url: "{{route('limpieza_banos.update')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#formulario').serialize(),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var url = "{{route('limpieza_banos.index')}}"
                if (data == 'ok') {
                    setTimeout(function() {
                        swal("Editado!", "Correcto", "success");
                        window.location = url;
                    }, 3000);
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }  
    function goBack() {
        var url = '{{route("limpieza_banos.index")}}';
        window.location = url;
    }
</script>


@endsection