<div class="modal-content">
  <div  class="modal-header" style="color: white; padding-top: 5px; padding-bottom: 1px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1);">
     <button type="button" class="close" data-dismiss="modal" style="color: white;">&times;</button>
    <h4 class="modal-title">Master</h4>
  </div>
  <div class="modal-body">
    <form   enctype="multipart/form-data" method="POST">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
        
          
        <div class="col-md-12" style="text-align: center; margin-top: 20px;">
          <button type="submit" class="btn btn-primary active" style=" border-radius: 10px;">AGREGAR
          </button>
      </div>
    </form>
  </div>
  <div class="modal-footer"> 
  </div>
</div>