<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!--script src="{{ asset ("/bower_components/Chart.js/dist/Chart.min.js") }}"></script-->

<script type="text/javascript" src="https://www.google.com/jsapi"></script>

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
        <div class="row">
          <div class="col-12" >
            <center><h3 class="box-title" id="titulo1" ></h3></center>
          </div>  
          <div class="col-lg-6 col-sm-12">
            
            <div id="example2_wrapper" >
              <center>
                <div class="row">
                  <div class="table-responsive">
                    
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
                          @php
                            $val_pb = 0; $val_hl = 0;$val_hl2 = 0;$val_il = 0;$val_il2 = 0;
                            $valor_pb = $or_aniomes_doctor_publico->where('id_doctor_ieced',$value->id_doctor_ieced)->first();
                            if(!is_null($valor_pb)){
                              $val_pb = round($valor_pb->valor * $per_pb ,2);
                            } 

                            $valor_pr = $or_aniomes_doctor_privado->where('id_doctor_ieced',$value->id_doctor_ieced)->first();
                            $valor_hl = $or_aniomes_doctor_privado_hl->where('id_doctor_ieced',$value->id_doctor_ieced)->first();
                            if(!is_null($valor_pr)){
                              if(!is_null($valor_hl)){
                                $val_hl = $valor_hl->valor;  
                              }
                              $val_il = $valor_pr->valor - $val_hl; 
                            }
                            $val_hl = round($val_hl * $per_hl ,2);
                            $val_il = round($val_il * $per_il ,2); 
                            
                            $valor_pa = $or_aniomes_doctor_particular->where('id_doctor_ieced',$value->id_doctor_ieced)->first();
                            $valor_hl = $or_aniomes_doctor_particular_hl->where('id_doctor_ieced',$value->id_doctor_ieced)->first();
                            if(!is_null($valor_pa)){
                              if(!is_null($valor_hl)){
                                $val_hl2 = $valor_hl->valor;  
                              }
                              $val_il2 = $valor_pa->valor - $val_hl2; 
                            }
                            $val_hl2 = round($val_hl2 * $per_hl ,2);
                            $val_il2 = round($val_il2 * $per_il ,2); 
                            
                          @endphp
                          <tr>
                            <td>{{$value->apellido1}} {{$value->nombre1}}</td>
                            <td style="text-align: right;">{{$value->cantidad}}</td>
                            <td style="text-align: right;"> {{round($value->valor,2)}}</td>
                            <td style="text-align: right;">{{$val_pb + $val_hl + $val_il + $val_hl2 + $val_il2}}</td>
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

      <div class="col-md-12">
        <div class="row">
          <div class="col-12" >
            <center><h3 class="box-title" id="titulo2" ></h3></center>
          </div>  
          <div class="col-lg-6 col-sm-12">
            <div id="example2_wrapper" >
              <div class="row">
                <div class="table-responsive col-md-12">
                  
                  <table id="example21" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
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
          <div class="col-lg-6 col-sm-12">
              <canvas id="canvas_datos2" style="max-width: 100%;"></canvas>
          </div> 
        </div>
      </div> 

      <div class="col-md-12">
        <div class="row">
          <div class="col-12" >
            <center><h3 class="box-title" id="titulo4" ></h3></center>
          </div>  
          <div class="col-lg-6 col-sm-12">
            <div id="example2_wrapper" >
              <div class="row">
                <div class="table-responsive col-md-12">
                  
                  <table id="example23" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
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
          <div class="col-lg-6 col-sm-12">
              <canvas id="canvas_datos4" style="max-width: 100%;"></canvas>
          </div> 
        </div>
      </div>

      <div class="col-md-12">
        <div class="row">
          <div class="col-12" >
            <center><h3 class="box-title" id="titulo3" ></h3></center>
          </div>  
          <div class="col-lg-6 col-sm-12">
            <div id="example2_wrapper" >
              <div class="row">
                <div class="table-responsive col-md-12">
                  
                  <table id="example22" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
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
          <div class="col-lg-6 col-sm-12">
              <canvas id="canvas_datos3" style="max-width: 100%;"></canvas>
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
<script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>
<script src="{{ asset ("bower_components/datatables.net/js/jquery.dataTables.min.js")}}"></script>


