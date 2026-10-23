@extends('laboratorio.encuesta_laboratorio.pregunta_labs.base')
@section('action-content')
<section class="content">
	<div class="box">
		<div class="box-header">
			<form class="form-vertical" method="POST" action="{{route('laboratorio.estadisticalabs')}}">
				{{ csrf_field() }}
				<div class="form-group">
					<div class="col-md-3">
						<label>Año</label>
						<!--input type="text" name="anio" value="{{$anio}}" class="form-control"-->
						<select class="form-control" name="anio" value="{{$anio}}">
							@php $x=2018; $anio_actual=date('Y'); @endphp
							@for($x=2018;$x<=$anio_actual;$x++) <option @if($x==$anio) selected @endif>{{$x}}</option>
								@endfor
						</select>
					</div>
					<div class="col-md-3">
						<label>Mes</label>
						<select name="mes" class="form-control">
							<option value="1" @if($mes==1) selected @endif>Enero</option>
							<option value="2" @if($mes==2) selected @endif>Febrero</option>
							<option value="3" @if($mes==3) selected @endif>Marzo</option>
							<option value="4" @if($mes==4) selected @endif>Abril</option>
							<option value="5" @if($mes==5) selected @endif>Mayo</option>
							<option value="6" @if($mes==6) selected @endif>Junio</option>
							<option value="7" @if($mes==7) selected @endif>Julio</option>
							<option value="8" @if($mes==8) selected @endif>Agosto</option>
							<option value="9" @if($mes==9) selected @endif>Septiembre</option>
							<option value="10" @if($mes==10) selected @endif>Octubre</option>
							<option value="11" @if($mes==11) selected @endif>Noviembre</option>
							<option value="12" @if($mes==12) selected @endif>Diciembre</option>

						</select>
					</div>
					<div class="col-md-3">
						<div>&nbsp;</div>
						<button type="submit" class="btn btn-primary">Buscar</button>
					</div>
					<div class="col-md-3">
					 <button type="submit" class="btn btn-primary btn-sm" formaction="{{route('laboratorio.detalle_mes_labs')}}"></span>Descargar Detalle</button>
					</div>
				</div>
			</form>

			<div class="row">
				<div class="col-sm-8">
					@php
					$txt_mes=['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
					@endphp
					<br>
					<h3 class="box-title"> <b>Estadisticas del mes {{$txt_mes[$mes-1]}} / Año {{$anio}}</b></h3>
				</div>
			</div>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">

					<br>
					<h4><b>Encuestas Labs</b></h4>

					<table border="2">
						<thead>
							<tr role="row">
								<th width="20%" style=" font-size: 16px; vertical-align: middle;border-color:black; text-align: center;">CRITERIOS DE EVALUACIÓN</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">MUY BUENO</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">BUENO</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">NI BUENO NI MALO </th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">MALO</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">MUY MALO</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">TOTAL DE RESPUESTAS RECIBIDA POR PREGUNTAS</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">INDICADOR SIN PONDERAR POR PREGUNTA</th>
							</tr>
						</thead>
						<tbody>
							@php
							// el total de encuestas
							$total_encuestas= count($encuestas); 
							//dd($total_encuestas);
							$sin_ponderar=0;
							$acumulador=0;
							$contador=0;
							$promedio=0;
							//armamos un array
							$estadistico=array();
							@endphp
							@foreach($preguntas as $value)
							@php
							$excelentes=0;
							$muy_buenas=0;
							$buenas=0;
							$regular=0;
							$mala=0;
							$muy_mala=0;
							// detalle para sumar
							foreach($encuestas as $x){




							$muy_buenas = $muy_buenas+$x->complementos->where('id_pregunta_labs', $value->id)->where('valor','5')->count();


							$buenas=$buenas+$x->complementos->where('id_pregunta_labs', $value->id)->where('valor','4')->count();

							$regular=$regular+$x->complementos->where('id_pregunta_labs',$value->id)->where('valor','3')->count();

							$mala=$mala+$x->complementos->where('id_pregunta_labs',$value->id)->where('valor','2')->count();

							$muy_mala=$muy_mala+$x->complementos->where('id_pregunta_labs',$value->id)->where('valor','1')->count();
							}
							//dd($buenas);

							if($total_encuestas<>0){
								$sin_ponderar=round((($muy_buenas*10)+($buenas*8)+($regular*6)+($mala*4)+($muy_mala*2))*10/$total_encuestas,2);
								}
								$armar['pregunta']=$value->nombre; 
								$armar['valor']= $sin_ponderar;
								array_push($estadistico,$armar); 
								$acumulador=$acumulador+$sin_ponderar; 
								$contador++;

								$promedio=round(($acumulador/$contador),2); 
								@endphp
								<tr role="row">
									<td>{{$value->nombre}}</td>
									<td align="center">{{$muy_buenas}}</td>
									<td align="center">{{$buenas}}</td>
									<td align="center">{{$regular}}</td>
									<td align="center">{{$mala}}</td>
									<td align="center">{{$muy_mala}}</td>
									<td align="center">{{$total_encuestas}}</td>
									<td align="center">{{$sin_ponderar}}</td>
								</tr>
								@endforeach



								<tr role="row">
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td align="center"><b>INDICADOR MENSUAL SIN PONDERACIÓN</b></td>
									<td align="center">{{$promedio}}</td>


								</tr>
						</tbody>
					</table>

					<br>
					<div class="col-md-12">
						<center>
							<canvas id="canvas2" style="max-width: 70%;"></canvas>
						</center>
					</div>


				</div>
			</div>
		</div>
		<div class="box-body">
			<div class="row">
				<div class="col-md-12">

					<br>
					<h4><b>Calificación Labs</b></h4>

					<table border="2">
						<thead>
							<tr role="row">
								<th width="20%" style=" font-size: 16px; vertical-align: middle;border-color:black; text-align: center;">CRITERIOS DE EVALUACIÓN</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">MUY BUENO</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">BUENO</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">NI BUENO NI MALO </th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">MALO</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">MUY MALO</th>
								<th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">TOTAL DE RESPUESTAS RECIBIDA POR PREGUNTAS</th>

							</tr>
						</thead>
						<tbody>
							@php
							// el total de encuestas
							$total_encuestas2= count($encuestas); 
							$suma=0;
							$suma1=0;
							$suma2=0;
							$suma3=0;
							$suma4=0;
							//armamos un array
							$estadistico2=array();
							@endphp
							@foreach($preguntas_e as $value2)
							
							@php
					
							$calificacion1=0;
							$calificacion2=0;
							$calificacion3=0;
							$calificacion4=0;
							$calificacion5=0;
							// detalle para sumar
							foreach($encuestas as $x){
								//dd($x->complementos);
								$calificacion1 = $calificacion1+$x->complementos->where('id_pregunta_labs', $value2->id)->where('calificacion','5')->count();
							    
								$calificacion2=$calificacion2+$x->complementos->where('id_pregunta_labs', $value2->id)->where('calificacion','4')->count();

								$calificacion3=$calificacion3+$x->complementos->where('id_pregunta_labs',$value2->id)->where('calificacion','3')->count();

								$calificacion4=$calificacion4+$x->complementos->where('id_pregunta_labs',$value2->id)->where('calificacion','2')->count();

								$calificacion5=$calificacion5+$x->complementos->where('id_pregunta_labs',$value2->id)->where('calificacion','1')->count();
							}
							
							$suma+=$calificacion1;
							$suma1+=$calificacion2;
							$suma2+=$calificacion3;
							$suma3+=$calificacion4;
							$suma4+=$calificacion5;

							if($total_encuestas2<>0){
								
								}
								$preguntasf=['Muy bueno','Bueno','Ni bueno ni malo','Malo','Muy malo'];
								
								foreach($preguntasf as $p){
									$armar2=array();
									if($p=="Muy bueno"){
										$armar2['clasificador']=$p; 
										$armar2['valor']=$calificacion1;
									}elseif($p=="Bueno"){
										$armar2['clasificador']=$p; 
										$armar2['valor']=$calificacion2;
									}elseif($p=="Ni bueno ni malo"){
										$armar2['clasificador']=$p; 
										$armar2['valor']=$calificacion3;
									}elseif($p=="Malo"){
										$armar2['clasificador']=$p; 
										$armar2['valor']=$calificacion4;
									}elseif($p=="Muy malo"){
										$armar2['clasificador']=$p; 
										$armar2['valor']=$calificacion5;
									}
									array_push($estadistico2,$armar2);
									
								}

								@endphp
								<tr role="row">
									<td>{{$value2->nombre}}</td>
									<td align="center">{{$calificacion1}}</td>
									<td align="center">{{$calificacion2}}</td>
									<td align="center">{{$calificacion3}}</td>
									<td align="center">{{$calificacion4}}</td>
									<td align="center">{{$calificacion5}}</td>
									<td align="center">{{$total_encuestas2}}</td>
								
								</tr>
								@endforeach
					

								<tr role="row">
									<td>TOTAL DE RESPUESTAS RECIBIDA POR CALIFICACIÓN</td>
									<td  align="center">{{$suma}}</td>
									<td  align="center">{{$suma1}}</td>
									<td  align="center">{{$suma2}}</td>
									<td  align="center">{{$suma3}}</td>
									<td  align="center">{{$suma4}}</td>
									<td  align="center"></td>
						


								</tr>
        
						</tbody>
					</table>

					<br>
					<div class="col-md-12">
						<center>
							<canvas id="canvas2" style="max-width: 70%;"></canvas>
						</center>
					</div>
					<div class="col-md-12">
						<center>
							<canvas id="canvas3" style="max-width: 70%;"></canvas>
						</center>
					</div>
								

				</div>
			</div>
		</div>

		<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
		<script src="{{ asset ("/hc4/js/jquery.js") }}" type="text/javascript"></script>
		<script src="{{ asset ("/hc4/js/chart.min.js") }}"></script>
		<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
		<script>
			function random_bg_color() {
				var x = Math.floor(Math.random() * 256);
				var y = Math.floor(Math.random() * 256);
				var z = Math.floor(Math.random() * 256);
				var bgColor = "rgb(" + x + "," + y + "," + z + ")";
				return bgColor;
			}

			var barChartData = {
				labels: ['Mes {{$txt_mes[$mes-1]}} - Año {{$anio}}'],
				datasets: [
					@foreach($estadistico as $p) {
						label: '{{$p["pregunta"]}}',
						backgroundColor: random_bg_color(),
						//borderColor: 'blue',
						borderWidth: 1,
						data: ['{{$p["valor"]}}'],

					},
					@endforeach
				],
			};



			var ctx = document.getElementById('canvas2').getContext('2d');
			window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'bottom',
						align: 'start',
						fullWidth: 'true',
						labels: {
							//fontColor: 'rgb(255, 99, 132)',
						},
					},
					title: {
						display: true,
						text: 'Encuestas LABS'
					},
					scales: {
						yAxes: [{
							display: true,
							ticks: {
								beginAtZero: true,
								max: 100
							}
						}]
					}
				}
			});
		</script>
			<script>
			function random_bg_color() {
				var x = Math.floor(Math.random() * 256);
				var y = Math.floor(Math.random() * 256);
				var z = Math.floor(Math.random() * 256);
				var bgColor = "rgb(" + x + "," + y + "," + z + ")";
				return bgColor;
			}

			var barChartData = {
				labels: ['Mes {{$txt_mes[$mes-1]}} - Año {{$anio}}'],
				datasets: [
					@foreach($estadistico2 as $val) {
						label: '{{$val["clasificador"]}}',
						backgroundColor: random_bg_color(),
						//borderColor: 'blue',
						borderWidth: 1,
						data: ['{{$val["valor"]}}'],

					},
					@endforeach
				],
			};



			var ctx = document.getElementById('canvas3').getContext('2d');
			window.myBar = new Chart(ctx, {
				type: 'bar',
				data: barChartData,
				options: {
					responsive: true,
					legend: {
						position: 'bottom',
						align: 'start',
						fullWidth: 'true',
						labels: {
							//fontColor: 'rgb(255, 99, 132)',
						},
					},
					title: {
						display: true,
						text: 'Calificación LABS'
					},
					scales: {
						yAxes: [{
							display: true,
							ticks: {
								beginAtZero: true,
								max: 100
							}
						}]
					}
				}
			});
		</script>
</section>



@endsection