@extends('laboratorio.estadistico.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<!--script src="{{ asset ("/bower_components/Chart.js/dist/Chart.min.js") }}"></script-->

<script type="text/javascript" src="http://www.google.com/jsapi"></script>

<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th {
    padding: 0.4% ;
    
} 
.codigo{
  background-color: #e6ffff;
}
.total{
  background-color: #ffddcc;
}

@php

  $per_hl = 0.08;
  $per_ex = 0.10;
  /*$per_il = 0.02;
  $per_pb = 0.01;*/
  $per_il = 0.00;
  $per_pb = 0.00;

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
                      <th width="25%">Doctor</th>
                      <th width="25%">Ordenes</th>
                      <th width="25%">Valor ($)</th>   
                      <th width="25%">Comision ($)</th>                               
                    </tr>
                  </thead>
                  <tbody>
                    @php $xcant=0;$xvalor=0;$xcomision=0;$xvalor_codigo=0; @endphp
                    @foreach($or_aniomes_doctor as $value)
                      @php 
                        $xcant=$xcant+$value->cantidad; 
                        $xvalor=$xvalor+round($value->valor,2); 
                      @endphp
                      <tr>
                        <td>{{$value->apellido1}} {{substr($value->apellido2,0,1)}}. {{$value->nombre1}}</td>
                        <td style="text-align: right;">{{$value->cantidad}}</td>
                        <td style="text-align: right;"> {{number_format(round($value->valor,2),2)}}</td>
                        <td style="text-align: right;"><span id="{{$value->id_doctor_ieced}}"></span></td>
                      </tr>
                    @endforeach
                    @foreach($or_aniomes_doctor_codigo as $value)
                      @php 
                        $xcant=$xcant+$value->cantidad; 
                        $xvalor_codigo=$xvalor_codigo+round($value->valor,2); 
                      @endphp
                      <tr>
                        <td class="codigo">DOCTOR CODIGO</td>
                        <td class="codigo" style="text-align: right;">{{$value->cantidad}}</td>
                        <td class="codigo" style="text-align: right;">{{number_format(round($value->valor,2),2)}}</td>
                        <td class="codigo" style="text-align: right;">{{number_format(round($value->valor * $per_ex,2),2)}}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>

                <table id="example2x" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <tbody>
                      <tr>
                        <td class="total" width="25%">TOTAL</td>
                        <td class="total" width="25%" style="text-align: right;">{{$xcant}}</td>
                        <td class="total" width="25%" style="text-align: right;">{{number_format($xvalor + $xvalor_codigo ,2)}}</td>
                        <td class="total" width="25%" style="text-align: right;"><span id="xcomision"></span></td>
                      </tr>
                  </tbody>
                </table>

              </div>
            </div>
          
          </div>
        </div>  

        <div class="col-md-6">
            <canvas id="canvas_datos" style="max-width: 100%;"></canvas>
        </div> 
      </div> 
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
                      <th width="25%" >Doctor</th>
                      <th width="25%" >Ordenes</th>
                      <th width="25%" >Valor ($)</th> 
                      <th width="25%" >Comision ($)</th>                                 
                    </tr>
                  </thead>
                  <tbody>
                    @php $xp_cantidad=0; $xp_valor = 0; $xp_comi=0;@endphp
                    @foreach($or_aniomes_doctor_publico as $value)
                      @php $xp_cantidad+=$value->cantidad; $xp_valor+=round($value->valor,2); $xp_comi+=round($value->valor*$per_pb,2);@endphp
                      <tr>
                        <td>{{$value->apellido1}} {{substr($value->apellido2,0,1)}}. {{$value->nombre1}}</td>
                        <td style="text-align: right;">{{$value->cantidad}}</td>
                        <td style="text-align: right;"> {{number_format(round($value->valor,2),2)}}</td>
                        <td style="text-align: right;"><span id="pub{{$value->id_doctor_ieced}}"> {{number_format(round($value->valor*$per_pb,2),2)}}</span></td>

                      </tr>
                    @endforeach
                  </tbody>
                </table>
                <table id="example2x" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <tbody>
                      <tr>
                        <td class="total" width="25%">TOTAL</td>
                        <td class="total" width="25%" style="text-align: right;">{{$xp_cantidad}}</td>
                        <td class="total" width="25%" style="text-align: right;">{{number_format($xp_valor ,2)}}</td>
                        <td class="total" width="25%" style="text-align: right;">{{number_format($xp_comi ,2)}}</td>
                      </tr>
                  </tbody>
                </table>
              </div>
            </div>
          
          </div>
        </div>  


        
        <div class="col-md-6">
            <canvas id="canvas_datos2" style="max-width: 100%;"></canvas>
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
                      <th width="20%" >Doctor</th>
                      <th width="12%" >Ordenes</th>
                      <th width="16%" >Valor ($)</th> 
                      <th width="20%" >HumanLabs ($)</th>  
                      <th width="16%" >Referido ($)</th>
                      <th width="16%" >Comision ($)</th>                                 
                    </tr>
                  </thead>
                  <tbody>
                    @php $xpart_cant=0; $xpart_val=0; $xpart_hlab=0; $xpart_ref=0; $xpart_com=0; @endphp
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
                          $xpart_cant+=$value->cantidad; $xpart_val+=$total; $xpart_hlab+=$val_hl; $xpart_ref+=($total - $val_hl); $xpart_com+=$com;
                        @endphp
                        <td>{{$value->apellido1}} {{substr($value->apellido2,0,1)}}. {{$value->nombre1}}</td>
                        <td style="text-align: right;">{{$value->cantidad}}</td>
                        <td style="text-align: right;"> {{number_format(round($total,2),2)}}</td>
                        <td style="text-align: right;"> {{number_format($val_hl,2)}}</td>
                        <td style="text-align: right;"> {{number_format(round($total - $val_hl,2),2)}}</td>
                        <td style="text-align: right;"><span id="pri{{$value->id_doctor_ieced}}"> {{number_format($com,2)}}</span></td>

                      </tr>
                    @endforeach

                    @foreach($or_aniomes_doctor_privado_not as $value)

                      <tr>
                        @php

                          $val_hl = 0; 
                          $total = $value->valor;
                        
                          $com_hl = $val_hl*$per_hl;
                          $com_il = ($total - $val_hl)*$per_il;
                          $com = round($com_hl + $com_il,2);
                          $xpart_cant+=$value->cantidad; $xpart_val+=$total; $xpart_hlab+=$val_hl; $xpart_ref+=($total - $val_hl); $xpart_com+=$com;
                        @endphp
                        <td>&nbsp; {{$value->apellido1}} {{substr($value->apellido2,0,1)}}. {{$value->nombre1}}</td>
                        <td style="text-align: right;">{{$value->cantidad}}</td>
                        <td style="text-align: right;"> {{round($total,2)}}</td>
                        
                        <td style="text-align: right;"> {{$val_hl}}</td>
                        <td style="text-align: right;"> {{round($total - $val_hl,2)}}</td>
                        <td style="text-align: right;"><span> {{$com}}</span></td>

                      </tr>
                    @endforeach
                  </tbody>
                </table>
                 <table id="example2x" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <tbody>
                      <tr>
                        <td class="total" width="20%">TOTAL</td>
                        <td class="total" width="12%" style="text-align: right;">{{$xpart_cant}}</td>
                        <td class="total" width="16%" style="text-align: right;">{{number_format($xpart_val ,2)}}</td>
                        <td class="total" width="20%" style="text-align: right;">{{number_format($xpart_hlab ,2)}}</td>
                        <td class="total" width="16%" style="text-align: right;">{{number_format($xpart_ref ,2)}}</td>
                        <td class="total" width="16%" style="text-align: right;">{{number_format($xpart_com ,2)}}</td>
                      </tr>
                  </tbody>
                </table>
              </div>
            </div>
          
          </div>
        </div>    


        
        <div class="col-md-6">
            <canvas id="canvas_datos3" style="max-width: 100%;"></canvas>
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
                        <th width="20%" >Doctor</th>
                        <th width="12%" >Ordenes</th>
                        <th width="16%" >Valor ($)</th> 
                        <th width="20%" >HumanLabs ($)</th>  
                        <th width="16%" >Referido ($)</th>
                        <th width="16%" >Comision ($)</th>                                 
                      </tr>
                    </thead>
                    <tbody>
                      @php  $xpart_can=0;$xpart_hl=0;$xpart_ref=0;$xpart_com=0;$xpart_tot=0 @endphp
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
                            $xpart_can+=$value->cantidad;$xpart_hl+=$val_hl;$xpart_ref+=($total - $val_hl);$xpart_com+=$com;$xpart_tot+=$total;
                          @endphp
                          <td>{{$value->apellido1}} {{substr($value->apellido2,0,1)}}. {{$value->nombre1}}</td>
                          <td style="text-align: right;">{{$value->cantidad}}</td>
                          <td style="text-align: right;"> {{number_format($total,2)}}</td>
                          <td style="text-align: right;"> {{number_format($val_hl,2)}}</td>
                          <td style="text-align: right;"> {{number_format($total - $val_hl,2)}}</td>
                          <td style="text-align: right;"><span id="part{{$value->id_doctor_ieced}}"> {{number_format($com,2)}}</span></td>
                        </tr>
                      @endforeach
                      
                      @foreach($or_aniomes_doctor_particular_not1 as $value)
                        <tr>
                          @php
                            $val_hl = 0; 
                            $valor_hl = $or_aniomes_doctor_particular_hl3->where('id_doctor_ieced',$value->id_doctor_ieced)->first();
                            if(!is_null($valor_hl)){
                              $val_hl = round($valor_hl->valor,2);
                            } 
                            $total = round($value->valor,2);
                            $com = round($total * $per_ex,2);
                            $xpart_can+=$value->cantidad;$xpart_hl+=$val_hl;$xpart_ref+=($total - $val_hl);$xpart_com+=$com;$xpart_tot+=$total;
                          @endphp
                          <td class="codigo">DOCTOR CODIGO</td>
                          <td class="codigo" style="text-align: right;">{{$value->cantidad}}</td>
                          <td class="codigo" style="text-align: right;"> {{ number_format($total,2)}}</td>
                          <td class="codigo" style="text-align: right;"> {{ number_format($val_hl,2)}}</td>
                          <td class="codigo" style="text-align: right;"> {{ number_format($total - $val_hl,2)}}</td>
                          <td class="codigo" style="text-align: right;">{{number_format($com,2)}}</td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <table id="example2x" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <tbody>
                      <tr>
                        <td class="total" width="20%">TOTAL</td>
                        <td class="total" width="12%" style="text-align: right;">{{$xpart_can}}</td>
                        <td class="total" width="16%" style="text-align: right;">{{number_format($xpart_tot ,2)}}</td>
                        <td class="total" width="20%" style="text-align: right;">{{number_format($xpart_hl ,2)}}</td>
                        <td class="total" width="16%" style="text-align: right;">{{number_format($xpart_ref ,2)}}</td>
                        <td class="total" width="16%" style="text-align: right;">{{number_format($xpart_com ,2)}}</td>
                      </tr>
                  </tbody>
                </table>
                </div>
              </div>
            
            </div>
          </div>  
          
          <div class="col-md-6">
            <canvas id="canvas_datos4" style="max-width: 100%;"></canvas>
        </div>
        </div>  
      </div> 
      <div class="col-md-12">
        <div class="col-md-12" style="z-index: 9999;">
          <center ><h3 class="box-title">Ordenes de Laboratorio Doctor Externo con código de {{$mes_txt[$mes-1]}}/{{$anio}}</h3></center>
        </div>  
        <div class="row">
          <div class="col-md-6">

            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
              <div class="row">
                <div class="table-responsive col-md-12">
                  
                  <table id="example2_ex" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
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
                       @php $ext_cantidad=0;$ext_total=0;$ext_hl=0;$ext_ref=0;$ext_com=0; @endphp 
                       @foreach($Labs_doc_externos as $doc)
                        @php $value = $or_aniomes_doctor_particular_not->where('codigo',$doc->id)->first(); @endphp
                        <tr>
                          @php
                            $val_hl = 0;$total=0;$com_ex=0; $cantidad=0;
                            if(!is_null($value)){
                              $total = round($value->valor,2);
                              $valor_hl = $or_aniomes_doctor_particular_hl2->where('codigo',$value->codigo)->first();
                              if(!is_null($valor_hl)){
                                $val_hl = round($valor_hl->valor,2);
                              } 
                              $com_ex = $total*$per_ex;
                              $cantidad = $value->cantidad;
                            }
                            $ext_cantidad+=$cantidad;$ext_total+=$total;$ext_hl+=$val_hl;$ext_ref+=($total - $val_hl);$ext_com+=$com_ex;
                          @endphp
                          <td>{{$doc->apellido1}} {{substr($doc->apellido2,0,1)}}. {{$doc->nombre1}}</td>
                          <td style="text-align: right;">{{$cantidad}}</td>
                          <td style="text-align: right;"> {{$total}}</td>
                          
                          <td style="text-align: right;"> {{$val_hl}}</td>
                          <td style="text-align: right;"> {{$total - $val_hl}}</td>
                          <td style="text-align: right;"><span> {{round($com_ex,2)}}</span></td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                  <table id="example2x" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                  <tbody>
                      <tr>
                        <td class="total" width="20%">TOTAL</td>
                        <td class="total" width="12%" style="text-align: right;">{{$ext_cantidad}}</td>
                        <td class="total" width="16%" style="text-align: right;">{{number_format($ext_total ,2)}}</td>
                        <td class="total" width="20%" style="text-align: right;">{{number_format($ext_hl ,2)}}</td>
                        <td class="total" width="16%" style="text-align: right;">{{number_format($ext_ref ,2)}}</td>
                        <td class="total" width="16%" style="text-align: right;">{{number_format($ext_com ,2)}}</td>
                      </tr>
                  </tbody>
                </table>
                </div>
              </div>
            
            </div>
          </div>  
          
          <div class="col-md-6">
            <canvas id="canvas_datos5" style="max-width: 100%;"></canvas>
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


  

  /*function goBack() {
    window.history.back();
  }*/

</script> 




<script type="text/javascript">
            //load the Google Visualization API and the chart
            //google.load('visualization', '1', {'packages': ['corechart']});
            //google.load('visualization', '1', {'packages': ['piechart']});
            
            
 
            //set callback
            //google.setOnLoadCallback (createChart);
            //google.setOnLoadCallback (createChart_pub);
            //google.setOnLoadCallback (createChart_pri);
            //google.setOnLoadCallback (createChart_part);
            
           

            /*function createChart() {
 
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
                var options = {width: 400, height: 500, is3D: false, colors: [@foreach($or_aniomes_doctor as $value)'{{$value->color}}',@endforeach], legend:{ textStyle: {fontSize: 10}} };
 
                //draw our chart
                chart.draw(dataTable, options);
 
            }

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
 
            }*/

           
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
        </script>

@endsection