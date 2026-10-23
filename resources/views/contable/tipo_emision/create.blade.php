@extends('contable.tipo_emision.base')
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
        <li class="breadcrumb-item"><a href="{{route('tipo_emision.index')}}">Tipo Emisión</a></li>
        <li class="breadcrumb-item active" aria-current="page">Crear</li>
      </ol>
    </nav>
    <form id="enviar_tipo_emision" class="form-vertical" role="form" method="POST" action="{{route('tipo_emision.store')}}">
      {{ csrf_field() }}
      <div class="box">
        <div class="box-header color_cab">
          <div class="col-md-9">
            <h5><b>CREAR TIPO EMISIÓN</b></h5>
          </div>
          <div class="col-md-1 text-right">
              <button onclick="goBack()" class="btn btn-primary btn-gray" >
                  <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
              </button>
          </div>
        </div>
        <div class="separator"></div>
        <div class="box-body dobra">
            <!--TIPO EMISION-->
            <div class="form-group  col-xs-6">
              <label for="tipo_emision" class="col-md-4 texto">Tipo Emisión:</label>
              <div class="col-md-7">
                <input id="tipo_emision" name="tipo_emision" type="text" class="form-control" placeholder="Tipo Emision" autocomplete="off" required autofocus>
              </div>
            </div>
            <!--CODIGO-->
            <div class="form-group  col-xs-6">
                <label for="cod_tip_emision" class="col-md-4 texto">Código:</label>
                <div class="col-md-7">
                  <input id="cod_tip_emision" name="cod_tip_emision" type="text"  class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" placeholder="Código" autocomplete="off" required autofocus>
                </div>
            </div>
            <!--ESTADO TIPO EMISION-->
            <div class="form-group col-md-6">
                <label for="estado_tip_emision" class="col-md-4 texto">Estado</label>
                <div class="col-md-7">
                  <select id="estado_tip_emision" name="estado_tip_emision" class="form-control" required>
                    <option>Seleccione...</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
                </div>
            </div>
            <!--BUTTON AGREGAR TIPO EMISION-->
            <div class="form-group col-xs-10 text-center">
              <div class="col-md-6 col-md-offset-4">
                  <button type="submit"  class="btn btn-success btn-gray btn_add">
                    <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Agregar
                  </button>
              </div>
            </div>
        </div>
      </div>
    </form>
  </section>
     
@endsection
