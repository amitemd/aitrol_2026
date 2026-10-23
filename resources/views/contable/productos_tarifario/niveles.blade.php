<div class="form-group col-md-12">
<label for="id_nivel" class="col-md-4 control-label">Nivel:</label>
	<div class="col-md-8">                           
		<select id="id_nivel" name="id_nivel" class="form-control input-sm" required>
		    <!--option value="">Seleccione..</option-->
		@foreach ($convenios as $convenio)		    
		    <option value="{{$convenio->id_nivel}}">{{$convenio->nombre}}</option>
		@endforeach
		</select>
	</div>

</div>