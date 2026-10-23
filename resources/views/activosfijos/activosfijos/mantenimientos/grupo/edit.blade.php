@extends('contable.estructura_flujo_efectivo.base')
@section('action-content')
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
  <!-- Main content -->


<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Activos Fijos</a></li> 
        <li class="breadcrumb-item"><a href="#">Mantenimientos</a></li> 
        <li class="breadcrumb-item"><a href="{{ route('afTipo.index') }}">Tipos</a></li>
        <li class="breadcrumb-item active">Agregar Tipo</li>
      </ol>
    </nav>
    <div class="row">
        <div class="col-md-12 col-xs-24" >
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-md-9">
                      <h3 class=" texto box-title">Agregar nuevo Tipo</h3>
                    </div>
                    <!-- <div class="col-md-1 text-right">   
                        <a type="button" href="{{URL::previous()}}"  class="btn btn-default btn-gray">
                            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"> Regresar</span>
                        </a>
                    </div>  -->
                    <div class="col-md-1 text-right">
                        <button onclick="location.href='{{URL::previous()}}'" class="btn btn-default btn-gray" >
                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                        </button>
                    </div>
                </div>
                <div class="box-body dobra">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('afTipo.update',[$tipo->id]) }}">
                        {{ csrf_field() }}
                        {{ method_field('PUT')}}    
                        <div class="form-group{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-4 control-label">ID:</label>
                            <div class="col-md-6">
                            <input type="text" class="form-group" id="id" name="id" value="{{ $tipo->id }}" readonly/>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('codigo') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-4 control-label">Codigo:</label>
                            <div class="col-md-6">
                                <input type="number" class="form-group number" id="codigo" name="codigo" value="{{ $tipo->codigo }}" required/>
                                    @if ($errors->has('codigo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('codigo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-4 control-label">Nombre:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-group" size="59" id="nombre" name="nombre" value="{{ $tipo->nombre }}" required/>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('cuentamayor') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-4 control-label">Cta. Mayor:</label>
                            <div class="col-md-3">
                                <select id="cuentamayor" name="cuentamayor"  class="form-control select2_cuentas" style="width: 100%;" required>
                                    <option></option>
                                    @foreach(@$plan as $value)
                                        <option value="{{$value->id}}">{{$value->id}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('cuentamayor'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cuentamayor') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <select id="nomcuentamayor" name="nomcuentamayor"  class="form-control select2_cuentas required" style="width: 100%;" required>
                                    <option></option>
                                    @foreach(@$plan as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('nomcuentamayor'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nomcuentamayor') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('cuantadepreciacion') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-4 control-label">Cta. Depeciaci&oacute;n Acumulada:</label>
                            <div class="col-md-3">
                                <select id="cuantadepreciacion" name="cuantadepreciacion"  class="form-control select2_cuentas" style="width: 100%;" required>
                                    <option></option>
                                    @foreach(@$plan as $value)
                                        <option value="{{$value->id}}">{{$value->id}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('cuantadepreciacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cuantadepreciacion') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <select id="nomcuantadepreciacion" name="nomcuantadepreciacion"  class="form-control select2_cuentas" style="width: 100%;" required>
                                    <option></option>
                                    @foreach(@$plan as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('nomcuantadepreciacion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nomcuantadepreciacion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('cuentagastos') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-4 control-label">Cta. Gastos:</label>
                            <div class="col-md-3">
                                <select id="cuentagastos" name="cuentagastos"  class="form-control select2_cuentas" style="width: 100%;" required>
                                    <option></option>
                                    @foreach(@$plan as $value)
                                        <option value="{{$value->id}}">{{$value->id}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('cuentagastos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cuentagastos') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <select id="nomcuentagastos" name="nomcuentagastos"  class="form-control select2_cuentas" style="width: 100%;" required>
                                    <option> </option>
                                    @foreach(@$plan as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('nomcuentagastos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nomcuentagastos') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('tasa') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-4 control-label">Tasa:</label>
                            <div class="col-md-6">
                                <input type="number" class="form-group number" id="tasa" name="tasa" value="{{ $tipo->tasa }}" required/>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-4 control-label">Vida Util:</label>
                            <div class="col-md-6">
                                <input type="number" class="form-group number" id="vidautil" name="vidautil" value="{{ $tipo->vidautil }}" required/>
                            </div>
                        </div>   
                        <div class="form-group col-xs-10 text-center" >
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit"  class="btn btn-default btn_add">
                                    <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
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
        $('.select2_cuentas').select2({
            tags: false
            });
        }); 

        $('.number').keypress(function(event){
            if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
                event.preventDefault(); //stop character from entering input
            }
        });

        $('#cuentamayor').on('select2:select', function (e) {
            var cuentamayor = $('#cuentamayor').val();
            $('#nomcuentamayor').val(cuentamayor);
            $('#nomcuentamayor').select2().trigger('change');
        });

        $('#nomcuentamayor').on('select2:select', function (e) {
            var nomcuentamayor = $('#nomcuentamayor').val();
            $('#cuentamayor').val(nomcuentamayor);
            $('#cuentamayor').select2().trigger('change');
        });

        $('#cuantadepreciacion').on('select2:select', function (e) {
            var cuantadepreciacion = $('#cuantadepreciacion').val();
            $('#nomcuantadepreciacion').val(cuantadepreciacion);
            $('#nomcuantadepreciacion').select2().trigger('change');
        }); 

        $('#nomcuantadepreciacion').on('select2:select', function (e) {
            var nomcuantadepreciacion = $('#nomcuantadepreciacion').val();
            $('#cuantadepreciacion').val(nomcuantadepreciacion);
            $('#cuantadepreciacion').select2().trigger('change');
        });  

        $('#cuentagastos').on('select2:select', function (e) {
            var cuentagastos = $('#cuentagastos').val();
            $('#nomcuentagastos').val(cuentagastos);
            $('#nomcuentagastos').select2().trigger('change');
        });  

        $('#nomcuentagastos').on('select2:select', function (e) {
            var nomcuentagastos = $('#nomcuentagastos').val();
            $('#cuentagastos').val(nomcuentagastos);
            $('#cuentagastos').select2().trigger('change');
        });  


        $('#nomcuentamayor').val('{{ $tipo->cuentamayor }}');
        $('#nomcuentamayor').select2().trigger('change');

        $('#cuentamayor').val('{{ $tipo->cuentamayor }}');
        $('#cuentamayor').select2().trigger('change');

        $('#nomcuantadepreciacion').val('{{ $tipo->cuantadepreciacion }}');
        $('#nomcuantadepreciacion').select2().trigger('change');

        $('#cuantadepreciacion').val('{{ $tipo->cuantadepreciacion }}');
        $('#cuantadepreciacion').select2().trigger('change');

        $('#nomcuentagastos').val('{{ $tipo->cuentagastos }}');
        $('#nomcuentagastos').select2().trigger('change');

        $('#cuentagastos').val('{{ $tipo->cuentagastos }}');
        $('#cuentagastos').select2().trigger('change');

</script>
@endsection
