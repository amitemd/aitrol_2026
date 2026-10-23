
  <script src="{{asset('plugins/ajaxform/jquery.form.js')}}"></script>




<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  <h4 class="modal-title" id="myModalLabel">Sube {{$descripcion}} Al Sistema </h4>
</div>
<div class="modal-body">
   
    <form action="{{ route('controldoc.update2') }}" enctype="multipart/form-data" method="POST">
      <div class="alert alert-danger print-error-msg" style="display:none">
          <ul></ul>
      </div>


      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <input type="hidden" name="ahid" value="{{ $ahid }}">
      <input type="hidden" name="id_doc" value="{{ $id_doc }}">


      <!--div class="form-group">
        <label>Alt Title:</label>
        <input type="text" name="title" class="form-control" placeholder="Add Title">
      </div-->


       
         <div class="form-group">
        <input type="file" name="archivo" class="form-control" required>
      </div>


       <div class="col-md-offset-8">
        <button class="btn btn-success upload-image" type="submit" >Upload Image</button>
      </div>
       


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