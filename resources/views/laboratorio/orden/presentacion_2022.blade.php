@extends('laboratorio.orden.pres')

@section('action-content')


<style type="text/css">
	.e1 {
		background-color: #46d267 !important;
	}

	.e2 {
		background-color: #28a745 !important;
	}

	.e3 {
		background-color: #1e7b34 !important;
	}

	.cf1 {
		background-color: #33cee6 !important;
	}

	.cf2 {
		background-color: #17a2b8 !important;
	}

	.cf3 {
		background-color: #148c9f !important;
	}

	.c1 {
		background-color: #6acdfb !important;
	}

	.c2 {
		background-color: #06acf9 !important;
	}

	.c3 {
		background-color: #057CB4 !important;
	}

	.bg-info {
		background-color: #17a2b8 !important;
	}

	.bg-danger {
		background-color: #057CB4 !important;
	}


	.c4 {
		background-color: #FFB445;
	}

	.c5 {
		background-color: #FC8A28;
	}
</style>
<section class="content">
	<div class="box">
		<div class="container-fluid">
			<div class="row">

				<h3 style="font-weight: bold;">Estadisticos</h3>
				<div class="col-lg-3 col-6">

					<div class="small-box e1">
						<div class="inner">
							<h3><sup style="font-size: 20px">Estadisticos de <br>Laboratorio</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="ion ion-stats-bars"></i>
						</div>
						<a href="{{route('orden.estad_mes')}}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-6">

					<div class="small-box e2">
						<div class="inner">
							<h3><sup style="font-size: 20px">Procedimientos <br>Públicos</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="ion ion-stats-bars"></i>
						</div>
						<a href="{{ route('estaditicos_plano.index') }}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-6">

					<div class="small-box e3">
						<div class="inner">
							<h3><sup style="font-size: 20px">Consultas y <br>Procedimientos</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="ion ion-stats-bars"></i>
						</div>
						<a href="{{ route('estaditicos_plano.orden') }}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
			</div>
			<div class="row">
				<h3 style="font-weight: bold;">Contable / Financiero</h3>
				<div class="col-lg-3 col-6">

					<div class="small-box cf1">
						<div class="inner">
							<h3><sup style="font-size: 20px">Costos Vs<br> Ventas</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-dollar"></i>
						</div>
						<a href="{{ url('ap/imprimir_plantillas/pdf/contab/vs/110055/110058') }}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-6">

					<div class="small-box cf2">
						<div class="inner">
							<h3><sup style="font-size: 20px">Carga de <br>Insumos</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-barcode"></i>
						</div>
						<a href="{{ url('enfermeria/selec_prod/116626') }}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-6">

					<div class="small-box cf3">
						<div class="inner">
							<h3><sup style="font-size: 20px">Indices <br>Financieros</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-dollar"></i>
						</div>
						<a href="{{ route('financiero.indicefinanciero_index') }}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
			</div>
			<div class="row">
				<h3 style="font-weight: bold;">Area de Cirugías</h3>
				<div class="col-lg-3 col-6">

					<div class="small-box c1">
						<div class="inner">
							<h3><sup style="font-size: 20px">Sala de <br>Procedimientos</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-hospital-o"></i>
						</div>
						<a href="{{ url('pentax') }}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-6">

					<div class="small-box c2">
						<div class="inner">
							<h3><sup style="font-size: 20px">Riesgo de <br>Caida</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-bed"></i>
						</div>
						<a href="{{ route('camilla.index') }}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-6">

					<div class="small-box c3">
						<div class="inner">
							<h3><sup style="font-size: 20px">Record <br>Anestésico</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-file-text"></i>
						</div>
						<a href="{{ url('historiaclinica/anestesiologia/93979') }}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>


			</div>
			<div class="row">
				<h3 style="font-weight: bold;">Servicios Generales</h3>
				<div class="col-lg-3 col-6">

					<div class="small-box c1">
						<div class="inner">
							<h3><sup style="font-size: 20px">Limpieza de <br>Áreas</h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-check-square-o" aria-hidden="true"></i>
						</div>
						<a href="{{ url('limpieza/eleccion2/tipo2') }}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-6">

					<div class="small-box c2">
						<div class="inner">
							<h3><sup style="font-size: 20px">Limpieza de <br>Baños</h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-list-alt" aria-hidden="true"></i>
						</div>
						<a href="{{ url('limpieza_banos/buscar_fecha') }}?desde=2022-09-22&hasta=2022-09-24" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-6">

					<div class="small-box c3">
						<div class="inner">
							<h3><sup style="font-size: 20px">Limpieza <br>Pentax</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-hospital-o" aria-hidden="true"></i>
						</div>
						<a href="{{ url('limpieza/pentax/index') }}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
			</div>
			<div class="row">
				<h3 style="font-weight: bold;">Reportes</h3>
				<div class="col-lg-3 col-6">

					<div class="small-box c4">
						<div class="inner">
							<h3><sup style="font-size: 20px">Reportes<br> Tiempos</h3>
							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-file-excel-o" aria-hidden="true"></i>
						</div>
						<form method="POST" id="form_tiempo">
							{{ csrf_field() }}
							<div style="display: none;">
								<input type="date" class="form-control input-sm" name="fecha" id="fecha" value="2022-09-23">
								<input type="date" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" value="2022-09-24">
							</div>

							<!-- <button type="submit" class="btn btn-primary btn-sm" formaction="{{ route('consultam.reportetiempo') }}">{{trans('econsultam.Tiempos')}}</button> -->

							<button style="border: 1px solid transparent;width: 100%;background: rgba(0,0,0,0.1);color: white;" type="submit" formaction="{{ route('consultam.reportetiempo') }}" class="small-box-footer" target="_blank">
								Ver <i class="fa fa-arrow-circle-right"></i>
							</button>
						</form>
					</div>
				</div>

				<div class="col-lg-3 col-6">
					<div class="small-box c5">
						<div class="inner">
							<h3><sup style="font-size: 20px">Estadisticos<br>Sucursales</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-pie-chart" aria-hidden="true"></i>
						</div>
						<a href="http://bd.aitrol.com/" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
				<div class="col-lg-3 col-6">
					<div class="small-box c5">
						<div class="inner">
							<h3><sup style="font-size: 20px">Productos <br>Caducados</sup></h3>

							<p></p>
						</div>
						<div class="icon">
							<i class="fa fa-pie-chart" aria-hidden="true"></i>
						</div>
						<a href="{{ url('reporte/insumos/caducados/index') }}" class="small-box-footer" target="_blank">
							Ver <i class="fa fa-arrow-circle-right"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

@endsection