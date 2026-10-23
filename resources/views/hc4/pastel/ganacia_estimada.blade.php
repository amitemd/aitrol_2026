
<link href="{{ asset("/bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css")}}" rel="stylesheet" type="text/css" />
<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="doctor" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
    <!-- Main content -->
<section class="content">
  <div class="box box-primary">
    <div class="box-header">
      <div class="row">
          <div class="col-md-9">
            <h3 class="box-title">Estadísticas de Estimado de Ganancia</h3>
          </div>
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row" style="width: 100%;">
          <div class="col-md-6">
              <h3 id="titulo1"></h3>
              <canvas id="canvas_datos" style="max-width: 100%;"></canvas>
          </div>
          <div class="col-md-6">
              <h3 id="titulo2"></h3>
              <canvas id="canvas_datos2" style="max-width: 100%;"></canvas>
          </div>

        </div>

      </div>
    </div>
    <!-- /.box-body -->
  </div>
  <!-- <div class="box box-primary">
    <div class="box-header">
      <div class="row">
          <div class="col-md-9">
            <h3 class="box-title">Estadísticas de Ingresos por Consultas del Dr. Carlos Robles Medranda</h3>
            <h3 class="box-title">@if($request->fecha!=$request->fecha_hasta) Desde {{$request->fecha}} Hasta {{$request->fecha_hasta}} @else De la Fecha: {{$request->fecha}} @endif</h3>
          </div>
      </div>
    </div>
   
    <div class="box-body">
      
      <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
        <div class="row" style="width: 100%;">
          <div class="col-md-6">
            <div class="table-responsive col-md-10">
              <table id="tabla_consultas" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example1_info" style="font-size: 12px;">
                <thead>
                  <tr role="row">
                    <th >No.</th>
                    <th >Seguro</th>
                    <th >Cantidad</th>
                    <th >Valor</th> 
                                                     
                  </tr>
                </thead>
                <tbody>
                @php $total_cantidad = 0; $total_valor = 0; $cont = 1;$meta_cantidad_particular = 6; @endphp  
                @foreach($orden_venta_seguro as $value)

                  <tr role="row" >
                    <td><b>{{$cont}}</b></td>
                    <td><b>@if($value->cortesia=='SI')CORTESIA @elseif($value->vip=='1')VIP @else{{$value->seguro}}@endif</b></td>
                    <td>{{$value->cantidad}}</td>
                    <td style="background-color: #ffe6e6;color: red;font-size: 15px;">$ {{number_format(round($value->total1 + $value->total2),2) }}</td>
                    
                  </tr>
                  @php $total_cantidad = $total_cantidad + $value->cantidad; $total_valor = $total_valor + $value->total1 + $value->total2; $cont++;@endphp
                @endforeach
                @foreach($orden_venta_seguro2 as $value)

                  <tr role="row" >
                    <td><b>{{$cont}}</b></td>
                    <td><b>@if($value->cortesia=='SI')CORTESIA @elseif($value->vip=='1')VIP @else{{$value->seguro}}@endif</b></td>
                    <td>{{$value->cantidad}}</td>
                    <td style="background-color: #ffe6e6;color: red;font-size: 15px;">$ {{number_format(round($value->total1 + $value->total2),2) }}</td>
                  </tr>
                  @php $total_cantidad = $total_cantidad + $value->cantidad; $total_valor = $total_valor + $value->total1 + $value->total2; $cont++;@endphp
                @endforeach
                  <tr role="row" >
                    <td><b>{{$cont}}</b></td>
                    <td><b>TOTAL</b></td>
                    <td><b>{{$total_cantidad}}</b></td>
                    <td style="background-color: #ffe6e6;color: red;font-size: 15px;"><b>$ {{number_format(round($total_valor),2) }}</b></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-md-6">
              <h3 id="tituloc"></h3>
              <canvas id="canvasc" style="max-width: 100%;"></canvas>
          </div> 
         
        </div>

      </div>
      <div class="col-md-12">
        <div class="table-responsive col-md-10">
          <table id="tabla_consultas2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example1_info" style="font-size: 12px;">
            <thead>
              <tr role="row">
                <th >No.</th>
                <th >Tipo seguro</th> 
                <th >Pacientes</th>
                <th >Valor</th>
              </tr>
            </thead>
            <tbody>
            @php $total_cantidad = 0; $total_valor = 0; $cont = 1; @endphp  
            @foreach($arr as $value)
              
              <tr role="row" >
                <td><b>{{$cont}}</b></td>
                <td><b>{{$value['tipo']}}</b></td>
                <td><b>{{$value['cantidad']}}</b></td>
                <td style="background-color: #ffe6e6;color: red;font-size: 15px;">{{$value['valor']}}</td>
              </tr>

              @php $total_cantidad = $total_cantidad + $value['cantidad']; $total_valor = $total_valor + $value['valor']; $cont++;@endphp
            
             
            @endforeach
            <tr role="row" >
                <td><b>{{$cont}}</b></td>
                <td><b>TOTAL</b></td>
                <td><b>{{$total_cantidad}}</b></td>
                <td style="background-color: #ffe6e6;color: red;font-size: 15px;"><b>{{$total_valor}}</b></td>
              </tr>
              
            </tbody>
          </table>
        </div>
      </div>
    </div>
    
  </div> -->
