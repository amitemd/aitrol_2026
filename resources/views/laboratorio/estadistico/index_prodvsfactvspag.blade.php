<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <h4 style="background-color: #009999;text-align: center;color: white;">PRODUCCION/FACTURACIÓN/PAGO POR MES DEL AÑO {{$anio}}</h4>
      <table id="examplea" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="examplea_info" style="font-size: 11px;">
        <thead>
          <tr role="row">
            @php $mes = ['', 'ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'];@endphp
            <th >N.</th>
            <th >Mes</th>
            <th style="background-color: #1affff">Valor Producción</th>
            <th style="background-color: #1affff">Valor Facturación</th>
            <th style="background-color: #1affff">Valor Pagada</th>
            <!--th >Acción</th-->
          </tr>
        </thead>
        <tbody>
        @foreach($or_anio_mes as $value)
          <tr role="row" >
            <td><b>{{$value->mes}}</b></td>
            <td><b>{{$mes[$value->mes]}}</b></td>
            <td >$ {{number_format(round($value->valor_total,2),2) }}</td>
            <td >$ @if(isset($arr_aniomes_fact[$anio.'-'.$value->mes])){{number_format(round($arr_aniomes_fact[$anio.'-'.$value->mes][1],2))}}@else 0  @endif <input type="hidden" name="sistema" value="@if(isset($arr_fact2[$anio.'-'.$value->mes])){{number_format(round($arr_fact2[$anio.'-'.$value->mes][0],2))}}@else 0  @endif"> </td>
            <td >$ @if(isset($arr_fact2[$anio.'-'.$value->mes])){{number_format(round($arr_fact2[$anio.'-'.$value->mes][1],2))}}@else 0  @endif</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>  
  <div class="col-md-12">
    <center>
      <canvas id="canvas3" style="max-width: 70%;height: 250px;"></canvas>
    </center>
  </div>
</div>

<script>
    @php
      $array_total_mes = array();
      $array_facturado = array();
      $array_pagado    = array();
    @endphp

    @foreach($or_anio_mes as $value)
        @php $vfacturado = 0;$vpagado = 0;
          if(isset($arr_aniomes_fact[$anio.'-'.$value->mes])){
            $vfacturado = $arr_aniomes_fact[$anio.'-'.$value->mes][1];
          }
          if(isset($arr_fact2[$anio.'-'.$value->mes])){
            $vpagado = $arr_fact2[$anio.'-'.$value->mes][1];
          }
          
          array_push($array_total_mes, round($value->valor,2));

          array_push($array_facturado, round($vfacturado,2));
          
          array_push($array_pagado, round($vpagado,2));
        @endphp
    @endforeach
    var color = Chart.helpers.color;
    var barChartData_2 = {
      labels: ['ENE', 'FEB', 'MAR', 'ABR', 'MAY', 'JUN', 'JUL', 'AGO', 'SEP', 'OCT', 'NOV', 'DIC'],
      datasets: [
      {
        label: 'Producción',
        backgroundColor: color(window.chartColors.blue).rgbString(),
        borderColor: window.chartColors.blue,
        borderWidth: 1,
        data: [@foreach($array_total_mes as $arr_total)
          {{$arr_total}},
          @endforeach
        ]
      },
      {
        label: 'Facturación',
        backgroundColor: color(window.chartColors.red).rgbString(),
        borderColor: window.chartColors.red,
        borderWidth: 1,
        data: [@foreach($array_facturado as $fact)
          {{$fact}},
          @endforeach
        ]
      },

      {
        label: 'Pagado',
        backgroundColor: color(window.chartColors.yellow).rgbString(),
        borderColor: window.chartColors.yellow,
        borderWidth: 1,
        data: [@foreach($array_pagado as $pago)
          {{$pago}},
          @endforeach
        ]
      },



      ]

    };

    var ctx = document.getElementById('canvas3').getContext('2d');
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
        }
      }
    });

</script>