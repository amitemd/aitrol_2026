@extends('laboratorio.estadistico.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!--script src="{{ asset ("/bower_components/Chart.js/dist/Chart.min.js") }}"></script-->

<script type="text/javascript" src="http://www.google.com/jsapi"></script>

<style type="text/css">
th, td {
    padding: 3px !important;
    
} 
.codigo{
  background-color: #e6ffff;
}
.total{
  background-color: #ffddcc;
}



table.dataTable thead .sorting_asc:after, table.dataTable thead .sorting_desc:after{
  opacity: 100;
}
</style>

<!-- Main content -->
<section class="content">
  <div class="box box-success">
    @php $mes_txt = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']; @endphp
    <!--a class="btn btn-primary" onclick="goBack()"><span class="glyphicon glyphicon-arrow-left"></span> Regresar</a-->
    <!-- /.box-header -->
    <div class="box-body">
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">Cantidad Ordenes de Laboratorio Covid {{$mes_txt[$mes-1]}}/{{$anio}}</h3>

            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
              </button>
              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body no-padding">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
              <div class="row">
                <div class="table-responsive col-md-12">
                  
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 13px;">
                    <thead>
                      <tr role="row" style="background-color: #009999; color: white;">
                        <th rowspan="2">Paquete</th>
                        @php $x=0; @endphp
                        @foreach($arr as $a)
                          @php $ac_dom[$x]=0;$ac_valor[$x]=0;$x++; @endphp
                          <th colspan="2">Del {{$a['inicio']}} Hasta {{$a['fin']}}</th>
                        @endforeach                               
                          <th colspan="2">Total</th>
                      </tr>
                      <tr role="row" style="background-color: #009999; color: white;">
                        @foreach($arr as $a)
                           <th >Domicilio</th><th >Presencial</th>
                        @endforeach
                          <th >Domicilio</th><th >Presencial</th>                               
                      </tr>
                    </thead>
                    <tbody>
                      @php $tot_dom = 0; $tot_tot = 0; @endphp 
                      @foreach($arr_orden as $value)
                        <tr>
                          <td style="font-size: 10px;"><b>{{$value['nombre']}}</b></td>
                          @php $x=0;$acum_dom=0;$acum_tot=0; @endphp
                          @foreach($value['arr'] as $a)
                            @php
                              $ac_dom[$x] += $a['cantidad_dom'];$ac_valor[$x] += $a['cantidad'];$x++;$acum_dom+=$a['cantidad_dom'];
                              $acum_tot += $a['cantidad']; 
                            @endphp
                            <td style="text-align: right;">{{$a['cantidad_dom']}}</td>
                            <td style="text-align: right;">{{$a['cantidad'] - $a['cantidad_dom']}}</td>
                          @endforeach
                            <td style="text-align: right;">{{$acum_dom}}</td>
                            <td style="text-align: right;">{{$acum_tot - $acum_dom}}</td>
                            @php $tot_dom += $acum_dom;$tot_tot += $acum_tot; @endphp
                        </tr>
                      @endforeach
                      <tr>
                        <td class="total" style="font-size: 10px;"><b>Total</b></td>
                        @php $x = 0; @endphp
                        @foreach($value['arr'] as $a)
                          
                          <td class="total" style="text-align: right;">{{$ac_dom[$x]}}</td>
                          <td class="total" style="text-align: right;">{{$ac_valor[$x] - $ac_dom[$x]}}</td>
                          @php $x++; @endphp
                        @endforeach
                          <td class="total" style="text-align: right;">{{$tot_dom}}</td>
                          <td class="total" style="text-align: right;">{{$tot_tot - $tot_dom}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>  
        </div>
      </div>    

      <!--div class="col-md-6">
          <canvas id="canvas_datos" style="max-width: 100%;"></canvas>
      </div--> 
      
      <div class="col-md-12">
        <div class="box box-success">
          <div class="box-header with-border">
            <h3 class="box-title">Valor Ordenes de Laboratorio Covid ($) {{$mes_txt[$mes-1]}}/{{$anio}}</h3>

            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
              </button>
              <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body no-padding"> 
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
              <div class="row">
                <div class="table-responsive col-md-12">
                  
                  <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 13px;">
                    <thead>
                      <tr role="row" style="background-color: #009999; color: white;">
                        <th width="16%" rowspan="2">Paquete</th>
                        @php $x=0; @endphp
                        @foreach($arr as $a)
                          @php $ac_dom[$x]=0;$ac_valor[$x]=0;$x++; @endphp
                          <th width="16%" colspan="2">Del {{$a['inicio']}} Hasta {{$a['fin']}}</th>
                        @endforeach
                        <th colspan="2">Total</th>                               
                      </tr>
                      <tr role="row" style="background-color: #009999; color: white;">
                        @foreach($arr as $a)
                           <th >Domicilio</th><th >Presencial</th>
                        @endforeach
                          <th >Domicilio</th><th >Presencial</th>                               
                      </tr>
                    </thead>
                    <tbody>
                      @php $tot_dom = 0; $tot_tot = 0; @endphp 
                      @foreach($arr_orden as $value)
                        <tr>
                          <td style="font-size: 10px;"><b>{{$value['nombre']}}</b></td>
                          @php $x=0;$acum_dom=0;$acum_tot=0; @endphp
                          @foreach($value['arr'] as $a)
                            @php 
                              $ac_dom[$x] += $a['valor_dom'];$ac_valor[$x] += $a['valor'];$x++;
                              $acum_dom+=$a['valor_dom'];$acum_tot += $a['valor'];  
                            @endphp
                            <td style="text-align: right;">{{number_format(round($a['valor_dom'],2),2)}}</td>
                            <td style="text-align: right;">{{number_format(round($a['valor'] - $a['valor_dom'],2),2)}}</td>
                          @endforeach  
                          <td style="text-align: right;">{{number_format(round($acum_dom,2),2)}}</td>
                          <td style="text-align: right;">{{number_format(round($acum_tot - $acum_dom,2),2)}}</td>
                          @php $tot_dom += $acum_dom;$tot_tot += $acum_tot; @endphp
                        </tr>
                      @endforeach

                      <tr>
                        <td class="total" style="font-size: 10px;"><b>Total</b></td>
                        @php $x = 0; @endphp
                        @foreach($value['arr'] as $a)
                          <td class="total" style="text-align: right;">{{number_format(round($ac_dom[$x],2),2)}}</td>
                          <td class="total" style="text-align: right;">{{number_format(round($ac_valor[$x] - $ac_dom[$x],2),2)}}</td>
                          @php $x++; @endphp 
                        @endforeach
                        <td class="total" style="text-align: right;">{{number_format(round($tot_dom,2),2) }}</td>
                        <td class="total" style="text-align: right;">{{number_format(round($tot_tot - $tot_dom,2),2)}}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            
            </div>
          </div>  
         
          <!--div class="col-md-6">
              <canvas id="canvas_datos" style="max-width: 100%;"></canvas>
          </div--> 
        </div> 
      </div>

    
    <!-- /.box-body -->
    </div>
  </div>  
</section>
<!-- /.content -->
  
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
<script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>

<?php /*
  <script type="text/javascript">

    $(document).ready(function($){

      $("#body2").addClass('sidebar-collapse');
      var total = 0;
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
        //console.log('{{$value->id_doctor_ieced}}:'+elemento3);
        if(elemento3!=''){
          elemento3 = parseFloat(elemento3);
          
        }else{
          elemento3 = 0;
        }
        //console.log('{{$value->id_doctor_ieced}}:'+elemento3);
        //elemento = elemento + parseFloat($('#pri'+{{$value->id_doctor_ieced}}).text());
        numero = Math.round((elemento + elemento2 + elemento3)*100)/100;
        $('#'+'{{$value->id_doctor_ieced}}').text(numero.toLocaleString("en-US"));
        total = total + numero;
      @endforeach 
      $('#xcomision').text( Math.round((total + {{$xvalor_codigo * $per_ex}})*100)/100 );   

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

      $('#example2_ex').DataTable({
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


    

  

  </script> 




  <script type="text/javascript" >

    @php
      $total1 = 0;
        foreach ($or_aniomes_doctor as $value){
          $total1 = $total1 + $value->valor; 
        }
        foreach ($or_aniomes_doctor_codigo as $value){
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
            @foreach($or_aniomes_doctor_codigo as $value)
              '{{round($value->valor,2)}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($or_aniomes_doctor as $value)
              '{{$value->color}}',
            @endforeach
            @foreach($or_aniomes_doctor_codigo as $value)
              'blue',
            @endforeach

          ],
        }],
        labels: [
          @foreach($or_aniomes_doctor as $value)
            '{{$value->apellido1}} {{$value->nombre1}}',
          @endforeach
          @foreach($or_aniomes_doctor_codigo as $value)
            'DOCTOR CODIGO',
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
    $('#titulo1').text('Ordenes de Laboratorio por Doctor Mes: {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total1,2)}}');
    var ctx = document.getElementById('canvas_datos').getContext('2d');

      window.myPie = new Chart(ctx, config);
  </script>
  <script type="text/javascript" >

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
    $('#titulo1').text('Ordenes de Laboratorio por Doctor Mes: {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total2,2)}}');
    var ctx = document.getElementById('canvas_datos2').getContext('2d');

      window.myPie = new Chart(ctx, config);
  </script>
  
  <script type="text/javascript" >

    @php
      $total3 = 0;
        foreach ($or_aniomes_doctor_privado as $value){
          $total3 = $total3 + $value->valor; 
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
                    
                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$total3}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%'; 

                    

                    return label;
                }
            }
        }
      }
      
    };
    $('#titulo1').text('Ordenes de Laboratorio por Doctor Mes: {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total2,2)}}');
    var ctx = document.getElementById('canvas_datos3').getContext('2d');

      window.myPie = new Chart(ctx, config);
  </script>

  <script type="text/javascript" >

    @php
      $total4 = 0;
        foreach ($or_aniomes_doctor_particular as $value){
          $total4 = $total4 + $value->valor; 
        }
        
        foreach ($or_aniomes_doctor_particular_not1 as $value){
          $total4 = $total4 + $value->valor; 
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
            @foreach ($or_aniomes_doctor_particular_not1 as $value)
              '{{round($value->valor,2)}}',
            @endforeach
            
          ],
          backgroundColor: [
            @foreach($or_aniomes_doctor_particular as $value)
              '{{$value->color}}',
            @endforeach
            @foreach ($or_aniomes_doctor_particular_not1 as $value)
              'blue',
            @endforeach


          ],
        }],
        labels: [
          @foreach($or_aniomes_doctor_particular as $value)
            '{{$value->apellido1}} {{$value->nombre1}}',
          @endforeach
          @foreach ($or_aniomes_doctor_particular_not1 as $value)
            'DOCTOR CODIGO',
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
    $('#titulo1').text('Ordenes de Laboratorio por Doctor Mes: {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total2,2)}}');
    var ctx = document.getElementById('canvas_datos4').getContext('2d');

      window.myPie = new Chart(ctx, config);
  </script>

  <script type="text/javascript" >

    @php
      $total5 = 0;
        foreach ($or_aniomes_doctor_particular_not as $value){
          $total5 = $total5 + $value->valor; 
        }
        
    @endphp
    var config = {
      type: 'pie',

      data: {
        datasets: [{

          data: [
            @foreach($or_aniomes_doctor_particular_not as $value)
              '{{round($value->valor,2)}}',
            @endforeach
          ],
          backgroundColor: [
            @foreach($or_aniomes_doctor_particular_not as $value)
            @php
            $doc_externo = Sis_medico\Labs_doc_externos::find($value->codigo);
            @endphp
              '@if(!is_null($doc_externo)){{$doc_externo->color}}@endif',
            @endforeach
          ],
        }],
        labels: [
          
           @foreach($or_aniomes_doctor_particular_not as $value)
            @php
            $doc_externo = Sis_medico\Labs_doc_externos::find($value->codigo);
            @endphp
              '@if(!is_null($doc_externo)){{"EXTERNO"}} {{$doc_externo->apellido1}} {{$doc_externo->nombre1}} @else {{$value->codigo}}@endif',
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
                    
                    var pct = (data.datasets[0].data[tooltipItem.index]/{{$total5}})*100;
                    pct = Math.round(pct * 100) / 100;
                    label = label+' '+pct+'%'; 

                    

                    return label;
                }
            }
        }
      }
      
    };
    $('#titulo1').text('Ordenes de Laboratorio por Doctor Externo Mes: {{$mes_txt[$mes-1]}}/{{$anio}}- Total: $ {{round($total2,2)}}');
    var ctx = document.getElementById('canvas_datos5').getContext('2d');

      window.myPie = new Chart(ctx, config);
  </script> */ ?>

@endsection