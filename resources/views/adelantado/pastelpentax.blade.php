
@extends('consultam.base2')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header">



        </div>
        <div class="box-body">
          <div class="col-md-12">
            <form method="POST" action="{{ route('consultam.search2') }}" >
              {{ csrf_field() }}
              <div class="form-group col-md-4 col-xs-6">
                <label for="fecha" class="col-md-2 control-label">Fecha Desde</label>
                <div class="col-md-9">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control input-sm" name="fecha" id="fecha" required>
                  </div>
                </div>
              </div>

              <div class="form-group col-md-4 col-xs-6">
                <label for="fecha_hasta" class="col-md-3 control-label">Fecha Hasta</label>
                <div class="col-md-9">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta">
                    <div class="input-group-addon">
                      <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                    </div>
                  </div>
                </div>
              </div>
              <button type="submit" class="btn btn-primary" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span>Buscar</button>

            </form>
          </div>

          <div id="example1_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row" style="width: 100%;">
              <div class="col-md-12">
                  <h3 id="titulo1"></h3>
                  <canvas id="canvas_datos" style="max-width: 100%;"></canvas>
              </div>

              <div class="col-md-12">
                  <h3 id="titulo5"></h3>
                  <canvas id="canvas_datos5" style="max-width: 100%;"></canvas>
              </div>

              <div class="col-md-12">
                  <h3 id="titulo4"></h3>
                  <canvas id="canvas_datos4" style="max-width: 100%;"></canvas>
              </div>
              <div class="col-md-12">
                  <h3 id="titulo7"></h3>
                  <canvas id="canvas_datos7" style="max-width: 100%;"></canvas>
              </div>

              <div class="col-md-12">
                  <h3 id="titulo2"></h3>
                  <canvas id="canvas_datos2" style="max-width: 100%;"></canvas>
              </div>

              <div class="col-md-12">
                  <h3 id="titulo6"></h3>
                  <canvas id="canvas_datos6" style="max-width: 100%;"></canvas>
              </div>
              <div class="col-md-12">
                  <h3 id="titulo3"></h3>
                  <canvas id="canvas_datos3" style="max-width: 100%;"></canvas>
              </div>
              <div class="col-md-12">
                  <h3 id="titulo8"></h3>
                  <canvas id="canvas_datos8" style="max-width: 100%;"></canvas>
              </div>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>
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
            labels: {
              fontSize: 10,
            }
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
            labels: {
              fontSize: 10,
            }
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
            labels: {
              fontSize: 10,
            }
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
            labels: {
              fontSize: 10,
            }
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
            labels: {
              fontSize: 10,
            }
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
            labels: {
              fontSize: 10,
            }
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
    $('#titulo6').text('Consultas Agendados por Seguros - Total: {{$contador}}')
    var ctx = document.getElementById('canvas_datos6').getContext('2d');
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
            @foreach($se_procedimiento_seg as $value) @php $contador  =$contador + $value['cantidad'];  @endphp
              "{{$value['cantidad']}}",
            @endforeach
          ],
          backgroundColor: [
            @foreach($se_procedimiento_seg as $value) @php $hex = $value->color;
              list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x"); @endphp
              'rgb({{$r}}, {{$g}}, {{$b}})',
            @endforeach
          ],
        }],
        labels: [
        @foreach($se_procedimiento_seg as $value)
          '{{$value->nombre_seguro}} ({{$value->cantidad}})',
        @endforeach
        ]
      },
      options: {
        legend: {
            position: 'left',
            display: true,
            labels: {
              fontSize: 10,
            }
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
    $('#titulo7').text('Procedimientos Realizados por Seguros - Total: {{$contador}}')
    var ctx = document.getElementById('canvas_datos7').getContext('2d');
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
            @foreach($se_consultas_seg as $value) @php $contador  =$contador + $value['cantidad'];  @endphp
              "{{$value['cantidad']}}",
            @endforeach
          ],
          backgroundColor: [
            @foreach($se_consultas_seg as $value) @php $hex = $value->color;
              list($r, $g, $b) = sscanf($hex, "#%02x%02x%02x"); @endphp
              'rgb({{$r}}, {{$g}}, {{$b}})',
            @endforeach
          ],
        }],
        labels: [
        @foreach($se_consultas_seg as $value)
          '{{$value->nombre_seguro}} ({{$value->cantidad}})',
        @endforeach
        ]
      },
      options: {
        legend: {
            position: 'left',
            display: true,
            labels: {
              fontSize: 10,
            }
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
    $('#titulo8').text('Consultas Realizados por Seguros - Total: {{$contador}}')
    var ctx = document.getElementById('canvas_datos8').getContext('2d');
      window.myPie = new Chart(ctx, config);
</script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">

  $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',


            defaultDate: '{{$fecha}}',

            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',


            defaultDate: '{{$fecha_hasta}}',

            });
  });

  function buscar()
{
  var obj = document.getElementById("boton_buscar");
  obj.click();
}

</script>
@endsection
