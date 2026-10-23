@extends('contable.facturacion.base')

@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<style type="text/css">
	.input-sm {
		padding: 2px;
	}
	

	.bootstrap-datetimepicker-widget {
		background-color: white ;
		color: #333 ;
	}
</style>

<section class="content" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                	<div class="col-md-10"><h3 class="box-title"><b>EMPRESA: {{$empresa->razonsocial}} - {{$empresa->id}}</b></h3></div>
                	<div class="col-md-2"><a href="{{route('empresa.index')}}" class="btn btn-success">Regresar</a></div>
                </div>
                <div class="box-body">
                	<div class="col-md-6" id="work">
                		<div class="box box-warning box-solid">
                			<div class="box-header with-border" >
						        <div><b style="font-size: 16px;">LISTADO DE FACTURAS</b></div>
						        <div>
						        	<form method="POST" id="form_buscador_factura">
							            {{ csrf_field() }}
                                        <input type="hidden" name="empresa" id="empresa" value="{{$empresa->id}}"> 
							            <!--Fecha Desde-->
							            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
							              <label for="desde" class="control-label">Desde</label>
							                <div class="input-group date" id="desde_g">
							                  <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
							                    <i class="fa fa-calendar"></i>
							                  </div>
							                  <input type="text" class="form-control input-sm" name="desde" id="desde" autocomplete="off">
							                  <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
							                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('desde').value = '';"></i>
							                  </div>   
							                </div>
							            </div>
							            <!--Fecha Hasta-->
							            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
							              <label for="hasta" class="control-label">Hasta</label>
							                <div class="input-group date" id="hasta_g">
							                  <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
							                    <i class="fa fa-calendar"></i>
							                  </div>
							                  <input type="text" class="form-control input-sm" name="hasta" id="hasta" autocomplete="off">
							                  <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
							                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('hasta').value = '';"></i>
							                  </div>   
							                </div>
							            </div>
							            
							            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
							              <label for="cedula" class="control-label">Cédula</label>
							             
							                <div class="input-group">
							                  <input value="@if($cedula!=''){{$cedula}}@endif" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Cédula" onchange="buscar_factura();">
							                	<div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
							                    	<i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('cedula').value = '';"></i>
							                  	</div>  
							                </div>
							              
							            </div>

							            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
							              <label for="nombres" class="control-label">Paciente</label>
							              
							                <div class="input-group">
							                  <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Apellidos y Nombres" onkeypress="enviar_enter(event);">
							                  <div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
							                    <i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('nombres').value = '';"></i>
							                  </div>
							                </div>  
							              
							            </div>
							            
										<div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
							              <label for="id_seguro" class="control-label">Seguro</label>
							              
							                <select class="form-control input-sm" name="id_seguro" id="id_seguro" onchange="buscar_factura();">
							                  <option value="">Seleccione ...</option>
							                @foreach($seguros as $seguro)
							                  <option @if($seguro->id==$id_seguro) selected @endif value="{{$seguro->id}}">{{$seguro->nombre}}</option>
							                @endforeach  
							                </select>
							              
							            </div>

							            <div class="form-group col-md-4 col-xs-6" style="padding-left: 2px;padding-right: 2px;" >
							              <label for="factura" class="col-md-12 col-xs-12 control-label">Factura</label>
							              <div class="col-md-3 col-xs-3" style="padding-left: 1px;padding-right: 1px;">
							              	<input class="form-control input-sm" type="text" name="suc" id="suc" value="{{$suc}}" placeholder="001" >
							              </div>
							              
							              <div class="col-md-3 col-xs-3" style="padding-left: 1px;padding-right: 1px;">
							              	<input class="form-control input-sm" type="text" name="caj" id="caj" value="{{$caj}}" placeholder="001">
							              </div>
							              
							              <div class="col-md-6 col-xs-6 input-group" style="padding-left: 1px;padding-right: 1px;">
							              	<input class="form-control input-sm" type="text" name="factura" id="factura" value="{{$factura}}" placeholder="17439" onchange="buscar_factura();">
							              	<div class="input-group-addon" style="padding-left: 2px;padding-right: 2px;">
						                    	<i class="glyphicon glyphicon-remove-circle" style="color: #800000;" onclick="document.getElementById('suc').value = '';document.getElementById('caj').value = '';document.getElementById('factura').value = '';"></i>
						                  	</div>
							              </div>  
							              
							            </div>
							            <div class="form-group col-md-1 col-xs-4" >
							              <button type="button" onclick="buscar_factura();" class="btn btn-primary btn-sm">
							                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
							            </div>
                                        <!--<div class="form-group col-md-1 col-xs-4" >
							              <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
							                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>  
							            </div>-->
							            <!--div class="form-group col-md-2 col-xs-6" >
							              <button type="submit" class="btn btn-primary btn-sm" formaction="{{ url('consultam/pastelpentax ') }}"><span class="glyphicon glyphicon-stats" aria-hidden="true"> Estadísticas</button>
							            </div-->
                                    </form>
						        </div>
						    </div>
						    <div class="box-body"  id="id_tab_fact">
		                		<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
						            <div class="table-responsive col-md-12 col-xs-12">
						              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
						                <thead>
						                  <tr>
						                    <th>Fecha</th>
						                    <th>Cedula</th>
						                    <th>Paciente</th>
						                    <th>Factura</th>
						                    <th>Total</th>
						                  </tr>
						                </thead>
						                <tbody>
						                @foreach ($facturas as $factura)
						                	<tr>
						                		<td>{{$factura->fecha_emision}}</td>
						                		<td>{{$factura->id_paciente}}</td>
						                		<td>{{$factura->paciente->apellido1}} {{$factura->paciente->nombre1}}</td>
						                		<td>{{$factura->sucursal}}-{{$factura->caja}}-{{$factura->numero}}</td>
						                		<td align="right">$ {{$factura->total}}</td>
						                	</tr>
						                @endforeach
						                </tbody>
						              </table>
						            </div>
						          </div>
						          
						            <div class="col-md-5 col-xs-12">
						              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1+($facturas->currentPage()-1)*$facturas->perPage()}}  / @if(($facturas->currentPage()*$facturas->perPage())<$facturas->total()){{($facturas->currentPage()*$facturas->perPage())}} @else {{$facturas->total()}} @endif de {{$facturas->total()}} registros</div>
						            </div>
						            <div class="col-md-7 col-xs-12">
						              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
						                {{ $facturas->appends(Request::only(['fecha','cedula', 'nombres', 'proc_consul', 'pentax', 'fecha_hasta', 'id_doctor1', 'id_seguro', 'id_procedimiento', 'espid']))->links() }}
						              </div>
						            </div>	
						    </div>    	
					    </div>    
                	</div>	
                	<div class="col-md-6" id="data">
                		@foreach($empresas as $value)
						<div class="form-group col-md-6">
							<a href="{{route('factura.index',['id' => $value->id])}}" class="btn btn-success col-md-12"><div class="col-md-12"> EMPRESA: </div><div class="col-md-12">{{$value->nombrecomercial}}</div></a>
						</div>	
						@endforeach
						<!--<div class="form-group col-md-6">
							<button class="btn btn-success col-md-12" onclick="crear_factura('{{$empresa->id}}')">
								Crear Factura
							</button>
						</div>-->
						<div class="form-group col-md-6">
							<button class="btn btn-success col-md-12" onclick="crear_factura('0','0','{{$empresa->id}}','0','0','0')">
								Crear Factura
							</button>
						</div>
						<div class="form-group col-md-6">
							<button class="btn btn-success col-md-12">
								Listado Facturas
							</button>
						</div>	
                	
                	</div>
                </div>
               
            
            </div>
        </div>
    </div>
    
