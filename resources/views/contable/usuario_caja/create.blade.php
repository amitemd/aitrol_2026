@extends('contable.caja.base')
@section('action-content')
    
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
    <div class="box " style="background-color: white;">
      <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
          <div class="col-md-9">
            <h3 class="box-title">Crear Punto Emision</h3>
          </div>
          <div class="col-md-3" style="text-align: right;">
              <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                 <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
              </button>
          </div>
      </div>
      <div class="box-body" style="background-color: #ffffff;">
        <form id="enviar_punto_emision"  class="form-vertical" role="form" method="post" action="{{route('puntoemision.store')}}">
          {{ csrf_field() }}
          <!--CODIGO PUNTO EMISION-->
          <div class="form-group  col-xs-6">
            <label for="codigo_punto" class="col-md-2 control-label">Código:</label>
            <div class="col-md-8">
              <input id="codigo_punto" name="codigo_punto" type="text" class="form-control" placeholder="Código" maxlength="3" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" required autofocus>
            </div>
          </div>
          <!--NOMBRE PUNTO EMISION-->
          <div class="form-group  col-xs-6">
              <label for="nombre_punto" class="col-md-2 control-label">Nombre:</label>
              <div class="col-md-8">
                 <input id="nombre_punto" name="nombre_punto" type="text" class="form-control" placeholder="Nombre" required autofocus>
              </div>
          </div>
          <!--ESTADO PUNTO DE EMISION-->
          <div class="form-group col-md-6">
              <label for="estado_punto" class="col-md-2 control-label">Estado</label>
              <div class="col-md-8">
                <select id="estado_punto" name="estado_punto" class="form-control" required>
                  <option>Seleccione...</option>
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>
          </div>
          <!--NOMBRE SUCURSAL A QUE SE LE VA ASIGNAR EL PUNTO DE EMISION-->
          <div class="form-group  col-xs-6">
            <label for="id_sucursal" class="col-md-2 control-label">Sucursal</label>
            <div class="col-md-8">
              <select id="id_sucursal" name="id_sucursal" class="form-control" required>
                  <option value="">Seleccione...</option> 
                  @foreach($sucursales as $value)
                     <option value="{{$value->id}}">{{$value->nombre_sucursal}}</option>
                  @endforeach
              </select>
            </div>
          </div>
          <div class="form-group col-xs-10" style="text-align: center;">
            <div class="col-md-6 col-md-offset-4">
                <button type="submit"  class="btn btn-primary">
                   <span class="glyphicon glyphicon-floppy-disk"></span>Agregar
                </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
     

@endsection
