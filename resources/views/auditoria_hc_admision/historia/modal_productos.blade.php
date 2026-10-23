<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">FORMATO DE DESCARGO DE PRODUCTOS</h4>
</div>

<div class="modal-body">
	<div class="row" style="padding: 10px;">
		<form class="form-vertical" role="form" method="POST" action="{{ route('descargoProducto.excel') }}">
                {{ csrf_field() }}
				<input  type="hidden" class="form-control" name="protocolo" value="{{ $protocolo->id }}" >
				<input type="hidden" class="form-control" name="agenda" value="{{$agenda->id}}">
               
                <div class="box-body col-md-12">
	                <!--id_procedimiento_completo-->
	              
					<div class="form-group col-md-6{{ $errors->has('fecha_oxigeno') ? ' has-error' : '' }}">
	                    <label for="fecha_operacion" class="col-md-6">Fecha</label>

	                    <div class="col-md-6">
	                        <input class="form-control" type="text" name="fecha_oxigeno" id="fecha_oxigeno" value="" required="required"> 
	                        @if ($errors->has('fecha_operacion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('fecha_operacion') }}</strong>
                            </span>
	                        @endif
	                    </div>
	                </div>

	                <div class="form-group col-md-6">
                        <label class="col-md-6"> Tipo de formato </label>
                        <div class="col-md-6">
							<select class="form-control" name="tipo_formato" id="tipo_formato" required>
							    <option value="">Seleccione...</option>
								@foreach($formatos as $x)
									<option value="{{$x->id}}">{{$x->descripcion}}</option>
								@endforeach
							</select>
                            
                        </div>
                    </div>
					<!--
					<div class="form-group col-md-6 {{$errors->has('')}}">
						<label class="col-md-6 control-label" for="codigo">Código</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="codigo" id="codigo" placeholder="Ingrese código">
						</div>
					</div>-->
					<!--
					<div class="form-group col-md-6 {{$errors->has('litros')}}">
						<label class="col-md-6" for="codigo">Litros</label>
						<div class="col-md-6">
							<input type="number" class="form-control" name="litros" id="litros" required placeholder="Ingrese litros">
						</div>
					</div>-->
					

	                <div class="form-group col-md-12" style="text-align: center;">
	                  
	                        <button type="submit" class="btn btn-primary" formtarget="_blank">
	                             <i class="fa fa-download"></i> Descargar
	                        </button>
	                   
	                </div>
                </div>
        </form>    
	</div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script type="text/javascript">
	$(function () {
        $('#fecha_oxigeno').datetimepicker({
            format: 'YYYY/MM/DD'
        });
    }); 
    $('input[type="checkbox"].flat-orange').iCheck({
		checkboxClass: 'icheckbox_flat-orange',
		radioClass   : 'iradio_flat-orange'
	})    
	

</script>