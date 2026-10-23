
<style type="text/css">
	

	table{
		font-size: 13px;		
	}
</style>

@php 
	$mes_txt = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; 
@endphp


<div class="box box-success">
  <div class="box-header with-border">
    <center><h3 class="box-title" id="titulo1" ></h3></center>  
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body no-padding" style=""> 	

    <div class="col-md-12">
      <div class="row">  
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
  </div>
</div>  

<div class="box box-success">
  <div class="box-header with-border">
    <div class="row">
      <div class="col-lg-6"><h3 class="box-title">TABLA DE INCENTIVOS PUBLICOS</h3></div>
      <div class="col-lg-6"><h3 class="box-title">TABLA DE INCENTIVOS PARTICULARES Y PRIVADOS</h3></div>
    </div>
    

    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <!-- /.box-header -->
  <div class="box-body no-padding" style="">
    <div class="row">
      <div class="col-lg-6">
        <div class="row">  
          <div class="col-lg-12 col-sm-12">
            <div id="example2_wrapper" >
              <center>
                <div class="row">
                  <div class="table-responsive">
                    
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" stordenes_valores_totalesyle="font-size: 12px;">
                      <thead>
                        <tr role="row" style="background-color: #009999; color: white;">
                          <th >Rangos de Facturación</th>
                          <th >Porcentaje</th>
                          <th >Monto Referencial Más alto ($)</th>                                 
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>MENOR O IGUAL A  $ 100.000,00 </td>
                          <td style="text-align: right;">0,10%</td>
                          <td style="text-align: right;">$ 100,00 </td>
                        </tr>
                        <tr>
                          <td>DESDE $ 100.001,00 HASTA $ 120.000,00 </td>
                          <td style="text-align: right;">0,20%</td>
                          <td style="text-align: right;">$ 240,00  </td>
                        </tr>
                        <tr>
                          <td>DESDE $ 120.001,00 HASTA $ 140.000,00 </td>
                          <td style="text-align: right;">0,30%</td>
                          <td style="text-align: right;">$ 420,00  </td>
                        </tr>
                        <tr>
                          <td>DESDE $ 140.001,00 HASTA $ 160.000,00 </td>
                          <td style="text-align: right;">0,40%</td>
                          <td style="text-align: right;">$ 640,00  </td>
                        </tr>
                        <tr>
                          <td>DESDE $ 160.001,00 HASTA $ 180.000,00  </td>
                          <td style="text-align: right;">0,50%</td>
                          <td style="text-align: right;">$ 900,00  </td>
                        </tr>
                        <tr>
                          <td> DESDE $ 180.001,00 HASTA $ 200.000,00 </td>
                          <td style="text-align: right;">0,60%</td>
                          <td style="text-align: right;">$ 1.200,00  </td>
                        </tr>
                        <tr>
                          <td> DESDE $ 200.001,00 EN ADELANTE </td>
                          <td style="text-align: right;">0,75%</td>
                          <td style="text-align: right;">$ 1.500,00  </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </center>
            </div>
             
          </div> 
          
        </div>
      </div>
      <div class="col-lg-6">
        <div class="row">  
          <div class="col-lg-12 col-sm-12">
            <div id="example2_wrapper" >
              <center>
                <div class="row">
                  <div class="table-responsive">
                    
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" stordenes_valores_totalesyle="font-size: 12px;">
                      <thead>
                        <tr role="row" style="background-color: #009999; color: white;">
                          <th >Rangos de Facturación</th>
                          <th >Porcentaje</th>
                          <th >Monto Referencial Más alto ($)</th>                                 
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>MENOR O IGUAL A  $ 20.000,00 </td>
                          <td style="text-align: right;">1,50%</td>
                          <td style="text-align: right;">$ 300,00  </td>
                        </tr>
                        <tr>
                          <td>DESDE $ 20.001,00 HASTA $ 30.000,00 </td>
                          <td style="text-align: right;">1,60%</td>
                          <td style="text-align: right;">$ 480,00   </td>
                        </tr>
                        <tr>
                          <td> DESDE $ 30.001,00 HASTA $ 40.000,00  </td>
                          <td style="text-align: right;">1,70%</td>
                          <td style="text-align: right;">$ 680,00   </td>
                        </tr>
                        <tr>
                          <td> DESDE $ 40.001,00 HASTA $ 50.000,00  </td>
                          <td style="text-align: right;">1,80%</td>
                          <td style="text-align: right;">$ 900,00   </td>
                        </tr>
                        <tr>
                          <td>DESDE $ 50.001,00 HASTA $ 60.000,00   </td>
                          <td style="text-align: right;">1,90%</td>
                          <td style="text-align: right;">$ 1.140,00   </td>
                        </tr>
                        <tr>
                          <td>DESDE $ 60.001,00 HASTA $ 70.000,00  </td>
                          <td style="text-align: right;">2,00%</td>
                          <td style="text-align: right;">$ 1.400,00   </td>
                        </tr>
                        <tr>
                          <td>DESDE $ 70.001,00 EN ADELANTE </td>
                          <td style="text-align: right;">2,20%</td>
                          <td style="text-align: right;">$ 1.540,00  </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </center>
            </div>
             
          </div> 
          
        </div>
      </div>
    </div>
  </div> 
</div>   

<div class="box box-success">
  <div class="box-header with-border">
    <center><h3 class="box-title" id="titulo2" ></h3></center>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body no-padding" style=""> 
    <div class="col-md-12">
      <div class="row">
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
  </div>
</div>    

<div class="box box-success">
  <div class="box-header with-border">
    <center><h3 class="box-title" id="titulo3" ></h3></center>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body no-padding" style=""> 
    <div class="col-md-12">
        <div class="row"> 
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
  </div>
</div>    

<div class="box box-success">
  <div class="box-header with-border">
    <center><h3 class="box-title" id="titulo4" ></h3></center>
    <div class="box-tools pull-right">
      <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
      </button>
      <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
    </div>
  </div>
  <div class="box-body no-padding" style="">
    <div class="col-md-12">
        <div class="row"> 
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

    function refrescar(){
      location.reload();
    }

    $(document).ready(function(){
        document.getElementById("xboton").innerHTML = "<button type='button' class='btn btn-success btn-sm col-md-offset-9' onclick='refrescar();''><i class='glyphicon glyphicon-arrow-left'> </i> Regresar</button>";
    });  

    
     



</script> 