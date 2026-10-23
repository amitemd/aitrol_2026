@extends('contable.clientes.base')
@section('action-content')

<style type="text/css">
  .separator{
    width:100%;
    height:30px;
    clear: both;
  }
</style>

  <script type="text/javascript">
    
    //Valida que solo ingrese numeros
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    //Retorna a la pagina anterior
    function goBack() {
      window.history.back();
    }

  </script>

<section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="{{route('clientes.index')}}">Cliente</a></li>
        <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
      </ol>
    </nav>
    <form  class="form-vertical" role="form" method="POST" action="{{ route('clientes.update', ['id' => $cliente->identificacion]) }}">
        <input type="hidden" name="_token" value="{{csrf_token()}}">
        <input type="hidden" name="id" value="{{$cliente->identificacion}}">
        <div class="box">
            <div class="box-header color_cab">
                <div class="col-md-9">
                  <h5><b>DETALLE CLIENTE</b></h5>
                </div>
                <div class="col-md-1 text-right">
                    <button onclick="goBack()" class="btn btn-default btn-gray" >
                        <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                    </button>
                </div>
            </div>
            <div class="separator"></div>
            <div class="box-body dobra">
                <!--TIPO IDENTIFICACION-->
                <div class="form-group col-xs-8">
                    <label for="tipo_identificacion" class="col-md-2 texto" style="color: blue">Identificacion</label>
                    <div class="col-md-3">
                        <select id="tipo_identificacion" name="tipo_identificacion" class="form-control" required autofocus>
                            <option {{$cliente->tipo == 4 ? 'selected' : ''}} value="4">RUC</option>
                            <option {{$cliente->tipo == 5 ? 'selected' : ''}} value="5">CEDULA</option>
                            <option {{$cliente->tipo == 6 ? 'selected' : ''}} value="6">PASAPORTE</option>
                            <option {{$cliente->tipo == 8 ? 'selected' : ''}} value="8">CEDULA/RUC EXTRANJERO</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                    <input id="identificacion" type="text" class="form-control" name="identificacion" value="{{$cliente->identificacion}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" autofocus>
                        @if ($errors->has('identificacion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('identificacion') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-8{{ $errors->has('nombre') ? ' has-error' : '' }}">
                    <label for="nombre" class="col-md-2 texto" style="color: blue">Nombre</label>
                    <div class="col-md-7">
                    <input id="nombre" type="text" class="form-control" name="nombre" value="{{$cliente->nombre}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                        @if ($errors->has('nombre'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-8{{ $errors->has('clase') ? ' has-error' : '' }}">
                    <label for="clase" class="col-md-2 control-label" style="color: blue">Clase</label>
                    <div class="col-md-7">
                        <select id="clase" name="clase" class="form-control" required>
                            <option {{$cliente->clase == 'normal' ? 'selected' : ''}} value="normal" >normal</option>
                            <option {{$cliente->clase == '1' ? 'selected' : ''}} value="1">Precio 1</option>
                            <option {{$cliente->clase == '2' ? 'selected' : ''}} value="2">Precio 2</option>
                            <option {{$cliente->clase == '3' ? 'selected' : ''}} value="3">Precio 3</option>
                            <option {{$cliente->clase == '4' ? 'selected' : ''}} value="4">Precio 4</option>
                        </select>
                        @if ($errors->has('clase'))
                        <span class="help-block">
                            <strong>{{ $errors->first('clase') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="separator"></div>
                <!--NOMBRE REPRESENTANTE-->
                <div class="form-group  col-xs-6{{ $errors->has('nombre_representante') ? ' has-error' : '' }}">
                    <label for="nombre_representante" class="col-md-3 texto">Nombre:</label>
                    <div class="col-md-7">
                        <input id="nombre_representante" type="text" class="form-control" name="nombre_representante" value="{{$cliente->nombre_representante}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                        @if ($errors->has('nombre_representante'))
                        <span class="help-block">
                            <strong>{{ $errors->first('nombre_representante') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <!--Cedula Representante-->
                <div class="form-group col-xs-6{{ $errors->has('cedula_representante') ? ' has-error' : '' }}">
                    <label for="cedula_representante" class="col-md-3 texto">Cedula</label>
                    <div class="col-md-7">
                        <input id="cedula_representante" type="text" maxlength="13" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" class="form-control" name="cedula_representante" value="{{$cliente->cedula_representante}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                        @if ($errors->has('cedula_representante'))
                        <span class="help-block">
                            <strong>{{ $errors->first('cedula_representante') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <!--Ciudad Representante-->
                <div class="form-group col-xs-6{{ $errors->has('ciudad_representante') ? ' has-error' : '' }}">
                    <label for="ciudad_representante" class="col-md-3 texto">Ciudad</label>
                    <div class="col-md-7">
                        <input id="ciudad_representante" type="text" class="form-control" name="ciudad_representante" value="{{$cliente->ciudad_representante }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                        @if ($errors->has('ciudad_representante'))
                        <span class="help-block">
                            <strong>{{ $errors->first('ciudad_representante') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <!--Direccion Representante-->
                <div class="form-group col-xs-6{{ $errors->has('direccion_representante') ? ' has-error' : '' }}">
                    <label for="direccion_representante" class="col-md-3 texto">Direccion</label>
                    <div class="col-md-7">
                        <input id="direccion_representante" type="text" class="form-control" name="direccion_representante" value="{{$cliente->direccion_representante}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                        @if ($errors->has('direccion_representante'))
                        <span class="help-block">
                            <strong>{{ $errors->first('direccion_representante') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <!--Telefono_1-->
                <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                    <label for="telefono1" class="col-md-3 texto">Telefono 1</label>
                    <div class="col-md-7">
                        <input id="telefono1" maxlength="10" type="text" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" class="form-control" name="telefono1" value="{{ $cliente->telefono1_representante}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                        @if ($errors->has('telefono1'))
                        <span class="help-block">
                            <strong>{{ $errors->first('telefono1') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <!--Telefono_2-->
                <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                    <label for="telefono2" class="col-md-3 texto">Telefono 2</label>
                    <div class="col-md-7">
                        <input id="telefono2" type="text" class="form-control" maxlength="10" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="telefono2" value="{{$cliente->telefono2_representante}}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                        @if ($errors->has('telefono2'))
                        <span class="help-block">
                            <strong>{{ $errors->first('telefono2') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <!--Correo-->
                <div class="form-group col-xs-6{{ $errors->has('correo') ? ' has-error' : '' }}">
                    <label for="correo" class="col-md-3 texto">Correo</label>
                    <div class="col-md-7">
                        <input id="correo" type="email" class="form-control" name="correo" value="{{ $cliente->email_representante}}" autocomplete="off" required autofocus>
                        @if ($errors->has('correo'))
                        <span class="help-block">
                            <strong>{{ $errors->first('correo') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <!--Pais-->
                <div class="form-group col-xs-6{{ $errors->has('pais') ? ' has-error' : '' }}">
                    <label for="pais" class="col-md-3 texto">Pais</label>
                    <div class="col-md-7">
                        <select id="pais" name="pais" class="form-control select2_cuentas" style="width: 100%" required>
                            @foreach($pais as $value)
                            <option  {{ $value->id == $cliente->pais ? 'selected' : ''}} value="{{$value->id}}" >{{$value->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <!--Comentario-->
                <div class="form-group col-xs-6{{ $errors->has('comentario') ? ' has-error' : '' }}">
                    <label for="comentario" class="col-md-3 texto">Comentario</label>
                    <div class="col-md-7">
                        <textarea class="form-control" rows="2" name="comentario" id="comentario">{{$cliente->comentarios}}</textarea>
                        @if ($errors->has('comentario'))
                        <span class="help-block">
                            <strong>{{ $errors->first('comentario') }}</strong>
                        </span>
                        @endif
                    </div>
                </div>
                <div class="form-group col-xs-6">
                    <label for="estado" class="col-md-4 texto">Estado</label>
                    <div class="col-md-7">
                        <label style="padding-left: 15px"> Activo &nbsp; <input type="radio" id="estado" name="estado" value="1" {{ $cliente->estado == '1' ? 'checked' : ''}} > </label>
                        <label style="padding-left: 15px"> Inactivo &nbsp; <input type="radio" id="estado" name="estado" value="0" {{ $cliente->estado == '0' ? 'checked' : ''}}> </label>
                    </div>
                </div>
                <div class="form-group col-xs-10 text-center">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit"  class="btn btn-default btn-gray btn_add">
                            <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

</section>
  
<script type="text/javascript">
    document.getElementById("tipo_identificacion").addEventListener("change", function () {
            const selectValue = this.value; // Obtiene el valor del select
            const input = document.getElementById("identificacion"); // Obtiene el input

            // Cambia el maxlength según el valor del select
            if (selectValue === "5" || selectValue === "8") {
                input.maxLength = 10;
            } else if (selectValue === "4") {
                input.maxLength = 13;
            } else if (selectValue === "6") {
                input.maxLength = 25;
            }
        });
    $(document).ready(function(){
        $('.select2_cuentas').select2({
            tags: false
          });

    });
    
    function validarRango(elemento){
        var numero = parseInt(elemento.value,10);
          //Validamos que se cumpla el rango
        if(numero<1 ){
            alert("La cantidad debe ser mayor o igual a 1");
            $('#minimo').val("1");
            return false;
        }
        return true;
    }


    function validarRango_uso(elemento){
        var numero = parseInt(elemento.value,10);
          //Validamos que se cumpla el rango
        if(numero<1 ){
            alert("La cantidad debe ser mayor o igual a 1");
            $('#uso').val("1");
            return false;
        }
        return true;
    }

</script>
     
@endsection
