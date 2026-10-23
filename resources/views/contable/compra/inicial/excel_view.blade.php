@extends('contable.compras_pedidos.base')
@section('action-content')
<style type="text/css">
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 460px;
        _width: 460px !important;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
    .archivos{
      display: flex;
      justify-content:center;
    }
    .archivos input{
      font-size: 14px;
      font-weight: 600;
      color: #fff;
      background-color: #aaa;
      display: inline-block;
      transition: all .5s;
      cursor: pointer;
      padding: 15px 40px !important;
      text-transform: uppercase;
      width: 31%;
      text-align: center;
    }
    tr, th{
      text-align: center;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Compra - Interna</a></li>
            <li class="breadcrumb-item"><a href=""></a>Crear Producto Saldo Inicial</li>
            <li class="breadcrumb-item active" aria-current="page">Nueva Saldo Inicial</li>
        </ol>
    </nav>

    <div>
      
    <form id="formulario" class="form-vertical" enctype="multipart/form-data" action="{{route('contable.excelProdInicialCreate')}}" method="POST">
      {{ csrf_field() }}
      <div class="box">
        <div class="box-header color_cab">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-6">
                <h5><b>Saldo Iniciales de Productos</b></h5>
              </div>
              <div class="col-md-3 text-right">
                  <a type="button" href="{{route('contable.excelProdInicialdescargar')}}" target="_blank"  class="btn btn-default btn-gray"  >
                        <i class="fa fa-download" aria-hidden="true"></i>&nbsp;&nbsp;Descargar Plantilla
                  </a>
              </div>
              <div class="col-md-1 text-right">
                  <a href="{{route('contable.compraspedido.indexInicial')}}" class="btn btn-default btn-gray" >
                      <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                  </a>
              </div>
             
            </div> 
          </div>          
        </div>
        <div class="separator"></div>
        <div class="box-body dobra" >
           <div class="col-md-12 archivos" style="text-align: center;">
              <input id="excel" name="excel" type="file" >
           </div>
          <center>
          <table style="width:20%" class="table display compact cell-border responsive nowrap">
            <thead>
              <tr>
                <th colspan="2">BODEGAS</th>
              </tr>
              <tr>
                    <th style="width:10%;" scope="col">ID</th>
                    <th style="width:10%;" scope="col">NOMBRE</th>
              </tr>
            </thead>
            <tbody>
              @foreach($bodegas as $value)
                <tr>
                  <td>{{$value->id}}</td>
                  <td>{{$value->nombre}}</td>
                </tr>
              @endforeach
            </tbody>

           </table>

          </center>
         
             
          <div class="form-group col-xs-10 text-center">
              <div class="col-md-6 col-md-offset-4">
                  <button style="margin-top:20px" type="submit" id="btn_add" class="btn btn-default btn-gray">
                    <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Guardar
                  </button>
              </div>
          </div>

        </div>  
      </div>
    </form>
    </div>
</section>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script>
      $('.select2_cuentas').select2({
          tags: false
      });

      $('.select2_bodega').select2({
          tags: false
      });
      
    $(function () {

      $('#fecha').datetimepicker({
          format: 'YYYY'
      });

    });
   
</script>
@endsection