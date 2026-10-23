<div class="modal-content">
  <div class="modal-header">
    <h4 class="modal-title">Editar tipo de producto</h4>
    <button type="button" class="close" data-dismiss="modal">&times;</button>
  </div>
  <div class="modal-body">
   <form class="form-vertical" role="form" method="GET" action="{{route('hospital_admin.updatepro', ['id' => $productoid->id])}}">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-4" style="padding-top: 20px">Codigo</div>
              <div class="col-md-8"><input class = "form-control" type="text" value="{{$productoid->codigo}}" name="codigo" required maxlength="10" style="border: 1px solid #BFC9CA; margin-top: 20px"></div>
            </div>
          </div>

          <div class="col-md-12">
            <div class="row">
              <div class="col-md-4" style="padding-top: 20px">Nombre</div>
              <div class="col-md-8"><input class = "form-control" type="text" value="{{$productoid->nombre}}" name="nombre" required maxlength="10" style="border: 1px solid #BFC9CA; margin-top: 20px"></div>
            </div>
          </div>

          <!--Etiqueta nnumero 3 --->
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-4" style="padding-top: 20px">Descripci&oacute;n</div>
              <div class="col-md-8"><input class = "form-control" type="text" name="descripcion" value="{{$productoid->descripcion}}" required maxlength="20" style="border: 1px solid #BFC9CA; margin-top: 20px;"></div>
            </div>
          </div>
          
          <div class="col-md-12">
                    <div class="row">
                    <div class="col-md-4" style="padding-top: 20px">Estado</div>
                    <div class="col-md-8"style="padding-top: 20px" >
                      <select class="select form-control" id="estado" name="estado" style="margin-bottom: 25px;">
                          <option @if(($productoid->estado)==1) Selected @endif value="1">ACTIVO</option>
                          <option @if(($productoid->estado)==2) Selected @endif value="2">INACTIVO</option>
                      </select>
                    </div>
                          

                    </div>
          </div>
          <div class="col-md-12">
                    <div class="row"> 

                      <div class="col-md-4" style="padding-top: 20px">Medida</div>
                      <div class="col-md-8"style="padding-top: 20px" >
                      <select class="select form-control" id="medida" name="medida" style="margin-bottom: 25px;">
                                <option @if(($productoid->estado)=="uni") Selected @endif value="Uni">Unidad</option>
                                <option @if(($productoid->medida)=="kg") Selected @endif value="Kg">Kilogramos</option>
                                <option @if(($productoid->medida)=="G") Selected @endif value="G">Gramos</option>
                                <option @if(($productoid->medida)=="Mg") Selected @endif value="Mg">Miligramos</option>
                                <option @if(($productoid->medida)=="Ml") Selected @endif value="Ml">Mililitros</option>
                                <option @if(($productoid->medida)=="L") Selected @endif value="L">Litros</option>
                                <option @if(($productoid->medida)=="Lb") Selected @endif value="Lb">Libras</option>
                                <option @if(($productoid->medida)=="m") Selected @endif value="m">Metros</option>
                                <option @if(($productoid->medida)=="cm") Selected @endif value="cm">Centimetros</option>
                                <option @if(($productoid->medida)=="mm") Selected @endif value="cm">Milimetros</option>
                      </select>
                      </div>
                    </div>

          </div>
          <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-4" style="padding-top: 20px">Stock Minimo</div>
                      <div class="col-md-8"><input class = "form-control" type="text" name="minimo" value="{{$productoid->minimo}}" required maxlength="20" style="border: 1px solid #BFC9CA; margin-top: 20px;"></div>

                    </div>
          </div>
          <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-4" style="padding-top: 20px">Forma de despacho</div>
                      <div class="col-md-8"style="padding-top: 20px" >
                      <select class="select form-control" id="despacho" name="despacho" style="margin-bottom: 25px;">
                                <option @if(($productoid->despacho)==1) Selected @endif value="1">Código de Serie</option>
                                <option @if(($productoid->despacho)==2) Selected @endif value="2">Código de Producto</option>
                                
                      </select>
                                
                      </div>

                    </div>
          </div>

          <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-4" style="padding-top: 20px">Registro Sanitario</div>
                      <div class="col-md-8"><input class = "form-control" type="text" name="registro" value="{{$productoid->registro_sanitario}}" required maxlength="20" style="border: 1px solid #BFC9CA; margin-top: 20px;"></div>

                    </div>
          </div>

          <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-4" style="padding-top: 20px">Marcas</div>
                      <div class="col-md-8"style="padding-top: 20px" >
                      <select class="select form-control" id="marcas" name="marcas" style="margin-bottom: 25px;">
                                <option @if(($productoid->id_marca)==1) Selected @endif value="1">Primer Marca</option>
                                <option @if(($productoid->id_marca)==2) Selected @endif value="2">Segunda de Producto</option>
                                
                      </select>
                                
                      </div>

                    </div>
          </div>
          <div class="col-md-12">
                    <div class="row">
                      <div class="col-md-4" style="padding-top: 20px">Tipo de producto</div>
                      <div class="col-md-8"style="padding-top: 20px" >
                      <select class="select form-control" id="tipop" name="tipop" style="margin-bottom: 25px;">
                                <option @if(($productoid->tipo_producto)==1) Selected @endif value="1">Guantes</option>
                                <option @if(($productoid->tipo_producto)==2) Selected @endif value="2">ACICLOVIR</option>
                                
                      </select>
                                
                      </div>

                    </div>
          </div>
          <div class="col-md-12">
                     <div class="row"> 

                      <div class="col-md-4" style="padding-top: 25px">Cantidad de Usos</div>
                      <div class="col-md-8"><input type="number" name="usos" value="{{$productoid->usos}}" required maxlength="10" style="border-radius: 15px; border: 1px solid #BFC9CA; margin-top: 25px"></div>

                    </div>
         </div>
               
    
     
      <div class="col-md-12" style="text-align: center; margin-top: 20px;">
          <button type="submit" class="btn btn-primary active" style=" border-radius: 10px;">EDITAR
          </button>
      </div>
    </form>
  </div>
  <div class="modal-footer"> 
  </div>
</div>