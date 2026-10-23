<div class="box box-warning">
        <div class="row">
              @php
                $adicionales = \Sis_medico\Hc_Procedimiento_Final::where('id_hc_procedimientos', $id_proc)->get();
                $mas = true;
                $texto = "";

                foreach($adicionales as $value2)
                {
                  if($mas == true){
                   $texto = $texto.$value2->procedimiento->nombre  ;
                   $mas = false;
                  }
                  else{
                    $texto = $texto.' + '.  $value2->procedimiento->nombre  ;
                  }
                }
              @endphp

            <div class="table-responsive col-md-12">
              <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                  <tr role="row">
                    <!--th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Codigo</th-->
                    <th width="8%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Name: activate to sort column descending" aria-sort="ascending">Nombre</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Fecha</th>
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Usuario</th>
		    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Bodega</th>
                    @if($aprobado == 0)
                    <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Accion</th>
                    @endif
                  </tr>
                </thead>
                <tbody>
                @php 
                  if(Auth::user()->id == "0957258056"){
                    //dd($productos);
                  }
                    
                  @endphp
                  @foreach($productos as $producto)
                  
                    <tr>
                      <!--td>{{$producto->movimiento->serie}}</td-->
                      <td>{{$producto->movimiento->producto->nombre}}</td>
                      <td>{{$producto->created_at}}</td>
                      <td>{{$producto->usuario_crea->apellido1}} {{$producto->usuario_crea->nombre1}}</td>
                      <td>@if(isset($producto->bodega)){{$producto->bodega->nombre}} @endif</td>
		      @if($aprobado == 0)
                      <td>
                        <a onclick="eliminar_producto({{$producto->id}})" class="btn btn-danger col-md-8 col-sm-8 col-xs-8 btn-margin">Eliminar</a>
                      </td>
                      @endif
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
         </div>
      </div>
      <script type="text/javascript">
      function eliminar_producto(id){
        var r = confirm("Esta seguro de eliminar el registro?");
        if (r == true) {
          $.ajax({
              type: 'get',
              url:"{{asset('enfermeria/uso/paciente_insumo/eliminar')}}/"+id,
              headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
              datatype: 'json',
              data: '',
              success: function(data){
                if(data == 'ok'){
                  window.location.reload();
                }else{
                  alert('No se puede eliminar el registro');
                  console.log(data);
                }
              },
              error: function(data){
                  console.log(data);
              }
          });
        }

      }
      </script>