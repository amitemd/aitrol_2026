@extends('laboratorio.estadistico.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!--script src="{{ asset ("/bower_components/Chart.js/dist/Chart.min.js") }}"></script-->

<script type="text/javascript" src="http://www.google.com/jsapi"></script>

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
    
} 

table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after{
  opacity: 100;
}
</style>

<!-- Main content -->
<section class="content">
  <div class="box box-success">
    <div class="box-header">
      <div class="row">
        <div class="col-md-9">
          <h3 class="box-title">Ordenes de Laboratorio por año</h3>

        </div>
        <!--a class="btn btn-primary" onclick="goBack()"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a-->
        
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
      <div class="col-md-4">

        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="table-responsive col-md-12">
              
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                <thead>
                  <tr role="row" style="background-color: #009999; color: white;">
                    <th >Año</th>
                    <th >Ordenes</th>
                    <th >Valor</th>                                 
                  </tr>
                </thead>
                <tbody>
                  @foreach($or_anio as $value)
                    <tr>
                      <td>{{$value->anio}}</td>
                      <td style="text-align: right;">{{$value->cantidad}}</td>
                      <td style="text-align: right;">$ {{number_format(round($value->valor,2),2,',','.')}}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        
        </div>
      </div>  

      <div class="col-md-4">
        <div id="chart"></div>
      </div>

      <div class="col-md-4">
          <div id="chart1"></div>
      </div>  
      
    </div>
    
  <!-- /.box-body -->
  </div>
  <div class="box box-success" id="div_anio_doctor">
    <div class="box-header">
      <div class="row">
        <div class="col-md-9">
          <h3 class="box-title"></h3>

        </div>
        <!--a class="btn btn-primary" onclick="goBack()"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a-->
        
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
       <div class="col-md-8">
        <div id="chart_doc1"></div>
      </div>
     
      <div class="col-md-4">

        <div id="example2a_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
          <div class="row">
            <div class="table-responsive col-md-12">
              
              <table id="example2a" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2a_info" style="font-size: 12px;">
                <thead>
                  <tr role="row" style="background-color: #009999; color: white;">
                    <th >C</th>
                    <th >Doctor</th>
                    <th >Ordenes</th>
                    <th >Valor</th>                                 
                  </tr>
                </thead>
                <tbody>
                    @php $cantidad = 0; $valor = 0; @endphp
                  @foreach($or_anio_doctor as $value)
                    
                    <tr>
                      <td style="background-color: {{$value->color}}">&nbsp;</td>
                      <td>{{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}}</td>
                      <td style="text-align: right;">{{$value->cantidad}}</td>
                      <td style="text-align: right;">$ {{number_format(round($value->valor,2),2,',','.')}}</td>
                    </tr>
                    @php $cantidad = $cantidad + $value->cantidad; $valor = $valor + $value->valor; @endphp
                  @endforeach
                    <tr>
                      <td >&nbsp;</td>
                      <td><b>TOTAL</b></td>
                      <td style="text-align: right;"><b>{{$cantidad}}</b></td>
                      <td style="text-align: right;"><b>$ {{number_format(round($valor,2),2,',','.')}}</b></td>
                    </tr>
                </tbody>
              </table>
            </div>
          </div>
        
        </div>
      </div>  

    </div>
    
  <!-- /.box-body -->
  </div>
  
</section>
<!-- /.content -->
  
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

  $(document).ready(function($){

    

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
    })





  });

   $('#doctor').on('hidden.bs.modal', function(){
                location.reload();
                $(this).removeData('bs.modal');
            }); 


  

  /*function goBack() {
    window.history.back();
  }*/

</script> 


<!--script>
var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [
          @foreach($or_anio as $value)
                
            "{{$value->anio}}",
                  
                
          @endforeach
        ],
        datasets: [
          @foreach($or_anio as $value)
            {
            label: '{{$value->anio}}',
            data: [{{$value->valor}}],
            backgroundColor: [
                'blue',
                'green',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
            },
          @endforeach    
        ]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script--> 

<script type="text/javascript">
            //load the Google Visualization API and the chart
            google.load('visualization', '1', {'packages': ['corechart']});
            //google.load('visualization', '1', {'packages': ['piechart']});
            
            
 
            //set callback
            google.setOnLoadCallback (createChart);
            google.setOnLoadCallback (createChart1);
            google.setOnLoadCallback (createChart_doc1);
            

            
           
 
            //callback function
            function createChart() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Año');
                dataTable.addColumn('number', 'Cantidad');
                

 
                //define rows of data
                dataTable.addRows([@foreach($or_anio as $value)['{{$value->anio}}',{{$value->cantidad}}], @endforeach ]);
 
                //instantiate our chart object
                var chart = new google.visualization.ColumnChart (document.getElementById('chart'));
 
                //define options for visualization
                var options = {width: 400, height: 240, is3D: true, title: 'Cantidad de Ordenes de Laboratorio por año'};
 
                //draw our chart
                chart.draw(dataTable, options);
 
            }

            function createChart1() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Año');
                dataTable.addColumn('number', 'Valor');

                
                //console.log(dataTable);
                

 
                //define rows of data
                dataTable.addRows([@foreach($or_anio as $value)['{{$value->anio}}',{{$value->valor}}], @endforeach ]);
                console.log(dataTable);
 
                //instantiate our chart object
                var chart = new google.visualization.ColumnChart (document.getElementById('chart1'));
 
                //define options for visualization
                var options = {width: 400, height: 240, is3D: true, title: 'Ordenes de Laboratorio por año en $', colors: ['red'],};
 
                //draw our chart
                chart.draw(dataTable, options);
 
            }

            
           

            function createChart_doc1() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Doctor');
                dataTable.addColumn('number', 'Cantidad');
                

 
                //define rows of data
                dataTable.addRows([@foreach($or_anio_doctor as $value)['{{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}}',{{$value->cantidad}}], @endforeach ]);
 
                //instantiate our chart object
                var chart = new google.visualization.PieChart (document.getElementById('chart_doc1'));
 
                //define options for visualization
                var options = {width: 750, height: 500, is3D: false, title: 'Ordenes de Laboratorio por Doctor en el {{$anio}}', colors: [@foreach($or_anio_doctor as $value)'{{$value->color}}',@endforeach], };
 
                //draw our chart
                chart.draw(dataTable, options);
 
            }

           
        </script>

@endsection