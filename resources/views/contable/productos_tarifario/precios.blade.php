<div class="form-group col-md-12">
<label for="precio" class="col-md-4 control-label">Precio Producto:</label>
	<div class="col-md-4">
		<select id="precio" name="precio" class="form-control input-sm select2_productos" required>
		    <!--option value="">Seleccione..</option-->
		@foreach ($precio_prod as $value)
		    <option value="{{$value->precio}}">{{$value->precio}}</option>
		@endforeach
		</select>
	</div>

</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script type="text/javascript">
	$('.select2_productos').select2({
        tags: true
    });
</script>
