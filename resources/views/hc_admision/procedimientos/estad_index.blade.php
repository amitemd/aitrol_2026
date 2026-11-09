
<style type="text/css">
	.row{
		margin: 0;
	}
</style>

@php 
	$mes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; 
@endphp


	

	<div id="examplea_wrapper" >
        <div class="row">
          <div class="table-responsive">
            <h4 style="background-color: #009999;text-align: center;color: white;">PRODUCCION PROCEDIMIENTOS POR MES DEL AÑO {{$anio}}</h4>
            <table id="examplea" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="examplea_info" style="font-size: 11px;">
              <thead>
                <tr role="row">
                  <th >N.</th>
                  <th >Mes</th>
                  <th >Cant. Público</th>
                  <th >Cant. Privado</th>
                  <th >Cant. Particular</th>
                  <th >Cantidad Total</th>
                  <th style="background-color: #ffe6e6">Val. Público</th>
                  <th style="background-color: #ffe6e6">Val. Privado</th>
                  <th style="background-color: #ffe6e6">Val. Particular</th>
                  <th style="background-color: #ffe6e6">Valor Total</th>
                  <th >Acción</th>
                </tr>
              </thead>
              <tbody>

             @foreach($ordenes_valores_totales as $value)
             	@php
             		$v_pub = 0;
             		$publico = $ordenes_valores_publicos->where('anio',$value->anio)->where('mes',$value->mes)->first();
             		if(!is_null($publico)){
						$v_pub = $publico->total;
             		}
             		$v_pri = 0;
             		$privado = $ordenes_valores_privados->where('anio',$value->anio)->where('mes',$value->mes)->first();
             		if(!is_null($privado)){
						$v_pri = $privado->total;
             		}
             		$v_par = 0;
             		$particular = $ordenes_valores_particulares->where('anio',$value->anio)->where('mes',$value->mes)->first();
             		if(!is_null($particular)){
						$v_par = $particular->total;
             		}
                
             		
             	@endphp
                <tr role="row" >
                  <td><b>{{$value->mes}}</b></td>
                  <td><b>{{$mes[$value->mes -1]}}</b></td>
                  <td>{{$ordenes_valores_publicos_orden->where('anio',$value->anio)->where('mes',$value->mes)->count()}}</td>
                  <td>{{$ordenes_valores_privados_orden->where('anio',$value->anio)->where('mes',$value->mes)->count()}}</td>
                  <td>{{$ordenes_valores_particulares_orden->where('anio',$value->anio)->where('mes',$value->mes)->count()}}</td>
                  <td>{{$ordenes_valores_totales_orden->where('anio',$value->anio)->where('mes',$value->mes)->count()}}</td>
                  <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($v_pub,2),2) }}</td>
                  <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($v_pri,2),2) }}</td>
                  <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($v_par,2),2) }}</td>
                  <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($value->total,2),2) }}</td>
                  <td><center><button class="btn btn-info btn-sm" onclick="ver_mes_doctor('{{$anio}}','{{$value->mes}}')">Por Doctor</button></center></td>
                </tr>
              @endforeach
              </tbody>
            </table>
          </div>
          <div class="col-md-12">
            <center>
              <canvas id="canvas2" style="max-width: 70%;"></canvas>
            </center>
          </div>
        </div>

      </div>

	


<script type="text/javascript">
	$('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });



  function ver_mes_doctor(anio, mes){
    $.ajax({
            type: 'get',
            url:"{{url('produccion/estadistico/mes')}}/"+anio+'/'+mes,
            success: function(datahtml){
                $("#carga_js").html(datahtml);
            },
            error: function(data){
                console.log(data);
            }
        })  
  } 



</script> 