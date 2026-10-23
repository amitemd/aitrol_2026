@extends('contable.estructura_flujo_efectivo.base')
@section('action-content')
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
<!-- Main content -->
<style type="text/css">
    .color {
        text-transform: uppercase;
    }

    .check {
        transform: scale(1.5);
        /* Cambia el tamaño (1 = 100%, 1.5 = 150%) */
        margin-right: 5px;
        cursor: pointer;
    }
</style>

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.activosfijos')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{trans('contableM.mantenimientos')}}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('afActivo.index') }}">{{trans('contableM.activos')}}</a></li>
            <li class="breadcrumb-item active">{{trans('contableM.aggactivo')}}</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-md-12 col-xs-24">
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-md-9">
                        <h3 class=" texto box-title">{{trans('contableM.aggnewactivo')}}</h3>
                    </div>
                    <!-- <div class="col-md-1 text-right">   
                        <a type="button" href="{{URL::previous()}}"  class="btn btn-default btn-gray">
                            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"> Regresar</span>
                        </a>
                    </div>  -->
                    <div class="col-md-1 text-right">
                        <a href="{{route('afActivo.index')}}" class="btn btn-default btn-gray">
                            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('contableM.regresar')}}
                        </a>
                    </div>
                </div>
                <div class="box-body dobra">
                    <form class="form-horizontal" id="form_activo" enctype="multipart/form-data">
                        <!-- <form class="form-horizontal" id="form_activo" role="form" method="POST" action="{{ route('afActivo.store') }}" enctype="multipart/form-data"> -->
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('codigo') ? ' has-error' : '' }}">
                            <label for="codigo" class="texto col-md-2 control-label">{{trans('contableM.codigo')}}:</label>
                            <div class="col-md-2">
                                <input type="text" class="form-control validar" maxlength="16" id="codigo" name="codigo" value="{{ old('codigo') }}" required autocomplete="off" data-toggle="tooltip" title="Codigo ejemplo: AF-TIPO_ACTIVO-EMPRESA">
                            </div>
                            <div class="col-md-2">
                                <input type="text" class="form-control validar" maxlength="16" id="codigo_num" name="codigo_num" onchange="ingresar_cero();" required data-toggle="tooltip" title="Numero Secuencial">
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="texto col-md-2 control-label">{{trans('contableM.nombre')}}:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control validar" id="nombre" name="nombre" value="{{ old('nombre') }}" required />
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="texto col-md-2 control-label">{{trans('contableM.Descripcion')}}:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="descripcion" name="descripcion" value="{{ old('descripcion') }}" required />
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('tipo_id') ? ' has-error' : '' }}">
                            <label for="tipo" class="texto col-md-2 control-label">{{trans('contableM.tipo')}}:</label>
                            <div class="col-md-3">
                                <select id="tipo_id" name="tipo_id" class="form-control select2_cuentas validar" style="width: 100%;" required>
                                    <option value="">{{trans('contableM.seleccione')}}...</option>
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

                        </div>
                        <div class="form-group{{ $errors->has('subtipo_id') ? ' has-error' : '' }}">
                            <label for="tipo" class="texto col-md-2 control-label">{{trans('contableM.categoria')}}:</label>
                            <div class="col-md-3">
                                <select id="subtipo_id" name="subtipo_id" class="form-control select2_cuentas validar" style="width: 100%;" required>
                                    <option value="">{{trans('contableM.seleccione')}}...</option>
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

                        </div>
                        <div class="form-group{{ $errors->has('responsable') ? ' has-error' : '' }}">
                            <label for="responsable" class="texto col-md-2 control-label">{{trans('contableM.responsable')}}:</label>
                            <div class="col-md-3">
                                <select id="responsable" name="responsable" class="form-control select2_color" style="width: 100%;" onchange="guardar_responsable();">
                                    <option value="">{{trans('contableM.seleccione')}}...</option>
                                    @foreach($af_responsables as $responsable)
                                    <option value="{{$responsable->nombre}}">{{$responsable->nombre}}</option>
                                    @endforeach

                                </select>

                            </div>

                        </div>

                        <div class="form-group{{ $errors->has('acreedor') ? ' has-error' : '' }}">
                            <label for="acreedor" class="texto col-md-2 control-label">{{trans('contableM.acreedor')}}:</label>
                            <div class="col-md-3">
                                <select id="acreedor" name="acreedor" class="form-control select2_cuentas" style="width: 100%;">
                                    <option value="">{{trans('contableM.seleccione')}}...</option>
                                    @foreach($proveedor as $value)
                                    <option value="{{$value->id}}">{{$value->id}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('acreedor'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('acreedor') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <select id="nomacreedor" name="nomacreedor" class="form-control select2_cuentas" style="width: 100%;">
                                    <option value="">{{trans('contableM.seleccione')}}...</option>
                                    @foreach($proveedor as $value)
                                    <option value="{{$value->id}}">{{$value->razonsocial}}</option>
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
                            <label for="marca" class="texto col-md-2 control-label">{{trans('contableM.marca')}}:</label>
                            <div class="col-md-3">

                                <select id="marca" name="marca" class="form-control select2_color" style="width: 100%;" onchange="guardar_marca();">
                                    <option value="">{{trans('contableM.seleccione')}}..</option>
                                    @foreach($marcas as $value)
                                    <option value="{{$value->nombre}}">{{$value->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('marca'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('marca') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="factura" class="texto col-md-3 control-label">{{trans('contableM.factura')}}:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="factura" name="factura" size="16" value="{{ old('factura') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label for="color" class="texto col-md-2 control-label">{{trans('contableM.color')}}:</label>
                            <div class="col-md-3">
                                <!--input type="text" class="form-control" id="color" name="color" value="{{ old('color') }}" /-->
                                <select id="color" name="color" class="form-control select2_color" style="width:100%;" onchange="guardar_color();">
                                    <option value="">{{trans('contableM.seleccione')}}</option>
                                    @foreach($af_colores as $colores)
                                    <option value="{{$colores->nombre}}">{{$colores->nombre}}</option>
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
                            <label for="modelo" class="texto col-md-2 control-label">{{trans('contableM.modelo')}}:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="modelo" name="modelo" value="{{ old('modelo') }}" />
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('serie') ? ' has-error' : '' }}">
                            <label for="serie" class="texto col-md-2 control-label">{{trans('contableM.serie')}}:</label>
                            <div class="col-md-3">

                                <select id="serie" name="serie" class="form-control select2_color" style="width:100%;" onchange="guardar_serie();">
                                    <option value="">{{trans('contableM.seleccione')}}</option>
                                    @foreach($af_series as $series)
                                    <option value="{{$series->nombre}}">{{$series->nombre}}</option>
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
                            <label for="procedencia" class="texto col-md-2 control-label">{{trans('contableM.procedencia')}}:</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="procedencia" name="procedencia" value="{{ old('procedencia') }}" />
                                @if ($errors->has('procedencia'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('procedencia') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ubicacion" class="texto col-md-2 control-label">{{trans('contableM.ubicacion')}}:</label>
                            <div class="col-md-4">
                                <input type="text" name="ubicacion" id="ubicacion" class="form-control">
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('estado_activo') ? ' has-error' : '' }}">
                            <label for="estado" class="texto col-md-2 control-label">{{trans('contableM.estadoactivo')}}:</label>
                            <div class="col-md-3">
                                <select id="estado_activo" name="estado_activo" class="form-control select2_cuentas" style="width: 100%;">
                                    <option value="1">{{trans('contableM.nuevo')}}</option>
                                    <option value="2">{{trans('contableM.enuso')}}</option>
                                </select>
                                @if ($errors->has('estado_activo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('estado_activo') }}</strong>
                                </span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <label for="costo" class="texto col-md-3 control-label">{{trans('contableM.costcompra')}}:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control validar" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" id="costo" name="costo" size="16" value="0" required />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="estado" class="texto col-md-2 control-label">{{trans('contableM.estado')}}:</label>
                            <div class="col-md-3">
                                <select id="estado" name="estado" class="form-control select2_cuentas" style="width: 100%;">
                                    <option value="1">{{trans('contableM.activo')}}</option>
                                    <option value="0">{{trans('contableM.inactivo')}}</option>
                                </select>
                                @if ($errors->has('estado'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('estado') }}</strong>
                                </span>
                                @endif
                            </div>

                        </div>

                        <div class="form-group{{ $errors->has('fecha_compra') ? ' has-error' : '' }}">
                            <label for="fecha_compra" class="texto col-md-2 control-label">{{trans('contableM.fechacompra')}}:</label>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="fecha_compra" name="fecha_compra" min="{{ isset($min_date) ? date('Y-m-d', strtotime($min_date.'+1 day')) : '' }}" onblur="validarCierre();" value="{{ date('Y-m-d') }}" required />
                            </div>
                            <div class="col-md-6">
                                <label for="depreciacion_acum" class="texto col-md-3 control-label">{{trans('contableM.depacum')}}:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="depreciacion_acum" name="depreciacion_acum" size="16" value="0.00" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" />
                                    @if ($errors->has('depreciacion_acum'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('depreciacion_acum') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('tasa') ? ' has-error' : '' }}">
                            <label for="tasa" class="texto col-md-2 control-label">{{trans('contableM.tasa')}} {{trans('contableM.depreciacion')}}: %</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control validar" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" id="tasa" name="tasa" value="{{ old('tasa') }}" />
                                @if ($errors->has('tasa'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('tasa') }}</strong>
                                </span>
                                @endif
                            </div>

                            <label for="tipo_tasa" class="texto col-md-1 control-label">{{trans('contableM.tipo')}}:</label>
                            <div class="col-md-2">
                                <select id="tipo_tasa" name="tipo_tasa" class="form-control" style="width:100%;">
                                    <option value="">{{trans('contableM.seleccione')}}...</option>
                                    <option value="1">{{trans('contableM.mensual')}}</option>
                                    <option value="2">{{trans('contableM.anual')}}</option>
                                </select>
                            </div>


                        </div>
                        <div class="form-group{{ $errors->has('fecha_depreciacion') ? ' has-error' : '' }}">
                            <label for="fecha_depreciacion" class="texto col-md-2 control-label">{{trans('agenda.dias')}} {{trans('contableM.depreciacion')}}:</label>
                            <div class="col-md-3">
                                <!--input type="text" class="form-control" id="fecha_depreciacion" name="fecha_depreciacion" value="{{ old('fecha_depreciacion') }}"/-->
                                <select id="dias_depreciacion" name="dias_depreciacion" class="form-control">
                                    <option value="">{{trans('contableM.seleccione')}}...</option>
                                    <option value="1">30 {{trans('agenda.dias')}}</option>
                                </select>

                            </div>

                            <div class="col-md-6">
                                <label for="valor_residual" class="texto col-md-3 control-label">{{trans('contableM.vresidual')}}:</label>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="valor_residual" name="valor_residual" size="16" value="0.00" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" />
                                </div>
                            </div>


                        </div>
                        <div class="form-group{{ $errors->has('nota') ? ' has-error' : '' }}">
                            <label for="nota" class="texto col-md-2 control-label">{{trans('contableM.nota')}}:</label>
                            <div class="col-md-3">
                                <textarea type="number" class="form-control" cols="4" id="nota" name="nota" value="{{ old('nota') }}"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label for="vida_util" class="texto col-md-3 control-label">{{trans('contableM.vidautil')}} {{trans('laboratorio.anios')}}:</label>
                                <div class="col-md-3">
                                    <input type="number" class="form-control number validar" id="vida_util" name="vida_util" size="16" value="{{ old('vida_util') }}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('nota') ? ' has-error' : '' }}">
                            <label for="equipo_medico" class="texto col-md-2 control-label">{{trans('contableM.EquipoMedico')}}:</label>
                            <div class="col-md-3">
                                <input type="checkbox" class="check" name="equipo_medico" id="equipo_medico" value="0" data-toggle="tooltip" title="Al seleccionar se crea el equipo en el area de insumos">
                            </div>
                            <div class="col-md-6" id="check_equipo" style="display: none;">
                                <label for="prestamo" class="texto col-md-3 control-label">{{trans('contableM.EnCalidaddePrestamo')}}:</label>
                                <div class="col-md-3">
                                    <input type="checkbox" class="check" name="prestamo" id="prestamo" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="fotoaf" class="texto col-md-2 control-label">{{trans('contableM.FotoActivo')}}</label>
                            <div class="col-md-3">
                                <input type="file" name="fotoaf" id="fotoaf" accept="image/*">
                            </div>

                            <div class="col-md-6">
                                <label for="fotoacc" class="texto col-md-3 control-label">{{trans('contableM.FotoAccesorio')}}:</label>
                                <div class="col-md-3">
                                    <input type="file" name="fotoacc" id="fotoacc" accept="image/*">
                                </div>
                            </div>

                        </div>
                        <!--div class="form-group">
                            <label for="accesorios" class="texto col-md-2 control-label">{{trans('contableM.accesorio')}}:</label>
                            <div class="col-md-2">
                                <input type="checkbox" name="accesorios" id="accesorios" value="1">
                            </div>                            
                        </div-->
                        <div class="form-group">
                            <input name='contador_items' id='contador_items' type='hidden' value="0">
                            <div class="col-md-12 table-responsive">
                                <table id="items" class="table table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                                    <thead class="thead-dark">
                                        <tr class='well-darks'>
                                            <th width="30%" tabindex="0">{{trans('contableM.nombre')}}</th>
                                            <th width="20%" tabindex="0">{{trans('contableM.marca')}}</th>
                                            <th width="20%" tabindex="0">{{trans('contableM.modelo')}}</th>
                                            <th width="20%" tabindex="0">{{trans('contableM.serie')}}</th>
                                            <th width="10%" tabindex="0">
                                                <button type="button" class="btn btn-success btn-gray agregar_items">
                                                    <i class="glyphicon glyphicon-plus" aria-hidden="true"></i>
                                                </button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="fila-fija">
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="form-group col-xs-10 text-center">
                            <div class="col-md-6 col-md-offset-4">
                                <!-- <button type="submit" class="btn btn-default btn_add">
                                    <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('laboratorio.agregar')}}
                                </button> -->
                                <a onclick="guardar_activo();" type="button" class="btn btn-success" id="btn_guardar">{{trans('contableM.agregar')}}</a>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
    $(document).ready(function() {

        $('.select2_cuentas').select2({
            tags: false
        });

        $('.select2_color').select2({
            tags: true
        });

        $('.number').keypress(function(event) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault(); //stop character from entering input
            }
        });

        $('#tipo_id').on('select2:select', function(e) {
            var tipo_id = $('#tipo_id').val();
            $('#nomtipo').val(tipo_id);
            $('#nomtipo').select2().trigger('change');
        });

        $('#nomtipo').on('select2:select', function(e) {
            var nomtipo = $('#nomtipo').val();
            $('#tipo_id').val(nomtipo);
            $('#tipo_id').select2().trigger('change');
        });

        $('#subtipo_id').on('select2:select', function(e) {
            var subtipo_id = $('#subtipo_id').val();
            $('#nomsubtipo').val(subtipo_id);
            $('#nomsubtipo').select2().trigger('change');
        });

        $('#nomsubtipo').on('select2:select', function(e) {
            var nomsubtipo = $('#nomsubtipo').val();
            $('#subtipo_id').val(nomsubtipo);
            $('#subtipo_id').select2().trigger('change');
        });

        $('#responsable').on('select2:select', function(e) {
            var responsable = $('#responsable').val();
            $('#nomresponsable').val(responsable);
            $('#nomresponsable').select2().trigger('change');
        });

        $('#nomresponsable').on('select2:select', function(e) {
            var nomresponsable = $('#nomresponsable').val();
            $('#responsable').val(nomresponsable);
            $('#responsable').select2().trigger('change');
        });

        $('#producto').on('select2:select', function(e) {
            var producto = $('#producto').val();
            $('#nomproducto').val(producto);
            $('#nomproducto').select2().trigger('change');
        });

        $('#nomproducto').on('select2:select', function(e) {
            var nomproducto = $('#nomproducto').val();
            $('#producto').val(nomproducto);
            $('#producto').select2().trigger('change');
        });

        $('#org_funcional').on('select2:select', function(e) {
            var org_funcional = $('#org_funcional').val();
            $('#nom_org_funcional').val(org_funcional);
            $('#nom_org_funcional').select2().trigger('change');
        });

        $('#nom_org_funcional').on('select2:select', function(e) {
            var nom_org_funcional = $('#nom_org_funcional').val();
            $('#org_funcional').val(nom_org_funcional);
            $('#org_funcional').select2().trigger('change');
        });

        $('#acreedor').on('select2:select', function(e) {
            var acreedor = $('#acreedor').val();
            $('#nomacreedor').val(acreedor);
            $('#nomacreedor').select2().trigger('change');
        });

        $('#nomacreedor').on('select2:select', function(e) {
            var nomacreedor = $('#nomacreedor').val();
            $('#acreedor').val(nomacreedor);
            $('#acreedor').select2().trigger('change');
        });
    });

    $('#equipo_medico').on('change', function() {
        if (this.checked) {
            $('#check_equipo').show();
            $(this).val(1); // Cambia el valor a 1
        } else {
            $('#check_equipo').hide();
            $('#prestamo').prop('checked', false).val(0);
            $(this).val(0); // Vuelve a ponerlo en 0
        }
    });

    $('#prestamo').on('change', function() {
        if (this.checked) {
            $(this).val(1); // Cambia el valor a 1
        } else {
            $(this).val(0); // Vuelve a ponerlo en 0
        }
    });

    function guardar_color() {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{route('activofjo.guardar_color')}}",
            data: $("#form_activo").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data);
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });

    }

    function validarCierre() {
        <?php
        if (isset($min_date) && $min_date != null) {
        ?>
            let fechaAc = new Date();
            var fecha1 = '<?= $min_date; ?>';
            var fecha2 = document.getElementById("fecha_compra").value;
            const fechaInicial = new Date(fecha1);
            const fechaFinal = new Date(fecha2);
            if (fechaFinal <= fechaInicial) {
                alertas("error", "Recuerde!", 'No es posible modificar este registro debido a que pertenece a un período con cierre contable anterior.')
                const fechaFormateada = fechaAc.toISOString().split('T')[0];
                document.getElementById("fecha_compra").value = fechaFormateada;
            }
        <?php
        }
        ?>
    }

    function validar_campos() {
        let campo = document.querySelectorAll(".validar")
        let validar = false;
        for (let i = 0; i < campo.length; i++) {
            if (campo[i].value.trim() <= 0) {
                campo[i].style.border = '2px solid #CD6155';
                campo[i].style.borderRadius = '4px';
                validar = true;
            } else {
                campo[i].style.border = '1px solid #d2d6de';
                campo[i].style.borderRadius = '0px';
            }
        }
        return validar;
    }

    function guardar_activo() {
        var form = $('#form_activo')[0];
        var data = new FormData(form);
        if (!validar_campos()) {
            $.ajax({
                type: 'post',
                url: "{{route('afActivo.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: data,
                contentType: false, // Importante: No establecer el contentType
                processData: false, // Importante: No procesar los datos
                datatype: 'json',
                // data: $("#form_activo").serialize(),
                success: function(data) {
                    alertas(data.respuesta, data.respuesta.toUpperCase(), data.msj);
                    if (data.respuesta == "success") {
                        setTimeout(function() {
                            location.href = "{{route('afActivo.index')}}";
                        }, 2000);
                    }
                },
                error: function(data) {
                    alertas(data.respuesta, 'Error', data.msj);
                    console.log(data);
                }
            });
        } else {
            $('#boton_guardar').prop("disabled", false);
            alertas('error', 'ERROR', `{{trans('proforma.camposvacios')}}`)
        }
    }

    function guardar_serie() {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{route('activofjo.guardar_serie')}}",
            data: $("#form_activo").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data);
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
    }

    function serieac(id) {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/activofijo/serie_ac')}}/" + id,
            data: $("#form_activo").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data);
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
    }

    function marcaac(id) {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{url('activosfijos/activofijo/marca_ac')}}/" + id,
            data: $("#form_activo").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data);
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
    }

    function guardar_marca() {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{route('activofjo.guardar_marca')}}",
            data: $("#form_activo").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data);
                //alert(data)
            },
            error: function(data) {
                //console.log(data);
                //alert(data)
            }
        });
    }

    function guardar_responsable() {
        $.ajax({
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            url: "{{route('activofjo.guardar_responsable')}}",
            data: $("#form_activo").serialize(),
            datatype: 'json',
            success: function(data) {
                console.log(data);
                //alert(data)
            },
            error: function(data) {
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
                            <select id="marca_ac" name="marca_ac[]"  class="form-control select2_color"  onchange="marcaac(${id});" style="width:80%; height:25px;">
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
                            <select id="serie_ac" name="serie_ac[]" class="form-control select2_color" onchange="serieac(${id});" style="width:80%; height:25px;">
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
        document.getElementById('contador_items').value = id;

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
                swal("Error!", "{{trans('contableM.valornoper')}}", "error");
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
            swal("Error!", "{{trans('contableM.valornoper')}}", "error");
            $('#codigo_num').val('');
        }
    }
</script>
@endsection