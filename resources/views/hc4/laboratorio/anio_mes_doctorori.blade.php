<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!--script src="{{ asset ("/bower_components/Chart.js/dist/Chart.min.js") }}"></script-->



<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
    
} 

@php

  $per_hl = 0.08;
  $per_il = 0.02;
  $per_pb = 0.01;

@endphp

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
          @php $mes_txt = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; @endphp

        </div>
        <!--a class="btn btn-primary" onclick="goBack()"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a-->
        
      </div>
    </div>
    <!-- /.box-header -->
    <div class="box-body">

      <div class="col-md-12">
        <div class="col-md-12" style="z-index: 9999;">
          <center><h3 class="box-title">Total Ordenes de Laboratorio por Doctor de {{$mes_txt[$mes-1]}}/{{$anio}}</h3></center>
        </div>  
        <div class="col-md-6">
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <thead>
                    <tr role="row" style="background-color: #009999; color: white;">
                      <th >Doctor</th>
                      <th >Ordenes</th>
                      <th >Valor ($)</th>   
                      <th >Comision ($)</th>                               
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($or_aniomes_doctor as $value)
                      <tr>
                        <td>{{$value->apellido1}} {{$value->nombre1}}</td>
                        <td style="text-align: right;">{{$value->cantidad}}</td>
                        <td style="text-align: right;"> {{round($value->valor,2)}}</td>
                        <td style="text-align: right;"><span id="{{$value->id_doctor_ieced}}"></span></td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          
          </div>
        </div>  


        <div class="col-md-6">
          <div id="chart" style="margin-top: -75px;"></div>
        </div>
      </div> 
      <?php /*
      <div class="col-md-12" >
        <div class="col-md-12" style="z-index: 9999;">
          <center ><h3 class="box-title">Ordenes de Laboratorio Públicas por Doctor de {{$mes_txt[$mes-1]}}/{{$anio}}</h3></center>
        </div>  
        <div class="col-md-6">

          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                
                <table id="example2_pub" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <thead>
                    <tr role="row" style="background-color: #009999; color: white;">
                      <th >Doctor</th>
                      <th >Ordenes</th>
                      <th >Valor ($)</th> 
                      <th >Comision ($)</th>                                 
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($or_aniomes_doctor_publico as $value)
                      <tr>
                        <td>{{$value->apellido1}} {{$value->nombre1}}</td>
                        <td style="text-align: right;">{{$value->cantidad}}</td>
                        <td style="text-align: right;"> {{round($value->valor,2)}}</td>
                        <td style="text-align: right;"><span id="pub{{$value->id_doctor_ieced}}"> {{round($value->valor*$per_pb,2)}}</span></td>

                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          
          </div>
        </div>  


        <div class="col-md-6">
          <div id="chart_pub" style="margin-top: -75px;"></div>
        </div>
      </div>

      <div class="col-md-12">
        <div class="col-md-12" style="z-index: 9999;">
          <center ><h3 class="box-title">Ordenes de Laboratorio Privadas por Doctor de {{$mes_txt[$mes-1]}}/{{$anio}}</h3></center>
        </div>  
        <div class="col-md-6">

          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                
                <table id="example2_pri" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <thead>
                    <tr role="row" style="background-color: #009999; color: white;">
                      <th >Doctor</th>
                      <th >Ordenes</th>
                      <th >Valor ($)</th> 
                      <th >HumanLabs ($)</th>  
                      <th >Referido ($)</th>
                      <th >Comision ($)</th>                                 
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($or_aniomes_doctor_privado as $value)
                      <tr>
                        @php
                          $val_hl = 0; 
                          $total = $value->valor;
                          $valor_hl = $or_aniomes_doctor_privado_hl->where('id_doctor_ieced',$value->id_doctor_ieced)->first();
                          if(!is_null($valor_hl)){
                            $val_hl = round($valor_hl->valor,2);
                          } 
                          

                          $com_hl = $val_hl*$per_hl;
                          $com_il = ($total - $val_hl)*$per_il;
                          $com = round($com_hl + $com_il,2);
                        @endphp
                        <td>{{$value->apellido1}} {{$value->nombre1}}</td>
                        <td style="text-align: right;">{{$value->cantidad}}</td>
                        <td style="text-align: right;"> {{round($total,2)}}</td>
                        
                        <td style="text-align: right;"> {{$val_hl}}</td>
                        <td style="text-align: right;"> {{round($total - $val_hl,2)}}</td>
                        <td style="text-align: right;"><span id="pri{{$value->id_doctor_ieced}}"> {{$com}}</span></td>

                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          
          </div>
        </div>    


        <div class="col-md-6">
          <div id="chart_pri" style="margin-top: -75px;"></div>
        </div>
      </div>


      <div class="col-md-12">
        <div class="col-md-12" style="z-index: 9999;">
          <center ><h3 class="box-title">Ordenes de Laboratorio Particulares por Doctor de {{$mes_txt[$mes-1]}}/{{$anio}}</h3></center>
        </div>  
        <div class="row">
          <div class="col-md-6">

            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
              <div class="row">
                <div class="table-responsive col-md-12">
                  
                  <table id="example2_par" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                      <tr role="row" style="background-color: #009999; color: white;">
                        <th >Doctor</th>
                        <th >Ordenes</th>
                        <th >Valor ($)</th> 
                        <th >HumanLabs ($)</th>  
                        <th >Referido ($)</th>
                        <th >Comision ($)</th>                                 
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($or_aniomes_doctor_particular as $value)
                        <tr>
                          @php
                            $val_hl = 0; 
                            $total = round($value->valor,2);
                            $valor_hl = $or_aniomes_doctor_particular_hl->where('id_doctor_ieced',$value->id_doctor_ieced)->first();
                            if(!is_null($valor_hl)){
                              $val_hl = round($valor_hl->valor,2);
                            } 
                            $com_hl = $val_hl*$per_hl;
                            $com_il = ($total - $val_hl)*$per_il;
                            $com = round($com_hl + $com_il,2);
                          @endphp
                          <td>{{$value->apellido1}} {{$value->nombre1}}</td>
                          <td style="text-align: right;">{{$value->cantidad}}</td>
                          <td style="text-align: right;"> {{$total}}</td>
                          
                          <td style="text-align: right;"> {{$val_hl}}</td>
                          <td style="text-align: right;"> {{$total - $val_hl}}</td>
                          <td style="text-align: right;"><span id="part{{$value->id_doctor_ieced}}"> {{$com}}</span></td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            
            </div>
          </div>  
          <div class="col-md-6">
            <div id="chart_part" style="margin-top: -75px;"></div>
          </div>
        </div>  
      </div>   */ ?>
      
    </div>

    
  <!-- /.box-body -->
  </div>
  
  
