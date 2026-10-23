
<style type="text/css">
	
</style>

@php 
	$mes = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; 
@endphp

<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title">PRODUCCION PROCEDIMIENTOS POR MES DE COBRO /AÑO {{$anio}}</h3>	
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body no-padding" style="">  

  	<div id="examplea_wrapper" >
      <div class="row">
        <div class="table-responsive">
          <table id="examplea" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="examplea_info" style="font-size: 13px;">
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
  </div>
</div>    
<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title">PRODUCCION PROCEDIMIENTOS POR MES DE ORDEN /AÑO {{$anio}}</h3>  
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body no-padding" style=""> 
    <div id="exampleb_wrapper" >
          <div class="row">
            <div class="table-responsive">
              <table id="exampleb" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="exampleb_info" style="font-size: 13px;">
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

               @foreach($ordenes_valores_totales2 as $value)
                @php
                  $v_pub = 0;
                  $publico = $ordenes_valores_publicos2->where('anio',$value->anio)->where('mes',$value->mes)->first();
                  if(!is_null($publico)){
              $v_pub = $publico->total;
                  }
                  $v_pri = 0;
                  $privado = $ordenes_valores_privados2->where('anio',$value->anio)->where('mes',$value->mes)->first();
                  if(!is_null($privado)){
              $v_pri = $privado->total;
                  }
                  $v_par = 0;
                  $particular = $ordenes_valores_particulares2->where('anio',$value->anio)->where('mes',$value->mes)->first();
                  if(!is_null($particular)){
              $v_par = $particular->total;
                  }
                  
                  
                @endphp
                  <tr role="row" >
                    <td><b>{{$value->mes}}</b></td>
                    <td><b>{{$mes[$value->mes -1]}}</b></td>
                    <td>{{$ordenes_valores_publicos_orden2->where('anio',$value->anio)->where('mes',$value->mes)->count()}}</td>
                    <td>{{$ordenes_valores_privados_orden2->where('anio',$value->anio)->where('mes',$value->mes)->count()}}</td>
                    <td>{{$ordenes_valores_particulares_orden2->where('anio',$value->anio)->where('mes',$value->mes)->count()}}</td>
                    <td>{{$ordenes_valores_totales_orden2->where('anio',$value->anio)->where('mes',$value->mes)->count()}}</td>
                    <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($v_pub,2),2) }}</td>
                    <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($v_pri,2),2) }}</td>
                    <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($v_par,2),2) }}</td>
                    <td style="background-color: #ffe6e6;color: red;">$ {{number_format(round($value->total,2),2) }}</td>
                    <td><center><button class="btn btn-info btn-sm" onclick="ver_mes_doctor_od('{{$anio}}','{{$value->mes}}')">Por Doctor</button></center></td>
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
  </div>
</div>    
<?php /*
<div class="box box-success">
  <div class="box-header with-border">
    <h3 class="box-title">ESTADISTICO ORDENES DE PROCEDIMIENTOS DEL AÑO {{$anio}}</h3>  
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body no-padding" style=""> 
    <div id="exampleb_wrapper" >
          <div class="row">
            <div class="table-responsive">
              <h4 style="background-color: #009999;text-align: center;color: white;">PROCEDIMIENTOS POR AÑO</h4>
              <table id="exampleb" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="exampleb_info" style="font-size: 13px;">
                <thead>
                  <tr role="row">
                    <th >Año</th>
                    <th >Cantidad Total</th>
                    <th >Total</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($ordenes_anio as $value)
                  <tr role="row" >
                    <td><b>{{$value->anio}}</b></td>
                    <td>{{$value->cantidad}}</td>
                    <td></td>
                    
                  </tr>
                @endforeach 
              
                </tbody>
              </table>
            </div>
            <div class="col-md-12">
              <center>
                <canvas id="canvas" style="max-width: 70%;"></canvas>
              </center>
            </div>
          </div>

    </div>

    <div id="exampleb_wrapper" >
          <div class="row">
            <div class="table-responsive">
              <h4 style="background-color: #009999;text-align: center;color: white;">PROCEDIMIENTOS POR MES DEL AÑO {{$anio}}</h4>
              <table id="exampleb" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="exampleb_info" style="font-size: 13px;">
                <thead>
                  <tr role="row">
                    @php $mes = ['', 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];@endphp
                    <th>N.</th>
                    <th>Mes</th>
                    <th>Cantidad Total</th>
                    <th>Valor Total</th>
                    <th>Accion</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach($ordenes_anio_mes as $value)
                <tr role="row" >
                  <td><b>{{$value->mes}}</b></td>
                  <td><b>{{$mes[$value->mes]}}</b></td>
                  <td>{{$value->cantidad}}</td>
                  <td></td>
                  <td><center><button class="btn btn-info btn-sm" onclick="anio_mes_doctor('{{$anio}}','{{$value->mes}}')">Por Doctor</button></center></td>
                </tr>
              @endforeach
              
                </tbody>
              </table>
            </div>
            <div class="col-md-12">
              <center>
                <canvas id="canvas3" style="max-width: 70%;"></canvas>
              </center>
            </div>
          </div>

    </div>
  </div>
</div> 
*/ ?>   

	
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/bower_components/datatables.net/js/jquery.dataTables.min.js") }}"></script>
<script src="{{ asset ("/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js") }}"></script>

