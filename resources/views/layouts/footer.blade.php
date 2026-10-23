  <!-- Main Footer -->
  <footer class="main-footer">
    <!-- To the right -->
    <div class="pull-right hidden-xs">
    </div>
    <!-- Default to the left -->
    <strong>Copyright &copy; {{ date('Y') }}<a href="#"></a>.</strong> Todos los derechos reservados.
  </footer>

  <?php

  use Sis_medico\De_Empresa;

  $datosFirma = De_Empresa::getDatosFirma(session('id_empresa'));

  $vigente = $datosFirma['Vigente'];
  $dias = $datosFirma['Expiracion'];

  if ($datosFirma['Expiracion'] != '' && $datosFirma['Vigente'] == 1) {
    $dias = $datosFirma['Expiracion'];
  } else {
    $dias = 0;
  }

  ?>

  <div class="modal fade" id="myModalDatosFirma" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">
            <span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span>
          </button>
          <h4 class="modal-title" id="myModalLabel">Datos de la firma</h4>
        </div>
        <div class="modal-body">
          <div class="row" style="padding:15px">
            <div class="box-body dobra">
              <div class="form-group col-xs-12">
                <label for="tipo_identificacion" class="col-md-3 texto" style="color: blue">Nombre:</label>
                <div class="col-md-9">
                  <?= $datosFirma['Nombre']; ?>
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label for="tipo_identificacion" class="col-md-3 texto" style="color: blue">E-mail:</label>
                <div class="col-md-9">
                  <?= $datosFirma['Email']; ?>
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label for="tipo_identificacion" class="col-md-3 texto" style="color: blue">Emisión:</label>
                <div class="col-md-9">
                  <?= $datosFirma['Desde']; ?>
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label for="tipo_identificacion" class="col-md-3 texto" style="color: blue">Vence:</label>
                <div class="col-md-9">
                  <?= $datosFirma['Hasta']; ?>
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label for="tipo_identificacion" class="col-md-3 texto" style="color: blue">Estado:</label>
                <div class="col-md-9">
                  <?= $vigente == 1 ? 'VIGENTE' : '<label style="color:red;">CADUCADO</label>'; ?>
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label for="tipo_identificacion" class="col-md-3 texto" style="color: blue">Restantes:</label>
                <div class="col-md-9">
                  <?= $dias; ?> días
                </div>
              </div>
              <div class="form-group col-xs-12">
                <label for="tipo_identificacion" class="col-md-3 texto" style="color: blue">Emisor:</label>
                <div class="col-md-9">
                  <?= $datosFirma['Emisor']; ?>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>