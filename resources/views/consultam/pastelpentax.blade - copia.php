
@extends('consultam.base2')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header">
          
          <script src="{{ asset ("/bower_components/Chart.js/dist/Chart.min.js") }}"></script>
            
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
          <div class="col-md-5">
            <h4>Procedimientos por Doctores</h4>
            <canvas id="oilChart" style="height: 25%;"></canvas>
          </div>
          <div class="col-md-5">
            <h4>Consultas por Doctores</h4>
            <canvas id="oilChart3" style="height: 25%;"></canvas>
          </div>
          <div class="col-md-5">
            <h4>Procedimientos por Seguros</h4>
            <canvas id="oilChart2" style="height: 25%;"></canvas>
          </div>
          <div class="col-md-5">
            <h4>Consultas por Seguros</h4>
            <canvas id="oilChart4" style="height: 25%;"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>    

      <script type="text/javascript">
        var oilCanvas = document.getElementById("oilChart");

Chart.defaults.global.defaultFontFamily = "Lato";
Chart.defaults.global.defaultFontSize = 15;

var oilData = {
    labels: [
      @foreach($doctor_ag as $value)
        "Dr(a). {{substr($value['0']->nombre1,0,1)}}.{{$value['0']->apellido1}}",
      @endforeach  
    ],
    datasets: [
        {
            data: [ @foreach($doctor_ag as $value) {{$value['1']}}, @endforeach],
            backgroundColor: [
              @foreach($doctor_ag as $value)           
                "{{$value['0']->color}}",
              @endforeach  
            ]
        }]

};

var pieChart = new Chart(oilCanvas, {
  type: 'pie',
  data: oilData,
  options: {
    legend: {
      display: true,
      position: 'left',
      labels: {
        boxWidth: 10,
        fontColor: 'black'
      }
    }
  }
});
      </script>
      <script type="text/javascript">
        var oilCanvas3 = document.getElementById("oilChart3");

Chart.defaults.global.defaultFontFamily = "Lato";
Chart.defaults.global.defaultFontSize = 15;

var oilData3 = {
    labels: [
      @foreach($doctor_co as $value)
        "Dr(a). {{substr($value['0']->nombre1,0,1)}}.{{$value['0']->apellido1}}",
      @endforeach  
    ],
    datasets: [
        {
            data: [ @foreach($doctor_co as $value) {{$value['1']}}, @endforeach],
            backgroundColor: [
              @foreach($doctor_co as $value)           
                "{{$value['0']->color}}",
              @endforeach  
            ]
        }]

};

var pieChart3 = new Chart(oilCanvas3, {
  type: 'pie',
  data: oilData3,
  options: {
    legend: {
      display: true,
      position: 'left',
      labels: {
        boxWidth: 10,
        fontColor: 'black'
      }
    }
  }
});
</script>
      <script type="text/javascript">
        var oilCanvas2 = document.getElementById("oilChart2");

Chart.defaults.global.defaultFontFamily = "Lato";
Chart.defaults.global.defaultFontSize = 15;

var oilData2 = {
    labels: [
      @foreach($proc_seg as $value)
        "{{$value['0']->nombre}}",
      @endforeach  
    ],
    datasets: [
        {
            data: [ @foreach($proc_seg as $value) {{$value['1']}}, @endforeach],
            backgroundColor: [
              @foreach($proc_seg as $value)           
                "{{$value['0']->color}}",
              @endforeach  
            ]
        }]

};

var pieChart2 = new Chart(oilCanvas2, {
  type: 'pie',
  data: oilData2,
  options: {
    legend: {
      display: true,
      position: 'left',
      labels: {
        boxWidth: 10,
        fontColor: 'black'
      }
    }
  }
});
</script>
<script type="text/javascript">
        var oilCanvas4 = document.getElementById("oilChart4");

Chart.defaults.global.defaultFontFamily = "Lato";
Chart.defaults.global.defaultFontSize = 15;

var oilData4 = {
    labels: [
      @foreach($co_seg as $value)
        "{{$value['0']->nombre}}",
      @endforeach  
    ],
    datasets: [
        {
            data: [ @foreach($co_seg as $value) {{$value['1']}}, @endforeach],
            backgroundColor: [
              @foreach($co_seg as $value)           
                "{{$value['0']->color}}",
              @endforeach  
            ]
        }]

};

var pieChart4 = new Chart(oilCanvas4, {
  type: 'pie',
  data: oilData4,
  options: {
    legend: {
      display: true,
      position: 'left',
      labels: {
        boxWidth: 10,
        fontColor: 'black'
      }
    }
  }
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
 
