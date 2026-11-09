
<style type="text/css">
	.row{
		margin: 0;
	}

	table{
		font-size: 11px;		
	}
</style>

@php 
	$mes_txt = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; 
@endphp


	

	<div class="col-md-12">
        <div class="row">
          <div class="col-12" >
            <center><h3 class="box-title" id="titulo1" ></h3></center>
          </div>  
          <div class="col-lg-6 col-sm-12">
            
            <div id="example2_wrapper" >
              <center>
                <div class="row">
                  <div class="table-responsive">
                    
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" stordenes_valores_totalesyle="font-size: 12px;">
                      <thead>
                        <tr role="row" style="background-color: #009999; color: white;">
                          <th >Doctor</th>
                          <th >Ordenes</th>
                          <th >Valor ($)</th>   
                          <th >Comision ($)</th>                               
                        </tr>
                      </thead>
                      <tbody>@php $total1=0;$total2=0;$total3=0;$total4=0; @endphp
                        @foreach($ordenes_valores_totales as $value)
                          @php $publico=0;$privado=0;$particular=0;
                          	$doctor = $doctores->where('id',$value->id_doctor)->first();
                          	$cantidad = $ordenes_valores_totales_orden->where('anio',$value->anio)->where('mes',$value->mes)->where('id_doctor',$value->id_doctor)->count();
                          	$total1 = $total1 + $value->total;
                          	$arr_publico = $ordenes_valores_publicos->where('id_doctor',$value->id_doctor)->first();
   							if(!is_null($arr_publico)){
								$publico =$arr_publico->total*0.001;
							}
							$arr_privado = $ordenes_valores_privados->where('id_doctor',$value->id_doctor)->first();
   							if(!is_null($arr_privado)){
								$privado =$arr_privado->total*0.015;
							}
							$arr_particular = $ordenes_valores_particulares->where('id_doctor',$value->id_doctor)->first();
   							if(!is_null($arr_particular)){
								$particular =$arr_particular->total*0.015;
							}
                          @endphp
                          <tr>
                            <td>{{$doctor->apellido1}} {{$doctor->nombre1}}</td>
                            <td style="text-align: right;">{{$cantidad}}</td>
                            <td style="text-align: right;">$ {{number_format(round($value->total,2),2) }}</td>
                            <td style="text-align: right;">$ {{number_format(round($publico+$privado+$particular,2),2) }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </center>
            </div>
             
          </div> 
          <div class="col-lg-6 col-sm-12">
              <canvas id="canvas_datos" ></canvas>
          </div> 
        </div>
    </div>

    <div class="col-md-12">
        <div class="row">
          <div class="col-12" >
            <center><h3 class="box-title" id="titulo2" ></h3></center>
          </div>  
          <div class="col-lg-6 col-sm-12">
            
            <div id="example2_wrapper" >
              <center>
                <div class="row">
                  <div class="table-responsive">
                    
                    <table id="example2a" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" >
                      <thead>
                        <tr role="row" style="background-color: #009999; color: white;">
                          <th >Doctor</th>
                          <th >Ordenes</th>
                          <th >Valor ($)</th>   
                          <th >Comision ($)</th>                               
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($ordenes_valores_publicos as $value)
                          @php
                          	$doctor = $doctores->where('id',$value->id_doctor)->first();
                          	$cantidad = $ordenes_valores_publicos_orden->where('anio',$value->anio)->where('mes',$value->mes)->where('id_doctor',$value->id_doctor)->count();
                          	$total2 = $total2 + $value->total;
                          @endphp
                          <tr>
                            <td>{{$doctor->apellido1}} {{$doctor->nombre1}}</td>
                            <td style="text-align: right;">{{$cantidad}}</td>
                            <td style="text-align: right;">$ {{number_format(round($value->total,2),2) }}</td>
                            <td style="text-align: right;">$ {{number_format(round($value->total*0.001,2),2) }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </center>
            </div>
             
          </div> 
          <div class="col-lg-6 col-sm-12">
              <canvas id="canvas_datos2" ></canvas>
          </div> 
        </div>
    </div>

    <div class="col-md-12">
        <div class="row">
          <div class="col-12" >
            <center><h3 class="box-title" id="titulo3" ></h3></center>
          </div>  
          <div class="col-lg-6 col-sm-12">
            
            <div id="example2_wrapper" >
              <center>
                <div class="row">
                  <div class="table-responsive">
                    
                    <table id="example2b" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" >
                      <thead>
                        <tr role="row" style="background-color: #009999; color: white;">
                          <th >Doctor</th>
                          <th >Ordenes</th>
                          <th >Valor ($)</th>   
                          <th >Comision ($)</th>                               
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($ordenes_valores_privados as $value)
                          @php
                          	$doctor = $doctores->where('id',$value->id_doctor)->first();
                          	$cantidad = $ordenes_valores_privados_orden->where('anio',$value->anio)->where('mes',$value->mes)->where('id_doctor',$value->id_doctor)->count();
                          	$total3 = $total3 + $value->total;
                          @endphp
                          <tr>
                            <td>{{$doctor->apellido1}} {{$doctor->nombre1}}</td>
                            <td style="text-align: right;">{{$cantidad}}</td>
                            <td style="text-align: right;">$ {{number_format(round($value->total,2),2) }}</td>
                            <td style="text-align: right;">$ {{number_format(round($value->total*0.015,2),2) }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </center>
            </div>
             
          </div> 
          <div class="col-lg-6 col-sm-12">
              <canvas id="canvas_datos3" ></canvas>
          </div> 
        </div>
    </div>

	<div class="col-md-12">
        <div class="row">
          <div class="col-12" >
            <center><h3 class="box-title" id="titulo4" ></h3></center>
          </div>  
          <div class="col-lg-6 col-sm-12">
            
            <div id="example2_wrapper" >
              <center>
                <div class="row">
                  <div class="table-responsive">
                    
                    <table id="example2c" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" >
                      <thead>
                        <tr role="row" style="background-color: #009999; color: white;">
                          <th >Doctor</th>
                          <th >Ordenes</th>
                          <th >Valor ($)</th>   
                          <th >Comision ($)</th>                               
                        </tr>
                      </thead>
                      <tbody>
                        @foreach($ordenes_valores_particulares as $value)
                          @php
                          	$doctor = $doctores->where('id',$value->id_doctor)->first();
                          	$cantidad = $ordenes_valores_particulares_orden->where('anio',$value->anio)->where('mes',$value->mes)->where('id_doctor',$value->id_doctor)->count();
                          	$total4 = $total4 + $value->total;
                          @endphp
                          <tr>
                            <td>{{$doctor->apellido1}} {{$doctor->nombre1}}</td>
                            <td style="text-align: right;">{{$cantidad}}</td>
                            <td style="text-align: right;">$ {{number_format(round($value->total,2),2) }}</td>
                            <td style="text-align: right;">$ {{number_format(round($value->total*0.015,2),2) }}</td>
                          </tr>
                        @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
              </center>
            </div>
             
          </div> 
          <div class="col-lg-6 col-sm-12">
              <canvas id="canvas_datos4" ></canvas>
          </div> 
        </div>
    </div>
	
<script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>

<script src="{{ asset ("bower_components/datatables.net/js/jquery.dataTables.min.js")}}"></script>


<script type="text/javascript">
	$('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

    $('#example2a').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

    $('#example2b').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

    $('#example2c').DataTable({
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

  	var config = {
      type: 'pie',

      data: {
        datasets: [{

          data: [
            @foreach($ordenes_valores_totales as $value)
              '{{round($value->total,2)}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($ordenes_valores_totales as $value)
            	@php $doctor = $doctores->where('id',$value->id_doctor)->first(); @endphp
              '{{$doctor->color}}',
            @endforeach

          ],
        }],
        labels: [
          @foreach($ordenes_valores_totales as $value)
          	@php $doctor = $doctores->where('id',$value->id_doctor)->first(); @endphp
            '{{$doctor->apellido1}} {{$doctor->nombre1}}',
          @endforeach
        ]
      },
      options: {
        legend: {
            position: 'left',
            display: true,
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var item_arr = data.labels[tooltipItem.index].split(':');
                    var label = item_arr[0];
                    
                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$total1}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%'; 

                    

                    return label;
                }
            }
        }
      }
      
    };
    $('#titulo1').text('Producción de Procedimientos por Doctor {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total1,2)}}');
    var ctx = document.getElementById('canvas_datos').getContext('2d');

      window.myPie = new Chart(ctx, config);

    var config = {
      type: 'pie',

      data: {
        datasets: [{

          data: [
            @foreach($ordenes_valores_publicos as $value)
              '{{round($value->total,2)}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($ordenes_valores_publicos as $value)
            	@php $doctor = $doctores->where('id',$value->id_doctor)->first(); @endphp
              '{{$doctor->color}}',
            @endforeach

          ],
        }],
        labels: [
          @foreach($ordenes_valores_publicos as $value)
          	@php $doctor = $doctores->where('id',$value->id_doctor)->first(); @endphp
            '{{$doctor->apellido1}} {{$doctor->nombre1}}',
          @endforeach
        ]
      },
      options: {
        legend: {
            position: 'left',
            display: true,
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var item_arr = data.labels[tooltipItem.index].split(':');
                    var label = item_arr[0];
                    
                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$total1}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%'; 

                    

                    return label;
                }
            }
        }
      }
      
    };
    $('#titulo2').text('Producción de Procedimientos Públicos por Doctor {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total2,2)}}');
    var ctx = document.getElementById('canvas_datos2').getContext('2d');

      window.myPie = new Chart(ctx, config);

    var config = {
      type: 'pie',

      data: {
        datasets: [{

          data: [
            @foreach($ordenes_valores_privados as $value)
              '{{round($value->total,2)}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($ordenes_valores_privados as $value)
            	@php $doctor = $doctores->where('id',$value->id_doctor)->first(); @endphp
              '{{$doctor->color}}',
            @endforeach

          ],
        }],
        labels: [
          @foreach($ordenes_valores_privados as $value)
          	@php $doctor = $doctores->where('id',$value->id_doctor)->first(); @endphp
            '{{$doctor->apellido1}} {{$doctor->nombre1}}',
          @endforeach
        ]
      },
      options: {
        legend: {
            position: 'left',
            display: true,
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var item_arr = data.labels[tooltipItem.index].split(':');
                    var label = item_arr[0];
                    
                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$total1}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%'; 

                    

                    return label;
                }
            }
        }
      }
      
    };
    $('#titulo3').text('Producción de Procedimientos Privados por Doctor {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total3,2)}}');
    var ctx = document.getElementById('canvas_datos3').getContext('2d');

      window.myPie = new Chart(ctx, config);
      
    var config = {
      type: 'pie',

      data: {
        datasets: [{

          data: [
            @foreach($ordenes_valores_particulares as $value)
              '{{round($value->total,2)}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($ordenes_valores_particulares as $value)
            	@php $doctor = $doctores->where('id',$value->id_doctor)->first(); @endphp
              '{{$doctor->color}}',
            @endforeach

          ],
        }],
        labels: [
          @foreach($ordenes_valores_particulares as $value)
          	@php $doctor = $doctores->where('id',$value->id_doctor)->first(); @endphp
            '{{$doctor->apellido1}} {{$doctor->nombre1}}',
          @endforeach
        ]
      },
      options: {
        legend: {
            position: 'left',
            display: true,
        },
        tooltips: {
            callbacks: {
                label: function(tooltipItem, data) {
                    var item_arr = data.labels[tooltipItem.index].split(':');
                    var label = item_arr[0];
                    
                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$total1}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%'; 

                    

                    return label;
                }
            }
        }
      }
      
    };
    $('#titulo4').text('Producción de Procedimientos Particulares por Doctor {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total4,2)}}');
    var ctx = document.getElementById('canvas_datos4').getContext('2d');

      window.myPie = new Chart(ctx, config);     



</script> 