</section>
    

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

    <script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/bower_components/datatables.net/js/jquery.dataTables.min.js") }}"></script>
<script type="text/javascript" >
  $(document).ready(function($){

    $('#tabla_consultas').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });
    $('#tabla_consultas2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

  });  

  @php
    $contador = 0;
  @endphp
    var config = {
      type: 'pie',
      data: {
        datasets: [{

          data: [
            @foreach($doctor_ganacia as $value) @php $contador  =$contador + $value[1];  @endphp
              '{{$value[1]}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($doctor_ganacia as $value) @php $hex = $value[0]->color;
              list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x"); @endphp
              'rgb({{$r}}, {{$g}}, {{$b}})',
            @endforeach
          ],
        }],
        labels: [
        @foreach($doctor_ganacia as $value)
          'Dr. {{substr($value[0]->nombre1,0, 1)}}. {{$value[0]->apellido1}} ($ {{$value[1]}})',
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

                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$contador}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%';



                    return label;
                }
            }
        }
      }
    };
    $('#titulo1').text('Ganancia de Consultas por Doctores - Total: ${{$contador}}')
    var ctx = document.getElementById('canvas_datos').getContext('2d');
      window.myPie = new Chart(ctx, config);
</script>
<script type="text/javascript" >
  @php
    $contador = 0;
  @endphp
    var config_2 = {
      type: 'pie',
      data: {
        datasets: [{

          data: [
            @foreach($co_seg as $value) @php $contador  =$contador + $value[1];  @endphp
              '{{$value[1]}}',
            @endforeach
            @foreach($vip as $value) @php $contador  =$contador + $value[1];  @endphp
              '{{$value[1]}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($co_seg as $value) @php $hex = $value[0]->color;
              list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x"); @endphp
              'rgb({{$r}}, {{$g}}, {{$b}})',
            @endforeach
            @foreach($vip as $value)
              'rgb(125, 62, 152)',
            @endforeach
          ],
        }],
        labels: [
        @foreach($co_seg as $value)
          '{{$value[0]->nombre}} ($ {{$value[1]}})',
        @endforeach

        @foreach($vip as $value)
          'VIP ($ {{$value[1]}})',
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

                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$contador}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%';



                    return label;
                }
            }
        }
      }
    };
    $('#titulo2').text('Ganancia de Consultas por Seguros - Total: ${{$contador}}')
    var ctx2 = document.getElementById('canvas_datos2').getContext('2d');
      window.myPie = new Chart(ctx2, config_2);
</script>
<script type="text/javascript" >

    function random_bg_color() {
        var x = Math.floor(Math.random() * 256);
        var y = Math.floor(Math.random() * 256);
        var z = Math.floor(Math.random() * 256);
        var bgColor = "rgb(" + x + "," + y + "," + z + ")";
      return bgColor;
    }
  
    var config_c = {
      type: 'pie',
      data: {
        datasets: [{

          data: [
            @foreach($orden_venta_seguro as $value) 
              '{{$value->total1 + $value->total2}}',
            @endforeach
            @foreach($orden_venta_seguro2 as $value) 
              '{{$value->total1 + $value->total2}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($orden_venta_seguro as $value) 
              random_bg_color(),
            @endforeach
            @foreach($orden_venta_seguro2 as $value) 
              random_bg_color(),
            @endforeach
          ],
        }],
        labels: [
        @foreach($orden_venta_seguro as $value)
          @if($value->cortesia=='SI')'CORTESIA' @elseif($value->vip=='1')'VIP' @else '{{$value->seguro}}' @endif,
        @endforeach
        @foreach($orden_venta_seguro2 as $value)
          @if($value->cortesia=='SI')'CORTESIA' @elseif($value->vip=='1')'VIP' @else '{{$value->seguro}}' @endif,
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

                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$contador}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%';



                    return label;
                }
            }
        }
      }
    };
    
    var ctx2 = document.getElementById('canvasc').getContext('2d');
    window.myPie = new Chart(ctx2, config_c);
</script>
