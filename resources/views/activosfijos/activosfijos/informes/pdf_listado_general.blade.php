<!DOCTYPE html>
<style type="text/css">
	
</style>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>LISTADO POR TIPO</title>
</head>

<body>
	<div style="display: table">
		<div style="display:table-row;">
			<div style="display:table-cell;"> <img src='{{storage_path("app/logo/$empresa->logo")}}' style="height:60px;width:80px;" alt=""></div>
			<div style="display:table-cell;vertical-align: middle;"><span style="font-size: 10px;margin-left:20px;">{{$empresa->razonsocial}}</span></div>
		</div>
	</div>
	<div style="border-bottom: 1px solid;padding: 0px;">
		<table style="font-size: 10px;" width="100%">
			<thead>
				<tr style="background-color: #92CFEF; ">
					<th>FECHA ADQ.</th>
					<th>TIPO ACTIVO</th>
					<th>CATEGORIA</th>
					<th>CODIGO</th>
					<th>NOMBRE</th>
					<th>DESCRIPCION</th>
					<th>MARCA</th>
					<th>MODELO</th>
					<th>SERIE</th>
					<th>COLOR</th>
					<th>UBICACIÓN</th>
					<th>TASA %</th>
					<th>VIDA UTIL</th>
					<th>V. ORIGEN</th>
					<th>V. SALVA.</th>
					<th>TOT. DEPRE</th>
					<th>VAL. ACTUAL</th>				
				</tr>
			</thead>
			<tbody>
				@php
				$cont1 = 0;
				$cont2 = 0;
				$cont3 = 0;
				$cont4 = 0;
				@endphp
				@foreach($activos as $activo)

				@php
				$cont1 += $activo->costo;
				$depreciacion = Sis_medico\AfDepreciacionCabecera::where('af_depreciacion_cabecera.estado', '1')->join('af_depreciacion_detalle as depre_det', 'depre_det.depreciacion_cabecera_id', 'af_depreciacion_cabecera.id')->select('depre_det.*', 'af_depreciacion_cabecera.*')->where('depre_det.activo_id', $activo->id)->get();
				$tot_activo = 0;

				if (!is_null($depreciacion)) {
				foreach ($depreciacion as $dep) {
				$tot_activo += $dep->valordepreciacion;
				}
				}

				$val_actual = $activo->costo - $tot_activo;
				$cont2 += $tot_activo;
				$cont3 += $val_actual;
				@endphp
				<tr>
					<td>{{substr($activo->fecha_compra, 0, 10)}}</td>
					<td>{{$activo->tipo->nombre}}</td>
					<td>{{$activo->sub_tipo->nombre}}</td>
					<td>{{$activo->codigo}}</td>
					<td>{{$activo->nombre}}</td>
					<td>{{$activo->descripcion}}</td>
					<td>{{$activo->marca}}</td>
					<td>{{$activo->modelo}}</td>
					<td>{{$activo->serie}}</td>
					<td>{{$activo->color}}</td>
					<td>{{$activo->ubicacion}}</td>
					<td>{{$activo->tasa}}</td>
					<td>{{$activo->vida_util}}</td>
					<td>{{number_format($activo->costo,2,'.',',')}}</td>
					<td>0,00</td>
					<td>{{number_format($tot_activo,2,'.',',')}}</td>
					<td>{{number_format($val_actual,2,'.',',')}}</td>					
				</tr>
				@endforeach
				<tr>
					<td><b>Total</b></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td><b>{{number_format($cont1,2,'.',',')}}</b></td>
					<td><b>0,00</b></td>
					<td><b>{{number_format($cont2,2,'.',',')}}</b></td>
					<td><b>{{number_format($cont3,2,'.',',')}}</b></td>


				</tr>
			</tbody>
		</table>
	</div>

</body>

</html>