<script type="text/javascript">
	$('#examplea').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

  $('#exampleb').DataTable({
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
                $("#div_grafico").html(datahtml);
            },
            error: function(data){
                console.log(data);
            }
        })  
  } 

  function ver_mes_doctor_od(anio, mes){
    $.ajax({
            type: 'get',
            url:"{{url('produccion/estadistico/orden/mes')}}/"+anio+'/'+mes,
            success: function(datahtml){
                $("#div_grafico").html(datahtml);
            },
            error: function(data){
                console.log(data);
            }
        })  
  } 

  function anio_mes_doctor(anio, mes){
    $.ajax({
            type: 'get',
            url:"{{url('produccion/estadistico/anio_mes_doc')}}/"+anio+'/'+mes,
            success: function(datahtml){
                $("#div_grafico").html(datahtml);
            },
            error: function(data){
                console.log(data);
            }
        })  
  } 

</script> 

<?php /*
<script>
    function random_bg_color() {
      var x = Math.floor(Math.random() * 256);
      var y = Math.floor(Math.random() * 256);
      var z = Math.floor(Math.random() * 256);
      var bgColor = "rgb(" + x + "," + y + "," + z + ")";
    return bgColor;
    }
    var barChartData = {
      labels: [@foreach($ordenes_anio as $value) '{{$value->anio}}',  @endforeach],
      datasets: [
      {
        label: 'Total',
        backgroundColor: random_bg_color(),
        borderWidth: 1,
        data: ''
      }

      ]

    };

    var ctx = document.getElementById('canvas').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData,
        options: {
          responsive: true,
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Examenes por AÑO'
          },
          scales: {
            yAxes: [{
              display: true,
              ticks:{
                beginAtZero: true,
              }
            }]
          }
        }
      });
</script>
*/ ?>
<script>
    
    function random_bg_color() {
      var x = Math.floor(Math.random() * 256);
      var y = Math.floor(Math.random() * 256);
      var z = Math.floor(Math.random() * 256);
      var bgColor = "rgb(" + x + "," + y + "," + z + ")";
    return bgColor;
    }
    var barChartData_2 = {
      labels: ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'],
      datasets: [
      {
        label: 'Total',
        backgroundColor: random_bg_color(),
        borderWidth: 1,
        data: ''
      }
      ]

    };

    /*var ctx = document.getElementById('canvas3').getContext('2d');
      window.myBar = new Chart(ctx, {
        type: 'bar',
        data: barChartData_2,
        options: {
          responsive: true,
          legend: {
            position: 'top',
          },
          title: {
            display: true,
            text: 'Examenes por Mes'
          },
          scales: {
            yAxes: [{
              display: true,
              ticks:{
                beginAtZero: true,
              }
            }]
          }
        }
      });*/
</script>