</section>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
	
	$(function () {
        
		$('#desde_g').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$desde}}',
            
        });

		$('#hasta_g').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$hasta}}',
            
        });
        
        
        $("#desde_g").on("dp.change", function (e) {
            //buscar_factura();
        });

         $("#hasta_g").on("dp.change", function (e) {
            buscar_factura();
        });
  	});

  	$('#desde').on('click', function(){
	        $('#desde_g').datetimepicker('show');
	});

	$('#hasta').on('click', function(){
	        $('#hasta_g').datetimepicker('show');
	});

	$('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "asc" ]]
    });

    
    function buscar_factura(){

    	$.ajax({
          type: 'post',
          url:"{{route('factura.busqueda')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form_buscador_factura").serialize(),
          success: function(data){

            console.log(data);
            
            //$('#work').empty().html(data);
            //$('#work').removeClass( "col-md-12" );
	        //$('#work').addClass( "col-md-6" );
	        //$('#data').removeClass( "col-md-12" );
	        //$('#data').addClass( "col-md-6" );
	        //location.reload();
	        $("#id_tab_fact").html(data);
          
          },
          error:  function(){
            alert('error al cargar');
          }
        });

    }

    function crear_factura(id,id_cliente,id_empresa,id_suc,id_caj,id_factura){

    	//alert(id);

    	$.ajax({
        type: 'get',
        url: "{{url('contable/empresas/facturacion/crear')}}/"+id+'/'+id_cliente+'/'+id_empresa+'/'+id_suc+'/'+id_caj+'/'+id_factura,           
        success: function(data){

        	    //console.log(data);

	            $('#work').empty().html(data);
	            $('#work').removeClass( "col-md-12" );
	            $('#work').addClass( "col-md-6" );
	            $('#data').removeClass( "col-md-12" );
	            $('#data').addClass( "col-md-6" );


        	}
    	})  
    }


    function enviar_enter(e){
    
      tecla = (document.all) ? e.keyCode : e.which;
      if (tecla==13){
        buscar_factura();
      };
    
    }


</script>



@endsection