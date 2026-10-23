@extends('layouts.app-template-apps')
@section('content')
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            Banners Editar
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="{{route('bannersapps.update')}}">
            {{ csrf_field() }}
                <div class="form-group">
                    <label for="descripcion">Descripcion</label>
                    <input type="hidden" name="id" value="{{$banners->id}}">
                    <input type="text" class="form-control" name="descripcion" value="{{$banners->descripcion}}" id="descripcion">
                </div>
                <div class="form-group">
                    <label for="url">Url</label>
                    <input type="text" class="form-control" name="link" id="link" value="{{$banners->link}}">
                </div>
                <div class="form-group">
                    <label for="file" class="col-md-12">Archivo</label>
                    <input type="file" name="archivo" id="archivo">
                </div>
                
                <div class="form-group">
                    <label for="tipo">Tipo</label>
                    <select class="form-control" name="tipo" id="tipo">
                        <option value="">Seleccione... </option>
                        <option value="Top" @if($banners->tipo=='Top') selected="selected" @endif>Top</option>
                        <option value="Buttom" @if($banners->tipo=='Buttom') selected="selected" @endif>Buttom</option>
                        <option value="Home" @if($banners->tipo=='Home') selected="selected" @endif>Home</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="estado">Estado</label>
                    <select class="form-control" name="estado" id="estado">
                        <option value="">Seleccione... </option>
                        <option value="1" @if($banners->estado==1) selected="selected" @endif>Activo</option>
                        <option value="0" @if($banners->estado==0) selected="selected" @endif>Inactivo</option>
                    </select>
                </div>
                <div class="form-group" style="text-align: center;">
                   <button class="btn btn-primary" type="submit">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    
</script>
@endsection
