


<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
    text-align: right;
}
</style>
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
            <h3 class="box-title">Estadísticas de Procedimientos</h3>
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
              <h3 id="titulo5"></h3>
              <canvas id="canvas_datos5" style="max-width: 100%;"></canvas>
          </div>

          <div class="col-md-6">
              <h3 id="titulo4"></h3>
              <canvas id="canvas_datos4" style="max-width: 100%;"></canvas>
          </div>

          <div class="col-md-6">
              <h3 id="titulo2"></h3>
              <canvas id="canvas_datos2" style="max-width: 100%;"></canvas>
          </div>

          <div class="col-md-6">
              <h3 id="titulo3"></h3>
              <canvas id="canvas_datos3" style="max-width: 100%;"></canvas>
          </div>

          <div class="col-md-6">
              <h3 id="titulo6"></h3>
              <canvas id="canvas_datos6" style="max-width: 100%;"></canvas>
          </div>

        </div>

      </div>




    </div>
    <!-- /.box-body -->
  </div>
  </section>
    <!-- /.content -->

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

    <script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
    <script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript" >
  @php
    $contador = 0;
  @endphp
    var config = {
      type: 'pie',
      data: {
        datasets: [{

          data: [
            @foreach($doctor_ag as $value) @php $contador  =$contador + $value[1];  @endphp
              '{{$value[1]}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($doctor_ag as $value) @php $hex = $value[0]->color;
              list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x"); @endphp
              'rgb({{$r}}, {{$g}}, {{$b}})',
            @endforeach
          ],
        }],
        labels: [
        @foreach($doctor_ag as $value)
          'Dr. {{substr($value[0]->nombre1,0, 1)}}. {{$value[0]->apellido1}} ({{$value[1]}})',
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
    $('#titulo1').text('Procedimientos Agendados por Doctores - Total: {{$contador}}')
    var ctx = document.getElementById('canvas_datos').getContext('2d');
      window.myPie = new Chart(ctx, config);
</script>

<script type="text/javascript" >
  @php
    $contador = 0;
  @endphp
    var config = {
      type: 'pie',
      data: {
        datasets: [{

          data: [
            @foreach($doctor_co as $value) @php $contador  =$contador + $value[1];  @endphp
              '{{$value[1]}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($doctor_co as $value) @php $hex = $value[0]->color;
              list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x"); @endphp
              'rgb({{$r}}, {{$g}}, {{$b}})',
            @endforeach
          ],
        }],
        labels: [
        @foreach($doctor_co as $value)
          'Dr. {{substr($value[0]->nombre1,0, 1)}}. {{$value[0]->apellido1}} ({{$value[1]}})',
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
    $('#titulo2').text('Consultas Agendadas por Doctores - Total: {{$contador}}')
    var ctx = document.getElementById('canvas_datos2').getContext('2d');
      window.myPie = new Chart(ctx, config);
</script>

<script type="text/javascript" >
  @php
    $contador = 0;
  @endphp
    var config = {
      type: 'pie',
      data: {
        datasets: [{

          data: [
            @foreach($doctor_co_ok as $value) @php $contador  =$contador + $value[1];  @endphp
              '{{$value[1]}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($doctor_co_ok as $value) @php $hex = $value[0]->color;
              list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x"); @endphp
              'rgb({{$r}}, {{$g}}, {{$b}})',
            @endforeach
          ],
        }],
        labels: [
        @foreach($doctor_co_ok as $value)
          'Dr. {{substr($value[0]->nombre1,0, 1)}}. {{$value[0]->apellido1}} ({{$value[1]}})',
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
    $('#titulo3').text('Consultas Agendadas Realizadas por Doctores - Total: {{$contador}}')
    var ctx = document.getElementById('canvas_datos3').getContext('2d');
      window.myPie = new Chart(ctx, config);
</script>

<script type="text/javascript" >
  @php
    $contador = 0;
  @endphp
    var config = {
      type: 'pie',
      data: {
        datasets: [{

          data: [
            @foreach($proc_doc as $value) @php $contador  =$contador + $value[1];  @endphp
              '{{$value[1]}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($proc_doc as $value) @php $hex = $value[0]->color;
              list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x"); @endphp
              'rgb({{$r}}, {{$g}}, {{$b}})',
            @endforeach
          ],
        }],
        labels: [
        @foreach($proc_doc as $value)
          'Dr. {{substr($value[0]->nombre1,0, 1)}}. {{$value[0]->apellido1}} ({{$value[1]}})',
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
    $('#titulo4').text('Procedimientos Realizados por Doctor(Pentax) - Total: {{$contador}}')
    var ctx = document.getElementById('canvas_datos4').getContext('2d');
      window.myPie = new Chart(ctx, config);
</script>

<script type="text/javascript" >
  @php
    $contador = 0;
  @endphp
    var config = {
      type: 'pie',
      data: {
        datasets: [{

          data: [
            @foreach($proc_seg as $value) @php $contador  =$contador + $value[1];  @endphp
              '{{$value[1]}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($proc_seg as $value) @php $hex = $value[0]->color;
              list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x"); @endphp
              'rgb({{$r}}, {{$g}}, {{$b}})',
            @endforeach
          ],
        }],
        labels: [
        @foreach($proc_seg as $value)
          '{{$value[0]->nombre}} ({{$value[1]}})',
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
    $('#titulo5').text('Procedimientos Agendados por Seguros - Total: {{$contador}}')
    var ctx = document.getElementById('canvas_datos5').getContext('2d');
      window.myPie = new Chart(ctx, config);
</script>

<script type="text/javascript" >
  @php
    $contador = 0;
  @endphp
    var config = {
      type: 'pie',
      data: {
        datasets: [{

          data: [
            @foreach($co_seg as $value) @php $contador  =$contador + $value[1];  @endphp
              '{{$value[1]}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($co_seg as $value) @php $hex = $value[0]->color;
              list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x"); @endphp
              'rgb({{$r}}, {{$g}}, {{$b}})',
            @endforeach
          ],
        }],
        labels: [
        @foreach($co_seg as $value)
          '{{$value[0]->nombre}} ({{$value[1]}})',
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
    $('#titulo6').text('Procedimientos Agendados por Seguros - Total: {{$contador}}')
    var ctx = document.getElementById('canvas_datos6').getContext('2d');
      window.myPie = new Chart(ctx, config);
</script>
