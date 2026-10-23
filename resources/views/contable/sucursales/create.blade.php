@extends('contable.sucursales.base')
@section('action-content')

<style type="text/css">
  .separator {
    width: 100%;
    height: 30px;
    clear: both;
  }
</style>

<script type="text/javascript">
  $(document).ready(function() {
    $('.select2_color').select2({
      tags: true
    });
  });

  function guardar_ciudad(id) {
    var ciud = $('#ciud option:selected').text();
    $.ajax({
      type: 'post',
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      url: "{{url('sucursales/guardarCiudad')}}/" + ciud,
      data: {
        ciud: ciud
      },
      datatype: 'json',
      success: function(data) {
        /* console.log(data);
         $('#ciud').val(data);*/
        //$('#ciud').trigger('changer');
        //alert(data)
      },
      error: function(data) {
        //console.log(data);
        //alert(data)
      }
    });
  }
  //Valida que solo ingrese numeros
  function check(e) {
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
      <li class="breadcrumb-item"><a href="{{route('establecimiento.index')}}">Establecimiento</a></li>
      <li class="breadcrumb-item active" aria-current="page">Crear</li>
    </ol>
  </nav>
  <form id="enviar_establecimiento" class="form-vertical" role="form" method="POST" action="{{route('establecimiento.store')}}">
    {{ csrf_field() }}
    <div class="box">
      <div class="box-header color_cab">
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-9">
              <h5><b>CREAR ESTABLECIMIENTO</b></h5>
            </div>
            <div class="col-md-1 text-right">
              <button onclick="goBack()" class="btn btn-default btn-gray">
                <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
              </button>
            </div>
          </div>
        </div>
      </div>
      <div class="separator"></div>
      <div class="box-body dobra">
        <!--CODIGO ESTABLECIMIENTO-->
        <div class="form-group  col-xs-6">
          <label for="codigo" class="col-md-4 texto">Código:</label>
          <div class="col-md-7">
            <input id="codigo" name="codigo" type="text" class="form-control" placeholder="Código" maxlength="3" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" autocomplete="off" required autofocus>
          </div>
        </div>
        <!--NOMBRE ESTABLECIMIENTO-->
        <div class="form-group  col-xs-6">
          <label for="nombre" class="col-md-4 texto">Nombre:</label>
          <div class="col-md-7">
            <input id="nombre" name="nombre" type="text" class="form-control" placeholder="Nombre" autocomplete="off" required autofocus>
          </div>
        </div>
        <!--CIUDAD ESTABLECIMIENTO-->
        <div class="form-group  col-xs-6">
          <label for="id_ciudad" class="col-md-4 texto">Ciudad</label>
          <div class="col-md-7">
            <select id="ciud" name="ciud" class="form-control select2_color" width="90%" required onchange="guardar_ciudad();">
              <option value="">Seleccione</option>
              @foreach($ciudad as $value)
              <option value="{{$value->nombre}}">{{$value->nombre}}</option>
              @endforeach
            </select>

          </div>
        </div>
        <!--DIRECCION ESTABLECIMIENTO-->
        <div class="form-group col-md-6">
          <label for="direccion" class="col-md-4 texto">Dirección:</label>
          <div class="col-md-7">
            <input id="direccion" name="direccion" type="text" class="form-control" placeholder="Dirección" autocomplete="off" required autofocus>
          </div>
        </div>
        <!--EMAIL ESTABLECIMIENTO-->
        <div class="form-group col-md-6">
          <label for="email" class="col-md-4 texto">Email:</label>
          <div class="col-md-7">
            <input id="email" name="email" type="text" class="form-control" placeholder="Email" autocomplete="off" required>
          </div>
        </div>
        <!--TELEFONO  ESTABLECIMIENTO-->
        <div class="form-group col-md-6">
          <label for="telefono" class="col-md-4 texto">Teléfono:</label>
          <div class="col-md-7">
            <input id="telefono" name="telefono" type="text" class="form-control" placeholder="Telefono" autocomplete="off" required>
          </div>
        </div>
        <!--ESTADO  ESTABLECIMIENTO-->
        <div class="form-group col-md-6">
          <label for="estado" class="col-md-4 texto">Estado</label>
          <div class="col-md-7">
            <select id="estado" name="estado" class="form-control" required>
              <option>Seleccione...</option>
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
        </div>
        <div class="form-group col-md-6">
            <label for="estado" class="col-md-4 texto">{{trans('contableM.uso_nomina')}}</label>
            <div class="col-md-7">
                <input  id="uso_nomina" name="uso_nomina" type="checkbox" class="form-check-input">
            </div>
        </div>
        <!--EMPRESA-->
        <!--<div class="form-group col-xs-6">
            <label for="id_empresa" class="col-md-4 texto">Empresa</label>
            <div class="col-md-7">
              <select class="form-control " name="id_empresa" id="id_empresa" required>
                  <option value="">Seleccione...</option> 
                  @foreach($empresas as $value)
                      <option value="{{$value->id}}">{{$value->razonsocial}}</option>
                  @endforeach
              </select>
            </div>
          </div>-->
        <div class="form-group col-xs-10 text-center">
          <div class="col-md-6 col-md-offset-4">
            <button type="submit" class="btn btn-default btn-gray btn_add">
              <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</section>
@endsection