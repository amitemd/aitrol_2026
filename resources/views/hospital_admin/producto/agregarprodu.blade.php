@extends('hospital_admin.base')
@section('action-content')
<div class="container-fluid" id="area_cambiar">
	<div class="box-header">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-2" style="font-family: Montserrat Medium; font-size: 18px; color: #626567">
					FARMACIA
				</div>

				<div class="col-md-10" style="border-bottom-style: dashed; border-bottom-width: 3px; margin-bottom: 12px; opacity: : 0.7;">
				</div>

				<br>
				<div class="col-md-12">
					<div class="row">
						<div onclick="location.href='{{route('hospital_admin.modalprovedor')}}'" class="col-md-8" style="font-family: Montserrat Bold; font-size: 18px; color: #797D7F">AGREGAR PRODUCTOS
						</div>
						<div class="col-md-4" style="text-align: right;">
                        <a type="button" href="{{route('hospital_admin.farmacia')}}" class="btn btn-primary btn-sm">
                        <span class="glyphicon glyphicon-arrow-left">Regresar</span>
                        </a>
                        </div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
	<div class="box-body" style="border: 2px solid #004AC1;border-radius:8px;">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-4">
				<div class="row">
					<div class="col-md-6">
						<label style="font-family: Montserrat Medium;" class="grey-text font-weight-light">C&oacutedigo</label>
					</div>
					<div class="col-md-6">
						<input type="number" name="codigo" id="defaultFormNameModalEx" class="form-control form-control-sm" style="border-radius: 10px;">
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<label style="font-family: Montserrat Medium;" class="grey-text font-weight-light">Cantidad de medidas</label>
					</div>
					<div class="col-md-6">
						<input type="number" name="codigo" id="defaultFormNameModalEx" class="form-control form-control-sm" style="border-radius: 10px;">
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<label style="font-family: Montserrat Medium;" class="grey-text font-weight-light">Registro Sanitario</label>
					</div>
					<div class="col-md-6">
						<input type="text" name="codigo" id="defaultFormNameModalEx" class="form-control form-control-sm" style="border-radius: 10px;">
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<label style="font-family: Montserrat Medium;" class="grey-text font-weight-light">Laboratorio</label>
					</div>
					<div class="col-md-6">
						<input type="text" name="codigo" id="defaultFormNameModalEx" class="form-control form-control-sm" style="border-radius: 10px;">
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<label style="font-family: Montserrat Medium;" class="grey-text font-weight-light">Cantidad de usos</label>
					</div>
					<div class="col-md-6">
						<input type="number" name="codigo" id="defaultFormNameModalEx" class="form-control form-control-sm" style="border-radius: 10px;">
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="row">
					<div class="col-md-4">
						<label style="font-family: Montserrat Medium;" class="grey-text font-weight-light">Nombre</label>
					</div>
					<div class="col-md-6">
						<input type="text" name="codigo" id="defaultFormNameModalEx" class="form-control form-control-sm" style="border-radius: 10px;">
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-4">
						<label style="font-family: Montserrat Medium;" class="grey-text font-weight-light">Descripci&oacuten</label>
					</div>
					<div class="col-md-6">
						<textarea class="col-md-12" name="" rows="8" style="border-radius: 10px;"></textarea>
					</div>
				</div>
			</div>
			<div class="col-md-4">
				<div class="row">
					<div class="col-md-6">
						<label style="font-family: Montserrat Medium;" class="grey-text font-weight-light">Stock m&iacutenimo</label>
					</div>
					<div class="col-md-6">
						<input type="text" name="stock" id="defaultFormNameModalEx" class="form-control form-control-sm" style="border-radius: 10px;">
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<label style="font-family: Montserrat Medium;" class="grey-text font-weight-light">Proveedor</label>
					</div>
					<div class="col-md-6">
						<input type="text" name="proveedor" id="defaultFormNameModalEx" class="form-control form-control-sm" style="border-radius: 10px;">
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<label style="font-family: Montserrat Medium;" class="grey-text font-weight-light">Tipo de producto</label>
					</div>
					<div class="col-md-6">
						<input type="text" name="tip_prod" id="defaultFormNameModalEx" class="form-control form-control-sm" style="border-radius: 10px;">
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<label style="font-family: Montserrat Medium;" class="grey-text font-weight-light">Forma de despacho</label>
					</div>
					<div class="col-md-6">
						<input type="text" name="codigo" id="defaultFormNameModalEx" class="form-control form-control-sm" style="border-radius: 10px;">
					</div>
				</div>
			</div>
		</div>
	</div>	
	<center>
		<button class="btn btn-primary boton-proce" style="font-family: Montserrat Medium; border-radius: 20px; width: 125px;margin: 20px" id="boton" onclick="guardar_procedimiento();">AGREGAR
		</button>
	</center>
</div>
</div>
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>

@endsection