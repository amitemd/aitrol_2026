@extends('activosfijos.documentos.depreciacion.base')
@section('action-content')
<style>
  div.dataTables_wrapper {
        width: 95%;
        margin: 0 auto;
  }
</style>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}"> 
<!-- Ventana modal editar -->
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
  <!-- Main content -->

  <section class="content">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Activos Fijos</a></li>
        <li class="breadcrumb-item"><a href="#">Informes</a></li>
        <li class="breadcrumb-item"><a href="#">Saldos de Activo Fijo</a></li>
    </ol>
  </nav>
  <div class="box">
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">BUSCADOR</label>
        </div>
      </div>
      <!-- /.box-header -->
      <div class="box-body dobra">
        <form method="POST" id="reporte_master" action="{{ route('activosfijos.informe.buscar') }}" >
        {{ csrf_field() }}
 
          <div class="form-group col-md-5 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="texto col-md-3 control-label">Fecha:</label>
            <div class="col-md-9">
              <div class="input-group date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control input-sm" name="fecha" id="fecha" value="{{$fecha}}"  autocomplete="off">
                <div class="input-group-addon">
                  <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                </div>
              </div>
            </div>
          </div> 

          {{-- <div class="form-group col-md-5 col-xs-4" style="padding-left: 0px;padding-right: 0px;">
            <label for="fecha_hasta" class="texto col-md-3 control-label">Tipo:</label>
            <div class="col-md-9 px-0">
                <select class="form-control" id="tipo" name="tipo">
                    <option></option>
                    @foreach ($tipos as $value)
                        <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                    @endforeach
                </select>
            </div>
          </div>  --}}

          <div class="form-group col-md-6 col-xs-9 pull-right" style="text-align: right;">  
            
            <button type="submit" class="btn btn-primary" id="btn_generar">
              <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
            </button> 
            {{-- <button type="button" class="btn btn-primary" id="btn_guardar">
              <span class="glyphicon glyphicon-save" aria-hidden="true"></span> Guardar
            </button>  --}}

          </div> 
              

        </form>
        
      </div>
      <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" >SALDOS</label>
        </div>
      </div>
    {{ csrf_field() }}
      <div class="box-body dobra">
        <div class="form-group col-md-12">
          <div class="form-row">
              <div id="contenedor">
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                    <div class="row">
                      <div class="row">
                        <div class="col-md-12">
                            @include('activosfijos.informes.saldos.informe')
                        </div>
                        <!-- /.col -->
                      </div>
                      <!-- /.row -->
                    </div> 
                </div>
              </div>
          </div>
        </div> 
      </div>

  </div>
  </section>
  <input type="hidden" name="filfecha" id="filfecha" value="{{$fecha}}"> 

  <!-- /.content -->
  <script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery.validate.js") }}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(document).ready(function(){

    //   $('#tbl_ventas').DataTable({
    //     'paging'      : false,
    //     'searching'   : false,
    //     'ordering'    : false,
    //     'info'        : false,
    //     'autoWidth'   : false,
    //     "scrollY"     : 200,
    //     "scrollX"     : true,
    //     'scrollCollapse': true,
    //   });

      $('#tbl_depreciacion').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : false,
        'info'        : false,
        'autoWidth'   : false,
        "scrollY"     : 200,
        "scrollX"     : true,
        'scrollCollapse': true,
      });

      tinymce.init({
        selector: '#hc'
      });

      $('input[type="checkbox"].flat-green').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass   : 'iradio_flat-green'
      });

      $('input[type="checkbox"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-red',
        radioClass   : 'iradio_flat-red'
      });

    });

    $(function () { 
        $('#fecha').datetimepicker({
            format: 'DD/MM/YYYY',
            });
        // $("#fecha").on("dp.change", function (e) {
        //     //buscar();
        // });
  
  });
   
    function seleccionar_todo(checked,tablename){
      var miTabla = document.getElementById(tablename);
      for (i=0; i<miTabla.rows.length; i++)
      {	
        miTabla.rows[i].getElementsByTagName("input")[0].checked = checked;
      }
    }
    function exportarxls(id){
        $("#id_ats").val(id);
        $("#filfecha").val($("#fecha").val());
        $("#xls").val(1);  
        $("#xml").val(0);  
        $("#print_reporte_master" ).submit();
    }
    function exportarxml(id){
        $("#id_ats").val(id);
        $("#filfecha").val($("#fecha").val());
        $("#xls").val(0);  
        $("#xml").val(1);  
        $("#print_reporte_master" ).submit();
    }
</script>
@endsection
