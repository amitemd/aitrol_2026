@extends('activosfijos.mantenimientos.activofijo.base')
@section('action-content')
<style type="text/css">
  .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity : 1;
    }
    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 12px;
    }

    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
</style>
<section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Activos Fijos</a></li>
      <li class="breadcrumb-item"><a href="#">Informe</a></li>
      <li class="breadcrumb-item active">Listado Activo Fijo</li>
    </ol>
  </nav>
  <div class="box">
  	<div class="box-header header_new">
        <div class="col-md-9">
          <h3 class="box-title">Informe Listado de Activos</h3>
        </div>
        
    </div>

    <div class="row head-title">
      <div class="col-md-12 cabecera">
          <label class="color_texto" for="title">BUSCADOR DE ACTIVOS FIJOS</label>
      </div>
    </div>

    <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('activofjo.excel_listado_general') }}" >
        {{ csrf_field() }}
          
          <div class="form-group col-md-1">
            <label class="texto" for="buscar_nombre">Tipo: </label>
          </div>
          <div class="form-group col-md-2">
            <select id="id_tipo" name="id_tipo" class="form-control">
              <option value="">Seleccione...</option>
              @foreach($tipos as $value)
                <option value="{{$value->id}}">{{$value->nombre}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group col-md-1 ">
            <label class="texto" for="desde">Desde: </label>
          </div>
          <div class="form-group col-md-2">
            <input type="date" name="desde" id="desde" class="form-control" value="{{$desde}}">
          </div>
          
          <div class="form-group col-md-1 ">
            <label class="texto" for="hasta">Hasta: </label>
          </div>
          <div class="col-md-2">
            <input type="date" name="hasta" id="hasta" class="form-control" value="{{$hasta}}">
          </div>

          <div class="col-md-1">
            <button type="submit" id="excel_listado" class="btn btn-xs btn-primary"> Listado Tipo </button>
          </div>

          <div class="form-group col-md-2">
            <div class="btn-group">
              <button type="submit" id="listado_tipo" name="listado_tipo"  class="btn btn-primary oculto" formaction="{{route('activofjo.index_listado_tipo')}}"> Listado General</button>
              <button type="submit" id="pdflistado" name="pdflistado" class="btn btn-primary oculto" formaction="{{route('activofjo.pdf_listado_general')}}">Pdf Tipo</button>
              <button type="submit" id="pdflistado_tipo" name="pdflistado_tipo" class="btn btn-primary oculto" formaction="{{route('activofjo.pdf_listado_tipo')}}"> Pdf General </button>

              <button type="button" class="btn btn-info">Reportes</button>
              <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-expanded="false" style="height: 34px;">
                  <span class="caret"></span>
                  <span class="sr-only">Toggle Dropdown</span>
              </button>
                <ul class="dropdown-menu" role="menu">
                  <li>
                    <a class="btn" onclick="pdf_general();" style="border-bottom: 1px solid #000;"><span>Pdf Tipo</span></a>
                  </li>
                  <li>
                    <a class="btn" onclick="listado_tipo();" style="border-bottom: 1px solid #000;"><span>Listado General</span></a>
                  </li>
                  <li>
                    <a class="btn" onclick="pdf_tipo();" style="border-bottom: 1px solid #000;"><span>Pdf General</span></a>
                  </li>
                </ul>
            </div>
          </div>

        </form>
      </div>

  </div>
</section>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script type="text/javascript">
  function pdf_general(){
    $('#pdflistado').click();   
  }

  function listado_tipo() {
    $('#listado_tipo').click();  
  }

  function pdf_tipo(){
    $('#pdflistado_tipo').click();
  }
</script>
@endsection