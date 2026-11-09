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
            <h3 class="card-title">{{ trans('hospitalizacion.CargarPlanilla') }}</h3>
            <div class="card-tools pull-right">
                <button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary" onclick="aplicar_todas()">{{ trans('hospitalizacion.AplicarCargos') }}</button>
        </div>
            <div class="card-body">
            <div class="row">
                <div class="col-md-6"><h4>{{ trans('hospitalizacion.Cargos') }}</h4>
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
                <div class="col-md-6"><h4>{{ trans('hospitalizacion.DetallePlanilla') }}</h4>
                    <div class="card-body table-responsive" id="tablaajax">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ trans('hospitalizacion.No') }}</th>
                                    <th>{{ trans('hospitalizacion.Detalle') }}</th>
                                    <th>{{ trans('hospitalizacion.Cantidad') }}</th>
                                    <th>{{ trans('hospitalizacion.Valor') }}</th>
                                    <th>{{ trans('hospitalizacion.Total') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $cont = 1 @endphp    
                                @foreach ($detalles as $detalle)
                                <tr>
                                    <td>{{ $cont }}</td>
                                    <td>{{ $detalle->observacion }}</td>
                                    <td>{{ $detalle->cantidad }}</td>
                                    <td>{{ $detalle->precio }}</td>
                                    <td>{{ $detalle->cantidad * $detalle->precio }}</td>
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
</div>

<script type="text/javascript">
    function buscador() {
        $.ajax({
            url: "{{ route('buscador.ajax_emergencia') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#form_buscar').serialize(),
            type: 'POST',
            data: {'_token': '{{ csrf_token() }}'},
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
            url: "{{ route('hospital.aplicar_todos',['id' => $id_solicitud])}}",
           
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
    
</script>
@endsection