</section>
<!-- /.content -->
  
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

  $(document).ready(function($){


    @foreach($or_aniomes_doctor as $value)
      var elemento = $('#pub'+'{{$value->id_doctor_ieced}}').text();
      
      if(elemento!=''){
        elemento = parseFloat(elemento);

      }else{
        elemento = 0;
      }

      var elemento2 = $('#pri'+'{{$value->id_doctor_ieced}}').text();
      if(elemento2!=''){
        elemento2 = parseFloat(elemento2);
      }else{
        elemento2 = 0;
      }
      var elemento3 = $('#part'+'{{$value->id_doctor_ieced}}').text();
      console.log('{{$value->id_doctor_ieced}}:'+elemento3);
      if(elemento3!=''){
        elemento3 = parseFloat(elemento3);
        
      }else{
        elemento3 = 0;
      }
      console.log('{{$value->id_doctor_ieced}}:'+elemento3);
      //elemento = elemento + parseFloat($('#pri'+{{$value->id_doctor_ieced}}).text());
      
      $('#'+'{{$value->id_doctor_ieced}}').text(Math.round((elemento + elemento2 + elemento3)*100)/100);
    @endforeach    

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

    $('#example2_pub').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

    $('#example2_pri').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

    $('#example2_par').DataTable({
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




<script type="text/javascript">
            //load the Google Visualization API and the chart
            google.load('visualization', '1', {'packages': ['corechart']});
            //google.load('visualization', '1', {'packages': ['piechart']});
            
            
 
            //set callback
            google.setOnLoadCallback (createChart);
            /*google.setOnLoadCallback (createChart_pub);
            google.setOnLoadCallback (createChart_pri);
            google.setOnLoadCallback (createChart_part);*/
            
           

            function createChart() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Doctor');
                dataTable.addColumn('number', 'Cantidad');
                

 
                //define rows of data
                dataTable.addRows([@foreach($or_aniomes_doctor as $value)['{{$value->apellido1}} {{$value->nombre1}}',{{$value->valor}}], @endforeach ]);
 
                //instantiate our chart object
                var chart = new google.visualization.PieChart (document.getElementById('chart'));
 
                //define options for visualization
                var options = {width: 750, height: 500, is3D: false, colors: [@foreach($or_aniomes_doctor as $value)'{{$value->color}}',@endforeach], legend:{ textStyle: {fontSize: 10}} };
 
                //draw our chart
                chart.draw(dataTable, options);
 
            }

            <?php /*
            function createChart_pub() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Doctor');
                dataTable.addColumn('number', 'Cantidad');
                

 
                //define rows of data
                dataTable.addRows([@foreach($or_aniomes_doctor_publico as $value)['{{$value->apellido1}} {{$value->nombre1}}',{{$value->valor}}], @endforeach ]);
 
                //instantiate our chart object
                var chart = new google.visualization.PieChart (document.getElementById('chart_pub'));
 
                //define options for visualization
                var options = {width: 750, height: 500, is3D: false, colors: [@foreach($or_aniomes_doctor_publico as $value)'{{$value->color}}',@endforeach], legend:{ textStyle: {fontSize: 10}} };
 
                //draw our chart
                chart.draw(dataTable, options);
 
            }

            function createChart_pri() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Doctor');
                dataTable.addColumn('number', 'Cantidad');
                

 
                //define rows of data
                dataTable.addRows([@foreach($or_aniomes_doctor_privado as $value)['{{$value->apellido1}} {{$value->nombre1}}',{{$value->valor}}], @endforeach ]);
 
                //instantiate our chart object
                var chart = new google.visualization.PieChart (document.getElementById('chart_pri'));
 
                //define options for visualization
                var options = {width: 750, height: 500, is3D: false, colors: [@foreach($or_aniomes_doctor_privado as $value)'{{$value->color}}',@endforeach], legend:{ textStyle: {fontSize: 10}} };
 
                //draw our chart
                chart.draw(dataTable, options);
 
            }

            function createChart_part() {
 
                //create data table object
                var dataTable = new google.visualization.DataTable();
 
                //define columns
                dataTable.addColumn('string','Doctor');
                dataTable.addColumn('number', 'Cantidad');
                

 
                //define rows of data
                dataTable.addRows([@foreach($or_aniomes_doctor_particular as $value)['{{$value->apellido1}} {{$value->nombre1}}',{{$value->valor}}], @endforeach ]);
 
                //instantiate our chart object
                var chart = new google.visualization.PieChart (document.getElementById('chart_part'));
 
                //define options for visualization
                var options = {width: 750, height: 500, is3D: false, colors: [@foreach($or_aniomes_doctor_particular as $value)'{{$value->color}}',@endforeach], legend:{ textStyle: {fontSize: 10}}  };
 
                //draw our chart
                chart.draw(dataTable, options);
 
            }*/ ?>

           
        </script>