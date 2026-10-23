

      <div class="table-responsive col-md-12" style="min-height: 210px;">
        <table id="example2_labs" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;overflow: none;">
          <thead>
            <tr role="row">
              <th width="10">Sel.</th>
              <th width="10">Fecha</th>
              <th width="10">Medico</th>
              <!--th width="25">Descripcion</th>
              <th width="10">Cant.</th>
              <th width="10">Valor</th--> 
            </tr>
          </thead>
          <tbody>
          @foreach($medicinas as $value)
            @php 
              $receta = Sis_medico\hc_receta::find($value->id);
            @endphp
              <tr>
                  <td><button type="button" class="btn btn-xs btn-success" onclick="seleccionar_medicina('{{$a_plano->id}}','{{$value->id}}')"><span class="glyphicon glyphicon-ok-sign"></span></button></td>
                  <td>{{substr($value->created_at,0,10)}}</td>
                  <td>@if($receta->doctor != null){{$receta->doctor->apellido1}} {{$receta->doctor->nombre1}}@endif</td>
                  <!--td>{{$value->descripcion_procedimiento}}</td>
                  <td>{{$value->cant}}</td>
                  <td>{{$value->valor_procedimiento}}</td-->
              </tr>
          @endforeach
          </tbody>
        </table>
      </div>
 