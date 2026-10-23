@extends('archivo_plano.generar.base')
@section('action-content')
<style>
  td {
    font-size: 12px;
    
  }
  .table>tbody>tr>td{
    padding: 2px;
  }
  th{
    font-size: 12px;
  }

  .table
  {
    padding: 4px;
  }
  .autocomplete {
    z-index:999999 !important;
    z-index:999999999 !important;
    z-index:99999999999999 !important;
    position: absolute;
    top: 0px;
    left: 0px;
    float: left;
    display: block;
    min-width: 160px;   
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box; 
  }
  .ui-autocomplete {
    z-index: 5000;
  }
  .ui-autocomplete {
    z-index: 999999;
    list-style:none;
    background-color:#FFFFFF;
    width:300px;
    border:solid 1px #EEE;
    border-radius:5px;
    padding-left:10px;
    line-height:2em;
  }

</style>
<section class="content">
	<div class="box">
		<!--div class="box-header">
		    <div class="row">
		        <div class="col-sm-8">
		          <h3 class="box-title"> Generación Planillas </h3>
		        </div>
		    </div>
		</div-->
	  	<div class="box-body">
            <form method="POST" id="generar" action="{{route('planilla.genera_planillas')}}">
            {{ csrf_field() }}
	  		<div class="form-group col-md-6 ">
        <div class="row" >
	  			<div class="form-group col-md-10 ">
                        <label for="cedula" class="col-md-4 control-label">Cédula:</label>
                        <div class="col-md-7">
                            <input id="cedula" maxlength="13" type="text" class="form-control input-sm" name="cedula" value="{{$cedula}}">
                            <input type="hidden" name="nombres" id="nombre" value="{{$nombres}}">
                        </div>
                </div>
                <div class="form-group col-md-2 ">                     
                        <div class="col-md-7">
                            <button id="buscar" type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-search"></span></button>
                        </div>
                </div>
            </div>
            </div>
        	</form>
          <div class="form-group col-md-6">
          <div class="form-group col-md-10">         
              <label for="paciente" class="col-md-2 control-label">Paciente:</label>
              <div class="col-md-8">
                  <input id="paciente" maxlength="70" type="text" class="form-control input-sm" name="paciente" value="@if($paciente !=null){{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}@endif">
              </div>
          </div>
          <div class="form-group col-md-2 ">                     
              <div class="col-md-7">
                  <button id="buscar_pac" type="button" class="btn btn-primary" > <span class="glyphicon glyphicon-search"></span></button>
              </div>
          </div>
          </div>

        	<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
              <div class="table-responsive col-md-12">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                  <thead>
                    <tr >
                      <th style="text-align: center;" >FECHA INGRESO</th>
                      <th style="text-align: center;" >EMPRESA</th>
                      <th style="text-align: center;" >SEGURO</th>
                      <!--<th style="text-align: center;" >TIPO SEGURO</th>-->
                      <th style="text-align: center;">PROCEDIMIENTO</th>
                      <th style="text-align: center;">HC</th>
                      <th style="text-align: center;">CEDULA</th>
                      <th style="text-align: center;">NOMBRES</th>
                      <th style="text-align: center;">CONVENIO</th>
                      <!--<th style="text-align: center;">TOTAL</th>-->
                      <th style="text-align: center;">PLANILLAR</th>
                      <!--<th style="text-align: center;">ACCIÓN</th>-->
                    </tr>
                  </thead>
                  <tbody>                  
                      
                    @if($proc!=null) 
                      @foreach($proc as $value)
                        @if($value->id_seguro == '2' || $value->id_seguro == '5')
                          @php
                            $fecha=substr($value->fechaini,0,10);
                            $fecha_inv=date("d/m/Y",strtotime($fecha));
                            $seg_iees = 2;
                            $seg_msp = 5;
                            
                          @endphp
                          <tr>   
                            <td>{{$fecha_inv}}</td>
                            <td>{{$value->nombre_corto}}</td>
                            <td>{{$value->nombre_seguro}}</td>
                            <!--<td></td>-->
                            <td >{{$value->nombre}}</td>
                            <td>{{$value->hcid}}</td>
                            <td>{{$paciente->id}}</td>
                            <td>{{$paciente->nombre1}} {{$paciente->nombre2}} {{$paciente->apellido1}} {{$paciente->apellido2}}</td>
                            <td>{{$value->nombre_seguro}} / {{$value->nombre_corto}}</td>
                            <!--<td></td>-->
                            <td>
                              <a type="button" class="btn btn-success btn-xs" id="planillar" href="{{route('archivo_plano.planilla_hcid',['hcid' => $value->id_hc_proced, 'id_seguro' => $seg_iees])}}"><span style="font-size: 12px"> Iess</span>  </a>
                              <a id="planilla_msp" type="button" href="{{route('archivo_plano.planilla_msp',['hcid' => $value->id_hc_proced, 'id_seguro' => $seg_msp])}}" class="btn  btn-warning btn-xs"> <span style="font-size: 12px"> Msp</span>
                              </a>
                            </td>
                          </tr>
                        @endif
                      @endforeach
                    @else
                    <h4>Paciente No encontrado</h4>
                    @endif
                  </tbody>
                  <tfoot>
                  </tfoot>
                </table>
              </div>
            </div>
        </div>
    </div>
</div>
</section>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">
  $("#paciente").autocomplete({
    source: function( request, response ) {
      //alert("autocomplete");
      $.ajax({
          url:"{{route('planilla.paciente_nombre')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          data: {
              term: request.term
                },
          dataType: "json",
          type: 'post',
          success: function(data){
              response(data);
              console.log(data);

          }
      })
    },
    minLength: 2,
  } );

  $("#paciente").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('planilla.paciente_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#paciente"),
            success: function(data){
                console.log(data);
                //alert("hola");
                if(data!='0'){
                    $('#cedula').val(data.id);
                    $('#nombre').val(data.value);
                    //alert("hola");
                    $( "#generar" ).submit();
                }

            },
            error: function(data){

                }
        })
    });


    /*$("#cedula").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('planilla.paciente_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#cedula"),
            success: function(data){
                console.log(data);
                //alert("hola");
                if(data!='0'){
                    
                    $('#nombre').val(data.value);
                    //alert("hola");
                    $( "#generar" ).submit();
                }

            },
            error: function(data){

                }
        })
    });*/
    

  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  

</script>
@endsection