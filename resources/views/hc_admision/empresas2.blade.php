<label for="id_empresa" class="control-label">Empresa</label>
<select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="id_empresa" id="id_empresa">
    @foreach($empresas as $value)
        <option @if($id_empresa == $value->id) selected @endif value="{{$value->id}}" >{{$value->nombrecomercial}}</option>
    @endforeach
</select>