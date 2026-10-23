@extends('archivo_plano.procedimientos.baselista')

  <!-- Main content -->
    <section class="content">
      <div class="box">
        <div class="box-header">
          <div class="row">
            <div class="col-sm-12">
              <h3 class="box-title">Edito Procedimiento: <br><br>
                <span style="color:#3c8dbc">{{ $cat->descripcion }}</span>
              </h3>
            </div>
          </div>
        </div> 
        <hr>
        <div class="box-body">
          <div class="row">
            <div class="col-sm-6"></div>
            <div class="col-sm-6"></div>
          </div>
          <form method="POST" action="{{ $cat->id }}">
            {!! csrf_field() !!}
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
              <div class="row">
                <div class="col-xs-6">
                  Cantidad:<br> 
                  <input type="text" name="cantidad" value="{{ $cat->cantidad }}" class="form-control" style="width: 100%;">
                </div>
                <div class="col-xs-6">
                  Valor:<br> 
                  <input type="text" name="valor" value="{{ $cat->valor }}" class="form-control" style="width: 100%;">
                </div>
                <div class="col-xs-6"><br> 
                  IVA:<br> 
                  <input type="text" name="iva" value="{{ $cat->iva }}" class="form-control" style="width: 100%;">
                </div>
                <div class="col-xs-6"><br> 
                  Total:<br> 
                  <input type="text" name="total" value="{{ $cat->total }}" class="form-control" style="width: 100%;">
                </div>
                <div class="col-xs-12"><br>
                    <input type="submit" name="" value="Guardar" class="btn btn-success">
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </section>
  



