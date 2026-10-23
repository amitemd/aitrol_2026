@extends('layouts.app-template-h')
@section('content')
<div class="content">
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-9">
                <h3>
                    MASTER
                </h3>
            </div>

        </div>
    </section>

    <div class="card card-primary">

        <div class="card-header with-border">
            <h3 class="card-title">{{ trans('hospitalizacion.CargarPlanilla') }} {{ $solicitud->paciente->apellido1 }} {{ $solicitud->paciente->apellido2 }} {{ $solicitud->paciente->nombre1 }} {{ $solicitud->paciente->nombre2 }}</h3>
            <div class="card-tools pull-right">
                <button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div><br>
            @php
                $seguros = \Sis_medico\Seguro::all();
            @endphp
            <h3> Seguro Principal: </h3>
            <div class="input-group col-md-12">
                <select class="form-control input-sm" name="id_tipo" id="id_tipo" required onchange="cargar_seguro();guardar_auto();">
                    <!--option value="">Seleccione ...</option-->
                    @foreach($seguros as $seguro)
                    <option @if($seguro->id == 2) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if($solicitud->id == 28)
        <h3>  Registro Completo</h3>
        @else
        <div class="col-md-3">
            <button class="btn btn-primary" onclick="aplicar_todas()">{{ trans('hospitalizacion.AplicarCargos') }}</button>
        </div>
        @endif
        <div class="card-body">
            <div class="row">
                <div class="col-md-5"><h4>{{ trans('hospitalizacion.Cargos') }}</h4>
                    <div class="card-body table-responsive" id="tablaajax">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('hospitalizacion.Aplicar') }}</th>
                                    <th>{{ trans('hospitalizacion.Fecha') }}</th>
                                    <th>{{ trans('hospitalizacion.Paso') }}</th>
                                    <th>{{ trans('hospitalizacion.Valor') }}</th>
                                    <!--th>{{ trans('hospitalizacion.Valor_Aplicado') }}</th-->
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($logs as $log)
                                @php $estado_insumo = $log->paso->paso_insumos->first(); @endphp
                                @php 
                                    $insumo    = null;$valor = 0;
                                    if(!is_null($estado_insumo)){
                                        $insumo = $estado_insumo->insumo;
                                        if(!is_null($insumo)){
                                            $producto_insumo = Sis_medico\Ct_productos_insumos::where('id_insumo',$insumo->id)->first();
                                            if(!is_null($producto_insumo)){ 
                                                $producto = $producto_insumo->ct_producto;
                                                if(!is_null($producto)){
                                                    $valor = $producto->valor_total_paq;
                                                }
                                            } 
                                        }        
                                    } 
                                @endphp
                                @php
                                    $detalle = Sis_medico\Planilla_Detalle::where('id_log_solic',$log->id)->where('estado',1)->where('check',1)->first();
                                @endphp
                                @if(is_null($detalle))
                                <tr>
                                    <td><input type="checkbox" name="{{$log->id}}" id="{{$log->id}}" onclick="aplicar('{{$log->id}}')"></td>
                                    <td>{{ substr($log->fecha_ingreso, 0, 10) }}</td>
                                    <td>{{ $log->paso->nombre_paso}}</td>
                                    <td>{{ $valor }}</td>
                                    <!--td>@if(!is_null($detalle)) {{ $valor }} @endif</td-->
                                </tr>
                                @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div> 
                <div class="col-md-7"><h4>{{ trans('hospitalizacion.DetallePlanilla') }}</h4>
                    <div class="card-body table-responsive" id="tablaajax">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('hospitalizacion.No') }}</th>
                                    <th>{{ trans('hospitalizacion.Fecha') }}</th>
                                    <th>{{ trans('hospitalizacion.Detalle') }}</th>
                                    <th>{{ trans('hospitalizacion.Cantidad') }}</th>
                                    <th>{{ trans('hospitalizacion.Valor') }}<br>(COSTO)</th>
                                    <th>{{ trans('hospitalizacion.Total') }}</th>
                                    <th>{{ trans('hospitalizacion.Accion') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $cont = 1 @endphp    
                                @foreach ($detalles as $detalle)
                                <tr>
                                    <td>{{ $cont }}</td>
                                    <td>{{ substr($detalle->created_at,0,10)}}</td>
                                    <td>{{ $detalle->observacion }} <br> <b> {{$detalle->producto->nombre}} </b> </td>
                                    <td>{{ $detalle->cantidad }}</td>
                                    <td>{{ $detalle->precio }}</td>
                                    <td>{{ $detalle->cantidad * $detalle->precio }}</td>
                                    <td>@if($solicitud->id != 28)
                                        <button type="button" class="btn btn-warning btn-sm" onclick="cargar_recibo_planilla('{{$detalle->id}}')">Aplicar</button>
                                        @endif
                                    </td>
                                </tr>
                                @php $cont = $cont + 1 @endphp
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>    
            </div>
        </div>
    </div>
    <div class="card card-primary">

        <div class="card-header with-border">
            <h3 class="card-title">Recibos de Cobro</h3>
            <div class="card-tools pull-right">
                <button type="button" class="btn btn-success btn-sm" onclick="crear_recibos()"> Crear</button>
            </div>
        </div>
       <input type="hidden" name="id_recibo" id="id_recibo" >
        <div class="card-body">
            <div class="row">
                <div class="col-md-6" id="listar_recibo">
                </div>
                <div class="col-md-6" id="editar_recibo">
                </div>    
                  
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    listar_recibos();

    function listar_recibos(){
        $.ajax({
            url: "{{ route('hospital.listar_recibo_hospital' , ['id_solicitud' => $id_solicitud]) }}",
           
            type: 'GET',
            dataType: 'html',
            beforeSend: function() {

                //$('#loading').show();
            },
            success: function(datahtml, data) {
                //$('#loading').hide();
                $("#listar_recibo").empty().html(datahtml).fadeIn();
            },
            error: function(xhr, status) {
                alert('Existió un problema en la Tabla');

            },
        });    
    }
    function crear_recibos(){
        $.ajax({
            url: "{{ route('hospital.crear_recibo_hospital' , ['id_solicitud' => $id_solicitud]) }}",
           
            type: 'GET',
            dataType: 'html',
            beforeSend: function() {

                //$('#loading').show();
            },
            success: function(datahtml, data) {
                //$('#loading').hide();
                listar_recibos();
                //$("#editar_recibo").empty().html(datahtml).fadeIn();
            },
            error: function(xhr, status) {
                alert('Existió un problema en la Tabla');

            },
        });    
    }

    function buscador() {
        $.ajax({
            url: "{{ route('buscador.ajax_emergencia') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#form_buscar').serialize(),
            type: 'GET',
            dataType: 'html',
            beforeSend: function() {

                //$('#loading').show();
            },
            success: function(datahtml, data) {
                //$('#loading').hide();
                $("#tablaajax").empty().html(datahtml).fadeIn();
            },
            error: function(xhr, status) {
                alert('Existió un problema en la Tabla');

            },
        });
    }
    function buscador() {
        $.ajax({
            url: "{{ route('buscador.ajax_emergencia') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#form_buscar').serialize(),
            type: 'GET',
            dataType: 'html',
            beforeSend: function() {

                //$('#loading').show();
            },
            success: function(datahtml, data) {
                //$('#loading').hide();
                $("#tablaajax").empty().html(datahtml).fadeIn();
            },
            error: function(xhr, status) {
                alert('Existió un problema en la Tabla');

            },
        });
    }

    function aplicar(id){

        $.ajax({
            url: "{{ url('planillar_hospital/detalle/aplicar') }}/"+id,
           
            type: 'GET',
            dataType: 'html', 
           
            success: function(data) {
                console.log(data); 
                location.reload();   
            },
            error(error) {
                console.log(error);
                alert('Existió un problema en la Tabla');

            },
        });

    }

    function aplicar_todas(){

        $.ajax({
            url: "{{ route('hospital.aplicar_todos',['id_solicitud' => $id_solicitud])}}",
           
            type: 'GET',
            dataType: 'html', 
           
            success: function(data) {
                console.log(data); 
                location.reload();   
            },
            error(error) {
                console.log(error);
                alert('Existió un problema en la Tabla');

            },
        });
    }

    function cargar_recibo_planilla(id){

        //alert("Solo disponible en version pagada");
        console.log("xxxxx");
        console.log(id);
        var id_recibo = $('#id_recibo').val();
        $.ajax({
            url: "{{ url('rc_hospital/carga_planilla/recibo')}}/"+ id + "/" + id_recibo,
           
            type: 'GET',
            dataType: 'html', 
           
            success: function(data) {
                console.log(data); 
                editar(id_recibo);  
            },
            error(error) {
                console.log(error);
                alert('Existió un problema en la Tabla');

            },
        });

    }
    
</script>
@endsection