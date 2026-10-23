@extends('servicios_generales.limpieza_banos.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Servicios Generales</a></li>
            <li class="breadcrumb-item"><a href="../ambiente">Limpieza y Desinfección de Baños</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar</li>
        </ol>
    </nav>
    <form class="form-vertical" method="post" role="form" id="formulario" action="{{route('limpieza_banos.update')}}" accept-charset="UTF-8" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="box-body col-xs-12">
            <input type="hidden" name="_method" value="POST">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" id="id" name="id" value="{{$id}}">
            <div class="box">
                <div class="box-header">
                    <div class="col-md-4">
                        <h5><b>Control de Limpieza y Desinfección de Baños</b></h5>
                    </div>
                    <div class="col-md-3">
                        <select name="piso" id="piso" class="form-control" disabled>
                            <option value="">Seleccione</option>
                            @foreach($pisoBa as $value)
                            <option {{$control_limp->id_piso == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5 text-right">
                        <button onclick="goBack()" class="btn btn-danger">
                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                        </button>
                    </div>
                </div>


                <div class="separator"></div>
                <div class="box-body">

                    <!--div class="form-group col-md-6 col-xs-6">
                    <label for="responsable" class="col-md-3 control-label">Responsable</label>
                    <div class="col-md-9">
                      <input id="responsable" type="text" class="form-control" name="responsable" value="{{ $control_limp->responsable }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('responsable'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('responsable') }}</strong>
                                    </span>
                                @endif
                    </div>  
              </div-->

                    <!--div class="form-group col-md-6 col-xs-6">
                        <label for="desinfectante" class="col-md-3 control-label">Desinfectante</label>
                        <div class="col-md-9">
                            <input id="desinfectante" type="text" class="form-control" name="desinfectante" value="{{$control_limp->desinfectante }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            @if ($errors->has('desinfectante'))
                            <span class="help-block">
                                <strong>{{ $errors->first('desinfectante') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div-->
                    <!--div id="div_hc1" class="form-group col-md-12 col-xs-6{{ $errors->has('archivo') ? ' has-error' : '' }}">

                        <label for="archivo" class="col-md-6 control-label">Evidencia antes de la limpieza y desinfección del baño (img)</label>
                        <div class="col-md-6" style="padding-left: 10px;padding-right: 5%;">
                            @php
                            $emplace=asset('/');
                            $remplace= str_replace('public','storage',$emplace);
                            @endphp
                            @if(empty($control_limp->evidencia_antes))
                            <input id="file-input" name="evidencia_antes" type="file" />
                            @else
                            <input id="file-input" name="evidencia_antes" type="file" />
                            <img src="{{$remplace.'app/avatars/'.$control_limp->evidencia_antes}}" width="50" alt="">
                        </div>
                        @endif
                    </div-->
                    <div id="div_hc2" class="form-group col-md-12 col-xs-6 {{ $errors->has('archivo') ? ' has-error' : '' }}">

                        <label for="archivo" class="col-md-6 control-label">Evidencia después de la limpieza y desinfección del baño (img)</label>
                        <div class="col-md-6" style="padding-left: 10px;padding-right: 5%;">
                            @php
                            $emplace=asset('/');
                            $remplace= str_replace('public','storage',$emplace);
                            //dd($control_limp);
                            @endphp
                            @if (empty($control_limp->evidencia_desp))
                            <input id="file-input" name="evidencia_desp" type="file" />
                            @else
                            <input id="file-input" name="evidencia_desp" type="file" />
                            <img src="{{$remplace.'app/avatars/'.$control_limp->evidencia_desp}}" width="50" alt="">
                        </div>
                        @endif
                    </div>
                    <div id="div_hc2" class="form-group col-lg-12">
                        <label for="archivo" class="col-md-2 control-label">Tipo de Desinfección</label>
                        <div class="col-md-3" style="padding-left: 10px;padding-right: 5%;">
                            <select name="limpieza" id="limpieza" class="form-control" disabled>
                                <option value="">Seleccione</option>
                                <option {{$control_limp->limpieza == 1 ? 'selected' : '' }} value="1">Concurrente</option>
                                <option {{$control_limp->limpieza == 2 ? 'selected' : '' }} value="2">Terminal</option>
                            </select>

                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        <div class="col-md-2">
                            <label for="producto" class="col-md-2 control-label">Insumos utilizados</label>
                        </div>
                        <div class="col-md-3">
                          <select disabled name="insumos[]" id="insumos" class="form-control js-example-basic-multiple" multiple="multiple" required>
                              <option value="">Seleccione</option>
                              @foreach($control_limp->productos as $value)
                              @foreach($productos as $val)
                              <option {{$value->id_insumos == $val->id ? 'selected': ''}} value="{{$val->id}}">{{$val->nombre}}</option>
                              @endforeach
                              @endforeach
                          </select>
                        </div>
                        <label for="insumos" class="col-md-2 control-label">Dotación</label>
                        <div class="col-md-3">
                          <select disabled name="producto[]" id="producto" class="form-control js-example-basic-multiple" multiple="multiple" required>
                              <option value="">Seleccione</option>
                              @foreach($control_limp->insumos as $value)
                              @foreach($insumos as $val)
                              <option {{$value->id_insumos_banos == $val->id ? 'selected': ''}} value="{{$val->id}}">{{$val->nombre}}</option>
                              @endforeach
                              @endforeach
                          </select>

                        </div>
                    </div>
                    <div class="form-group col-md-12 col-xs-6 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                        <label for="observaciones" id="titulo" class="col-md-2 control-label">Observaciones</label>
                        <div class="col-md-10" style="padding-left: 10px;padding-right: 5%;">
                            <input id="observaciones" type="text" class="form-control" disabled name="observaciones" value="{{$control_limp->observacion }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            @if ($errors->has('observaciones'))
                            <span class="help-block">
                                <strong>{{ $errors->first('observaciones') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
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
    $(document).ready(function() {
        $('.js-example-basic-multiple').select2();
    });
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