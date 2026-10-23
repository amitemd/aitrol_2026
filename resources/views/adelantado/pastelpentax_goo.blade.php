
@extends('consultam.base2')
@section('action-content')
<style type="text/css">

.chart {
  width: 100%;
  min-height: 450px;
  /*margin-left: -90px;*/

}

.row {
  margin: 0px !important;
}

</style>
<link rel='stylesheet prefetch' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css'>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">





<script src="{{asset('js/jsapi.js')}}"></script>

    <div class="container-fluid">
      <div class="box box-primary">
        <div class="box-header with-border">
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
                    <div class="input-group-addon">
                      <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                    </div>
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

        </div>
        <div class="box-body">
          <div class="row">

            <div class="col-md-6">
              <div id="chart_div1" class="chart"></div>
            </div>
            <div class="col-md-6">
              <div id="chart_div2" class="chart"></div>
            </div>
            <div class="col-md-6">
              <div id="chart_div2a" class="chart"></div>
            </div>
            <div class="col-md-6">
              <div id="chart_div3" class="chart"></div>
            </div>
            <div class="col-md-6">
              <div id="chart_div3_ok" class="chart"></div>
            </div>
            <div class="col-md-6">
              <div id="chart_div4" class="chart"></div>
            </div>

          </div>
        </div>
      </div>
    </div>







<script type="text/javascript">

  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart1);
  function drawChart1() {
    var data1 = google.visualization.arrayToDataTable([
      ['Dr', 'Nro'],
      @php $cont_doctor_ag=0; @endphp
      @foreach($doctor_ag as $value)
        @php $cont_doctor_ag = $cont_doctor_ag + $value['1']; @endphp
        ['Dr. {{substr($value['0']->nombre1,0,1)}}.{{$value['0']->apellido1}}', {{$value['1']}}],
      @endforeach

    ]);

    var options1 = {
      title: 'Procedimientos Agendados por Doctores - Total: {{$cont_doctor_ag}}',
      //hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
      is3D: true,
      colors:[
        @foreach($doctor_ag as $value)
          '{{$value['0']->color}}',
        @endforeach
      ],
    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_div1'));
      chart.draw(data1, options1);
  }


  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart2);
  function drawChart2() {
    var data2 = google.visualization.arrayToDataTable([
      ['Sr', 'Nro'],
      @php $cont_proc_seg=0; @endphp
      @foreach($proc_seg as $value)
        @php $cont_proc_seg = $cont_proc_seg + $value['1'];  @endphp
        ['{{$value['0']->nombre}}', {{$value['1']}}],
      @endforeach

    ]);

    var options2 = {
      title: 'Procedimientos Agendados por Seguros  - Total: {{$cont_proc_seg}}',
      //hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
      is3D: true,
      colors:[
        @foreach($proc_seg as $value)
          '{{$value['0']->color}}',
        @endforeach
      ],
    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_div2'));
      chart.draw(data2, options2);
  }

  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart2a);
  function drawChart2a() {
    var data2a = google.visualization.arrayToDataTable([
      ['Sr', 'Nro'],
      @php $cont_proc_doc=0; @endphp
      @foreach($proc_doc as $value)
        @php $cont_proc_doc = $cont_proc_doc + $value['1'];  @endphp
        ['Dr. {{substr($value['0']->nombre1,0,1)}}.{{$value['0']->apellido1}}', {{$value['1']}}],
      @endforeach

    ]);

    var options2a = {
      title: 'Procedimientos Realizados por Doctor(Pentax)  - Total: {{$cont_proc_doc}}',
      //hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
      is3D: true,
      colors:[
        @foreach($proc_doc as $value)
          '{{$value['0']->color}}',
        @endforeach
      ],
    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_div2a'));
      chart.draw(data2a, options2a);
  }

  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart3);
  function drawChart3() {
    var data3 = google.visualization.arrayToDataTable([
      ['Sr', 'Nro'],
      @php $cont_doctor_co=0; @endphp
      @foreach($doctor_co as $value)
        @php $cont_doctor_co = $cont_doctor_co + $value['1']; @endphp
        ['Dr. {{substr($value['0']->nombre1,0,1)}}.{{$value['0']->apellido1}}', {{$value['1']}}],
      @endforeach

    ]);

    var options3 = {
      title: 'Consultas Agendadas por Doctores  - Total: {{$cont_doctor_co}}',
      //hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
      is3D: true,
      colors:[
        @foreach($doctor_co as $value)
          '{{$value['0']->color}}',
        @endforeach
      ],
    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_div3'));
      chart.draw(data3, options3);
  }

  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart3_ok);
  function drawChart3_ok() {
    var data3_ok = google.visualization.arrayToDataTable([
      ['Sr', 'Nro'],
      @php $cont_doctor_co_ok=0; @endphp
      @foreach($doctor_co_ok as $value)
        @php $cont_doctor_co_ok = $cont_doctor_co_ok + $value['1']; @endphp
        ['Dr. {{substr($value['0']->nombre1,0,1)}}.{{$value['0']->apellido1}}', {{$value['1']}}],
      @endforeach

    ]);

    var options3_ok = {
      title: 'Consultas Agendadas Realizadas por Doctores  - Total: {{$cont_doctor_co_ok}}',
      //hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
      is3D: true,
      colors:[
        @foreach($doctor_co_ok as $value)
          '{{$value['0']->color}}',
        @endforeach
      ],
    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_div3_ok'));
      chart.draw(data3_ok, options3_ok);
  }

  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart4);
  function drawChart4() {
    var data4 = google.visualization.arrayToDataTable([
      ['Sr', 'Nro'],
      @php $cont_co_seg=0; @endphp
      @foreach($co_seg as $value)
        @php $cont_co_seg = $cont_co_seg + $value['1']; @endphp
        ['{{$value['0']->nombre}}', {{$value['1']}}],
      @endforeach

    ]);

    var options4 = {
      title: 'Consultas Agendadas por Seguros  - Total: {{$cont_co_seg}}',
      //hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
      is3D: true,
      colors:[
        @foreach($co_seg as $value)
          '{{$value['0']->color}}',
        @endforeach
      ],
    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_div4'));
      chart.draw(data4, options4);
  }

$(window).resize(function(){
  drawChart1();
  drawChart2();
  drawChart3();
  drawChart4();
});





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
