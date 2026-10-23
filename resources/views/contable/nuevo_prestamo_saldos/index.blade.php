@extends('contable.rh_prestamos_empleados.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active" aria-current="page">Prestamos - Saldos del Empleado</li>
      </ol>
    </nav>

    <div class="box">
    	<div class="box-header">
            <div class="col-md-9">
              <h5><b>PRESTAMOS - SALDOS DEL EMPLEADOS</b></h5>
            </div>
        </div>

        <div class="box-body">
        	<div class="row">
        		<div class="form-group col-md-1 col-xs-2">
	                <label class="texto" for="identificacion">Cédula</label>
	            </div>
	            <div class="form-group col-md-4 col-xs-10 container-4" style="padding-left: 15px;">
	                  <label class="texto">{{$user->id}}</label>
	            </div>
	            <div class="form-group col-md-1 col-xs-2">
	                <label class="texto" for="nombre">Nombres:</label>
	            </div>
	            <div class="form-group col-md-4 col-xs-10 container-4" style="padding-left: 15px;">
	                  <label class="texto">{{$user->apellido1}} {{$user->apellido2}} {{$user->nombre1}} {{$user->nombre2}}</label>
	            </div>

	            <div class="form-group col-md-1 col-xs-2">
	                <a type="button" class="btn btn-info" href="{{route('nuevo_rol.excel_prestamos_saldos',['id_user' => $user->id])}}" ><i class="fa fa-download"></i></a>
	            </div>
        	</div>

        	<div class="row head-title">
	            <div class="col-md-12 cabecera">
	                <label class="color_texto" >LISTADO DE PRESTAMOS</label>
	            </div>
	        </div>

	        <div class="row">
	        	<div class="col-md-12">
	        		<table id="example2" class="table-bordered table-hover dataTable table-striped">
	        			<thead>
	        				<tr class='well-dark'>
	        					<th width="5%">Id</th>
	        					<th width="10%">Fecha Creación</th>
	        					<th width="15%">Concepto</th>
	        					<th width="10%">Numero Cuota</th>
	        					<th width="10%">Valor Cuota</th>
	        					<th width="10%">Mes/Año Inicio Cobro</th>
	        					<th width="10%">Mes/Año Fin Cobro</th>
	        					<th width="10%">Monto Prestamo</th>
	        					<th width="10%">Valor a Pagar</th>
	        					<th width="10%">Estado</th>
	        					<th width="5%">Acción</th>
	        				</tr>
	        			</thead>
	        			<tbody>

	        				@php
	        					$meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

	        				@endphp
	        		
	        				@foreach($prestamos as $prestamo)
	        				@php
	        					$detalle_prestamo = Sis_medico\Ct_Rh_Prestamos_Detalle::where('id_ct_rh_prestamos',$prestamo->id)->get();
	        					
	        				@endphp
		        				<tr>
		        					<td>{{$prestamo->id}}</td>
		        					
		        					<td>{{$prestamo->fecha_creacion}}</td>
		        					<td>{{$prestamo->concepto}}</td>
		        					<td>{{$prestamo->num_cuotas}}</td>
		        					<td>${{$prestamo->valor_cuota}}</td>
		        					<td>@if($prestamo->mes_inicio_cobro == '1') Enero @elseif($prestamo->mes_inicio_cobro == '2') Febrero @elseif($prestamo->mes_inicio_cobro == '3') Marzo @elseif($prestamo->mes_inicio_cobro == '4') Abril @elseif($prestamo->mes_inicio_cobro == '5') Mayo @elseif($prestamo->mes_inicio_cobro == '6') Junio @elseif($prestamo->mes_inicio_cobro == '7') Julio @elseif($prestamo->mes_inicio_cobro == '8') Agosto @elseif($prestamo->mes_inicio_cobro == '9') Septiembre @elseif($prestamo->mes_inicio_cobro == '10') Octubre @elseif($prestamo->mes_inicio_cobro == '11') Noviembre @elseif($prestamo->mes_inicio_cobro == '12') Diciembre @endif  

                      @if(!is_null($prestamo->anio_inicio_cobro)){{$prestamo->anio_inicio_cobro}}@endif
                      </td>

                      <td >@if($prestamo->mes_fin_cobro == '1') Enero @elseif($prestamo->mes_fin_cobro == '2') Febrero @elseif($prestamo->mes_fin_cobro == '3') Marzo @elseif($prestamo->mes_fin_cobro == '4') Abril @elseif($prestamo->mes_fin_cobro == '5') Mayo @elseif($prestamo->mes_fin_cobro == '6') Junio @elseif($prestamo->mes_fin_cobro == '7') Julio @elseif($prestamo->mes_fin_cobro == '8') Agosto @elseif($prestamo->mes_fin_cobro == '9') Septiembre @elseif($prestamo->mes_fin_cobro == '10') Octubre @elseif($prestamo->mes_fin_cobro == '11') Noviembre @elseif($prestamo->mes_fin_cobro == '12') Diciembre @endif 

                      @if(!is_null($prestamo->anio_fin_cobro)){{$prestamo->anio_fin_cobro}}@endif
                      </td>
                      <td style="text-align: right;">$ @if($prestamo->estado == '1')<b>{{number_format($prestamo->monto_prestamo, 2, ',', ' ')}}</b>@else {{number_format($prestamo->monto_prestamo, 2, ',', ' ')}} @endif</td>
                      <td style="text-align: right;">$ @if($prestamo->estado == '1')<b>{{number_format($prestamo->saldo_total, 2, ',', ' ')}}</b>@else {{number_format($prestamo->monto_prestamo, 2, ',', ' ')}} @endif</td>

                      <td style="text-align: center;">@if($prestamo->estado == '1') @if($prestamo->prest_cobrad == '0')<span class="label pull bg-red">Activo</span> @else <span class="label pull bg-green">PAGADO</span> @endif @elseif($prestamo->estado =='0') Inactivo @else Activo @endif</td>
                      <td>
                      	@if($prestamo->estado == '1')
                      	<button class="btn btn-info btn-xs" onclick="mostrar_detalle({{$prestamo->id}});"><span id="b{{$prestamo->id}}" class="glyphicon glyphicon-plus"></span></button>
                      	@endif
                      </td>
                      
		        				</tr>

		        				<tr>
		        					<td class="celda{{$prestamo->id}}" style="display: none;"><b>Año</b></td>
		        					<td class="celda{{$prestamo->id}}" style="display: none;"><b>Mes</b></td>
		        					<td class="celda{{$prestamo->id}}" style="display: none;"><b>Cuota</b></td>
		        					<td class="celda{{$prestamo->id}}" style="display: none;"><b>Valor Cuota</b></td>
									<td class="celda{{$prestamo->id}}" style="display: none;"><b>Observaciones</b></td>
		        				</tr>
		        				@php
		        					$tot_det_prest = 0;
		        					@endphp
		        				@foreach($detalle_prestamo as $det_prest)
		        					@php
		        						$tot_det_prest += $det_prest->valor_cuota;
		        						 
		        						$ms = intval($det_prest->mes)-1;
		        					@endphp
			        				<tr>
			        					<td class="celda{{$prestamo->id}}" style="display: none;">{{$det_prest->anio}}</td>
			        					<td class="celda{{$prestamo->id}}" style="display: none;">{{$meses[$ms]}}</td>
			        					<td class="celda{{$prestamo->id}}" style="display: none;">{{$det_prest->cuota}}</td>
			        					<td class="celda{{$prestamo->id}}" style="display: none;">${{$det_prest->valor_cuota}}</td>
										<td class="celda{{$prestamo->id}}" style="display: none;">{{$det_prest->observacion}}</td>
			        				</tr>
		        				@endforeach
		        					<tr>
		        						<td class="celda{{$prestamo->id}}" style="display: none;"></td>
		        						<td class="celda{{$prestamo->id}}" style="display: none;"></td>
		        						<td class="celda{{$prestamo->id}}" style="display: none;"><b>Total</b></td>
		        						<td class="celda{{$prestamo->id}}" style="display: none;">${{$tot_det_prest}}</td>
		        					</tr>
		        				
		        				    				
		        				
	        				@endforeach
	        			</tbody>
	        			
	        		</table>
	        	</div>
	        </div>

	        <div class="row head-title">
	            <div class="col-md-12 cabecera">
	                <label class="color_texto" >LISTADO DE SALDOS INICIALES</label>
	            </div>
	        </div>

	        <div class="row">
	        	<div class="col-md-12">
	        		<table id="example2" class="table-bordered table-hover dataTable table-striped">
	        			<thead>
	        				<tr class='well-dark'>
	        					<th width="5%">Id</th>
	        					<th width="10%">Fecha Creación</th>
	        					<th width="15%">Concepto</th>
	        					<th width="10%">Numero Cuota</th>
	        					<th width="10%">Valor Cuota</th>
	        					<th width="10%">Mes/Año Inicio Cobro</th>
	        					<th width="10%">Mes/Año Fin Cobro</th>
	        					<th width="10%">Saldo Inicial</th>
	        					<th width="10%">Valor a Pagar</th>
	        					<th width="10%">Estado</th>
	        					<th width="5%">Acción</th>
	        				</tr>
	        			</thead>
	        			<tbody>
	        				
	        				@foreach($saldos as $saldo)
	        				@php
	        					$detalle_saldo = Sis_medico\Ct_Rh_Saldos_Iniciales_Detalle::where('id_ct_rh_saldos_iniciales',$saldo->id)->get();
	        				@endphp
		        				<tr>
		        					<td>{{$saldo->id}}</td>
		        					
		        					<td>{{$saldo->fecha_creacion}}</td>
		        					<td>{{$saldo->observacion}}</td>
		        					<td>{{$saldo->num_cuotas}}</td>
		        					<td>${{$saldo->valor_cuota}}</td>
		        					<td>@if($saldo->mes_inicio_cobro == '1') Enero @elseif($saldo->mes_inicio_cobro == '2') Febrero @elseif($saldo->mes_inicio_cobro == '3') Marzo @elseif($saldo->mes_inicio_cobro == '4') Abril @elseif($saldo->mes_inicio_cobro == '5') Mayo @elseif($saldo->mes_inicio_cobro == '6') Junio @elseif($saldo->mes_inicio_cobro == '7') Julio @elseif($saldo->mes_inicio_cobro == '8') Agosto @elseif($saldo->mes_inicio_cobro == '9') Septiembre @elseif($saldo->mes_inicio_cobro == '10') Octubre  @elseif($saldo->mes_inicio_cobro == '11')  Noviembre @elseif($saldo->mes_inicio_cobro == '12') Diciembre @endif  

                      @if(!is_null($saldo->anio_inicio_cobro)){{$saldo->anio_inicio_cobro}}@endif
                      </td>

                      <td >@if($saldo->mes_fin_cobro == '1') Enero @elseif($saldo->mes_fin_cobro == '2') Febrero @elseif($saldo->mes_fin_cobro == '3') Marzo @elseif($saldo->mes_fin_cobro == '4') Abril @elseif($saldo->mes_fin_cobro == '5') Mayo @elseif($saldo->mes_fin_cobro == '6') Junio @elseif($saldo->mes_fin_cobro == '7') Julio @elseif($saldo->mes_fin_cobro == '8') Agosto @elseif($saldo->mes_fin_cobro == '9') Septiembre @elseif($saldo->mes_fin_cobro == '10') Octubre @elseif($saldo->mes_fin_cobro == '11')  Noviembre @elseif($saldo->mes_fin_cobro == '12') Diciembre @endif 

                      @if(!is_null($saldo->anio_fin_cobro)){{$saldo->anio_fin_cobro}}@endif
                      </td>
                      <td style="text-align: right;">$ @if($saldo->estado == '1')<b>{{number_format($saldo->saldo_inicial, 2, ',', ' ')}}</b>@else {{number_format($saldo->saldo_inicial, 2, ',', ' ')}} @endif</td>

                      <td style="text-align: right;">$ @if($saldo->estado == '1')<b>{{number_format($saldo->saldo_res, 2, ',', ' ')}}</b>@else {{number_format($saldo->saldo_inicial, 2, ',', ' ')}} @endif</td>
                      <td style="text-align: center;">@if($saldo->estado == '1') @if($saldo->saldo_cobrad == '0')<span class="label pull bg-red">Activo</span> @else <span class="label pull bg-green">PAGADO</span> @endif @elseif($saldo->estado =='0') Inactivo @else Activo @endif</td>
                      <td>
                      	@if($saldo->estado == '1')
												<button class="btn btn-info btn-xs" onclick="mostrar_detalle({{$saldo->id}});"><span id="b{{$saldo->id}}" class="glyphicon glyphicon-plus"></span></button>
												@endif
                      </td>
		        				</tr>
		        				<tr>
		        					<td class="celda{{$saldo->id}}" style="display: none;"><b>Año</b></td>
		        					<td class="celda{{$saldo->id}}" style="display: none;"><b>Mes</b></td>
		        					<td class="celda{{$saldo->id}}" style="display: none;"><b>Cuota</b></td>
		        					<td class="celda{{$saldo->id}}" style="display: none;"><b>Valor Cuota</b></td>
									<td class="celda{{$saldo->id}}" style="display: none;"><b>Observaciones</b></td>
		        				</tr>
		        				@php
			        				$tot_det_saldo = 0;
			        			@endphp
		        				@foreach($detalle_saldo as $det_sal)
		        					@php
		        						$tot_det_saldo += $det_sal->valor_cuota;
		        						 
		        						$m = intval($det_sal->mes)-1;
		        					@endphp
			        				<tr>
			        					<td class="celda{{$saldo->id}}" style="display: none;">{{$det_sal->anio}}</td>
			        					<td class="celda{{$saldo->id}}" style="display: none;">{{$meses[$m]}}</td>
			        					<td class="celda{{$saldo->id}}" style="display: none;">{{$det_sal->cuota}}</td>
			        					<td class="celda{{$saldo->id}}" style="display: none;">${{$det_sal->valor_cuota}}</td>
										<td class="celda{{$saldo->id}}" style="display: none;">{{$det_sal->observacion}}</td>
			        				</tr>
		        				@endforeach
		        					<tr>
		        						<td class="celda{{$saldo->id}}" style="display: none;"></td>
		        						<td class="celda{{$saldo->id}}" style="display: none;"></td>
		        						<td class="celda{{$saldo->id}}" style="display: none;"><b>Total</b></td>
		        						<td class="celda{{$saldo->id}}" style="display: none;">{{$tot_det_saldo}}</td>
		        					</tr>

		        				
	        				@endforeach
	        			</tbody>
	        			
	        		</table>
	        		
	        	</div>
	        </div>
        	
        </div>
    	
    </div>

</section>

<script type="text/javascript">
	function mostrar_detalle(id){
    
    var clase = $('#b'+id).attr("class");
    if(clase == 'glyphicon glyphicon-plus'){
      $('.celda'+id).show();
      $('#b'+id).removeClass("glyphicon-plus").addClass("glyphicon-minus");  
    }else{
      $('.celda'+id).hide();
      $('#b'+id).removeClass("glyphicon-minus").addClass("glyphicon-plus");  
    }
    

  }
</script>
@endsection