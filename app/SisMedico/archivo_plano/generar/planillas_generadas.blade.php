@extends('archivo_plano.generar.base_generar')
@section('action-content')
<style>
   td {
    font-size: 12px;
    padding: 0;
  }
  .table>tbody>tr>td{
    padding: 2px;
  }
  th{
    font-size: 12px;
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
		          <h3 class="box-title"> Planillas Generadas</h3>
		        </div>
		    </div>
		</div-->
	  	<div class="box-body">
            <form method="POST" id="generar" action="{{route('planilla.planillas_generadas')}}">
            {{ csrf_field() }}
	  		<div class="form-group col-md-6 ">
        <div class="row" >
	  			<div class="form-group col-md-10 ">
                        <label for="cedula" class="col-md-4 control-label">Cédula:</label>
                        <div class="col-md-7">
                            <input id="cedula" maxlength="20" type="text" class="form-control input-sm" name="cedula" value="@if($cedula !=''){{$cedula}}@endif">
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
                      <th style="text-align: center;">Fecha Ingreso</th>
                      <th style="text-align: center;">Seguro</th>
                      <th style="text-align: center;">Tipo Seguro</th>
                      <th style="text-align: center;">Procedimiento</th>
                      <th style="text-align: center;">Hc</th>
                      <th style="text-align: center;">Cedula</th>
                      <th style="text-align: center;">Nombres</th>
                      <!--<th style="text-align: center;">Total</th>-->
                      <th style="text-align: center;">Total</th>
                      <th style="text-align: center;">Acción</th>
                    </tr>
                  </thead>
                  <tbody> 
                  @if($archivo_plano != null)
                  @foreach($archivo_plano as $archivo)
                    @php
                      $cantidad_det=$archivo->detalles->count(); 
                      $suma_sub=$archivo->detalles->sum('subtotal');
                      $suma_tot=$archivo->detalles->sum('total');

                      $fecha=substr($archivo->fecha_ing,0,10);
                      $fecha_inv=date("d/m/Y",strtotime($fecha));
                    @endphp
                  <tr>                
                    <td>{{$fecha_inv}}</td>
                    <td>{{$archivo->seguro->nombre}}</td>
                    <td>@if($archivo->id_tipo_seguro !=null){{$archivo->tiposeguro->tipo}}@endif</td>
                    <td>{{$archivo->nom_procedimiento}}</td>
                    <td>{{$archivo->id_hc}}</td>
                    <td>{{$archivo->id_paciente}}</td>
                    <td>{{$archivo->paciente->apellido1}} {{$archivo->paciente->apellido2}} {{$archivo->paciente->nombre1}} {{$archivo->paciente->nombre2}}</td>
                    <!--<td>{{round($suma_sub,2)}}</td>-->
                    <td>{{round($suma_tot,2)}}</td>
                    <td>@if($archivo->id_seguro == '2')
                          <a type="button" class="btn btn-success btn-xs" id="planillar" href="{{route('archivo_plano.planilla_hcid',['hcid' => $archivo->id_hc_procedimimentos, 'id_seguro' => $archivo->id_seguro])}}"> <span  class="glyphicon glyphicon-edit"></span> </a>
                          <a onclick="elimina_cabecera('{{ $archivo->id }}')" class="btn btn-danger btn-xs" > <span  class="glyphicon glyphicon-trash"></span> </a>
                          <a class="btn btn-primary btn-xs" id="planilla_iess" href="{{route('archivo_plano.planilla_individual',['idcab' => $archivo->id])}}"> <span class="glyphicon glyphicon-download-alt"></span> </a>
                        @elseif($archivo->id_seguro == '5')
                          <a id="planilla_msp" type="button" href="{{route('archivo_plano.planilla_msp',['hcid' => $archivo->id_hc_procedimimentos, 'id_seguro' => $archivo->id_seguro])}}" class="btn btn-success btn-xs"> <span  class="glyphicon glyphicon-edit"></span></a>
                          <a onclick="elimina_cabecera('{{ $archivo->id }}')" class="btn btn-danger btn-xs" id="planillar" href="#"> <span  class="glyphicon glyphicon-trash"></span> </a>
                          <a class="btn btn-primary btn-xs" id="planilla_msp" href="{{route('archivo_plano_msp.planilla_cargo_individual',['idcab' => $archivo->id])}}"> <span class="glyphicon glyphicon-download-alt"></span> </a>
                          
                        @endif
                    </td>
                  </tr>
                  @endforeach
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

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

    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })

    function elimina_cabecera(i) {
      $.ajax({
          url:"{{ url('archivo_plano/planilla/cabecera_planilla/eliminar') }}/" + i,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          type: 'POST',
          data: { id: i },
          success: function(response)
          {
            console.log(response);
            swal({
                    title: "Planilla Eliminada",
                    icon: "success",
                    type: 'success',
                    buttons: true,
            })
            window.location.reload(true);
          }
      });
    }




</script>
@endsection