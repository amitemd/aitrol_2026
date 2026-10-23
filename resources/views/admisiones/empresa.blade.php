
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<label for="id_empresa" class="col-md-12 control-label">Empresa</label>
<div class="col-md-12">
    <!--select id="id_empresa" name="id_empresa" class="form-control input-sm" required>
        <option value=""  >Seleccione..</option>
        @php $eban='0'; @endphp    
        @foreach($empresas as $empresa) 
        <option @if(old('id_empresa')==$empresa->id){{"selected"}} @php $eban='1'; @endphp @elseif($cita->id_empresa==$empresa->id){{"selected"}} @php $eban='1'; @endphp @endif value="{{$empresa->id}}" @if($empresa->id == "1391707460001" && $cita->proc_consul == "1" && $eban==false) {{"selected"}}@endif>{{$empresa->nombrecomercial}}
        </option>
        @endforeach 
    </select-->
    @foreach($empresas as $empresa)
    <input type="radio" class="flat-red" @if($oldva==$empresa->id) checked @elseif($cita->id_empresa==$empresa->id) checked @endif name="id_empresa" id="{{$empresa->id}}" value="{{$empresa->id}}"><b style="font-size: 14px;">  {{$empresa->nombrecomercial}}</b><br>
    @endforeach
    @if($errors->has('id_empresa'))
    <span class="help-block">
        <strong>{{ $errors->first('id_empresa') }}</strong>
    </span>
    @endif         
</div>

<script type="text/javascript">

//Flat red color scheme for iCheck
    $('input[type="radio"].flat-red').iCheck({
      checkboxClass: 'icheckbox_flat-red',
      radioClass   : 'iradio_square-red'
    })  

</script>    