<div class="modal-header">
    <div class="col-md-10" style="text-align: center;"><h3>Devolver a Cotizacion / Eliminar Orden</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-md-6">
      <label class="col-md-3">Opcion</label>
      <select class="form-control col-md-9" name="estado_dev" id="estado_dev" required>
          <option value="">Seleccionar...</option>
          <option value="1">Devolver a Cotizacion</option>
          <option value="0">Eliminar</option>
      </select>
    </div>
    <div class="col-md-6">
      <label class="col-md-3">Orden</label>
      <input class="form-control col-md-9" type="number" name="n_orden" id="n_orden" required>
    </div>

    <div class="col-md-12" style="text-align: center; padding-top: 20px;">
      <button onclick="actualizar_orden();" class="btn btn-primary">Aceptar</button>
    </div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
<script type="text/javascript">

  function actualizar_orden(){
    var n_orden = $("#n_orden").val();
    var estado_dev = $("#estado_dev").val();
    var msj = "";

    if(estado_dev == ""){
       msj = msj + "Por favor, Seleccione una opcion. <br>";
    }

    if(n_orden == ""){
       msj = msj + "Por favor, Ingrese el No. orden. <br>";
    }

    if(msj != ""){
      Swal.fire("Oops...", msj, "error");
      return false;
    }
    Swal.fire({
      title: 'Alerta',
      text: "¿ Esta seguro que desea realizar esta accion ?",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Si'
    }).then((result) => {
      if (result.isConfirmed) {
    $.ajax({
        type: 'post',
        url:"{{route('orden.devolver_update')}}",
        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
        datatype: 'json',
        data: { n_orden: n_orden, estado_dev: estado_dev},
        success: function(data){
            if(data == 'orden_no_existe'){
                Swal.fire("Oops...", "La orden "+n_orden+" no existe.", "error");
            }else if(data == 'orden_facturada'){
                if(estado_dev == '1'){
                    Swal.fire("Oops...", "La orden "+n_orden+" ya se encuentra facturada, no es posible devolverla a cotización.", "error");
                }else{
                    Swal.fire("Oops...", "La orden "+n_orden+" ya se encuentra facturada, no es posible eliminarla.", "error");
                }
              }else if(data == 'orden_eliminada'){
                Swal.fire("Oops...", "La orden "+n_orden+" ya se encuentra eliminada, no es posible realizar esta acción.", "error");
            }
              else{
                Swal.fire("Mensaje", "!! Correcto !!", "success");
          location.reload();
            }
        },
        error: function(data){
          }
    });
      }
    })
  }
</script>
