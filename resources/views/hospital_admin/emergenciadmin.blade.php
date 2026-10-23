@extends('hospital_admin.base')
@section('action-content')

<script src="{{asset('js/jsapi.js')}}"></script>  
  
    <div class="container-fluid">
      <div class="box box-primary" >
        <div class="box-header with-border">
          <div class="col-md-12">
            <form method="POST" action="" >
              {{ csrf_field() }}
              <div class="form-group col-md-5 col-xs-6">
                <label for="fecha" class="col-md-2 control-label">Fecha Desde</label>
                <div class="col-md-9">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control input-sm" name="fecha" id="fecha" required>
                    <div class="input-group-addon">
                      <i class="glyphicon glyphicon-remove-circle"></i>
                    </div>
                  </div>
                </div>  
              </div>
             </form>
              <div class="form-group col-md-5 col-xs-6">
                <label for="fecha_hasta" class="col-md-3 control-label">Fecha Hasta</label>
                <div class="col-md-9">
                  <div class="input-group date">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta">
                    <div class="input-group-addon">
                      <i class="glyphicon glyphicon-remove-circle"></i>
                    </div>   
                  </div>
                </div>  
              </div>
              <button type="submit" class="btn btn-primary" id="boton_buscar">
                <span class="glyphicon glyphicon-search" aria-hidden="true" style="right:5px;"></span>Buscar</button>

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

              <div id="example1_wrapper" style="margin-left: 200px;" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row">
                      <div class="table-responsive col-md-10">
                        <h4 style="padding-top:8px;background-color: #004AC1;text-align: center;color:#FDFEFE ;height:30px; ">INGRESO ECONOMICO DE EMERGENCIAS POR AÑO</h4>
                        <table id="example1" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example1_info" style="font-size: 12px;">
                          <thead>
                            <tr role="row">
                              
                              <th >Año</th>
                              <th >Cant. Público</th>
                              <th >Cant. Privado</th>
                              <th >Cantidad Total</th>
                              <th style="background-color: #ffe6e6">Val. Público</th>
                              <th style="background-color: #ffe6e6">Val. Privado</th> 
                              <th style="background-color: #ffe6e6">Valor Total</th>  
                                                                      
                            </tr>
                          </thead>
                          <tbody>
                         
                            <tr role="row" >
                              <td><b>2019</b></td>
                              <td>22</td>
                            
                              <td>20</td>
                              <td>20</td>
                              <td style="background-color: #ffe6e6;color: red;font-size: 15px;">50</td>
                              <td style="background-color: #ffe6e6;color: red;font-size: 15px;">26</td>
                              <td style="background-color: #ffe6e6;color: red;font-size: 15px;">25</td>
                          
                            </tr>
                      
                          </tbody>
                        </table>
                      </div>
                      <div class="col-md-10" >
                        <div id="chart"></div>
                      </div>
                      
                    </div>
        
              </div>
              <div id="example1_wrapper" style="margin-left: 200px;" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row">
                      <div class="table-responsive col-md-10">
                        <h4 style="padding-top:8px;background-color:#004AC1;text-align: center;height:30px;color:#FDFEFE;"> VENTA DE INSUMOS POR EMERGENCIA POR AÑO</h4>
                        <table id="example1" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example1_info" style="font-size: 12px;">
                          <thead>
                            <tr role="row">
                              
                              <th >Año</th>
                              <th >Cant. Público</th>
                              <th >Cant. Privado</th>
                              <th >Cantidad Total</th>
                              <th style="background-color: #ffe6e6">Val. Público</th>
                              <th style="background-color: #ffe6e6">Val. Privado</th> 
                              <th style="background-color: #ffe6e6">Valor Total</th>  
                                                                      
                            </tr>
                          </thead>
                          <tbody>
                         
                            <tr role="row" >
                              <td><b>2019</b></td>
                              <td>22</td>
                            
                              <td>20</td>
                              <td>20</td>
                              <td style="background-color: #ffe6e6;color: red;font-size: 15px;">50</td>
                              <td style="background-color: #ffe6e6;color: red;font-size: 15px;">26</td>
                              <td style="background-color: #ffe6e6;color: red;font-size: 15px;">25</td>
                          
                            </tr>
                      
                          </tbody>
                        </table>
                      </div>
                      <div class="col-md-10">
                        <div id="chart1"></div>
                      </div>
                      
                    </div>
        
              </div>

              <div id="example1_wrapper" style="margin-left: 200px;" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row">
                      <div class="table-responsive col-md-10">
                        <h4 style="color:#FDFEFE;padding-top:8px;background-color: #004AC1;text-align: center;height:30px;">PROMEDIO DE PACIENTES HOSPITALIZADO QUE LLEGARON POR EMERGENCIA</h4>
                        <table id="example1" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example1_info" style="font-size: 12px;">
                          <thead>
                            <tr role="row">
                              
                              <th >Año</th>
                              <th >Cant. Público</th>
                              <th >Cant. Privado</th>
                              <th >Cantidad Total</th>
                              <th style="background-color: #ffe6e6">Val. Público</th>
                              <th style="background-color: #ffe6e6">Val. Privado</th> 
                              <th style="background-color: #ffe6e6">Valor Total</th>  
                                                                      
                            </tr>
                          </thead>
                          <tbody>
                         
                            <tr role="row" >
                              <td><b>2019</b></td>
                              <td>22</td>
                              <td>20</td>
                              <td>20</td>
                              <td style="background-color: #ffe6e6;color: red;font-size: 15px;">50</td>
                              <td style="background-color: #ffe6e6;color: red;font-size: 15px;">26</td>
                              <td style="background-color: #ffe6e6;color: red;font-size: 15px;">25</td>
                          
                            </tr>
                      
                          </tbody>
                        </table>
                      </div>
                      <div class="col-md-10">
                        <div id="chart2"></div>
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
      ['Dr.', 'nro'],
     // ['Dr. Eduardo Brillo','1'],
      ['2019',30],
      ['2018', 10],
      ['2017', 10],
      ['2016', 40],
      ['2015', 10],
      
    ]);


    var options1 = {
      title: 'EMERGENCIAS RECIBIDAS POR AÑO',
      //hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
      is3D: true, 
      colors:[
        'red',
        '#F08080',
        '#008080',
        '#00FFFF',
        '#800000',       
        
      ],
    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_div1'));
      chart.draw(data1, options1);
  }


  google.load("visualization", "1", {packages:["corechart"]});
  google.setOnLoadCallback(drawChart2);
  function drawChart2() {
    var data2 = google.visualization.arrayToDataTable([
     ['Dr.', 'Nro'],
     ['10-17 DE AGOSTO',40],
     ['5-12 DE JUNIO',10],
     ['1-8 DE JULIO',10],
     ['17-24 DE SEPTIEMBRE',10],
     ['24-31 DE MAYO',30],
     
    ]);

    var options2 = {
      title: 'EMERGENCIAS RECIBIDAS POR SEMANA',
      //hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
      is3D: true,
      colors:[
       '#FF5733',
        '#FFC300',
        '#900C3F',
        '#DAF7A6',
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
      ['LUNES', 13],
      ['MARTES', 20],
      ['MIERCOLES', 17],
      ['JUEVES', 25],
      ['VIERNES',25],
    ]);

    var options2a = {
      title: 'EMERGENCIAS RECIBIDAS POR DIA',
      //hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
      is3D: true,
      colors:[
        '#808000',
        '#FFC300',
        '#900C3F',
        '#DAF7A6',
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
       ['PARTICULAR',20],
     ['IESS',20],
     ['HUMANA',20],
     ['MSP',20],
     ['ISSPOL',20],
    
    ]);

    var options3 = {
      title: 'EMERGENCIAS POR TIPO DE SEGURO',
      //hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
      is3D: true,
      colors:[
        '#800000',
        '#808000',
        '#900C3F',
        '#DAF7A6',
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
      ['DR. EDUARDO FAUSTO',25],
      ['DR. MIGUEL POVEDA',30],
      ['DR. FAUSTO ORTIZ',20],
      ['DR. ANTHONY CHILÁN',25],
    ]);

    var options3_ok = {
      title: 'EMERGENCIAS RECIBIDAS POR DOCTOR',
      //hAxis: {title: 'Year', titleTextStyle: {color: 'red'}}
      is3D: true,
      colors:[
       'pink',
       '#800000',
       '#808080',
       'red',
       '#808000'
      ],

    };

    var chart = new google.visualization.PieChart(document.getElementById('chart_div3_ok'));
      chart.draw(data3_ok, options3_ok);
  }

      $(window).resize(function(){
      drawChart1();
      drawChart2();
      drawChart3();
      drawChart4();
      });  


