<html lang="es">

<head>
	<meta charset="UTF-8">
	<TITLE>Correo</TITLE>
</head>

<body>
	<style type="text/css">
		* {
			font-family: sans-serif !important;
		}
	</style>
	<div style="width: 90%; padding-left: 5%;">
		<div class="display:flex;align-items:center;justify-content:center">
			<span>Estimado proveedor:</span>
		</div>
		<?php
		$comoPago = '';
		if ($formaPago == 'EFECTIVO') {
		} elseif ($formaPago == 'CHEQUE') {
		} elseif ($formaPago == 'DEPOSITO') {
		} elseif ($formaPago == 'TARJETA CREDITO') {
		} elseif ($formaPago == 'TRANSFERENCIA BANCARIA') {
			$comoPago = 'se procedio a transferir';
		} elseif ($formaPago == 'TARJETA DEBITO') {
		} elseif ($formaPago == 'PENDIENTE PAGO') {
		} elseif ($formaPago == 'PAYPAL') {
		} elseif ($formaPago == 'DATALINK') {
		} elseif ($formaPago == 'PAGO POR APP') {
		}
		$dias = array("Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sábado");
		$meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
		?>
		<p>Le notificamos que el día <?= $dias[date('w')] . " " . date('d') . " de " . $meses[date('n') - 1] . " del " . date('Y') ?> <?= $comoPago ?> la cantidad de
			{{ number_format($abono, 2, '.', '') }} por concepto de la cancelación de las siguientes facturas:
		</p>
		<table>
			<thead>
				<tr>
					<th>
						Concepto
					</th>
					<th>
						Div.
					</th>
					<th>
						Saldo
					</th>
					<th>
						Abono
					</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						{{$concepto}} Ref.: {{$secuencia}}
					</td>
					<td>
						$
					</td>
					<td style="background-color: greenyellow;text-align:left">
						{{ number_format($saldo, 2, '.', '') }}
					</td>
					<td style="background-color: greenyellow;text-align:left">
						{{ number_format($abono, 2, '.', '') }}
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</body>
</html>