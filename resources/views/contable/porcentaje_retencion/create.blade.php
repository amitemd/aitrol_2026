@extends('contable.porcentaje_retencion.base')
@section('action-content')
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Contable</a></li>
      <li class="breadcrumb-item"><a href="{{route('retenciones.index')}}">Retenciones</a></li>
      <li class="breadcrumb-item active" aria-current="page">Crear</li>
    </ol>
  </nav>
  <form id="enviar_tipo_ambiente" class="form-vertical" role="form" method="POST" action="{{ route('porcentaje_retencion.store') }}">
    {{ csrf_field() }}
    <div class="box">
      <div class="box-header color_cab">
        <div class="col-md-9">
          <h5><b>CREAR PORCENTAJE RETENCIONES</b></h5>
        </div>
        <div class="col-md-1 text-right">
          <a href="{{route('retenciones.index')}}" class="btn btn-default btn-gray">
            <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
          </a>
        </div>
      </div>
      <div class="separator"></div>
      <div class="box-body dobra">
        <div class="form-group  col-xs-6">
          <label for="codigo" class="col-md-4 texto">Código:</label>
          <div class="col-md-7">
            <input id="codigo" name="codigo" type="text" class="form-control" placeholder="Codigo" autocomplete="off" required autofocus>
          </div>
        </div>
        <div class="form-group  col-xs-6">
          <label for="codigo" class="col-md-4 texto">Código Interno:</label>
          <div class="col-md-7">
            <input id="codigo" name="codigo_interno" type="text" class="form-control" placeholder="Codigo" autocomplete="off" required autofocus>
          </div>
        </div>
        <div class="form-group  col-xs-6">
          <label for="nombre" class="col-md-4 texto">Nombre:</label>
          <div class="col-md-7">
            <input id="nombre" name="nombre" type="text" class="form-control" placeholder="Nombre" autocomplete="off" required autofocus>
          </div>
        </div>
        <div class="form-group col-xs-6">
          <label for="tipo" class="col-md-4 texto">Tipo:</label>
          <div class="col-md-7">
            <select id="tipo" name="tipo" class="form-control" required>
              <option>Seleccionar</option>
              <option value="1">Iva</option>
              <option value="2">Fuente</option>
            </select>
          </div>
        </div>
        <div class="form-group  col-xs-6">
          <label for="valor" class="col-md-4 texto">Valor:</label>
          <div class="col-md-7">
            <input id="valor" name="valor" type="text" class="form-control" placeholder="valor" autocomplete="off" required autofocus>
          </div>
        </div>
        <div class="form-group  col-xs-6">
          <label for="cuenta_clientes" class="col-md-4 texto">Cuenta Cliente:</label>
          <div class="col-md-7">
            <select class="form-control select2_find_cta_retencion" name="cuenta_clientes" id="cuenta_clientes" style="width: 100%;" required>
              
            </select>
          </div>
        </div>
        <div class="form-group  col-xs-6">
          <label for="cuenta_acreedores" class="col-md-4 texto">Cuenta Acreedores:</label>
          <div class="col-md-7">
            <select class="form-control select2_find_cta_retencion" name="cuenta_acreedores" id="cuenta_acreedores" style="width: 100%;" required>
              
            </select>
          </div>
        </div>
        <div class="form-group col-xs-6">
          <label for="tipo" class="col-md-4 texto">Estado:</label>
          <div class="col-md-7">
            <select id="estados" name="estados" class="form-control" required>
              <option>Selccionar...</option>
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
          </div>
        </div>
        <div class="form-group  col-xs-6">
          <label for="nota" class="col-md-4 texto">Nota:</label>
          <div class="col-md-7">
            <textarea id="nota" name="nota" type="text" class="form-control" required autofocus></textarea>
          </div>
        </div>
        <div class="form-group  col-xs-6">
          <label for="cuenta_deudora" class="col-md-4 texto">Cuenta Deudora:</label>
          <div class="col-md-7">
            <select class="form-control select2_find_cta_retencion" name="cuenta_deudora" id="cuenta_deudora" style="width: 100%;" required>
              
            </select>
          </div>
        </div>
        <div class="form-group col-xs-10 text-center">
          <div class="col-md-6 col-md-offset-4">
            <button type="submit" class="btn btn-default btn-gray btn_add">
              <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Agregar
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>

  </div>
</section>
<script type="text/javascript"> 


  $('.select2_find_cta_retencion').select2({
      placeholder: "Seleccione la cuenta",
       allowClear: true,
      ajax: {
          url: '{{route("porcentajeretencion.find_cta_retencion")}}',
          data: function (params) {
          var query = {
              search: params.term,
              type: 'public'
          }
          return query;
          },
          processResults: function (data) {
              // Transforms the top-level key of the response object from 'items' to 'results'
              console.log(data);
              return {
                  results: data
              };
          }
      }
  });
</script>
@endsection
