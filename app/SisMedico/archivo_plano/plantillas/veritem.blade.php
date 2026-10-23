@extends('archivo_plano.plantillas.base')
@section('action-content')


    <!-- Main content -->
    <section class="content">
      <div class="box">
  <div class="box-header">
    <div class="row">
        <div class="col-sm-6">
          <h3 class="box-title" style="margin-top: 7px;">Ver Información de Plantillas</h3>
        </div><br><hr>
 
    </div>
  </div> 
  <!-- /.box-header -->
    <div class="content" style="margin-top:-20px">
          <form method="post" id="dynamic_form">
            {{ csrf_field() }}
            <div class="row">
            <div class="col-md-12">
                <h4 style="color:#367fa9">Información de Cabecera</h4><hr>
            </div>
              <div class="col-md-6">
                <label>Código</label><br>
                {{ $fila->codigo }}
              </div>
              <div class="col-md-6">
                <label>Descripción</label><br>
                {{ $fila->descripcion }}
              </div>
              <div class="col-md-6"><br>
                <label>Descripción Completa</label><br>
                {{ $fila->desc_comp }}
              </div>
              <div class="col-md-6"><br>
                <label>Estado</label><br>
                @if(($fila->estado)===1)
                Activo
                @else
                Inactivo
                @endif
              </div>
<br>
<div class="col-md-12"><br><hr>
<h4 align="center" style="color:#00A65A"><i class="fa fa-list"></i> Items de Plantilla</h4><br>

<!-- bloque 5 -->
  
     <br />
   <div class="table-responsive">
                
                 <span id="result"></span>
                 <table class="table table-bordered table-striped" id="user_table">
               <thead>
                <tr>
                    <th width="30%">Item</th>
                    <th width="15%">Cantidad</th>
                    <th width="15%">Honorario</th>
                    <th width="15%">Orden</th>
                    <th width="15%">Separado</th>
                </tr>
               </thead>
               <tbody>
               <div style="display:none">
               {{ $conti=1 }}
               </div>
               @foreach($item as $fila)

               <tr>
                    <td>
                        Codigo: {{ $fila->codigo }}| {{ $fila->descripcion }}
                    </td>
                    <td>
                        {{ $fila->cantidad }}
                    </td>
                    <td>
                        {{ $fila->honorario }}
                    </td>
                    <td>
                        {{ $fila->orden }}
                    </td>
                    <td>
                         {{ $fila->separado }}
                    </td>
                    
              </tr>
              <div style="display:none">
              {{ $conti++ }}
              </div>
            @endforeach
               </tbody>

           </table>
                
   </div>


</div>
<br>

              <div class="col-md-6"><br></div><div id="proced_list"></div>
              <!-- <input type="submit" name="" value="Guardar" class="btn btn-success">-->
              </div>
            
          </form>
      
    </div>
  <!-- /.box-body -->
</div>
    </section>
    <!-- /.content -->
  </div>
<script src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>


@endsection