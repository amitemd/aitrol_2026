<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title">EDITAR TIPO DE MARCAS</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <div class="modal-body">
   <form class="form-vertical" role="form" method="GET" action="{{route('hospital_admin.updatep')}}">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="col-md-12">
                    <div class="row"> 

                      <div class="col-md-4" style="padding-top: 20px">Nombre</div>
                      <div class="col-md-8"><input class = "form-control" type="text" value="" name="nombre" required maxlength="10" style="border: 1px solid #BFC9CA; margin-top: 20px"></div>

                    </div>

          </div>
                  <!--Etiqueta nnumero 3 --->
         
           
    
     
      <div class="col-md-12" style="text-align: center; margin-top: 20px;">
          <button type="submit" class="btn btn-primary active" style=" border-radius: 10px;">AGREGAR
          </button>
      </div>
    </form>
  </div>
  <div class="modal-footer"> 
  </div>
</div>