<script type="text/javascript" >

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

    @php
      $total1 = 0;
        foreach ($or_aniomes_doctor as $value){
          $total1 = $total1 + $value->valor; 
        }
        
    @endphp
    var config = {
      type: 'pie',

      data: {
        datasets: [{

          data: [
            @foreach($or_aniomes_doctor as $value)
              '{{round($value->valor,2)}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($or_aniomes_doctor as $value)
              '{{$value->color}}',
            @endforeach

          ],
        }],
        labels: [
          @foreach($or_aniomes_doctor as $value)
            '{{$value->apellido1}} {{$value->nombre1}}',
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
    $('#titulo1').text('Ordenes de Laboratorio por Doctor {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total1,2)}}');
    var ctx = document.getElementById('canvas_datos').getContext('2d');

      window.myPie = new Chart(ctx, config);
</script>
<script type="text/javascript" >

    $('#example21').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

    @php
      $total2 = 0;
        foreach ($or_aniomes_doctor_publico as $value){
          $total2 = $total2 + $value->valor; 
        }
        
    @endphp
    var config = {
      type: 'pie',

      data: {
        datasets: [{

          data: [
            @foreach($or_aniomes_doctor_publico as $value)
              '{{round($value->valor,2)}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($or_aniomes_doctor_publico as $value)
              '{{$value->color}}',
            @endforeach

          ],
        }],
        labels: [
          @foreach($or_aniomes_doctor_publico as $value)
            '{{$value->apellido1}} {{$value->nombre1}}',
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
                    
                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$total2}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%'; 

                    

                    return label;
                }
            }
        }
      }
      
    };
    $('#titulo2').text('Ordenes de Laboratorio Públicas {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total2,2)}}');
    var ctx = document.getElementById('canvas_datos2').getContext('2d');

      window.myPie = new Chart(ctx, config);
</script>

<script type="text/javascript" >

    $('#example22').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

    @php
      $total3 = 0;
        foreach ($or_aniomes_doctor_particular as $value){
          $total3 = $total3 + $value->valor; 
        }
        
    @endphp
    var config = {
      type: 'pie',

      data: {
        datasets: [{

          data: [
            @foreach($or_aniomes_doctor_particular as $value)
              '{{round($value->valor,2)}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($or_aniomes_doctor_particular as $value)
              '{{$value->color}}',
            @endforeach

          ],
        }],
        labels: [
          @foreach($or_aniomes_doctor_particular as $value)
            '{{$value->apellido1}} {{$value->nombre1}}',
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
                    
                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$total3}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%'; 

                    

                    return label;
                }
            }
        }
      }
      
    };
    $('#titulo3').text('Ordenes de Laboratorio Particulares {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total3,2)}}');
    var ctx = document.getElementById('canvas_datos3').getContext('2d');

      window.myPie = new Chart(ctx, config);
</script>

<script type="text/javascript" >

    $('#example23').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    });

    @php
      $total4 = 0;
        foreach ($or_aniomes_doctor_privado as $value){
          $total4 = $total4 + $value->valor; 
        }
        
    @endphp
    var config = {
      type: 'pie',

      data: {
        datasets: [{

          data: [
            @foreach($or_aniomes_doctor_privado as $value)
              '{{round($value->valor,2)}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($or_aniomes_doctor_privado as $value)
              '{{$value->color}}',
            @endforeach

          ],
        }],
        labels: [
          @foreach($or_aniomes_doctor_privado as $value)
            '{{$value->apellido1}} {{$value->nombre1}}',
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
                    
                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$total4}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%'; 

                    

                    return label;
                }
            }
        }
      }
      
    };
    $('#titulo4').text('Ordenes de Laboratorio Privadas {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total4,2)}}');
    var ctx = document.getElementById('canvas_datos4').getContext('2d');

      window.myPie = new Chart(ctx, config);
</script>

