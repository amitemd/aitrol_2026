<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">AGREGAR PROCEDIMIENTO</h4>
</div>

<div class="modal-body">
	<div class="row" style="padding: 10px;">
		<form class="form-vertical" role="form" method="POST" action="{{ route('procedimientos_hc.guardar') }}">
                {{ csrf_field() }}
                <input  type="hidden" class="form-control" name="id_hc" value="{{ $historia_clinica->hcid }}" >
                <input  type="hidden" class="form-control" name="id_seguro" value="{{ $historia_clinica->id_seguro }}" >
                <div class="box-body col-xs-24">
	                <!--id_procedimiento_completo-->
	                <div class="form-group col-xs-12{{ $errors->has('id_procedimiento_completo') ? ' has-error' : '' }}">
	                    <label for="id_procedimiento_completo" class="col-md-4 control-label">Procedimiento Realizado</label>

	                    <div class="col-md-7">
	                        <select id="id_procedimiento_completo" name="id_procedimiento_completo" class="form-control" required="required">
	                            <option value="">Seleccione..</option>
	                            @foreach($procedimientos_completos as $value)
	                                @if ($value->estado != 0)
	                                        <option value="{{$value->id}}"> {{$value->nombre_completo}}</option>
	                                @endif    
	                            @endforeach
	                        </select>  
	                        @if ($errors->has('id_procedimiento_completo'))
	                            <span class="help-block">
	                                <strong>{{ $errors->first('id_procedimiento_completo') }}</strong>
	                            </span>
	                        @endif
	                    </div>
	                </div>

	                <div class="form-group">
	                    <div class="col-md-6 col-md-offset-4">
	                        <button type="submit" class="btn btn-primary">
	                            Agregar
	                        </button>
	                    </div>
	                </div>
                </div>
        </form>    
	</div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>
