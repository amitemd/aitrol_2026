<label for="id_nivel" class="control-label">Nivel</label>
                             
<select id="id_nivel" name="id_nivel" class="form-control input-sm" required onchange="cotizador_recalcular();">
    <!--option value="">Seleccione..</option-->
@foreach ($convenios as $convenio)
    
    <option @if(!is_null($orden)) @if($orden->id_nivel == $convenio->id_nivel) selected @endif @endif value="{{$convenio->id_nivel}}">{{$convenio->nombre}}</option>
    
@endforeach
</select>
@if ($errors->has('id_nivel'))
<span class="help-block">
    <strong>{{ $errors->first('id_nivel') }}</strong>
</span>
@endif 