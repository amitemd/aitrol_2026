@extends('contable.rh_saldos_iniciales.base')
@section('action-content')
<div class="modal fade" id="modal_ver_saldos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<section class="content">
	<nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item active" aria-current="page">Saldos Iniciales</li>
      </ol>
    </nav>
    <div class="box">
    	<div class="box-header">
            <div class="col-md-9">
              <h5><b>SALDOS INICIALES EMPLEADOS</b></h5>
            </div>
        </div>
        <div class="row head-title">
          <div class="col-md-12 cabecera">
              <label class="color_texto" for="title">BUSCADOR DE SALDOS INICIALES</label>
          </div>
        </div>

        <div class="box-body dobra">
          <form method="POST" id="buscar_saldos" action="{{route('prestamo_empleado.buscar_saldos')}}">
            {{ csrf_field() }}
                <div class="form-group col-md-2 col-xs-2">
                    <label class="texto" for="identificacion">Identificaci&oacute;n:</label>
                </div>
                <div class="form-group col-md-3 col-xs-6 container-3" style="padding-left: 15px;">
                      <input class="form-control" type="text" id="identificacion" name="identificacion"  placeholder="Ingrese Identificación..."  value="@if(isset($searchingVals)){{$searchingVals['id_empl']}}@endif" autocomplete="off" />
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="nombre">Nombres:</label>
                </div>
                <div class="form-group col-md-4 col-xs-8 container-4" style="padding-left: 15px;">
                      <input class="form-control" type="text" id="nombre" name="nombre"  placeholder="Ingrese nombres..."  value="@if(isset($searchingVals)){{$searchingVals['nombres']}}@endif" autocomplete="off" />
                </div>
                <div class="col-xs-2">
                  <button type="submit" id="buscarsaldo" class="btn btn-primary">
                      <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                  </button>
                </div>
          </form>
        </div>

        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" >LISTADO DE SALDOS INICIALES</label>
            </div>
        </div>

        <div class="box-body dobra">
        	<div class="form-group col-md-12">
            	<div class="form-row">
              		<div id="contenedor">
              			<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
              				<div class="row">
              					<div class="table-responsive col-md-12">
              						<table id="example4" class="table table-bordered table-hover">
              							<thead>
              								<tr class='well-dark'>
              									<th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >Cedula</th>
				                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empleado</th>
				                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo Inicial</th>
				                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha Creacion</th>
				                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo Rol</th>
				                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Numero Cuota</th>
				                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor Cuota</th>
				                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Mes/Año Inicio Cobro</th>           
				                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Mes/Año Fin Cobro</th>
				                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
				                                <th width="5%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
              								</tr>
              							</thead>
              							<tbody>
              								@foreach($saldos as $s)
              									<tr>
              										<td>{{$s->id_empl}}</td>
              										<td>{{$s->nombres}}</td>
              										<td>{{$s->saldo_inicial}}</td>
              										<td>{{$s->fecha_creacion}}</td>
              										<td>{{$s->tipo_rol}}</td>
              										<td>{{$s->num_cuotas}}</td>
              										<td>{{$s->valor_cuota}}</td>
              										<td>
              											@if($s->mes_inicio_cobro == '1') 
				                                         Enero 
				                                       	@elseif($s->mes_inicio_cobro == '2') 
				                                         Febrero
				                                       	@elseif($s->mes_inicio_cobro == '3')
				                                         Marzo
				                                       	@elseif($s->mes_inicio_cobro == '4')
				                                         Abril
				                                       	@elseif($s->mes_inicio_cobro == '5')
				                                         Mayo
				                                       	@elseif($s->mes_inicio_cobro == '6')
				                                         Junio
				                                       	@elseif($s->mes_inicio_cobro == '7')
				                                         Julio
				                                       	@elseif($s->mes_inicio_cobro == '8')
				                                         Agosto
				                                       	@elseif($s->mes_inicio_cobro == '9')
				                                         Septiembre
				                                       	@elseif($s->mes_inicio_cobro == '10')
				                                         Octubre 
				                                       	@elseif($s->mes_inicio_cobro == '11') 
				                                         Noviembre
				                                       	@elseif($s->mes_inicio_cobro == '12')
				                                         Diciembre
				                                       	@endif  

                                       					@if(!is_null($s->anio_inicio_cobro)){{$s->anio_inicio_cobro}}@endif
              										</td>
              										<td>
              											@if($s->mes_fin_cobro == '1') 
				                                         Enero 
				                                        @elseif($s->mes_fin_cobro == '2') 
				                                         Febrero
				                                        @elseif($s->mes_fin_cobro == '3')
				                                         Marzo
				                                        @elseif($s->mes_fin_cobro == '4')
				                                         Abril
				                                        @elseif($s->mes_fin_cobro == '5')
				                                         Mayo
				                                        @elseif($s->mes_fin_cobro == '6')
				                                         Junio
				                                        @elseif($s->mes_fin_cobro == '7')
				                                         Julio
				                                        @elseif($s->mes_fin_cobro == '8')
				                                         Agosto
				                                        @elseif($s->mes_fin_cobro == '9')
				                                         Septiembre
				                                        @elseif($s->mes_fin_cobro == '10')
				                                         Octubre 
				                                        @elseif($s->mes_fin_cobro == '11') 
				                                         Noviembre
				                                        @elseif($s->mes_fin_cobro == '12')
				                                         Diciembre
				                                        @endif 

				                                        @if(!is_null($s->anio_fin_cobro)){{$s->anio_fin_cobro}}@endif
              										</td>
              										<td>@if($s->estado == '1') Activo @elseif($s->estado =='0') Anulada @else Activo @endif</td>
              										<td>
              											<a href="{{route('prestamos_empleados.modal_saldos',['id_saldo' =>$s->id])}}" data-toggle="modal" data-target="#modal_ver_saldos" class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-eye-open"></span></a>
              											
              										</td>
              									</tr>
              								@endforeach
              							</tbody>
              						</table>
              					</div>
              				</div>
              			</div>
              		</div>
              	</div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
	$('#modal_ver_saldos').on('hidden.bs.modal', function() {
	    location.reload();
	    $(this).removeData('bs.modal');
  	});

	$(document).ready(function(){
 
      $('#example4').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : false
      });

  	});
</script>
@endsection