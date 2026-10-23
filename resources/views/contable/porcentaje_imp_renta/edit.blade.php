@extends('contable.porcentaje_imp_renta.base')
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
        <li class="breadcrumb-item"><a href="{{route('porcentaje_imp_renta.index')}}">Porcentaje IR</a></li>
        <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
      </ol>
    </nav>
    <form  class="form-vertical" role="form" method="POST" action="{{route('porcentaje_imp_renta.update')}}">
      {{ csrf_field() }}
      <input  name="id_porcentaje_ir" id="id_porcentaje_ir" type="text" class="hidden" value="@if(!is_null($porcentaje_ir)){{$porcentaje_ir->id}}@endif">
      <div class="box">
        <div class="box-header color_cab">
          <div class="col-md-9">
            <h5><b>DETALLE PORCENTAJE IR</b></h5>
          </div>
          <div class="col-md-1 text-right">
              <button onclick="goBack()" class="btn btn-default btn-gray" >
                  <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
              </button>
          </div>     
        </div>
        <div class="separator"></div>
        <div class="box-body dobra">
            <!--Porcentaje IR-->
            <div class="form-group  col-xs-6">
              <label for="porcentaje" class="col-md-4 texto">Porcentaje IR:</label>
              <div class="col-md-7">
                <input id="porcentaje" name="porcentaje" type="text" class="form-control" value="@if(!is_null($porcentaje_ir)){{$porcentaje_ir->porcentaje}}@endif" autocomplete="off" required autofocus>
              </div>
            </div>
            <!--CODIGO-->
            <div class="form-group  col-xs-6">
                <label for="anio_porcentaje_ir" class="col-md-4 texto">Año:</label>
                <div class="col-md-7">
                  <input id="anio_porcentaje_ir" name="anio_porcentaje_ir" type="text" maxlength="4" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="@if(!is_null($porcentaje_ir)){{$porcentaje_ir->anio}}@endif" autocomplete="off" required autofocus>
                </div>
            </div>
            <!--ESTADO Porcentaje IR-->
            <div class="form-group col-md-6">
                <label for="estado_porcentaje_ir" class="col-md-4 texto">Estado</label>
                <div class="col-md-7">
                  <select id="estado_porcentaje_ir" name="estado_porcentaje_ir" class="form-control" required>
                    <option {{ $porcentaje_ir->estado == 1 ? 'selected' : ''}} value="1">Activo</option>
                    <option {{ $porcentaje_ir->estado == 0 ? 'selected' : ''}} value="0">Inactivo</option>
                  </select>
                </div>
            </div>
            <!--BUTTON AGREGAR Porcentaje IR-->
            <div class="form-group col-xs-10 text-center">
              <div class="col-md-6 col-md-offset-4">
                  <button type="submit" id="btn_add" class="btn btn-default btn-gray btn_add">
                    <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar
                  </button>
              </div>
            </div>
        </div>
      </div>
    </form>
        
   
  </section>
     
@endsection
