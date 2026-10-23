@extends('contable.rh_prestamos_visualizar.base2')
@section('action-content')
<section class="content">
	<nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active" aria-current="page">Prestamos a Empleado</li>
      </ol>
    </nav>
	<div class="box">

		<div class="box-header">
	        <div class="row">
	            <div class="col-sm-8">
	              <h4 class="box-title"> Prestamos</h4>
	            </div>
	        </div>
    	</div>
		<div class="box-body dobra">
			<div class="row">
	          	<div class="form-group col-md-6 ">
	              <div class="row" >
	                <div class="form-group col-md-10 ">
	                    <label for="usuario" class="col-md-4 control-label">Empleado:</label>
                      	<div class="col-md-7">
                          {{$usuario->apellido1}} {{$usuario->apellido2}} {{$usuario->nombre1}} {{$usuario->nombre2}}

                      	</div>
                      	
	                </div>
	              </div>
	            </div>
	            <div class="form-group col-md-6">
	            	<div class="row">
	            		<div class="form-group col-md-10">
	            			<label for="cedula" class="col-md-4 control-label"> Cédula:</label>
	            			<div class="col-md-7">
	            				{{$usuario->id}}
	            			</div>
	            		</div>
	            	</div>            	
	            </div>
	        </div>

	        <div class="row head-title">
	          <div class="col-md-12 cabecera">
	              <label class="color_texto" for="title">SALDOS</label>
	          </div>
	        </div>

	        <div class="row">
	            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
	              	<div class="table-responsive col-md-12">
		                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
		                  <thead>
		                    <tr class='well-dark'>
		                      <th style="text-align: center;">Fecha</th>
		                      <th style="text-align: center;">Descripción</th>
		                      <th style="text-align: center;">Abonos</th>
		                      <th style="text-align: center;">Saldos</th>
		                    </tr>
		                  </thead>
		                  	@php
		                  	$total = 0; @endphp
		                  	@if(count($saldos) >0)
		                  	<?php $total = $saldos[0]->saldo_inicial; //$total = $p->monto_prestamo; ?>
		              		@endif
		                  
		                  <tbody>
		                      @foreach($saldos as $s)

		                      <tr class="well">
		                      	<td>{{$s->fecha_creacion}}</td>
		                      	<td>{{$s->observacion}}</td>
		                      	<td></td>
		                      	<td>${{$s->saldo_inicial}}</td>
		                      </tr>
			                      @php
			                      $fecha_hoy = date('Y-m-d');
			                      	$rol = Sis_medico\Ct_Rol_Pagos::where('id_user',$s->id_empl)
			                      	->whereBetween('fecha_elaboracion', [$s->fecha_creacion, $fecha_hoy])
			                      	->join('ct_detalle_rol as detrol','detrol.id_rol','ct_rol_pagos.id')
			                      	->select('ct_rol_pagos.id as idrol','detrol.*')->get();

			                      	$pres_utili = Sis_medico\Ct_Prestamos_Utilidades::where('id_usuario', $s->id_empl)->where('pres_sal','2')->where('estado','1')->whereNotNull('id_asiento')->first();
			                      @endphp
			                      @foreach($rol as $r)
				                      <?php $total = $total - $r->saldo_inicial_prestamo; ?>
			                      <tr class="well">
			                      	<td>{{substr($r->created_at,0,10)}}</td>
			                      	<td>Desc. Rol de Pagos</td>
			                      	<td>-${{$r->saldo_inicial_prestamo}}</td>
			                      	<td>${{$total}}</td>
			                      </tr>
			                      @endforeach
			                      @if(!is_null($pres_utili))
			                      <tr>
			                      	<td>{{$pres_utili->fecha_creacion}}</td>
			                      	<td>Cruce Utilidades - Prestamos Año {{$pres_utili->anio}}</td>
			                      	<td>-${{$pres_utili->total}}</td>
			                      	<td>${{$pres_utili->valor_total}}</td>
			                      </tr>
			                      @endif
		                      @endforeach
		                  </tbody>
		                  <tfoot>
		                  </tfoot>
		                </table>
	              	</div>
          		</div>
        	</div>

            <div class="row head-title">
	          <div class="col-md-12 cabecera">
	              <label class="color_texto" for="title">PRESTAMOS</label>
	          </div>
	        </div>

		    <div class="row">
	            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
	              	<div class="table-responsive col-md-12">
		                <table id="example4" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
		                  <thead>
		                    <tr class='well-dark'>
		                      <th style="text-align: center;">Fecha</th>
		                      <th style="text-align: center;">Descripción</th>
		                      <th style="text-align: center;">Abonos</th>
		                      <th style="text-align: center;">Saldos</th>
		                    </tr>
		                  </thead>

		                  @php
		                  	$tot = 0; @endphp
		                  	@if(count($prestamos) >0)
		                  	<?php $tot = $prestamos[0]->monto_prestamo; //$tot = $p->monto_prestamo; ?>
		              		@endif

		                  <tbody>
		                      @foreach($prestamos as $p)

		                      <tr class="well">
		                      	<td>{{$p->fecha_creacion}}</td>
		                      	<td>{{$p->concepto}}</td>
		                      	<td></td>
		                      	<td>${{$p->monto_prestamo}}</td>
		                      </tr>
			                      @php
			                      $fecha_hoy = date('Y-m-d');
			                      	$rol = Sis_medico\Ct_Rol_Pagos::where('id_user',$p->id_empl)
			                      	->whereBetween('fecha_elaboracion', [$p->fecha_creacion, $fecha_hoy])
			                      	->join('ct_detalle_rol as detrol','detrol.id_rol','ct_rol_pagos.id')
			                      	->select('ct_rol_pagos.id as idrol','detrol.*')->get();

			                      	$pres_utili = Sis_medico\Ct_Prestamos_Utilidades::where('id_usuario', $p->id_empl)->where('pres_sal','1')->where('estado','1')->whereNotNull('id_asiento')->first();
			                      @endphp
			                      @foreach($rol as $r)
				                      <?php $tot = $tot - $r->prestamos_empleado; ?>
			                      <tr class="well">
			                      	<td>{{substr($r->created_at,0,10)}}</td>
			                      	<td>Desc. Rol de Pagos</td>
			                      	<td>-${{$r->prestamos_empleado}}</td>
			                      	<td>${{$tot}}</td>
			                      </tr>
			                      @endforeach

			                      @if(!is_null($pres_utili))
			                      <tr>
			                      	<td>{{$pres_utili->fecha_creacion}}</td>
			                      	<td>Cruce Utilidades - Prestamos Año {{$pres_utili->anio}}</td>
			                      	<td>-${{$pres_utili->total}}</td>
			                      	<td>${{$pres_utili->valor_total}}</td>
			                      </tr>
			                      @endif
		                      @endforeach
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

<script type="text/javascript">
	$('#example4').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "desc" ]]
    });
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "desc" ]]
    })
</script>
@endsection