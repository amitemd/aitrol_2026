@extends('contable.estructura_flujo_efectivo.base')
@section('action-content')
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
  <!-- Main content -->


<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
        <li class="breadcrumb-item"><a href="{{ route('estructuraflujoefectivo.index') }}">Estructura flujo de efectivo</a></li>
        <li class="breadcrumb-item active">Editar</li>
      </ol>
    </nav>
    <div class="row">
        <div class="col-md-12 col-xs-24" >
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-md-9">
                      <h3 class="box-title">Editar cuenta</h3>
                    </div> 
                    <div class="col-md-1 text-right">
                        <button onclick="location.href='{{URL::previous()}}'" class="btn btn-default btn-gray" >
                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                        </button>
                    </div>
                </div>
                <div class="box-body dobra">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('estructuraflujoefectivo.update', ['id' => $estructura->id]) }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('id_plan') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-4 control-label">Numero de cuenta:</label>
                            <div class="col-md-6"> 
                            <select id="id_plan" name="id_plan"  class="form-control select2_cuentas" style="width: 100%;" >
                                <option>Seleccionar </option>
                                @foreach($scuentas as $value) 
                                    <option @if(old('id_plan')==$value->id){{"selected"}}@endif value="{{$value->id}}">{{$value->id}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_plan'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_plan') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('id_plan') ? ' has-error' : '' }}">
                            <label for="id_hospital" class="texto col-md-4 control-label">Nombre cuenta:</label>
                            <div class="col-md-6">
                            <select id="nombre" name="nombre"  class="form-control select2_cuentas" style="width: 100%;" >
                                <option> Seleccionar</option>
                                @foreach($scuentas as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_plan'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_plan') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div> 
                        <div class="form-group">
                            <label for="id_hospital" class="texto col-md-4 control-label">Signo:</label>
                            <div class="col-md-6">
                            <select id="signo" name="signo"  class="form-control select2_cuentas" style="width: 100%;" >
                                <option> Seleccionar</option> 
                                    <option value="1">Ingreso</option> 
                                    <option value="2">Egreso</option> 
                            </select>
                            </div>
                        </div> 
                        <div class="form-group{{ $errors->has('id_grupo') ? ' has-error' : '' }}">
                            <label for="id_hospital" class="texto col-md-4 control-label">Nombre grupo:</label>
                            <div class="col-md-6">
                            <select id="id_grupo" name="id_grupo"  class="form-control select2_cuentas" style="width: 100%;" >
                                <option> Seleccionar</option>
                                @foreach($grupos as $value)
                                    <option value="{{$value->id}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_plan'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_grupo') }}</strong>
                                </span>
                            @endif
                            </div>
                        </div> 
                        <!-- <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Modificar
                                </button>
                            </div>
                        </div> -->

                        <div class="form-group col-xs-10 text-center" >
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit"  class="btn btn-default btn_add">
                                Modificar
                                </button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
       

          
      <!-- /.box-body -->
    </div>
  </section>


  <!-- /.content -->
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">

    $(document).ready(function(){
        var id_plan = "{{ $estructura->id_plan }}";
        $('#id_plan').val(String(id_plan));
        $('#id_plan').trigger('change');

        $('#nombre').val(String(id_plan)); 
        $('#nombre').trigger('change');

        $('#signo').val({{ $estructura->signo }}); 
        $('#signo').trigger('change');

        $('#id_grupo').val({{ $estructura->id_grupo }}); 
        $('#id_grupo').trigger('change');

        $('.select2_cuentas').select2({
            tags: false
        });

    }); 


    $('#id_plan').on('select2:select', function (e) {
        var id_plan = $('#id_plan').val();
        $('#nombre').val(id_plan);
        $('#nombre').select2().trigger('change');
      });


    $('#nombre').on('select2:select', function (e) {
        var nombre = $('#nombre').val();
        $('#id_plan').val(nombre);
        $('#id_plan').select2().trigger('change');
      }); 
</script>
@endsection
