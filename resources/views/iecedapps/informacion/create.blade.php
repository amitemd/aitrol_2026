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
            Charlas Crear
        </div>
        <div class="card-body">

            <form method="POST"  enctype="multipart/form-data" action="{{route('apps_informacion.store')}}">
            {{ csrf_field() }}
                <div class="form-group">
                    <label for="nobre">Nombre Sucursal</label>
                    <input type="text" class="form-control" name="nombre" required id="nombre">
                </div>
                <div class="form-group">
                    <label for="direccion">Direccion</label>
                    <input type="text" class="form-control" name="direccion" required id="direccion">
                </div>
                <div class="form-group">
                    <label for="ciudad">Ciudad</label>
                    <select name="ciudad" id="ciudad" class="form-control">
                        <option value="">Seleccione...</option>
                        <option value="Guayaquil">Guayaquil</option>
                        <option value="Portoviejo">Portoviejo</option>
                       
                    </select>
                </div>
                <div class="form-group">
                    <label for="whatsapp">Whatsapp</label>
                    <input type="text" maxlength="10" class="form-control" name="whatsapp" required id="whatsapp">
                </div>
                <div class="form-group">
                    <label for="telefono">Telefono</label>
                    <input type="text" maxlength="10" class="form-control" name="telefono" required id="telefono">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" required id="email">
                </div>
                <div class="form-group">
                    <label for="ubicacion">Url Google Maps</label>
                    <input type="text" class="form-control" required name="ubicacion" id="ubicacion">
                </div>
                <div class="form-group">
                    <label for="file" class="col-md-12">Archivo</label>
                    <input type="file" name="archivo" id="archivo" required>
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
