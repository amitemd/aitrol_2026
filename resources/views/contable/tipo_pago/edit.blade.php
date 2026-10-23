@extends('contable.tipo_ambiente.base')
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
        <li class="breadcrumb-item"><a href="../ambiente">Tipo Ambiente</a></li>
        <li class="breadcrumb-item active" aria-current="page">Actualizar</li>
      </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
              <h3 class="box-title">Detalle Tipo Ambiente</h3>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #D4D0C8;">
          <form  class="form-vertical" role="form" method="POST" action="{{route('tipo_ambiente.update')}}">
            {{ csrf_field() }}
            <input  name="id_tipo_ambiente" id="id_tipo_ambiente" type="text" class="hidden" value="@if(!is_null($tip_ambiente)){{$tip_ambiente->id}}@endif">
            <div class="separator"></div>
            <div class="box-body col-xs-24">
               <!--TIPO AMBIENTE-->
              <div class="form-group  col-xs-6">
                <label for="tipo_ambiente" class="col-md-4 texto">Tipo Ambiente:</label>
                <div class="col-md-7">
                  <input id="tipo_ambiente" name="tipo_ambiente" type="text" class="form-control" value="@if(!is_null($tip_ambiente)){{$tip_ambiente->tipo_ambiente}}@endif" required autofocus>
                </div>
              </div>
              <!--CODIGO TIPO AMBIENTE-->
              <div class="form-group  col-xs-6">
                <label for="cod_tip_ambiente" class="col-md-4 texto">Código:</label>
                <div class="col-md-7">
                   <input id="cod_tip_ambiente" name="cod_tip_ambiente" type="text" maxlength="1" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" value="@if(!is_null($tip_ambiente)){{$tip_ambiente->codigo}}@endif"  required autofocus>
                </div>
              </div>
              <!--ESTADO TIPO AMBIENTE-->
              <div class="form-group col-xs-6">
                  <label for="estado_tip_ambiente" class="col-md-4 texto">Estado</label>
                  <div class="col-md-7">
                    <select id="estado_tip_ambiente" name="estado_tip_ambiente" class="form-control" required>
                      <option {{ $tip_ambiente->estado == 1 ? 'selected' : ''}} value="1">Activo</option>
                      <option {{ $tip_ambiente->estado == 0 ? 'selected' : ''}} value="0">Inactivo</option>
                    </select>
                  </div>
              </div>
              <div class="form-group col-xs-10 text-center">
                <div class="col-md-6 col-md-offset-4">
                    <button type="submit" id="btn_add" class="btn btn-default btn-gray btn_add">
                      <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar
                    </button>
                </div>
              </div>
            </div>
          </form>
        </div>
    </div>
  </section>
     
@endsection
