@extends('contable.caja_banco.base')
@section('action-content')

<style type="text/css">
  .separator{
    width:100%;
    height:30px;
    clear: both;
  }
</style>


  <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="../banco">Caja Banco</a></li>
        <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
      </ol>
    </nav>
    <form class="form-vertical" role="form" method="POST" action="{{route('caja_banco.update')}}">
      {{ csrf_field() }}
      <input  name="id_caja_banco" id="id_caja_banco" type="text" class="hidden" value="@if(!is_null($caja_banco)){{$caja_banco->id}}@endif">
      <input type="text" name="id_empresa" id="id_empresa" class="hidden" value="{{$empresa->id}}">
      <div class="box">
        <div class="box-header color_cab">
            <div class="col-md-9">
              <h5><b>DETALLE CAJA BANCO</b></h5>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="separator"></div>
        <div class="box-body dobra">
              <!--CODIGO CAJA BANCO-->
              <div class="form-group  col-xs-6">
                <label for="cod_caja_banco" class="col-md-4 texto">Código:</label>
                <div class="col-md-7">
                  <input id="cod_caja_banco" name="cod_caja_banco" type="text" class="form-control" placeholder="Código" value="@if(!is_null($caja_banco)){{$caja_banco->codigo}}@endif" autocomplete="off" required autofocus>
                </div>
              </div>
              <!--NOMBRE CAJA BANCO-->
              <div class="form-group  col-xs-6">
                  <label for="nombre_caja_banco" class="col-md-4 texto">Nombre:</label>
                  <div class="col-md-7">
                      <input id="nombre_caja_banco" name="nombre_caja_banco" type="text" class="form-control" placeholder="Nombre" value="@if(!is_null($caja_banco)){{$caja_banco->nombre}}@endif" autocomplete="off" required autofocus>
                  </div>
              </div>
              <!--NUMERO DE CUENTA CAJA BANCO-->
              <div class="form-group  col-xs-6">
                <label for="numero_cuenta" class="col-md-4 texto">Número de Cuenta:</label>
                <div class="col-md-7">
                  <input id="numero_cuenta" name="numero_cuenta" type="number" class="form-control" placeholder="Número de cuenta" value="@if(!is_null($caja_banco)){{$caja_banco->numero_cuenta}}@endif" autocomplete="off">
                </div>
              </div>
              <!--CLASE CAJA BANCO-->
              <div class="form-group col-md-6">
                  <label for="clase" class="col-md-4 texto">Clase:</label>
                  <div class="col-md-7">
                    <select id="clase" name="clase" class="form-control" required>
                      <option>Seleccione...</option>
                      <option {{ $caja_banco->clase == 1 ? 'selected' : ''}} value="1">Cuenta Bancaria</option>
                      <option {{ $caja_banco->clase == 2 ? 'selected' : ''}} value="2">Cuenta de Caja</option>
                    </select>
                  </div>
              </div>
              <!--GRUPO CAJA BANCO-->
              <div class="form-group  col-xs-6">
                <label for="grupo_plan_cuenta" class="col-md-4 texto">Grupo:</label>
                <div class="col-md-7">
                  <select id="grupo_plan_cuenta" name="grupo_plan_cuenta"  class="form-control" onchange="obtener_detalle_grupo()" required>
                      <option value="">Seleccione...</option>
                      @foreach($plan_cuenta as $value)
                        <option   {{ $caja_banco->grupo == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                  </select>
                </div>
              </div>
              <!--CUENTA MAYOR CAJA Y BANCO -->
              <div class="form-group  col-xs-6">
                <label for="detalle_grupo" class="col-md-4 texto">Cuenta Mayor:</label>
                <div class="col-md-7">
                  <select id="detalle_grupo" name="detalle_grupo"  class="form-control" required>
                      <option value="">Seleccione...</option>
                      @foreach($cuentas_padres as $value_2)
                      <option {{ $caja_banco->cuenta_mayor == $value_2->id  ? 'selected' : ''}} value="{{$value_2->id}}">{{$value_2->nombre}}</option>
                      @endforeach
                  </select>
                </div>
              </div>
              <!--SUCURSAL CAJA Y BANCO-->
              <div class="form-group  col-xs-6">
                <label for="nomb_sucursal" class="col-md-4 texto">Sucursal:</label>
                <div class="col-md-7">
                  <select id="nomb_sucursal" name="nomb_sucursal"  class="form-control" required>
                      <option value="">Seleccione...</option>
                      @foreach($sucursales as $value)
                        <option {{ $caja_banco->sucursal == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre_sucursal}}</option>
                      @endforeach
                  </select>
                </div>
              </div>
              <!--DIVISA CAJA Y BANCO-->
              <div class="form-group  col-xs-6">
                <label for="divisa" class="col-md-4 texto">Divisa:</label>
                <div class="col-md-7">
                  <select id="divisa" name="divisa" class="form-control" required>
                      <option value="">Seleccione...</option>
                      @foreach($divisas as $value)
                        <option {{ $caja_banco->divisa == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->descripcion}}</option>
                      @endforeach
                  </select>
                </div>
              </div>
              <!--TIPO PAGO CAJA Y BANCO-->
              <div class="form-group  col-xs-6">
                <label for="forma_pago" class="col-md-4 texto">Forma de Pago:</label>
                <div class="col-md-7">
                  <select id="forma_pago" name="forma_pago"  class="form-control" required>
                      <option value="">Seleccione...</option>
                      @foreach($form_pago as $value)
                        <option {{ $caja_banco->formas_pago == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombre}}</option>
                      @endforeach
                  </select>
                </div>
              </div>
              <!--ULTIMO CHEQUE CAJA BANCO-->
              <div class="form-group  col-xs-6">
                <label for="ultimo_cheque" class="col-md-4 texto">Ultimo Cheque:</label>
                <div class="col-md-7">
                  <input id="ultimo_cheque" name="ultimo_cheque" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  value="@if(!is_null($caja_banco)){{$caja_banco->ultimo_cheque}}@endif"  autocomplete="off" placeholder="Ultimo Cheque">
                </div>
              </div>
              <!--COMENTARIOS CAJA BANCO-->
              <div class="form-group  col-xs-6">
                  <label for="comentario" class="col-md-4 texto">Comentarios:</label>
                  <div class="col-md-7">
                    <textarea id="comentario" name="comentario" rows="3" cols="50">{{$caja_banco->comentarios}}</textarea>
                  </div>
              </div>
              <!--ESTADO CAJA BANCO-->
              <div class="form-group col-md-6">
                  <label for="estado_caj_banco" class="col-md-4 texto">Estado</label>
                  <div class="col-md-7">
                    <select id="estado_caj_banco" name="estado_caj_banco" class="form-control" required>
                      <option {{ $caja_banco->estado == 1 ? 'selected' : ''}} value="1">Activo</option>
                      <option {{ $caja_banco->estado == 0 ? 'selected' : ''}} value="0">Inactivo</option>
                    </select>
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
   $( document ).ready(function() {
    //obtener_detalle_grupo();
    });
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

    function obtener_detalle_grupo(){

      var valor= $("#grupo_plan_cuenta").val();

      $.ajax({
        type: 'post',
        url:"{{route('caja_banco.detallegrupo')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: {'opcion': valor},
        success: function(data){

          if(data.value!='no'){

            if(valor!=0){
              console.log(data);
              $("#detalle_grupo").empty();
              $.each(data,function(key, registro) {
                $("#detalle_grupo").append('<option value='+registro.id+'>'+registro.nombre+'</option>');
              });

            }else{
                $("#detalle_grupo").empty();
            }

          }
        },
        error: function(data){
          console.log(data);
        }

      })

    }

  </script>

@endsection
