<div class="modal-content">
  <div class="modal-header" style="background-color: #9b9b9b;">
    <input type="hidden" name="empresacheck">
    <button style="line-height: 30px;" type="button" class="close" data-dismiss="modal">&times;</button>
    <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">SUBIR PDF</h3>
  </div>
  <form method="post" id="formulario" action="{{ route('ventas_subirpdf') }}" enctype='multipart/form-data'>
    {{csrf_field()}}
    <input type="hidden" id="id" name="id" value="{{$id}}">
    <div style="margin-top: 5px;" class="col-md-12">
      <div class="form-row">
        <div style="margin-top: 2px;" class="col-md-12">
          <div class="form-row">
            <div class="form-group col-md-4">
              <input type="file" name="file" id="file" style="margin-top:3%;" size accept="application/pdf" required>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-default btn-gray" style="margin-top:-4px;"><i class="glyphicon glyphicon-folder-open" style="margin-right:6px;"></i>Guardar</button>
    </div>
  </form>
</div>
<script>
  var uploadField = document.getElementById("file");
  uploadField.onchange = function() {
    if (this.files[0].size > 2097152) {
      alert('El tamaño del archivo es muy grande');
      this.value = "";
    };
  };
</script>