@extends('contable.Guia_Remision.Transportistas.base')
@section('action-content')
<div class="box-body">
    <div class="box">
        <div class="box-header">
          <div class="col-md-12">
              <div class="col-md-8">
                  <h3 class="box-title">Editar Transportista</h3>
              </div>
              <div class="col-md-4 text-right">
                <a href="javascript: history.go(-1)" id="btnFalla" class="btn btn-primary btn-gray">
                    Regresar
              </a>
              </div>
          </div>

        </div>
        <form id="editar_plantilla" method="post" action="{{route('transportistas.update')}}">
            {{ csrf_field() }}
            <input type="hidden" name="id" id="id" value="{{$id}}">
            <div class="box-body">
                <div class="form-group col-xs-6">
                    <label for="ci_ruc" class="col-md-20 control-label">Identificación/RUC</label>
                    <input type="number" name="ci_ruc" size="20" class="form-control" value="{{$mantenimientos_transportistas->ci_ruc}}" onblur="">
                </div>


                <div class="form-group col-xs-6">
                    <label for="tipo_documento" class="col-md-20 control-label">Tipo de Documento</label>
                    <select class="form-control validar" name="tipo_documento" id="tipo_documento">
                        <option {{$mantenimientos_transportistas->tipo_documento == 4 ? 'selected' : ''}} value="4">RUC</option>
                        <option {{$mantenimientos_transportistas->tipo_documento == 5 ? 'selected' : ''}} value="5">Cédula</option>
                        <option {{$mantenimientos_transportistas->tipo_documento == 6 ? 'selected' : ''}} value="6">Pasaporte</option>
                        <option {{$mantenimientos_transportistas->tipo_documento == 8 ? 'selected' : ''}} value="8">Identificación del Exterior</option>
                    </select>
                </div>

                <div class="form-group col-xs-6">
                    <label for="razon_social" class="col-md-20 control-label">Razón Social </label>
                    <input type="text" name="razon_social" size="20" class="form-control" value="{{$mantenimientos_transportistas->razon_social}}">
                </div>

                <div class="form-group col-xs-6">
                    <label for="nombres" class="col-md-20 control-label">Nombre</label>
                    <input type="text" name="nombres" size="20" class="form-control" value="{{$mantenimientos_transportistas->nombres}}">
                </div>

                <div class="form-group col-xs-6">
                    <label for="apellidos" class="col-md-20 control-label">Apellidos</label>
                    <input type="text" name="apellidos" size="20" class="form-control" value="{{$mantenimientos_transportistas->apellidos}}">
                </div>

                <div class="form-group col-xs-6">
                    <label for="nombrecomercial" class="col-md-20 control-label">Nombre Comercial</label>
                    <input type="text" name="nombrecomercial" size="20" class="form-control" value="{{$mantenimientos_transportistas->nombrecomercial}}">
                </div>

                <div class="form-group col-xs-6">
                    <label for="ciudad" class="col-md-20 control-label">Ciudad</label>
                    <input type="text" name="ciudad" size="20" class="form-control" value="{{$mantenimientos_transportistas->ciudad}}">
                </div>

                <div class="form-group col-xs-6">
                    <label for="direccion" class="col-md-20 control-label">Dirección</label>
                    <input type="text" name="direccion" size="20" class="form-control" value="{{$mantenimientos_transportistas->direccion}}">
                </div>

                <div class="form-group col-xs-6">
                    <label for="email" class="col-md-20 control-label">Email 1</label>
                    <input type="text" name="email" size="20" class="form-control" value="{{$mantenimientos_transportistas->email}}">
                </div>

                <div class="form-group col-xs-6">
                    <label for="email2" class="col-md-20 control-label">Email 2</label>
                    <input type="text" name="email2" size="20" class="form-control" value="{{$mantenimientos_transportistas->email2}}">
                </div>

                <div class="form-group col-xs-6">
                    <label for="telefono1" class="col-md-20 control-label">Teléfono 1</label>
                    <input type="number" name="telefono1" size="20" class="form-control" value="{{$mantenimientos_transportistas->telefono1}}">
                </div>

                <div class="form-group col-xs-6">
                    <label for="telefono2" class="col-md-20 control-label">Teléfono 2</label>
                    <input type="number" name="telefono2" size="20" class="form-control" value="{{$mantenimientos_transportistas->telefono2}}">
                </div>

                <div class="form-group col-xs-6{{ $errors->has('placa') ? ' has-error' : '' }}">
                    <label for="placa" class="col-md-20 control-label">Placa</label>
                    <input type="text" onchange="" name="placa" size="20" class="form-control" value="{{$mantenimientos_transportistas->placa}}">
                </div>

                <div class="form-group col-xs-6">
                    <label for="rise" class="col-md-20 control-label">RISE</label>
                    <select class="form-control validar" name="rise" id="rise">
                        <option {{$mantenimientos_transportistas->rise == 1 ? 'selected' : ''}} value="1">SI</option>
                        <option {{$mantenimientos_transportistas->rise == 0 ? 'selected' : ''}} value="0">NO</option>
                    </select>
                </div>

                <div class="form-group col-xs-6{{ $errors->has('contabilidad') ? ' has-error' : '' }}">
                    <label for="contabilidad" class="col-md-20 control-label">Contabilidad</label>
                    <select class="form-control validar" name="contabilidad" id="contabilidad">
                        <option {{$mantenimientos_transportistas->contabilidad == 1 ? 'selected' : ''}} value="1">SI</option>
                        <option {{$mantenimientos_transportistas->contabilidad == 0 ? 'selected' : ''}} value="0">NO</option>
                    </select>
                </div>

                <div class="form-group col-xs-6{{ $errors->has('contribuyente_especial') ? ' has-error' : '' }}">
                    <label for="contribuyente_especial" class="col-md-20 control-label">Editar Contribuyente Especial</label>
                    <select class="form-control validar" name="contribuyente_especial" id="contribuyente_especial">
                        <option {{$mantenimientos_transportistas->contribuyente_especial == 1 ? 'selected' : ''}} value="1">SI</option>
                        <option {{$mantenimientos_transportistas->contribuyente_especial == 0 ? 'selected' : ''}} value="0">NO</option>
                    </select>
                </div>

                <div class="form-group">
                    <div class="col-md-12 text-center">
                        <button type="submit" id="btnFalla" class="btn btn-primary btn-gray">
                            Actualizar
                      </button>
                    </div>
                </div>
            </div>
          </div>
        </form>
    </div>
</div>

<script>
      /*
        $("#btnFalla").click(function() {
            $.ajax({
                url: "{{route('transportistas.update')}}",
                type: 'POST',
                data: $('#editar_plantilla').serialize(),
                dataType: 'json',
                success: function(json) {
                    if (json.result == 'ok') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Datos guardados correctamente',
                            confirmButtonText: 'OK',
                        }).then((result) => {
                            /* Read more about isConfirmed, isDenied below 
                            if (result.isConfirmed) {
                                window.open('index', '_self');
                            }
                        })
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al guardar los datos',
                            text: json.mensajes,
                            confirmButtonText: 'OK',
                        })
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        });
    });
      */
</script>
@endsection
