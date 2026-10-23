<style>
  #tui-image-editor-container{
    height: 700px!important;
  }
</style>
<!-- by Anthony chilan -->
<div class="modal-header">

    <div class="col-md-10"><h3>@lang('contable.editorimagen')</h3></div>
    <div class="col-md-2">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span>
    </button>
    </div>
</div>
<div class="modal-body">
    
        
    
            
    <form id="frm">
        <div class="box-body">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">    
            <input type="hidden" name="id_protocolo" id="id_protocolo" value="{{$id}}">      
            <div id="tui-image-editor-container"></div> 
            <div class="col-md-12" style="text-align: center; margin-top: 10px;">
                <button class="btn btn-info btn_accion doSaveFile" type="button">  <i class="fa fa-save"></i> &nbsp; Guardar</button>
            </div>
        </div>    
    </form>
     
        
     
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >@lang('contable.cerrar')</button>
</div>
    <script type="text/javascript" src="{{asset('imageedit/dist/tui-image-editor.js')}}"></script>
    <script type="text/javascript" src="{{asset('imageedit/js/theme/white-theme.js')}}"></script>
    <script type="text/javascript" src="{{asset('imageedit/js/theme/black-theme.js')}}"></script>
    <script type="text/javascript">
      var imageEditor = new tui.ImageEditor('#tui-image-editor-container', {
        includeUI: {
          loadImage: {
            path: '{{asset("hc_ima")}}/{{$imagenf->nombre}}',
            name: 'Imagen',
          },
          theme: blackTheme, // or whiteTheme
          initMenu: 'filter',
          menuBarPosition: 'bottom',
        },
        usageStatistics: false,
      });
      imageEditor.ui.resizeEditor({
        uiSize: {width: 1000, height: 1000}
    });

    // Apply the ui state while preserving the previous attribute (for example, if responsive Ui)
    function dataURLtoBlob(dataurl) {
      var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
          bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
      while(n--){
          u8arr[n] = bstr.charCodeAt(n);
      }
      return new Blob([u8arr], {type:mime});
    }
$(document).ready(function ($) {

$('.doSaveFile').on('click', function (e) {

    // GET TUI IMAGE AS A BLOB
    var blob = dataURLtoBlob(imageEditor.toDataURL());
    var id= $("#id_protocolo").val();
    console.log(blob);
    // PREPARE FORM DATA TO SEND VIA POST
    var formData = new FormData();
    formData.append('croppedImage', blob, 'sampleimage.png');
    $(e).attr('disabled','disabled');
    $.ajax({
            url: "{{route('hc4.saveimage')}}?id="+id, // upload url
            method: "POST",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            processData: false,
            contentType: false,
            data: formData,
            success: function (data) {
                alert('Subido correctamente...');
                $('#editimage').modal('hide');
                location.reload(true);             
            },
            error: function(xhr, status, error) {
                alert('Error, contactase con el programador');
            }
    });
        
    
    return false;
});
});
    </script>