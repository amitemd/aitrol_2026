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
            Membresias Editar
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" action="{{route('membresiasapps.update')}}">
            {{ csrf_field() }}
                <div class="form-group">
                    <input type="hidden" name="id" value="{{$membresias->id}}">
                    <label for="descripcion">Descripcion</label>
                    <input type="text" class="form-control" required name="descripcion" value="{{$membresias->nombre}}" id="descripcion">
                </div>
                <div class="form-group">
                    <label for="anual" class="col-md-12">Anual</label>
                    <input class="form-control" type="number" value="{{$membresias->precio_anual}}" name="anual" id="anual" placeholder="Anual" value="0.00">
                </div>
                <div class="form-group">
                    <label for="file" class="col-md-12">Archivo</label>
                    <input type="file" name="archivo" id="archivo">
                </div>
                <div class="col-md-12">
                    <label> Informacion de Membresia </label>
                </div>
                <div class="col-md-12" style="margin-top: 10px;">
                    <table style="width:100%;">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Porcentaje</th>
                                <th><button type="button" class="btn btn-primary btn-sm" onclick="nuevo()">Agregar</button></th>
                            </tr>
                        </thead>
                        <tbody id="agregar">
                            <tr style="display: none;" id="mifila">
                                <td><input class="form-control" type="text" name="nombre[]"></td>
                                <td><input class="form-control" type="number" max="100" name="porcentaje[]"></td>
                                <td><button type="button" class="btn btn-danger btn-sm" onclick="return $(this).parent().parent().remove()">Eliminar</button></td>
                            </tr>
                            @foreach($membresias->detalles as $value)
                            <tr>
                                <td><input class="form-control" type="text" name="nombre[]" value="{{$value->nombre}}"></td>
                                <td><input class="form-control" type="number" max="100" name="porcentaje[]" value="{{$value->porcentaje_descuento}}"></td>
                                <td><button type="button" class="btn btn-danger btn-sm" onclick="return $(this).parent().parent().remove()">Eliminar</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="form-group" style="text-align: center;">
                   <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var fila = $("#mifila").html();
    function nuevo() {
        var nuevafila = $("#mifila").html();
        var rowk = document.getElementById("agregar").insertRow(-1);
        //$('#mifila tr:last').before("<tr class='well'>"+nuevafila+"</tr>")
        rowk.innerHTML = fila;
        //rowk.className="well";
/*         $('.select2_cuentas').select2({
            tags: false
        }); */
    }
</script>
@endsection
