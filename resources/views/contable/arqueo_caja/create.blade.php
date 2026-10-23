@extends('contable.arqueo_caja.base')
@section('action-content')
@php
  $tipo_usuario  = Auth::user()->id_tipo_usuario;
@endphp
<section class="content">
    <div class="box">
        <div class="box-header">
          <div class="row">
            <div class="col-md-10"><h3 class="box-title">Detalle Arqueo de Caja - Fecha del Proceso: {{ $arqueo_caja->fecha_proceso }} @if($arqueo_caja->id_usuario != null) - Usuario: {{ $arqueo_caja->usuario->apellido1 }} {{ $arqueo_caja->usuario->apellido2 }} {{ $arqueo_caja->usuario->nombre1 }} {{ $arqueo_caja->usuario->nombre2 }} @endif</h3></div>
            <div class="col-md-2"><a class="btn btn-primary" href="{{ route('reporte.index_cierre')}}">Regresar</a></div>
          </div>
          
        </div>
        <div class="box-body">
          <div class="row">
          
            <div class="col-md-4"><b>Efectivo A Recaudar: </b>$ {{ $arqueo_caja->valor_efectivo }}</div> 
           
            <div class="col-md-4"><b>Ingresado: </b><span id="ingresado" style="font-size: 22px">{{ number_format($total, 2, ',', ' ') }}</span></div>
            <div class="col-md-4"><b>Saldo: </b><span id="saldo" style="font-size: 22px;@if($saldo < 0)color: red;@endif">{{ number_format($saldo, 2, ',', ' ') }}</span></div>
            @php $hoy = date('Y-m-d') @endphp
            @if($tipo_usuario == 1 or $arqueo_caja->fecha_proceso == $hoy)
            <div class="col-md-2"><a href="{{route('ct_arqueo_caja.recalcular_valor_efectivo_arqueo', ['id'=>$arqueo_caja->id])}}"  class="btn btn-warning"> RECALCULAR </a></div>
            @endif
          </div>
            <form id="crear_arqueo" method="post" action="">
                {{ csrf_field() }}
                <div class="row">
                  <div class="form-group col-md-3">
                    <label for="vales" class="control-label" style="font-size: 18px; font-weight: bold;">Vales</label>
                    <input type="text" required name="vales" id="vales" value="{{$arqueo_caja->vales}}" class="form-control input-sm" onchange="actualiza_cabe()" >    
                  </div>
                  <div class="form-group col-md-3">
                    <label for="base" class="control-label" style="font-size: 18px; font-weight: bold;">Base</label>
                    <input type="text" required name="base" id="base" value="{{$arqueo_caja->base}}" class="form-control input-sm" onchange="actualiza_cabe()" >    
                  </div>
                </div>
                <div class="form-group col-md-6">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row" id="listado_pro_agrup">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr>
                                            <th>Denominacion</th>
                                            <th> Cantidad </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detalles->where('valor','<','1.00') as $detalle)
                                        <tr>
                                            <td>{{$detalle->denominacion}}</td>
                                            <td> <input type="text" class="form-control input-sm" name="detalle_cantidad{{$detalle->id}}" id="detalle_cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" onchange="actualizar_cantidad({{$detalle->id}})"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group col-md-6">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row" id="listado_pro_agrup">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr>
                                            <th>Denominacion</th>
                                            <th> Cantidad </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detalles->where('valor','>','0.90') as $detalle)
                                        <tr>
                                            <td>{{$detalle->denominacion}}</td>
                                            <td> <input type="text" class="form-control input-sm" name="detalle_cantidad{{$detalle->id}}" id="detalle_cantidad{{$detalle->id}}" value="{{$detalle->cantidad}}" onchange="actualizar_cantidad({{$detalle->id}})"></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                                            
            </form>

        </div>
    </div>
</section>
<!--  -->


@endsection

<script>
    function actualizar_cantidad(id){
      let valor = document.getElementById("detalle_cantidad"+id).value;
      
      $.ajax({
        type: 'post',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        url:"{{route('ct_arqueo_caja.update_arqueo')}}",
        data:{
            'id_detalle': id,
            'valor': valor
          },
        datatype: 'html',
        success: function(data){
            //console.log(data);
            $('#ingresado').text(data.total);
            $('#saldo').text(data.saldo);
            if(parseFloat(data.saldo) < 0){
              console.log($('#saldo'));
              $('#saldo').css('color', 'red');
            }else{
              $('#saldo').css('color', 'black');  
            }

            //alert(data)
        },
        error: function(data){
          //console.log(data);
          //alert(data)
        }
      })

    }

    function actualiza_cabe(){
      let base = document.getElementById("base").value;
      let vales = document.getElementById("vales").value;
      
      $.ajax({
        type: 'post',
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        url:"{{route('ct_arqueo_caja.update_cabecera')}}",
        data:{
            'id'   : '{{ $arqueo_caja->id }}',
            'base': base,
            'vales': vales,
          },
        datatype: 'html',
        success: function(data){
            //console.log(data);
            $('#ingresado').text(data.total);
            $('#saldo').text(data.saldo);
            if(parseFloat(data.saldo) < 0){
              console.log($('#saldo'));
              $('#saldo').css('color', 'red');
            }else{
              $('#saldo').css('color', 'black');  
            }

            //alert(data)
        },
        error: function(data){
          //console.log(data);
          //alert(data)
        }
      })
    }
</script>