</script>
<script type="text/javascript">
  
  google.load('visualization', '1', {'packages': ['corechart']});
  google.setOnLoadCallback (createChart);
  function createChart() {  
      //create data table object
      var dataTable = new google.visualization.DataTable();

      //define columns
      dataTable.addColumn('string','Año');
      dataTable.addColumn('number', 'Total');
      dataTable.addColumn('number', 'Publico');
      dataTable.addColumn('number', 'Privado');
      


      //define rows of data
      dataTable.addRows([
        
        
        ['2019',10,30,35]]);

      //instantiate our chart object
      var chart = new google.visualization.ColumnChart (document.getElementById('chart'));

      //define options for visualization
      var options = {width: 600 , height: 240, is3D: true, title: 'INGRESO ECONOMICO DE EMERGENCIAS POR AÑO'};

      //draw our chart
      chart.draw(dataTable, options);

      
      }

       google.setOnLoadCallback (createChart1);
      function createChart1() {
     
         var dataTable = new google.visualization.DataTable();
          //define columns
          dataTable.addColumn('string','Año');
          dataTable.addColumn('number', 'Total');
          dataTable.addColumn('number', 'Publico');
          dataTable.addColumn('number', 'Privado'); 
          //define rows of data
          dataTable.addRows([
            
            
            ['2019',20,30,50]]);

          
          var chart = new google.visualization.ColumnChart (document.getElementById('chart1'));
          var options = {width: 600 , height: 240, is3D: true, title: 'VENTA DE INSUMOS POR EMERGENCIA POR AÑO'};

          chart.draw(dataTable, options);

        }
         google.setOnLoadCallback (createChart2);
      function createChart2() {
     
         var dataTable = new google.visualization.DataTable();
          //define columns
          dataTable.addColumn('string','Año');
          dataTable.addColumn('number', 'Total');
          dataTable.addColumn('number', 'Publico');
          dataTable.addColumn('number', 'Privado'); 
          //define rows of data
          dataTable.addRows([
            
            
            ['2019',60,10,40]]);

          
          var chart = new google.visualization.ColumnChart (document.getElementById('chart2'));
          var options = {width: 600 , height: 240, is3D: true, title: 'PROMEDIO DE PACIENTES HOSPITALIZADO QUE LLEGARON POR EMERGENCIA'};

          chart.draw(dataTable, options);

        }


 
 
</script>

             
      

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>


<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">

  $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '2019/08/02',
            
            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            
            
            defaultDate: '2019/08/02',
            
            });
  });

  function buscar()
{
  
}

</script>
@endsection