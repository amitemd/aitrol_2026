@extends('financiero.base')
@section('action-content')
<!-- Ventana modal editar -->
<style>

table tr {
  text-align: center;
}
table {
    border-spacing: 0px;
    border-collapse: separate;
}
td {
    padding: 5px;
}
table{
    margin: auto;
 }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
  <!-- Main content -->
  <section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">{{trans('etodos.Financiero')}}</a></li>
	  <li class="breadcrumb-item"><a href="#">{{trans('etodos.IndiceFinanciero')}}</a></li>
	  <li class="breadcrumb-item"><a href="#">{{trans('etodos.IndicadorFinanciero')}}</a></li>
    </ol>
  </nav>
  <div class="box">
  <div class="box-header header_new">
            <div class="col-md-9">
              <h3  style="color:#000000" class="box-title">{{trans('etodos.INDICADORESFINANCIEROS')}}</h3>
            </div>
        </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">{{trans('etodos.BUSCADOR')}}</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('financiero.indicefinanciero_index') }}" >
        {{ csrf_field() }}

          <div class="form-group col-md-6 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha" class="texto col-md-3 control-label">{{trans('etodos.Fechadesde')}}:</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_desde" id="fecha_desde" value="{{$fecha_desde}}" autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>

          <div class="form-group col-md-6 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="texto col-md-3 control-label">{{trans('etodos.Fechahasta')}}:</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="{{$fecha_hasta}}"  autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div>



          {{-- <div class="form-group col-md-6 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
              <label for="mostrar_detalles" class="texto col-md-5 control-label" >{{trans('etodos.Mostrarresumen')}}</label>
              <input type="checkbox" id="mostrar_detalles" class="flat-green" name="mostrar_detalles" value="1"  @if(@$mostrar_detalles=="1") checked @endif>
          </div> --}}

          <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">
            <button type="submit" class="btn btn-primary" id="boton_buscar">
                  <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('etodos.Buscar')}}
            </button>
            <button type="button" class="btn btn-primary" id="btn_imprimir" name="btn_imprimir">
                  <span class="glyphicon glyphicon-print" aria-hidden="true"></span> {{trans('etodos.Imprimir')}}
            </button>
            <button type="button" class="btn btn-primary" id="btn_exportar">
              <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span> {{trans('etodos.Exportar')}}
            </button>
          </div>

        </form>
      </div>
	   <!-- /.box-header -->
	   <div class="box-body">
                <div class="col-md-1">
                  <dl>
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa">
                    {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
                    <dd>&nbsp; {{$empresa->id}}</dd>  --}}
                  </dl>
                </div>
                <div class="col-md-3">
                  <dl>
                    <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                    <dd>&nbsp; {{$empresa->id}}</dd>
                  </dl>
                </div>
                <div class="col-md-4">
                  <h4 style="text-align: center;">{{trans('etodos.IndiceFinanciero')}}</h4>
                  <!--h5 style="text-align: center;">Del {{$periodo_desde}} <br> al {{$periodo_hasta}}</h5-->
                </div>
                <div class="col-md-4">
                  {{-- <dl>
                    <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
                    <dd style="text-align:right">Telf: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
                    <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>
                  </dl> --}}
                </div>
              </div>
              <!-- /.box-body -->


    <div class="box">
		<!--ECUACIONES-->
		@php
		 //liquidez

		 if($pasivo_corriente!= 0) {$liquidez_corriente= round($activo_corriente/$pasivo_corriente,2);} else {$liquidez_corriente = 0;}
		 //prueba acida
		 $activo_corriente_inventario=round($activo_corriente - $inventarios_total,2);
		 if($pasivo_corriente!= 0) {
			 $prueba_acida= round($activo_corriente_inventario/$pasivo_corriente,2);
		 } else {
			 $prueba_acida = 0;
		 }
		 //capital trabajo neto
		 $capital_trabajo_neto= round($activo_corriente-$pasivo_corriente,2);
		 //endeudamiento activo
		 if($activo_total!= 0) {$endeudamiento_activo = round($pasivo_total/$activo_total,2);} else {$endeudamiento_activo = 0;}
		 //Endeudamiento Patrimordial
		 if($patrimonio_neto!= 0) {$endeudamiento_patrimordial= round($pasivo_total/$patrimonio_neto,2);} else {$endeudamiento_patrimordial = 0;}
		 //endeudamiento activo_fijo
		 if($activo_ncorriente!= 0) {$endeudamiento_activo_fijo = round($patrimonio_neto/$activo_ncorriente,2);} else {$endeudamiento_activo_fijo = 0;}
		 // Apalancamiento
		 if($activo_ncorriente!= 0) {$apalancamiento= round($patrimonio_neto/$activo_total,2);} else {$apalancamiento = 0;}
		 //Apalancamiento Financiero
		 if($activo_total!= 0) {
		 	$uaii_activostotales= $uaii/$activo_total;
		 } else {
		 	$uaii_activostotales = 0;
		 }
		 if($patrimonio_neto!= 0) {
		 	$uai_patrimonio= $uai/$patrimonio_neto;
		 } else {
		 	$uai_patrimonio = 0;
		 }
		 if($uai_patrimonio!= 0) {
		 	$apalancamiento_financiero= round($uaii_activostotales/$uai_patrimonio,2);
		 } else {
		 	$apalancamiento_financiero = 0;
		 }
		 // Rotacion de Cartera
		 //dd($documentos_cobrar);
		 if($documentos_cobrar!= 0) {$rotacion_cartera= round($ventas/$documentos_cobrar,2);} else {$rotacion_cartera = 0;}
		 // Rotacion Activo
		 if($activo_ncorriente!= 0) {$rotacion_activo= round( $ventas/$activo_ncorriente,2);} else {$rotacion_activo = 0;}
		 // Rotacion Ventas
		 if($activo_total!= 0) {$rotacion_ventas= round($ventas/ $activo_total,2);} else {$rotacion_ventas = 0;}
		 //Periodo por cobranza
		 $documentos_cobrar365= $documentos_cobrar*360;
		 //dd($documentos_cobrar365);
		 if($ventas!= 0) {$periodo_de_cobranza= round(($documentos_cobrar365)/($ventas),2);} else {$periodo_de_cobranza = 0;}
		 // Periodo Medio Pago
		 $documentos_pagar365= $documentos_pagar*360;
		 //dd($documentos_cobrar);
		 if($compras!= 0) {$periodo_medio_pago= round(($documentos_pagar365)/($compras),2);} else {$periodo_medio_pago = 0;}
		 //dd($periodo_medio_pago.' -- '.$documentos_pagar.' -- '.$compras);
		 // Impacto Gasto Admministracion y Ventas
		 if($ventas!= 0) {$impacto_gastoadmin= round($gastos_administracion/$ventas,2);} else {$impacto_gastoadmin = 0;}
		 //Impacto de la carga financiera
		 if($ventas!= 0) {$impacto_carga_financiera= round($gastos_financieros/$ventas,2);} else {$impacto_carga_financiera = 0;}
		 //Rotacion inventario
		 if($inventarios_total!= 0) {$rotacion_inventario= round($costos_ventas/$inventarios_total,2);} else {$rotacion_inventario = 0;}
		 //Periodo Inventario
		 if($rotacion_inventario!= 0) {$periodo_inventario= round(360/$rotacion_inventario, 2);} else {$periodo_inventario = 0;}

		 //dd($periodo_inventario);
		 //Rentabilidad del Activo (Dupont)
		 if($ventas!=0 && $activo_total!=0){ $rentabilidad_neta= round((($utilidad_neta/$ventas) * ($ventas/$activo_total)*100),2);} else{ $rentabilidad_neta=0; }
		 //Margen Bruto
		 $costosmenosventa= round($ventas-$costos_ventas,2);
		 if($ventas!= 0) {$margen_bruto= round(($costosmenosventa/$ventas)*100,2);} else {$margen_bruto = 0;}
		 // Margen Operacional
		 $utilidad_bruta= round($ventas-$costos_ventas,2);

		 if($ventas!= 0) {$margen_operacional= round( ($utilidad_operacional/$ventas)*100,2);} else {$margen_operacional = 0;}
		 //Rentabilidad Neta de Ventas
		 if($ventas!= 0) {$rentabilidad_netav= round(($utilidad_neta/$ventas)*100,2);} else {$rentabilidad_netav = 0;}
		 //rentabilidad operacional del patrimonio
		 if($patrimonio_neto!= 0) {$rentabilidad_op= round(($utilidad_operacional/$patrimonio_neto)*100,2);} else {$rentabilidad_op = 0;}
		 //rentabilidad financiera
		 //$rentabilidad_fin_total= round((($ventas/$activo_total)* ($uaii/$ventas)* ($activo_total/$patrimonio_neto) * (($uai)/($uaii))*($utilidad_neta/$uai))*100,2);
		 if($activo_total!=0 && $ventas!=0  && $patrimonio_neto!=0  && $uaii!=0 && $uai!=0){ $rentabilidad_fin_total= round((($ventas/$activo_total)* ($uaii/$ventas)* ($activo_total/$patrimonio_neto) * (($uai)/($uaii))*($utilidad_neta/$uai))*100,2);} else{ $rentabilidad_fin_total=0; }
		@endphp
      <!-- /.box-header -->
      <div class="box-body dobra">
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto">{{trans('etodos.LIQUIDEZ')}}</label>
        </div>
      </div>
      <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
            <div id="resultados">
			</div>
            <div id="contenedor">
              <div  id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                  <div class="row">
                    <div class="table-responsive col-md-12">
                    <table  width="80%" border="2" cellspacing="0" cellpadding="0">
					<tr>
					<td rowspan="2"><b>{{trans('etodos.Liquidezcorriente')}}</b></td>
					<td><p>{{trans('etodos.Activocorriente')}} </p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($activo_corriente,2)}}</p></td>
					<td rowspan="2"><p> {{$liquidez_corriente}}</p></td>
					</tr>
					<tr>
					<td><p>{{trans('etodos.PasivoCorriente')}} </p></td>
					<td><p>$ {{number_format($pasivo_corriente,2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>Prueba Acida </b></td>
					<td><p>{{trans('etodos.Activocorriente')}}  - {{trans('etodos.Inventario')}}</p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($activo_corriente_inventario,2 )}}</p></td>
					<td rowspan="2"><p> {{$prueba_acida}}</p></td>
					</tr>
					<tr>
					<td><p>{{trans('etodos.PasivoCorriente')}}  </p></td>
					<td><p>$ {{number_format($pasivo_corriente,2)}}</p></td>
					</tr>
					<td rowspan="1"><b>Capital trabajo neto </b></td>
					<td><p>{{trans('etodos.Activocorriente')}}  - {{trans('etodos.PasivoCorriente')}}   </p></td>
					<td rowspan="2"><p>=</p></td>
					<td rowspan="2"><p> {{number_format($capital_trabajo_neto,2)}}</p></td>
					</tr>
					</table>
                    </div>
                  </div>
              </div>
            </div>
        </div>
	  </div>

      </div>
	  <!-- /.box-body -->
	  <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto">{{trans('etodos.SOLVENCIA')}}</label>
        </div>
      </div>
      <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
            <div id="resultados">
            </div>
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                  <div class="row">
                    <div class="table-responsive col-md-12">
					<table width="80%" border="2" cellspacing="0" cellpadding="0">
					<tr>
					<td rowspan="2"><b>{{trans('etodos.EndeudamientodelActivo')}}</b></td>
					<td><p>{{trans('etodos.PasivoTotal')}} </p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>${{number_format($pasivo_total,2)}}</p></td>
					<td rowspan="2"><p>{{$endeudamiento_activo}}</p></td>
					</tr>
					<tr>
					<td><p>{{trans('etodos.Activototal')}}</p></td>
					<td><p>$ {{number_format($activo_total, 2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>{{trans('etodos.EndeudamientoPatrimonial')}}</b></td>
					<td><p>{{trans('etodos.PasivoTotal')}} </p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($pasivo_total,2)}}</p></td>
					<td rowspan="2"><p> {{$endeudamiento_patrimordial}}</p></td>
					</tr>
					<tr>
					<td><p>{{trans('etodos.Patrimonio')}}</p></td>
					<td><p>$ {{number_format($patrimonio_neto,2)}}</p></td>
					</tr>
					<tr>
					<tr>
					<td rowspan="2"><b>{{trans('etodos.EndeudamientodelActivoFijo')}}</b></td>
					<td><p>{{trans('etodos.Patrimonio')}} </p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($patrimonio_neto,2)}}</p></td>
					<td rowspan="2"><p> {{$endeudamiento_activo_fijo}}</p></td>
					</tr>
					<tr>
					<td><p>{{trans('etodos.ActivoFijoNeto')}}</p></td>
					<td><p>$ {{number_format($activo_ncorriente,2)}}</p></td>
					</tr>
					<tr>

					<td rowspan="2"><b>{{trans('etodos.Apalancamiento')}}</b></td>
					<td><p>{{trans('etodos.Patrimonio')}}</p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($patrimonio_neto,2)}}</p></td>
					<td rowspan="2"><p> {{$apalancamiento}}</p></td>
					</tr>
					<tr>
					<td><p>{{trans('etodos.Activototal')}}</p></td>
					<td><p>$ {{number_format($activo_total, 2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>{{trans('etodos.Apalancamiento')}}  {{trans('etodos.Financiero')}}</b></td>
					<td><p>(UAII/{{trans('etodos.Activototal')}})</p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p> {{$uaii_activostotales}}</p></td>
					<td rowspan="2"><p> {{$apalancamiento_financiero}}</p></td>
					</tr>
					<tr>
					<td><p>(UAI/{{trans('etodos.Patrimonio')}})</p></td>
					<td><p>$ {{$uai_patrimonio}}</p></td>
					</tr>
					</table>
                    </div>
                  </div>
               </div>
            </div>
        </div>
	  </div>

	  </div>
	  <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto">{{trans('etodos.GESTIÓN')}}</label>
        </div>
      </div>
      <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
            <div id="resultados">
            </div>
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                  <div class="row">
                    <div class="table-responsive col-md-12">
					<table width="80%" border="2" cellspacing="0" cellpadding="0">
					<tr>
					<td rowspan="2"><b>{{trans('etodos.RotacióndeCartera')}}</b></td>
					<td><p>{{trans('etodos.Ventas')}}</p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($ventas,2 )}}</p></td>
					<td rowspan="2"><p> {{round($rotacion_cartera,2)}}</p></td>
					</tr>
					<tr>
					<td><p>{{trans('etodos.CuentasporCobrar')}}</p></td>
					<td><p>$ {{number_format($documentos_cobrar,2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>{{trans('etodos.RotacióndeActivoFijo')}}</b></td>
					<td><p>{{trans('etodos.Ventas')}}</p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($ventas,2 )}}</p></td>
					<td rowspan="2"><p>{{round($rotacion_activo,2)}}</p></td>
					</tr>
					<tr>
					<td><p>{{trans('etodos.ActivosFijoNeto')}} </p></td>
					<td><p>$ {{number_format($activo_ncorriente,2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>{{trans('etodos.RotacióndeVentas')}}</b></td>
					<td><p>{{trans('etodos.Ventas')}}</p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($ventas, 2)}}</p></td>
					<td rowspan="2"><p> {{$rotacion_ventas}}</p></td>
					</tr>
					<tr>
					<td><p>{{trans('etodos.ActivosTotal')}}</p></td>
					<td><p>$ {{number_format($activo_total, 2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>{{trans('etodos.Periodomediocobranza')}} </b></td>
					<td><p>{{trans('etodos.documentosporcobrar')}} * 360 {{trans('etodos.días')}}  </p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($documentos_cobrar365,2)}}</p></td>
					<td rowspan="2"><p> {{$periodo_de_cobranza}}</p></td>
					</tr>
					<tr>
					<td><p>Ventas</p></td>
					<td><p>$ {{number_format($ventas, 2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>Periodo medio pago</b></td>
					<td><p>Cuentas y Documentos por pagar  * 360 {{trans('etodos.días')}}  </p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($documentos_pagar365,2)}}</p></td>
					<td rowspan="2"><p> {{$periodo_medio_pago}}</p></td>
					</tr>
					<tr>
					<td><p>Compras </p></td>
					<td><p>${{number_format($compras,2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>Impacto gasto administracion y ventas </b></td>
					<td><p>Gastos administracion</p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($gastos_administracion,2 )}}</p></td>
					<td rowspan="2"><p>{{$impacto_gastoadmin}} %</p></td>
					</tr>
					<tr>
					<td><p>Ventas</p></td>
					<td><p>$ {{number_format($ventas,2 )}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>Impacto de la carga Financiera </b></td>
					<td><p>Gastos financieros</p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($gastos_financieros,2)}}</p></td>
					<td rowspan="2"><p>{{$impacto_carga_financiera}} %</p></td>
					</tr>
					<tr>
					<td><p>Ventas</p></td>
					<td><p>$ {{number_format($ventas,2 )}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>Rotación de inventario</b></td>
					<td><p>Costo de ventas o produccion</p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($costos_ventas,2)}}</p></td>
					<td rowspan="2"><p> {{round($rotacion_inventario,2)}}</p></td>
					</tr>
					<tr>
					<td><p>inventarios total promedio</p></td>
					<td><p>$ {{number_format($inventarios_total,2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>Periodo de inventario</b></td>
					<td><p>360</p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>360</p></td>
					<td rowspan="2"><p>{{$periodo_inventario}}</p></td>
					</tr>
					<tr>
					<td><p>Rotacion de inventarios</p></td>
					<td><p>{{$rotacion_inventario}}</p></td>
					</tr>
					</table>
                    </div>
                  </div>
              </div>
            </div>
        </div>
	  </div>
	  </div>
	  <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto">RENTABILIDAD</label>
        </div>
      </div>
      <div class="box-body dobra">
      <div class="form-group col-md-12">
        <div class="form-row">
            <div id="resultados">
            </div>
            <div id="contenedor">
              <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                  <div class="row">
                    <div class="table-responsive col-md-12">
					<table width="100%" border="2" cellspacing="0" cellpadding="0">
					<tr>
					<td rowspan="2"><b>Rentabilidad Neta de Activo (Dupont)</b></td>
					<td rowspan="2"><p>Utilidad Neta/Ventas  *  Ventas/Activo total</p></td>
					<td rowspan="2"><p> = </p></td>
					<td><p>$ {{number_format($utilidad_neta,2)}}</p></td>
					<td rowspan="2"><p>*</p></td>
					<td><p>$ {{number_format($ventas,2)}}</p></td>
					<td rowspan="2"><p> {{$rentabilidad_neta}} %</p></td>
					</tr>
					<tr>
					<td><p>$ {{number_format($ventas,2)}}</p></td>
					<td><p>$ {{number_format($activo_total, 2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>Margen Bruto</b></td>
					<td><p>Ventas- Costos de ventas    </p></td>
					<td rowspan="2"><p> = </p></td>
					<td><p>$ {{number_format($costosmenosventa,2)}}</p></td>
					<td rowspan="2"><p>{{$margen_bruto}} %</p></td>
					</tr>
					<tr>
					<td><p>Ventas</p></td>
					<td><p>$ {{number_format($ventas,2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>Margen operacional </b></td>
					<td><p>Utilidad  operacional </p></td>
					<td rowspan="2"><p> = </p></td>
					<td><p>$ {{number_format($utilidad_operacional,2)}}</p></td>
					<td rowspan="2"><p> {{$margen_operacional}} %</p></td>
					</tr>
					<tr>
					<td><p>Ventas</p></td>
					<td><p>$ {{number_format($ventas,2 )}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>Rentabilidad Neta de Ventas ( margen neto)</b></td>
					<td><p>Utilidad  Neta </p></td>
					<td rowspan="2"><p> = </p></td>
					<td><p>$ {{number_format($utilidad_neta,2)}}</p></td>
					<td rowspan="2"><p>{{$rentabilidad_netav}} %</p></td>
					</tr>
					<tr>
					<td><p>Ventas</p></td>
					<td><p>$ {{number_format($ventas,2 )}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>Rentabilidad Operacional del Patrimonio</b></td>
					<td><p>Utilidad  operacional</p></td>
					<td rowspan="2"><p>=</p></td>
					<td><p>$ {{number_format($utilidad_operacional,2)}}</p></td>
					<td rowspan="2"><p>{{$rentabilidad_op}} %</p></td>
					</tr>
					<tr>
					<td><p>Patrimonio</p></td>
					<td><p>$ {{number_format($patrimonio_neto,2)}}</p></td>
					</tr>
					<tr>
					<td rowspan="2"><b>Rentabilidad Financiera </b></td>
					<td rowspan="2"><p>Ventas/Activos * UAII/ Ventas  * Activo/Patrimonio * UAI/AUII  * Utilidad neta/UAI</p></td>
					<td rowspan="2"><p>=</p></td>
					<td rowspan="2"><p>{{$rentabilidad_fin_total}} %</p></td>
					</tr>
					</table>
                    </div>
                  </div>
              </div>
            </div>
        </div>
	  </div>

	  </div>
	  </div>
	</div>


  </section>
  <form method="POST" id="print_reporte_master" action="{{ route('indicefinanciero_index.excel') }}" target="_blank">
    {{ csrf_field() }}
    <input type="hidden" name="filfecha_desde" id="filfecha_desde" value="{{$fecha_desde}}">
    <input type="hidden" name="filfecha_hasta" id="filfecha_hasta" value="{{$fecha_hasta}}">
    <input type="hidden" name="filcuentas_detalle" id="filcuentas_detalle" value="{{@$cuentas_detalle}}">
    <input type="hidden" name="filmostrar_detalles" id="filmostrar_detalles" value="{{@$mostrar_detalles}}">
    <input type="hidden" name="exportar" id="exportar" value="0">
    <input type="hidden" name="imprimir" id="imprimir" value="">
  </form>
  <!-- /.content -->
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">

   $(document).ready(function(){

// $('#example2').DataTable({
//   'paging'      : false,
//   'lengthChange': false,
//   'searching'   : false,
//   'ordering'    : true,
//   'info'        : false,
//   'autoWidth'   : false
// });

tinymce.init({
  selector: '#hc'
});

$('input[type="checkbox"].flat-green').iCheck({
  checkboxClass: 'icheckbox_flat-green',
  radioClass   : 'iradio_flat-green'
});

$('input[type="checkbox"].flat-red').iCheck({
  checkboxClass: 'icheckbox_flat-red',
  radioClass   : 'iradio_flat-red'
});

});

$(function () {
  $('#fecha_desde').datetimepicker({
		format: 'YYYY',
            //defaultDate: '{{$fecha_desde}}',
        });
  $('#fecha_hasta').datetimepicker({
			format: 'YYYY',
            //defaultDate: '{{$fecha_hasta}}',

            });
  $("#fecha_desde").on("dp.change", function (e) {
	  //buscar();
  });

   $("#fecha_hasta").on("dp.change", function (e) {
	  //buscar();
  });
  $( "#btn_imprimir").click(function() {
	$("#filfecha_desde").val($("#fecha_desde").val());
	$("#filfecha_hasta").val($("#fecha_hasta").val());
	// $("#filcuentas_detalle").val($("#cuentas_detalle").val()); alert($("#cuentas_detalle").val());
	if($("#cuentas_detalle").prop("checked")){
	  $("#filcuentas_detalle").val(1);
	}else{
	  $("#filcuentas_detalle").val("");
	}
	// $("#filmostrar_detalles").val($("#mostrar_detalles").val());
	$("#exportar").val(0);
	$( "#print_reporte_master" ).submit();
  });
});
function buscar()
{
var obj = document.getElementById("boton_buscar");
obj.click();
}
function verifica_fechas(){
var fecha_desde = Date($("#fecha_desde").val() + '01-01');
var fecha_hasta = Date($("#fecha_hasta").val() + '-30');
if(Date.parse(fecha_desde) > Date.parse(fecha_hasta)){
Swal.fire({
	icon: 'error',
	title: 'Oops...',
	text: 'Verifique el rango de fechas y vuelva consultar'
});
}
}
$( "#btn_exportar").click(function() {
  $("#filfecha_desde").val($("#fecha_desde").val());
  $("#filfecha_hasta").val($("#fecha_hasta").val());
  if($("#cuentas_detalle").prop("checked")){
	$("#filcuentas_detalle").val(1);
  }else{
	$("#filcuentas_detalle").val("");
  }
   //alert($("#cuentas_detalle").prop("checked"));
  $("#filmostrar_detalles").val($("#mostrar_detalles").val());
  $("#exportar").val(1);
  $("#print_reporte_master" ).submit();
});

</script>
@endsection
