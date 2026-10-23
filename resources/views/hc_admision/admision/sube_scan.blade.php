
  <script src="{{asset('plugins/ajaxform/jquery.form.js')}}"></script>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  <h4 class="modal-title" id="myModalLabel">Sube Escaneo Al Sistema </h4>
</div>
<div class="modal-body">

    <form action="{{ route('controldoc.agendascan') }}" enctype="multipart/form-data" method="POST">
      <div class="alert alert-danger print-error-msg" style="display:none">
          <ul></ul>
      </div>


      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="id_agenda" value="{{$id_agenda}}">
         <div class="form-group">
        <input type="file" name="archivo" class="form-control" required>
      </div>


       <div class="col-md-offset-8">
        <button class="btn btn-success upload-image" type="submit" >Subir Escaneo</button>
      </div>

      @php
        $agenda_scan = \Sis_medico\AgendaScan::where('id_agenda', $id_agenda)->first();
      @endphp

      @if(!is_null($agenda_scan))
        <a href="{{route('controldoc.descarga_scan2', ['id' => $id_agenda])}}" class="btn btn-success">Descargar Archivo</a>
      @endif



    </form>

</div>
<div class="modal-footer">
  <button type="button" id="bcerrar" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>



<script type="text/javascript">
  $("body").on("click",".upload-image",function(e){

    $(this).parents("form").ajaxForm(options);
  });


  var options = {
    complete: function(response)
    {


       if(response.statusText=='OK'){
        //$('#index_tb').empty().html(response.responseText);
        findex_tb();
        alert('Archivo subido correctamente');
        $('#bcerrar').click();


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
