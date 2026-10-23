
@extends('hc_admision.historia.base2')

@section('action-content')

<!-- Ventana modal editar -->
<div class="modal fade fullscreen-modal" id="Procedimiento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<div class="modal fade" id="foto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 80%;">
      <div class="modal-content" >

      </div>
    </div>  
</div>

<link rel="stylesheet" href="{{asset('/css/bootstrap-datetimepicker.css')}}">
<script src="{{asset('/plugins/colorpicker/bootstrap-colorpicker.js')}}"></script>
<script src="{{asset('/js/bootstrap-datetimepicker.js')}}"></script>


<script type="text/javascript">  




    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha}}'
            });
        $("#fecha").on("dp.change", function (e) {
            fechacalendario();
        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_hasta}}'
            });
        $("#fecha_hasta").on("dp.change", function (e) {
            fechacalendario();
        });
        /*$(".clickable-row").click(function() {
            //window.location = $(this).data("href");
            document.getElementById('a_modal').href = $(this).data("href");
            $('#a_modal').click(); 
            alert("AQUI");
        });*/
    });    

    


    function fechacalendario() {
        var dato = document.getElementById('fecha').value;
        $('#enviar_fecha').click();
    }

     

    var vartiempo = setInterval(function(){ location.reload(); }, 300000);

    

  
</script>

<style>

  body {
    margin: 0;
    padding: 0;
    font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
    font-size: 14px;
  }

  #calendar {
    /*max-width: 900px;*/
    margin: 50px auto;
  }

  .table-hover>tbody>tr:hover{
          background-color: #ccffff !important;
        }

</style>

