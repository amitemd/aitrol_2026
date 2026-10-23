<div class="modal-header">
    <div class="col-md-10"><h3>Actualiza Valor Anticipo 1 era Quincena</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>

<div class="modal-body">
  <div class="box-body">
    <form class="form-horizontal" id="form_anticipo">
      {{ csrf_field() }}
      <input  name="id_nomina" id="id_nomina" type="text" class="hidden" value="@if(!is_null($id_nomina)){{$id_nomina}}@endif">
      <div class="row">
        <div class="form-group col-md-6">
            <label for="val_anticipo" class="col-md-4 control-label">Valor Anticipo:</label>
            <div class="col-md-8">
              <input id="val_anticipo" type="text" class="form-control" name="val_anticipo" value="{{$inf_nomina->val_anticip_quince}}" onkeypress="return isNumberKey(event)" onblur="checkformat(this);" required autofocus>
            </div>
        </div>
      </div>
    </form>
    <br><br>
    <div style="padding-top: 10px;padding-left: 70px" class="form-group col-md-12">
      <center>
        <div class="col-md-6 col-md-offset-2">
          <div class="col-md-7">
            <button type="button" class="btn btn-primary" onclick="guardar()"><span class="glyphicon glyphicon-floppy-disk"> Guardar</span> </button>
          </div>
        </div>
      </center>
    </div>
  </div>
  <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">

    function isNumberKey(evt)
    {
     var charCode = (evt.which) ? evt.which : event.keyCode
     if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
        return false;

     return true;
    }

    function checkformat(entry) {

        var test = entry.value;

        if (!isNaN(test)) {
            entry.value=parseFloat(entry.value).toFixed(2);
        }

        if (isNaN(entry.value) == true){
            entry.value='0.00';
        }
        if (test < 0) {

            entry.value = '0.00';
        }

    }

  //Guarda Producto Tarifario
  function guardar(){

    var formulario = document.forms["form_anticipo"];

    var val_anticip = formulario.val_anticipo.value;

    var msj = "";

    if(val_anticip < 0){
      msj += "Por favor, Ingrese un Valor Anticipo<br/>";
    }

    if(msj != ""){

            swal({
                  title: "Error!",
                  type: "error",
                  html: msj
                });
            return false;
    }

    $.ajax({
      type: 'post',
      url:"{{ route('store_anticipo_valor.quincena') }}",
      headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
      datatype: 'json',
      data: $("#form_anticipo").serialize(),
      success: function(data){

        if(data.msj =='ok'){

          swal({
              title: "Datos Guardado con Exito",
              icon: "success",
              type: 'success',
              buttons: true,
          })

          $("#mod_val_anticipo").modal('hide');
          $('body').removeClass('modal-open');
          $('.modal-backdrop').remove();

          //Refrescar Pagina
          location.reload();

        }

      },

      error: function(data){
        console.log(data);
      }
    });

  }

</script>
