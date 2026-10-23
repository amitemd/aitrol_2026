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
            <label class="color_texto" >Detalle de activos a depreciados</label>
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
                            
                            <div id="contenedor">
                                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                                    <div class="row">
                                    <div class="table-responsive col-md-12">
                                        <table id="tbl_depreciacion" class="table table-sm table-bordered table-condensed dataTable table-striped dataTables_wrapper" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr class="well-dark">
                                            <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending"></th>
                                            <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Codigo</th>
                                            <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Activo</th>
                                            <th width="25%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Descripción</th>
                                            <th width="20%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tipo</th>
                                            <th width="25%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha de depreciación</th>
                                            <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Costo</th>
                                            <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Vida Util</th>
                                            <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Tasa / Porcentaje</th>
                                            <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Valor Depreciado</th>
                                            <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Total depreciado</th>
                                            <th width="5%" class="" tabindex="0" aria-controls="tbl_ventas" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Saldo</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl_detalles" name="tbl_detalles">
                                            @foreach ($detalles as $value)
                                            <tr class="well"> 
                                                <td ><input type="checkbox" id="id_activo{{ $value->id }}" class="form-check-input" name="id_activo[]" value="{{ $value->id }}"  @if(@$value['validado']=="1") checked @endif></td> 
                                                <td >{{ $value->activo->codigo }}</td>
                                                <td >{{ $value->activo->nombre }}</td>
                                                <td >{{ $value->activo->descripcion }}</td>
                                                <td >{{ $value->activo->tipo->nombre }}</td>
                                                <td >{{ date('d/m/Y', strtotime($value->created_at)) }}</td>
                                                <td style="text-align: right">{{ $value->activo->costo }}</td>
                                                <td style="text-align: center">{{ $value->activo->vida_util }}</td>
                                                <td style="text-align: center">{{ $value->porcentaje }} %</td>  
                                                <td style="text-align: right">{{ $value->valordepreciacion }} </td>
                                                <td style="text-align: right">{{ $value->totaldepreciado }}</td>
                                                <td style="text-align: right">{{ $value->saldo }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                        </table>
                                    </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-2">
                                            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Total de registros  {{count($detalles)}} </div>
                                        </div> 
                                    </div> 
                                </div>
                            </div>


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
  <input type="hidden" name="filfecha" id="filfecha" value=""> 

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



















