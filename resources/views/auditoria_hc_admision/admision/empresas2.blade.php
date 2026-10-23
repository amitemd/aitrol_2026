<label for="id_empresa" class="control-label">Empresas</label>
<select onchange="guardar_protocolo();"  class="form-control input-sm" style="width: 100%;" name="id_empresa" id="id_empresa">

     @php 
        $aud_procedimientos = DB::table('aud_hc_procedimientos')->where('id_procedimientos_org',$procedimiento->id)->first();
    @endphp 

    @foreach($empresas as $value)
        <option @if($aud_procedimientos->id_empresa == $value->id) selected @endif value="{{$value->id}}" >{{$value->nombrecomercial}}</option>
    @endforeach
</select>