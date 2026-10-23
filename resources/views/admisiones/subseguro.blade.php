@if($cantidad2>0)
    <label for="id_subseguro" class="col-md-8 control-label">Sub-Seguro</label>
    <select id="id_subseguro" name="id_subseguro" class="form-control input-sm" required autofocus>
        <option value="">Seleccione ...</option>
        @foreach ($subseguros as $subseguro)
        @if($parentesco=='Principal' && $subseguro->principal=='1')
            <option @if($oldv!='0') @if($oldv==$subseguro->id){{"selected"}}@endif @elseif(!is_null($historia))@if($historia->id_subseguro==$subseguro->id){{"selected"}}@endif @endif value="{{$subseguro->id}}">{{$subseguro->nombre}}</option>
        @endif
        @if($parentesco!='Principal' && $subseguro->principal=='0')
            <option @if($oldv!='0') @if($oldv==$subseguro->id){{"selected"}}@endif @elseif(!is_null($historia))@if($historia->id_subseguro==$subseguro->id){{"selected"}}@endif @endif value="{{$subseguro->id}}">{{$subseguro->nombre}}</option>
        @endif
    @endforeach
    </select>    
@endif 
