@extends('contable.divisas.base')
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
        <li class="breadcrumb-item"><a href="{{route('divisas.index')}}">Divisas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Crear</li>
      </ol>
    </nav>
    <form  class="form-vertical" role="form" method="POST" action="{{route('divisas.store')}}">
      {{ csrf_field() }}
      <div class="box">
        <div class="box-header color_cab">
          <div class="col-md-9">
            <h5><b>CREAR DIVISA</b></h5>
          </div>
          <div class="col-md-1 text-right">
              <button onclick="goBack()" class="btn btn-primary btn-gray" >
                  <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
              </button>
          </div>
        </div>
        <div class="separator"></div>
        <div class="box-body dobra">
          <div class="form-group  col-xs-6">
            <label for="divisas_nombre" class="col-md-4 texto">Nombre:</label>
            <div class="col-md-7">
              <input id="divisas_nombre" name="divisas_nombre" type="text" class="form-control" placeholder="Nombre...." autocomplete="off" required autofocus>
            </div>
          </div>
          <div class="form-group col-xs-6">
              <label for="estado_divisas" class="col-md-4 texto">Estado</label>
              <div class="col-md-7">
                <select id="estado_divisas" name="estado_divisas" class="form-control" required>
                  <option>Seleccione...</option>
                  <option value="1">Activo</option>
                  <option value="0">Inactivo</option>
                </select>
              </div>
          </div>
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