<section class="content" >
    
    <div class="box">
        <div class="box-header">
            <div class="form-group col-md-12" >
                <!--label class="col-md-1 control-label">Fecha</label-->
                <div class="col-md-12">
                    
                    <form method="POST" action="{{ route('masterhc.search') }}" > <!--PROTOCOLO CONTROLLER -->
                      {{ csrf_field() }}
                      
                        <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                            <label for="fecha" class="col-md-3 control-label" style="padding:0px;">Desde</label>
                            <div class="col-md-9">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                                    <div class="input-group-addon">
                                      <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                                    </div>   
                                </div>
                            </div>  
                        </div>

                        <div class="form-group col-md-3 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                            <label for="fecha_hasta" class="col-md-3 control-label" style="padding:0px;">Hasta</label>
                            <div class="col-md-9">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                                    <div class="input-group-addon">
                                      <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                                    </div>   
                                </div>
                            </div>  
                        </div>
                      

                      <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                        <label for="nombres" class="col-md-3 control-label">Paciente</label>
                        <div class="col-md-9">
                          <div class="input-group">
                            <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="APELLIDOS - NOMBRES" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                            <div class="input-group-addon">
                              <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                            </div>
                          </div>  
                        </div>
                      </div>
                   

                      <div class="form-group col-md-1 col-xs-2" >
                        <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                          <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
                      </div>

                        <div class="form-group col-md-3 col-xs-6">
                          <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('historia_clinica.reporte_hc')}}"><span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Historia Clinica</button>
                        </div>
                      
                        
                         
                    </form>
                </div>
            </div>
        </div>
        <div class="box-body">
          <h4>Procedimientos</h4>
          <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
            
            <div class="table-responsive col-md-12 col-xs-12">
              <!--a id="a_modal" style="display: none;" data-toggle="modal" data-target="#Procedimiento"></a-->
              <table id="example2" class="table table-striped table-hover" role="grid" aria-describedby="example2_info" style="font-size: 11px;">
                <thead>
                  <tr>
                    <th width="10%">Fecha</th>
                    <th width="5%">Hora</th>
                    <th width="5%">Cédula</th>
                    <th width="15%">Apellidos</th>
                    <th width="15%">Nombres</th>
                    <th width="20%">Procedimiento</th>
                    <th width="10%">Doctor Examinador</th>
                    <th width="5%" >Seguro</th>
                    <th width="5%">Estado</th>
                    <th width="10%" >Accion</th>
                  </tr>  
                </thead>
                <tbody>
                  @foreach($procedimientos as $procedimiento)
                    <tr >
                      <td>{{substr($procedimiento->fechaini,0,10)}}</td>
                      <td>{{substr($procedimiento->fechaini,10,10)}}</td>
                      <td>{{$procedimiento->id_paciente}}</td>
                      <td>{{$procedimiento->apellido1}}  {{$procedimiento->apellido2}}</td> 
                      <td>{{$procedimiento->nombre1}} {{$procedimiento->nombre2}}</td>
                      <td>@if($procedimiento->nombre_general==null) @php
                  $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $procedimiento->id_hc_procedimientos)->get();
                  $mas = true; 
                  $texto = "";

                  foreach($adicionales as $value2)
                  {
                    if($mas == true){
                     $texto = $texto.$value2->procedimiento->nombre  ;
                     $mas = false; 
                    }
                    else{
                      $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                    }                        
                  }
                @endphp @if(!is_null($texto)) {{$texto}} @else NO INGRESADO @endif @else{{$procedimiento->nombre_general}}@endif</td>
                      <td>@if($procedimiento->id_doctor_examinador!=null) {{$procedimiento->huapellido}} {{$procedimiento->hunombre}} @else {{$procedimiento->hdapellido}} {{$procedimiento->hdnombre}} @endif</td> 
                      <td>{{$procedimiento->hsnombre}}</td>
                      <td>
                        @if($procedimiento->pxestado=='0') EN ESPERA @endif 
                        @if($procedimiento->pxestado=='1') PREPARACIÓN @endif
                        @if($procedimiento->pxestado=='2') EN PROCEDIMIENTO @endif
                        @if($procedimiento->pxestado=='3') RECUPERACION @endif
                        @if($procedimiento->pxestado=='4') ALTA @endif
                        @if($procedimiento->pxestado=='5') SUSPENDER @endif</td>
                      <td>
                    
                        <button class="btn btn-success btn-xs" onclick="mostrar_detalle({{$procedimiento->id}});"><span id="b{{$procedimiento->id}}" class="glyphicon glyphicon-plus"></span>
                        </button>
                        <a class="btn btn-success btn-xs" href="{{ route("masterhc.detallehc", ['id' => $procedimiento->id])}}">Detalle</a>
                       
                        
                      </td>     
                    </tr>
                    <tr>
                      
                      <td style="display: none;">{{substr($procedimiento->fechaini,0,10)}}</td>
                      <td style="display: none;">{{substr($procedimiento->fechaini,10,10)}}</td>
                      <td style="display: none;">{{$procedimiento->id_paciente}}</td>
                      <td style="display: none;">{{$procedimiento->apellido1}}  {{$procedimiento->apellido2}}</td> 
                      <td style="display: none;">{{$procedimiento->nombre1}} {{$procedimiento->nombre2}}</td>
                      <td style="display: none;">@if($procedimiento->nombre_general==null) NO INGRESADO @else{{$procedimiento->nombre_general}}@endif</td>
                      <td style="display: none;">@if($procedimiento->id_doctor_examinador!=null) {{$procedimiento->huapellido}} {{$procedimiento->hunombre}} @else {{$procedimiento->hdapellido}} {{$procedimiento->hdnombre}} @endif</td> 
                      <td style="display: none;">{{$procedimiento->hsnombre}}</td>
                      <td style="display: none;">
                        @if($procedimiento->pxestado=='0') EN ESPERA @endif 
                        @if($procedimiento->pxestado=='1') PREPARACIÓN @endif
                        @if($procedimiento->pxestado=='2') EN PROCEDIMIENTO @endif
                        @if($procedimiento->pxestado=='3') RECUPERACION @endif
                        @if($procedimiento->pxestado=='4') ALTA @endif
                        @if($procedimiento->pxestado=='5') SUSPENDER @endif</td>
                      <td style="display: none;" id="{{$procedimiento->id}}" colspan="10">
                        <?php echo $procedimiento->hallazgos; ?>
                        <a href="{{route('hc_reporte.seleccion_descargar', ['id_protocolo' => $procedimiento->id])}}" data-toggle="modal" data-target="#foto">
                            <button id="nuevo_proc" type="button" class="btn btn-success btn-xs">
                                <span class="glyphicon glyphicon-file">Descargar Estudio</span>
                            </button>    
                        </a>
                      </td> 
                    </tr>  
                  @endforeach
                
                </tbody>
                
              </table>

              <div class="col-md-5 col-xs-12">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($procedimientos->currentPage()-1)*$procedimientos->perPage()}}  / @if(($procedimientos->currentPage()*$procedimientos->perPage())<$procedimientos->total()){{($procedimientos->currentPage()*$procedimientos->perPage())}} @else {{$procedimientos->total()}} @endif de {{$procedimientos->total()}} registros</div>
              </div>
              <div class="col-md-7 col-xs-12">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $procedimientos->appends(Request::only(['fecha','fecha_hasta', 'nombres']))->links() }}
                </div>
              </div>

            </div>
          </div>
          <h4>Consultas</h4>    
          <div id="example3_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
            
            <div class="table-responsive col-md-12 col-xs-12">  
              <table  id="example3" class="table table-striped table-hover" role="grid" aria-describedby="example3_info" style="font-size: 12px;">
                
                <thead>
                  <th >Fecha</th>
                  <th >Hora</th>
                  <th >Cédula</th>
                  <th >Apellidos</th>
                  <th >Nombres</th>
                  <th >Doctor Examinador</th>
                  <th >Seguro</th>
                  <th >Valor Copago</th>
                  <th >Valor Seguro</th>
                  <th >Tipo</th>
                  <th >Accion</th>
                </thead>
                <tbody>
                  @foreach($consultas as $consulta)
                    <tr>
                      <td>{{substr($consulta->fechaini,0,10)}}</td>
                      <td>{{substr($consulta->fechaini,10,10)}}</td>
                      <td>{{$consulta->id_paciente}}</td>
                      <td>{{$consulta->apellido1}}  {{$consulta->apellido2}}</td> 
                      <td>{{$consulta->nombre1}} {{$consulta->nombre2}}</td> 
                      <td>@if($consulta->id_doctor_examinador!=null) {{$consulta->huapellido}} {{$consulta->hunombre}} @else {{$consulta->hdapellido}} {{$consulta->hdnombre}} @endif</td> 
                      <td>{{$consulta->hsnombre}}</td>
                      @php
                           //if(!is_null($consulta)){
                            $valor = DB::table('ct_orden_venta as ov')->join('historiaclinica as h', 'h.id_agenda', 'ov.id_agenda')
                            ->where('hcid', $consulta->hcid)->first(); 
                            if(!is_null($valor)){
                              $val = $valor->total;
                              $oda = ($valor->valor_oda != null) ? $valor->valor_oda : 0; 
                            }
                            else{
                              $val = 0;
                              $oda = 0;
                            }
                        @endphp
                      <td>{{$val}}</td>
                      <td>{{$oda}}</td>
                      <td>@if($consulta->proc_consul=='4') VISITA @else CONSULTA @endif</td>  
                      <td>
                        <a class="btn btn-success btn-xs" href="{{ route("masterhc.detalle_consulta", ['id' => $consulta->id])}}">Detalle</a>
                        @php
                           if(!is_null($consulta)){
                              $procedimientos = Sis_medico\Ct_factura_procedimiento::where('id_hc_procedimientos', $consulta->hc_id_procedimiento)->count(); 
                              
                            }
                        @endphp
                        @if($procedimientos==0)
                          <a class="btn btn-success btn-xs" href="{{ route("ventas_crear", ['id' => $consulta->id])}}"><span id="b" class="glyphicon glyphicon-usd"></span></a>
                        @endif
                      </td>  
                    </tr>  
                  @endforeach
                
                </tbody>
                
              </table>
              <div class="col-md-5 col-xs-12">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($consultas->currentPage()-1)*$consultas->perPage()}}  / @if(($consultas->currentPage()*$consultas->perPage())<$consultas->total()){{($consultas->currentPage()*$consultas->perPage())}} @else {{$consultas->total()}} @endif de {{$consultas->total()}} registros</div>
              </div>
              <div class="col-md-7 col-xs-12">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                  {{ $consultas->appends(Request::only(['fecha','fecha_hasta', 'nombres']))->links() }}
                </div>
              </div>
            </div>  

          </div>  
        </div>
    </div>
</section>

<script type="text/javascript">  

  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      
    });

  $('#example3').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      
    });


  function mostrar_detalle(id){
    //alert(id);
    //alert($('#b'+id).attr("class"));
    var clase = $('#b'+id).attr("class");
    if(clase == 'glyphicon glyphicon-plus'){
      $('#'+id).show();
      $('#b'+id).removeClass("glyphicon-plus").addClass("glyphicon-minus");  
    }else{
      $('#'+id).hide();
      $('#b'+id).removeClass("glyphicon-minus").addClass("glyphicon-plus");  
    }
    

  }

</script> 



@endsection
