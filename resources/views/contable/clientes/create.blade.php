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
            <li class="breadcrumb-item active" aria-current="page">Crear</li>
        </ol>
        </nav>
        <form id="enviar_nuevo_cliente" class="form-vertical" role="form" method="POST" action="{{route('clientes.store')}}">
            {{ csrf_field() }}
            <div class="box">
                <div class="box-header color_cab">
                    <div class="col-md-9">
                      <h5><b>CREAR NUEVO CLIENTE</b></h5>
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
                            <select id="tipo_identificacion" name="tipo_identificacion" onchange="borrar()" class="form-control" required autofocus>
                                <option  value="">Seleccione.....</option>
                                <option  value="4">RUC</option>
                                <option  value="5">CEDULA</option>
                                <option  value="6">PASAPORTE</option>
                                <option  value="8">CEDULA/RUC EXTRANJERO</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input id="identificacion" type="text" class="form-control" name="identificacion" value="{{ old('identificacion') }}" onchange="return tipo_id(this);" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autocomplete="off" autofocus>
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
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autocomplete="off" autofocus>
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
                            <select id="clase" name="clase" class="form-control" >
                                <option  value="normal">Normal</option>
                                <option  value="1">Precio 1</option>
                                <option  value="2">Precio 2</option>
                                <option  value="3">Precio 3</option>
                                <option value="4">Precio 4</option>

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
                        <input id="nombre_representante" name="nombre_representante" type="text" class="form-control" placeholder="Nombre Representante" value="{{ old('nombre_representante') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                        </div>
                    </div>
                    <!--Cedula Representante-->
                    <div class="form-group col-xs-6{{ $errors->has('cedula_representante') ? ' has-error' : '' }}">
                        <label for="cedula_representante" class="col-md-3 texto">Cedula</label>
                        <div class="col-md-7">
                            <input id="cedula_representante" type="text" maxlength="13" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" class="form-control" name="cedula_representante" placeholder="Cedula Representante" value="{{ old('cedula_representante') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autocomplete="off" autofocus>
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
                            <input id="ciudad_representante" type="text" class="form-control" name="ciudad_representante" placeholder="Ciudad Representante"  value="{{ old('ciudad_representante') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autocomplete="off" autofocus required>
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
                            <input id="direccion_representante" type="text" class="form-control" name="direccion_representante" placeholder="Direccion Representante"   value="{{ old('direccion_representante') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
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
                            <input id="telefono1" maxlength="10" type="text" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" class="form-control" name="telefono1" value="{{ old('telefono1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
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
                            <input id="telefono2" type="text" class="form-control" maxlength="10" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="telefono2" value="{{ old('telefono2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"  autocomplete="off" autofocus>
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
                            <input id="correo" type="email" class="form-control" name="correo" value="{{ old('correo') }}"  autocomplete="off" autofocus>
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
                            <select id="pais" name="pais" class="form-control select2_cuentas" style="width: 100%">
                                @php $i=0; @endphp
                                @foreach($pais as $value)
                                    <option @if($i == '0') selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                                    @php $i++; @endphp
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <!--NO RECUERDO 1
                    <div class="form-group col-xs-6{{ $errors->has('direccion_representante') ? ' has-error' : '' }}">
                        <label for="direccion_representante" class="col-md-3 texto">No recuerdo 1</label>
                        <div class="col-md-7">
                            <input id="direccion_representante" type="text" class="form-control" name="direccion_representante" placeholder="No recuerdo 1"   value="{{ old('direccion_representante') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                            @if ($errors->has('direccion_representante'))
                            <span class="help-block">
                                <strong>{{ $errors->first('direccion_representante') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    NO RECUERDO 2
                    <div class="form-group col-xs-6{{ $errors->has('direccion_representante') ? ' has-error' : '' }}">
                        <label for="direccion_representante" class="col-md-3 texto">No recuerdo 2</label>
                        <div class="col-md-7">
                            <input id="direccion_representante" type="text" class="form-control" name="direccion_representante" placeholder="No recuerdo 2"   value="{{ old('direccion_representante') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autocomplete="off" required autofocus>
                            @if ($errors->has('direccion_representante'))
                            <span class="help-block">
                                <strong>{{ $errors->first('direccion_representante') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    Aqui termino-->
                    <!--Comentario-->
                    <div class="form-group col-xs-6{{ $errors->has('comentario') ? ' has-error' : '' }}">
                        <label for="comentario" class="col-md-3 texto">Comentario</label>
                        <div class="col-md-7">
                            <textarea class="form-control" rows="2" name="comentario" id="comentario"></textarea>
                            @if ($errors->has('comentario'))
                            <span class="help-block">
                                <strong>{{ $errors->first('comentario') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-10 text-center">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit"  class="btn btn-default btn-gray btn_add">
                                <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Agregar
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
    
    function tipo_id(elemento){
        var tipo =  $('#tipo_identificacion').val();
        var numero = parseInt(elemento.value,10);
        if(tipo=='4'){
            if(numero<111111111111 || numero>9999999999999){
                alert("Número de RUC Incorrecto");
                $('#identificacion').val('');
            }
        }
        if(tipo=='5'){
            if(numero<111111111 || numero>9999999999){
                alert("Número de cedula Incorrecto");
                $('#identificacion').val('');
            }
        }
    }

    function borrar (){
        $('#identificacion').val('');
    }

</script>
     
@endsection
