<link rel="stylesheet" href="{{asset('css/dropzone.css')}}">
<script src="{{asset('js/dropzone.js')}}"></script>

<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  <h4 class="modal-title" id="myModalLabel">Sube {{$descripcion}} Al Sistema </h4>
</div>
<div class="modal-body">
  <div class="col-md-12">
    <form id="myForm" action="comment.php" method="post"> 
    Name: <input type="text" name="name" /> 
    Comment: <textarea name="comment"></textarea> 
    <input type="submit" value="Submit Comment" /> 
</form> 
  </div> 
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>

 <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script> 
    <script src="http://malsup.github.com/jquery.form.js"></script> 
 
    <script> 
        // wait for the DOM to be loaded 
        $(document).ready(function() { 
            // bind 'myForm' and provide a simple callback function 
            $('#myForm').ajaxForm(function() { 
                alert("Thank you for your comment!"); 
            }); 
        }); 
    </script> 