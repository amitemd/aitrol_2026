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
        <li class="breadcrumb-item"><a href="{{ route('afActivo.index') }}">Activos</a></li>
        <li class="breadcrumb-item active">Editar Activo</li>
      </ol>
    </nav>
    <div class="row">
        <div class="col-md-12 col-xs-24" >
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-md-9">
                      <h3 class=" texto box-title">Editar Activo</h3>
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
                    <form class="form-horizontal"  id="form_activo" >
                        {{ csrf_field() }}
                        <!--div class="form-group{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-2 control-label">ID:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-group" id="id" name="id" value="{{ $activo->id }}" readonly/>
                            </div>
                        </div-->
                        <div class="form-group{{ $errors->has('codigo') ? ' has-error' : '' }}">
                            <label for="codigo" class="texto col-md-2 control-label">Codigo:</label>
                            <div class="col-md-2">
                                <input type="text" class="form-control" maxlength="16" id="codigo" name="codigo" value="{{ $activo->codigo_text }}" required autocomplete="off" />
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control" maxlength="16" id="codigo_num" name="codigo_num" value="{{ $activo->codigo_num }}" onchange="ingresar_cero();" required >
                            </div>

                        </div>
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-2 control-label">Nombre:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" size="59" id="nombre" name="nombre" value="{{ $activo->nombre }}" required/>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="texto col-md-2 control-label">Descripción:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" size="59" id="descripcion" name="descripcion" value="{{ $activo->descripcion }}" required/>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('tipo_id') ? ' has-error' : '' }}">
                            <label for="tipo" class="texto col-md-2 control-label">Grupo:</label>
                            <div class="col-md-3">
                                <select id="tipo_id" name="tipo_id"  class="form-control select2_cuentas" style="width: 100%;" required>
                                    <option></option>
                                    @foreach($tipos as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('tipo_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tipo') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--div class="col-md-3">
                                <select id="nomtipo" name="nomtipo"  class="form-control select2_cuentas required" style="width: 100%;" required>
                                    <option></option>
                                    @foreach($tipos as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('nomtipo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nomtipo') }}</strong>
                                    </span>
                                @endif
                            </div-->
                        </div>
                        <div class="form-group{{ $errors->has('subtipo_id') ? ' has-error' : '' }}">
                            <label for="tipo" class="texto col-md-2 control-label">Tipo:</label>
                            <div class="col-md-3">
                                <select id="subtipo_id" name="subtipo_id"  class="form-control select2_cuentas" style="width: 100%;" required>
                                    <option></option>
                                    @foreach($sub_tipos as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('subtipo_id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('subtipo_id') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <!--div class="col-md-3">
                                <select id="nomsubtipo" name="nomsubtipo"  class="form-control select2_cuentas" style="width: 100%;" required>
                                    <option></option>
                                    @foreach($tipos as $value)
                                        <option value="{{$value->id}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('nomsubtipo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nomsubtipo') }}</strong>
                                    </span>
                                @endif
                            </div-->


                        </div>
                        <div class="form-group{{ $errors->has('responsable') ? ' has-error' : '' }}">
                            <label for="responsable" class="texto col-md-2 control-label">Responsable:</label>
                            <div class="col-md-3">
                                <select id="responsable" name="responsable"  class="form-control select2_cuentas" style="width: 100%;" onchange="guardar_responsable();">
                                    <option value="">Seleccione...</option>
                                    @foreach($af_responsables as $responsable)
                                        <option @if($activo->responsable == $responsable->nombre) selected @endif value="{{$responsable->nombre}}">{{$responsable->nombre}}</option>
                                    @endforeach
                                    
                                </select>
                            </div>
                            
                        </div>
                      
                        <div class="form-group{{ $errors->has('acreedor') ? ' has-error' : '' }}">
                            <label for="acreedor" class="texto col-md-2 control-label">Acreedor:</label>
                            <div class="col-md-3">
                                <select id="acreedor" name="acreedor"  class="form-control select2_cuentas" style="width: 100%;">
                                    <option value="">Seleccione... </option>
                                    @foreach($proveedor as $value)
                                        <option @if($activo->acreedor == $value->id) selected @endif value="{{$value->id}}">{{$value->id}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('acreedor'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('acreedor') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <select id="nomacreedor" name="nomacreedor"  class="form-control select2_cuentas" style="width: 100%;">
                                    <option value="">Seleccione... </option>
                                    @foreach($proveedor as $value)
                                        <option  @if($activo->acreedor == $value->id) selected @endif value="{{$value->id}}">{{$value->razonsocial}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('nomacreedor'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nomacreedor') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('marca') ? ' has-error' : '' }}">
                            <label for="marca" class="texto col-md-2 control-label">Marca:</label>
                            <div class="col-md-3">
                                <select id="marca" name="marca"  class="form-control select2_color" style="width: 100%;" onchange="guardar_marca();">
                                    <option></option>
                                    @foreach($marcas as $value)
                                        <option @if($activo->marca == $value->nombre) selected @endif value="{{$value->nombre}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('marca'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('marca') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="factura" class="texto col-md-2 control-label">Factura:</label>
                                <div class="col-md-4">
                                    <input type="text" class="form-control" id="factura" name="factura" size="16" value="{{ $activo->factura }}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label for="color" class="texto col-md-2 control-label">Color:</label>
                            <div class="col-md-3">
                                <!--input type="text" class="form-control" id="color" name="color" value="{{ $activo->color }}" /-->
                                <select id="color" name="color" class="form-control select2_color" style="width: 100%;" onchange="guardar_color();">
                                    <option value="{{$activo->color}}">{{$activo->color}}</option>
                                    @foreach($af_colores as $colores)
                                        <option @if($activo->color == $colores->nombre) selected @endif value="{{$colores->nombre}}">{{$colores->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('color'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('color') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('modelo') ? ' has-error' : '' }}">
                            <label for="modelo" class="texto col-md-2 control-label">Modelo:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="modelo" name="modelo" value="{{ $activo->modelo }}" required/>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('serie') ? ' has-error' : '' }}">
                            <label for="serie" class="texto col-md-2 control-label">Serie:</label>
                            <div class="col-md-3">
                                <!--input type="text" class="form-control" id="serie" name="serie" value="{{ $activo->serie }}" required/-->
                                <select id="serie" name="serie" class="form-control select2_color" style="width: 100%;" onchange="guardar_serie();">
                                    <option value="">Seleccione...</option>
                                    @foreach($af_series as $serie)
                                        <option @if($activo->serie == $serie->nombre) selected @endif value="{{$serie->nombre}}">{{$serie->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('serie'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('serie') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('procedencia') ? ' has-error' : '' }}">
                            <label for="procedencia" class="texto col-md-2 control-label">Procedencia:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="procedencia" name="procedencia" value="{{ $activo->procedencia }}" required/>
                                @if ($errors->has('procedencia'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('procedencia') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('estado_activo') ? ' has-error' : '' }}">
                            <label for="estado_activo" class="texto col-md-2 control-label">Estado Activo:</label>
                            <div class="col-md-3">
                                <select id="estado_activo" name="estado_activo"  class="form-control select2_cuentas" style="width: 100%;">
                                    <option value="1">Nuevo</option>
                                    <option value="2">En Uso</option>
                                    
                                </select>
                                @if ($errors->has('estado_activo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado_activo') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="costo" class="texto col-md-3 control-label">Costo Compra:</label>
                                <div class="col-md-3">
                                    <input type="text"  class="form-control" id="costo" name="costo" size="16" value="{{ $activo->costo }}" required onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);"/>
                                    @if ($errors->has('costo'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('costo') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="estado" class="texto col-md-2 control-label">Estado:</label>
                            <div class="col-md-3">
                                <select id="estado" name="estado"  class="form-control select2_cuentas" style="width: 100%;">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                    
                                </select>
                                @if ($errors->has('estado'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ubicacion" class="texto col-md-2 control-label">Ubicación:</label>
                            <div class="col-md-4">
                                <input type="text" name="ubicacion" id="ubicacion" class="form-control" value="{{$activo->ubicacion}}">
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('fecha_compra') ? ' has-error' : '' }}">
                            <label for="fecha_compra" class="texto col-md-2 control-label">Fecha Compra:</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="fecha_compra" name="fecha_compra" value="{{ $activo->fecha_compra }}" required/>
                                @if ($errors->has('fecha_compra'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha_compra') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="depreciacion_acum" class="texto col-md-3 control-label">Dep. Acum:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="depreciacion_acum" name="depreciacion_acum" size="16" value="{{ $activo->depreciacion_acum }}" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);"/>
                                    @if ($errors->has('depreciacion_acum'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('depreciacion_acum') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('tasa') ? ' has-error' : '' }}">
                            <label for="tasa" class="texto col-md-2 control-label">Tasa Depeciación: %</label>
                            <div class="col-md-3">
                                <input type="text"  class="form-control" id="tasa" name="tasa" value="{{ $activo->tasa }}" required onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);">
                                @if ($errors->has('tasa'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('tasa') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <label for="tipo_tasa" class="texto col-md-1 control-label">Tipo:</label>
                            <div class="col-md-2">
                                <select id="tipo_tasa" name="tipo_tasa" class="form-control" style="width:100%;" required>
                                    <option value="">Seleccione...</option>
                                    <option @if($activo->tipo_tasa == '1') selected @endif value="1">Mensual</option>
                                    <option @if($activo->tipo_tasa == '2') selected @endif value="2">Anual</option>
                                </select>
                            </div>

                            
                        </div>
                        <div class="form-group{{ $errors->has('fecha_depreciacion') ? ' has-error' : '' }}">
                            <label for="fecha_depreciacion" class="texto col-md-2 control-label">Días Depeciación:</label>
                            <div class="col-md-3">
                                <!--input type="text" class="form-control" id="fecha_depreciacion" name="fecha_depreciacion" value="{{ $activo->fecha_depreciacion }}" /-->
                                <select id="dias_depreciacion" name="dias_depreciacion" class="form-control">
                                    <option value="">Seleccione...</option>
                                    <option @if($activo->dias_depreciacion == 1) selected @endif value="1">30 Días</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="valor_residual" class="texto col-md-3 control-label">Valor Residual:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="valor_residual" name="valor_residual" size="16" value="{{ $activo->valor_residual }}" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);"/>
                                </div>
                            </div>

                            
                        </div>
                        <div class="form-group{{ $errors->has('nota') ? ' has-error' : '' }}">
                            <label for="nota" class="texto col-md-2 control-label">Nota:</label>
                            <div class="col-md-3">
                                <textarea type="number" class="form-control col-md-3" cols="4" id="nota" name="nota">{{ $activo->nota }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="vida_util" class="texto col-md-3 control-label">Vida Util (Años):</label>
                                <div class="col-md-3">
                                    <input type="number" class="form-control number" id="vida_util" name="vida_util" size="16" value="{{ $activo->vida_util }}"/>
                                </div>
                            </div>
                        </div>

                        <!--div class="form-group">
                            <label for="accesorios" class="texto col-md-2 control-label">Accesorios:</label>
                            <div class="col-md-2">
                                <input type="checkbox" name="accesorios" id="accesorios" value="1" @if($activo->accesorios == 1) checked @endif>
                            </div>                            
                        </div-->

                        <div class="form-group">
                            <input name='contador_items' id='contador_items' type='hidden' value="0">
                            <div class="col-md-12 table-responsive">
                                <table id="items" class="table table table-bordered table-hover dataTable"  role="grid" aria-describedby="example2_info" style="width: 100%;">
                                    <thead class="thead-dark">
                                        <tr class='well-darks'>
                                            <th width="30%" tabindex="0">Nombre</th>
                                            <th width="20%" tabindex="0">Marca</th>
                                            <th width="20%" tabindex="0">Modelo</th>
                                            <th width="20%" tabindex="0">Serie</th>
                                            <th width="10%" tabindex="0">
                                                <button type="button" class="btn btn-success btn-gray agregar_items">
                                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($accesorios as $ac)
                                        <tr class="fila-fija">
                                            <td>
                                                <input required name="nombre_ac[]" id="nombre_ac" class="form-control" style="height:25px;width:90%;" value="{{$ac->nombre}}">
                                            </td>

                                            <td>
                                                <select id="marca_ac" name="marca_ac[]"  class="form-control select2_color"  onchange="guardar_marca();" style="width:80%; height:25px;">
                                                    <option value="">Seleccione..</option>
                                                    @foreach($marcas as $value)
                                                        <option @if($ac->marca == $value->nombre) selected @endif value="{{$value->nombre}}">{{$value->nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <input type="text" name="modelo_ac[]" id="modelo_ac" class="form-control cant" style="height:25px;width:75%;" value="{{$ac->modelo}}">
                                            </td>

                                            <td>
                                                <select id="serie_ac" name="serie_ac[]" class="form-control select2_color" onchange="guardar_serie();" style="width:80%; height:25px;">
                                                    <option value="">Seleccione</option>
                                                    @foreach($af_series as $series)
                                                        <option @if($ac->serie == $series->nombre) selected @endif value="{{$series->nombre}}">{{$series->nombre}}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                            <td>
                                                <button  type="button" onclick="deleteRow(this)" class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>
                                            </td> 
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div> 
                        </div>

                        <div class="form-group col-xs-10 text-center" >
                            <div class="col-md-6 col-md-offset-4">
                                <!--button type="submit"  class="btn btn-default btn_add">
                                    <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                                </button-->

                                <a onclick="actualizar_activo('{{$activo->id}}');" type="button" class="btn btn-success" id="btn_guardar">Actualizar</a>
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

        $('.select2_color').select2({
            tags: true
        });

        $('#fecha_compra').datetimepicker({
            format: 'YYYY/MM/DD',
        });

        $('#fecha_depreciacion').datetimepicker({
            format: 'YYYY/MM/DD',
        });

        $('.number').keypress(function(event){
            if(event.which != 8 && isNaN(String.fromCharCode(event.which))){
                event.preventDefault(); //stop character from entering input
            }
        });

        $('#tipo_id').val('{{ $activo->tipo_id }}');
        $('#tipo_id').select2().trigger('change');

        $('#tipo_id').on('select2:select', function (e) {
            var tipo_id = $('#tipo_id').val();
            $('#nomtipo').val(tipo_id);
            $('#nomtipo').select2().trigger('change');
        });

        $('#nomtipo').val('{{ $activo->tipo_id }}');
        $('#nomtipo').select2().trigger('change');

        $('#nomtipo').on('select2:select', function (e) {
            var nomtipo = $('#nomtipo').val();
            $('#tipo_id').val(nomtipo);
            $('#tipo_id').select2().trigger('change');
        });

        $('#subtipo_id').val('{{ $activo->subtipo_id }}');
        $('#subtipo_id').select2().trigger('change');

        $('#subtipo_id').on('select2:select', function (e) {
            var subtipo_id = $('#subtipo_id').val();
            $('#nomsubtipo').val(subtipo_id);
            $('#nomsubtipo').select2().trigger('change');
        });

        $('#nomsubtipo').val('{{ $activo->subtipo_id }}');
        $('#nomsubtipo').select2().trigger('change');

        $('#nomsubtipo').on('select2:select', function (e) {
            var nomsubtipo = $('#nomsubtipo').val();
            $('#subtipo_id').val(nomsubtipo);
            $('#subtipo_id').select2().trigger('change');
        });

    });


    function guardar_color(){

        $.ajax({
            type: 'post',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            url:"{{route('activofjo.guardar_color')}}",
            data: $("#form_activo").serialize(),
            datatype: 'json',
            success: function(data){
                console.log(data);
                //alert(data)
            },
            error: function(data){
                //console.log(data);
                //alert(data)
            }
        }); 
        
    }

    function guardar_serie(){
        $.ajax({
            type: 'post',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            url:"{{route('activofjo.guardar_serie')}}",
            data: $("#form_activo").serialize(),
            datatype: 'json',
            success: function(data){
                console.log(data);
                //alert(data)
            },
            error: function(data){
                //console.log(data);
                //alert(data)
            }
        });
    }


    function guardar_marca(){
        $.ajax({
            type: 'post',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            url:"{{route('activofjo.guardar_marca')}}",
            data: $("#form_activo").serialize(),
            datatype: 'json',
            success: function(data){
                console.log(data);
                //alert(data)
            },
            error: function(data){
                //console.log(data);
                //alert(data)
            }
        });
    }

    function guardar_responsable(){
        $.ajax({
            type: 'post',
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            url:"{{route('activofjo.guardar_responsable')}}",
            data: $("#form_activo").serialize(),
            datatype: 'json',
            success: function(data){
                console.log(data);
                //alert(data)
            },
            error: function(data){
                //console.log(data);
                //alert(data)
            }
        });
    }


    function isNumberKey(evt) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
            return false;

        return true;
    }

    function alertas(icon, title, text) {
        Swal.fire({
            icon: `${icon}`,
            title: `${title}`,
            html: `${text}`,
        })
    }

    function actualizar_activo(id){

        $.ajax({
            type: 'post',
            url: "{{url('acf/activofijo/update_activo')}}/"+id,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: $("#form_activo").serialize(),
            success: function(data) {
                if (data == "ok") {
                    alertas('success', 'Exito', 'Actualizado Correctamente');
                    location.href= "{{route('afActivo.index')}}";
                    
                }
                
            },
            error: function(data) {
                alertas('error', 'Error', 'Ocurrio un problema');
                console.log(data);
            }
        });

    }


    $('.agregar_items').on('click', function() {
            agregar_items();
        });

    function agregar_items() {
        var id = document.getElementById('contador_items').value;
        var tr = `<tr class="columnas"> 
                        <td>
                            <input required name="nombre_ac[]" id="nombre_ac" class="form-control" style="height:25px;width:90%;">
                        </td>

                        <td>
                            <select id="marca_ac" name="marca_ac[]"  class="form-control select2_color"  onchange="guardar_marca();" style="width:80%; height:25px;">
                                <option value="">Seleccione..</option>
                                @foreach($marcas as $value)
                                    <option value="{{$value->nombre}}">{{$value->nombre}}</option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            <input type="text" name="modelo_ac[]" id="modelo_ac" class="form-control cant" style="height:25px;width:75%;">
                        </td>

                        <td>
                            <select id="serie_ac" name="serie_ac[]" class="form-control select2_color" onchange="guardar_serie();" style="width:80%; height:25px;">
                                <option value="">Seleccione</option>
                                @foreach($af_series as $series)
                                    <option value="{{$series->nombre}}">{{$series->nombre}}</option>
                                @endforeach
                            </select>
                        </td>

                        <td>
                            <button  type="button" onclick="deleteRow(this)" class="btn btn-danger btn-gray" ><i class="glyphicon glyphicon-trash" aria-hidden="true"></i></button>
                        </td>                    
                    </tr> `;
        $('#items').append(tr);
        $('.select2_color').select2({
            tags: true
        });

        var ids = id;
        id++;
        document.getElementById('contador_items').value= id;

    }
    function deleteRow(btn) {
        var row = btn.parentNode.parentNode;
        row.parentNode.removeChild(row);
        //calcular();
    }

    function ingresar_cero() {
            var secuencia_factura = $('#codigo_num').val();
            var digitos = 6;
            var ceros = 0;
            var varos = '0';
            var secuencia = 0;
            if (secuencia_factura > 0) {
                var longitud = parseInt(secuencia_factura.length);
                if (longitud > 7) {
                    swal("Error!", "Valor no permitido", "error");
                    $('#codigo_num').val('');

                } else {

                    var concadenate = parseInt(digitos - longitud);
                    switch (longitud) {
                        case 1:
                            secuencia = '00000';
                            break;
                        case 2:
                            secuencia = '0000';
                            break;
                        case 3:
                            secuencia = '000';
                            break;
                        case 4:
                            secuencia = '00';
                            break;
                        case 5:
                            secuencia = '0';
                            break;
                        case 6:
                            secuencia = '';
                    }
                    $('#codigo_num').val(secuencia + secuencia_factura);
                }


            } else {
                swal("Error!", "Valor no permitido", "error");
                $('#codigo_num').val('');
            }
        }

</script>
@endsection
