<style type="text/css">
  .h3 {
    font-family: 'BrixSansBlack';
    font-size: 8pt;
    display: block;
    background: #3d7ba8;
    color: #FFF;
    text-align: center;
    padding: 3px;
    margin-bottom: 5px;
  }

  .info_nomina {
    width: 69%;
  }

  .round {
    border-radius: 10px;
    border: 1px solid #3d7ba8;
    overflow: hidden;
    padding-bottom: 15px;
  }

  .datos_nomina {
    font-size: 0.8em;
  }

  .mValue {
    width: 79%;
    display: inline-block;
    vertical-align: top;
    padding-left: 7px;
    font-size: 0.9em;
  }

  #rol_pago {
    width: 100%;
    margin-bottom: 10px;
  }


  .info_nomina .col-xs-8 {
    padding-left: 10px;
    font-size: 0.9em;
  }

  .info_nomina .round {
    padding-top: 10px;
  }

  .titulo-wrapper {
    width: 100%;
    text-align: center;
  }

  .modal-body .form-group {
    margin-bottom: 0px;
  }

  .h3.modal_h3 {
    font-family: 'BrixSansBlack';
    font-size: 8pt;
    display: block;
    background: #3d7ba8;
    color: #FFF;
    text-align: center;
    padding: 3px;
    margin-bottom: 5px;
    padding: 7px;
    font-size: 1em;
    margin-bottom: 15px;
  }

  .h3.modal_h3_2 {
    margin-top: -20px !important;
    margin-bottom: 25px !important;
    padding: 7px;
    font-size: 1em;
  }

  .swal-title {
    margin: 0px;
    font-size: 16px;
    box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
    margin-bottom: 28px;
  }

  .separator {
    width: 100%;
    height: 20px;
    clear: both;
  }

  .separator1 {
    width: 100%;
    height: 5px;
    clear: both;
  }


  /* Nuevo CSS*/

  .mLabel {
    color: #777;
    font-size: 0.9rem;
    margin-bottom: 0;
    line-height: 10px;
  }

  .texto {
    color: #777;
    font-size: 0.9rem;
    margin-bottom: 0;
    line-height: 15px;
  }

  .color_texto {
    color: #FFF;
  }

  .head-title {
    background-color: #888;
    margin-left: 0px;
    margin-right: 0px;
    height: 30px;
    line-height: 30px;
    color: #cccccc;
    text-align: center;
  }

  .t9 {
    font-size: 0.9rem;
  }

  .well-dark {
    background-color: #cccccc;
  }
</style>
<div class="modal-content" style="width: 100%;">
  <div class="modal-header" style="padding-top: 5px; padding-bottom: 1px;">
    <div class="row head-title">
      <div class="col-md-12">
        <label class="color_texto" for="title">PRESTAMOS EMPLEADOS</label>
        <button type="button" id="cerrar" class="close" data-dismiss="modal">&times;</button>
      </div>
    </div>
  </div>
  <div class="box-body dobra">
    <div class="row">
      <div class="form-group col-md-4 ">
        <div class="row" >
          <div class="form-group col-md-12 ">
              <label for="usuario" class="col-md-4 control-label">Empleado:</label>
                <div class="col-md-18">
                  {{$prestamo->usuario->apellido1}} {{$prestamo->usuario->apellido2}} {{$prestamo->usuario->nombre1}} {{$prestamo->usuario->nombre2}}
                </div>
          </div>
        </div>
      </div>
      <div class="form-group col-md-3">
        <div class="row">
          <div class="form-group col-md-12">
            <label for="cedula" class="col-md-4 control-label"> Cédula:</label>
            <div class="col-md-7">
              {{$prestamo->usuario->id}}
            </div>
          </div>
        </div>              
      </div>
      <div class="form-group col-md-2">
        <div class="row">
          <div class="form-group col-md-12">
            <label for="cedula" class="col-md-5 control-label"> Monto:</label>
            <div class="col-md-7">
              {{$prestamo->monto_prestamo}}
            </div>
          </div>
        </div>              
      </div>

      <div class="form-group col-md-3">
        <div class="row">
          <div class="form-group col-md-12">
            <label for="cedula" class="col-md-5 control-label"> Saldo Actual:</label>
            <div class="col-md-7">
              {{$prestamo->saldo_total}}
            </div>
          </div>
        </div>              
      </div>


    </div>
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      <div class="table-responsive col-md-12">
        <table id="example4" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
          <thead>
            <tr>
              <th style="text-align: center;">AÑO</th>
              <th style="text-align: center;">MES</th>
              <th style="text-align: center;">CUOTA</th>
              <th style="text-align: center;">VALOR CUOTA</th>
            </tr>
          </thead>
          <tbody>
            @php
              $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
              $total = 0;
            @endphp
            @foreach($detalle as $d)
             @php
             $ms = intval($d->mes)-1;
             $total += $d->valor_cuota;
             @endphp
            <tr>
              <td>{{$d->anio}}</td>
              <td>{{$meses[$ms]}}</td>
              <td>{{$d->cuota}}</td>
              <td>${{$d->valor_cuota}}</td>
            </tr>
            @endforeach
            <tr>
              <td></td>
              <td></td>
              <td>TOTAL</td>
              <td>${{$total}}</td>
            </tr>
          </tbody>
          <tfoot>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
  <div class="separator1"></div>
  <div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
  </div>
</div>
<script type="text/javascript">
  $('#example4').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false,
      'order'       : [[ 1, "desc" ]]
  })
</script>
