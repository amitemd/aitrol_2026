@extends('contable.tipo_tarjeta.base')
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
        <li class="breadcrumb-item"><a href="../ambiente">Tipo Tarjeta</a></li>
        <li class="breadcrumb-item active" aria-current="page">Crear</li>
      </ol>
    </nav>
    <form id="enviar_tipo_ambiente" class="form-vertical" role="form" method="POST" action="{{route('tipo_tarjeta.store')}}">
      {{ csrf_field() }}
      <div class="box"> 
        <div class="box-header color_cab">
            <div class="col-md-9">
              <h5><b>CREAR TIPO TARJETA</b></h5>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-default btn-gray" >
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div> 
        <div class="separator"></div>
        <div class="box-body dobra">
            <!--TIPO TARJETA-->
            <div class="form-group  col-xs-6">
              <label for="tipo_ambiente" class="col-md-4 texto">Tipo Tarjeta:</label>
              <div class="col-md-7">
                <input id="nombre_tarjeta" name="nombre_tarjeta" type="text" class="form-control" placeholder="Tipo Tarjeta" required autofocus>
              </div>
            </div>
            <!--ESTADO TIPO TARJETA-->
            <div class="form-group col-xs-6">
                <label for="estado_tip_ambiente" class="col-md-4 texto">Estado</label>
                <div class="col-md-7">
                  <select id="estado_tip_tarjeta" name="estado_tip_tarjeta" class="form-control" required>
                    <option>Seleccione...</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                  </select>
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
@endsection
