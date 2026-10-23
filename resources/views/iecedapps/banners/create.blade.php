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
            Banners Crear
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="{{route('bannersapps.store')}}">
            {{ csrf_field() }}
                <div class="form-group">
                    <label for="descripcion">Descripcion</label>
                    <input type="text" class="form-control" required name="descripcion" id="descripcion">
                </div>
                <div class="form-group">
                    <label for="url">Url</label>
                    <input type="text" class="form-control" required name="link" id="link">
                </div>
                <div class="form-group">
                    <label for="file" class="col-md-12">Archivo</label>
                    <input type="file" name="archivo" id="archivo" required>
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo</label>
                    <select class="form-control" name="tipo" id="tipo" required>
                        <option value="">Seleccione... </option>
                        <option value="Top">Top</option>
                        <option value="Buttom">Buttom</option>
                        <option value="Home">Home</option>
                    </select>
                </div>
                <div class="form-group" style="text-align: center;">
                   <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    
</script>
@endsection
