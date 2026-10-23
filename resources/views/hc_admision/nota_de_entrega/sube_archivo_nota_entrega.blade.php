<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<script src="{{asset('plugins/ajaxform/jquery.form.js')}}"></script>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  <h4 class="modal-title" id="myModalLabel"> SUBIR NOTA DE ENTREGA</h4>
</div>
<div class="modal-body">
  <form action="{{ route('receta.update_nota_entrega') }}" enctype="multipart/form-data" method="POST">
    <div class="alert alert-danger print-error-msg" style="display:none">
        <ul></ul>
    </div>
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="id_receta" value="{{ $id_receta }}">
    <input type="hidden" name="id_nota" value="{{ $id_nota }}">

    <div class="form-group">
      <input type="file" name="archivo" class="form-control" required>
    </div>
    <br>
    <center><button class="btn btn-success upload-image" type="submit" >SUBIR</button></center>
  </form> 
</div>
<div class="modal-footer">
  <button type="button" id="bcerrar" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>


<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">
  $("body").on("click",".upload-image",function(e){
    $(this).parents("form").ajaxForm(options);
  });

  var options = { 
    complete: function(response) 
    {
      if(response.statusText=='OK'){
        //$('#index_tb').empty().html(response.responseText);
        //findex_tb();
        alert('Archivo subido correctamente');
        $('#bcerrar').click();
        location.reload();
      }else{
        printErrorMsg(response.responseJSON.archivo);
      }
    }
  };

  function printErrorMsg (msg) {
  $(".print-error-msg").find("ul").html('');
  $(".print-error-msg").css('display','block');
  $.each( msg, function( key, value ) {
    $(".print-error-msg").find("ul").append('<li>'+value+'</li>');
  });
  }
</script>