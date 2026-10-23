<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">Añadir CSV a Record Anestesiologico</h4>
</div>

<div class="modal-body">
  <div class="row" >
  <form class="form-vertical" role="form" method="POST" action="{{ route('anestesiologia.creacsv') }}" >
    <input type="hidden" name="id_hc_anestesiologia" value="{{$id}}">
    <input type="hidden" name="mostrar" value="{{$agenda}}">    
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <div class="form-group col-md-12{{ $errors->has('hora') ? ' has-error' : '' }}">
        <label for="hora" class="col-md-4 control-label" >Hora</label>
        <div class="col-md-7">
          <input id="hora" required class="form-control" type="TIME" name="hora" value="@if(old('hora')!=''){{old('hora')}}@endif" >
          @if ($errors->has('hora'))
            <span class="help-block">
              <strong>{{ $errors->first('hora') }}</strong>
            </span>
          @endif
        </div>
    </div>
    <div class="form-group col-md-12{{ $errors->has('presion_arterial') ? ' has-error' : '' }}">
        <label for="presion_arterial" class="col-md-4 control-label" >Presion Arterial</label>
        <div class="col-md-7">
          <input id="presion_arterial" required class="form-control"  name="presion_arterial" value="@if(old('presion_arterial')!=''){{old('presion_arterial')}}@endif" >
          @if ($errors->has('presion_arterial'))
            <span class="help-block">
              <strong>{{ $errors->first('presion_arterial') }}</strong>
            </span>
          @endif
        </div>
    </div>
    <div class="form-group col-md-12{{ $errors->has('pulso') ? ' has-error' : '' }}">
        <label for="pulso" class="col-md-4 control-label" >Pulso</label>
        <div class="col-md-7">
          <input id="pulso" required class="form-control" type="number"  name="pulso" value="@if(old('pulso')!=''){{old('pulso')}}@endif" >
          @if ($errors->has('pulso'))
            <span class="help-block">
              <strong>{{ $errors->first('pulso') }}</strong>
            </span>
          @endif
        </div>
    </div>
    <div class="form-group col-md-12{{ $errors->has('respiracion') ? ' has-error' : '' }}">
        <label for="respiracion" class="col-md-4 control-label" >Respiracion</label>
        <div class="col-md-7">
          <input id="respiracion" required class="form-control" type="number"  name="respiracion" value="@if(old('respiracion')!=''){{old('respiracion')}}@endif" >
          @if ($errors->has('respiracion'))
            <span class="help-block">
              <strong>{{ $errors->first('respiracion') }}</strong>
            </span>
          @endif
        </div>
    </div>
    <div class="form-group col-md-12{{ $errors->has('o2') ? ' has-error' : '' }}">
        <label for="o2" class="col-md-4 control-label" >Oxigeno</label>
        <div class="col-md-7">
          <input id="o2" required class="form-control" type="number"  name="o2" value="@if(old('o2')!=''){{old('o2')}}@endif" >
          @if ($errors->has('o2'))
            <span class="help-block">
              <strong>{{ $errors->first('o2') }}</strong>
            </span>
          @endif
        </div>
    </div>
    <div class="form-group col-md-12{{ $errors->has('orina') ? ' has-error' : '' }}">
        <label for="orina" class="col-md-4 control-label" >Orina</label>
        <div class="col-md-7">
          <input id="orina" class="form-control" type="number"  name="orina" value="@if(old('orina')!=''){{old('orina')}}@endif" >
          @if ($errors->has('orina'))
            <span class="help-block">
              <strong>{{ $errors->first('orina') }}</strong>
            </span>
          @endif
        </div>
    </div>
    <div class="form-group col-md-12{{ $errors->has('temperatura') ? ' has-error' : '' }}">
        <label for="temperatura" class="col-md-4 control-label" >Temperatura</label>
        <div class="col-md-7">
          <input id="temperatura" class="form-control" type="number"  name="temperatura" value="@if(old('temperatura')!=''){{old('temperatura')}}@endif" >
          @if ($errors->has('temperatura'))
            <span class="help-block">
              <strong>{{ $errors->first('temperatura') }}</strong>
            </span>
          @endif
        </div>
    </div>
    <div class="form-group col-md-12{{ $errors->has('anotaciones') ? ' has-error' : '' }}">
        <label for="anotaciones" class="col-md-4 control-label" >Anotaciones</label>
        <div class="col-md-7">
          <input id="anotaciones" required="" class="form-control" name="anotaciones" value="@if(old('anotaciones')!=''){{old('anotaciones')}}@endif" >
          @if ($errors->has('anotaciones'))
            <span class="help-block">
              <strong>{{ $errors->first('anotaciones') }}</strong>
            </span>
          @endif
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-6 col-md-offset-9">
            <button type="submit" class="btn btn-primary">
                    Guardar
            </button>
        </div>
    </div>
  </div>
  </form>
</div>   