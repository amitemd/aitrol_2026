<div class="modal-content">
  <div class="modal-header" style="background: #3c8dbc;">
    <input type="hidden" name="empresacheck">
    <button style="line-height: 30px;" type="button" class="close" id="boton" data-dismiss="modal">&times;</button>
    <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">Cantidad de Tubos</h3>
  </div>
  <div class="modal-body">
    <table id="example" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
      <thead style="background-color: #337ab7;color:white;">
        <tr role="row" id="cabezera">
          <th>Cantidad</th>
          <th>Nombre</th>
          <th>Tipo</th>

        </tr>
      </thead>
      <tbody id="cuerpo">
        @foreach($arrayG as $key=>$val)
        <tr>
          <td>{{$val['cantidad']}}</td>
          <td>{{$val['nombre']}}</td>

          <td>{{$val['tipo']}}</td>
        </tr>
        @endforeach
        @foreach($arrayU as $key=>$val)
        <tr>
          <td>{{$val['cantidad']}}</td>
          <td>{{$val['nombre']}}</td>

          <td>{{$val['tipo']}}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="modal-footer" style="text-align:center">
 
    <form action="{{route('imprimir_codigo_barra',['id'=>$orden])}}">
      <input type="submit" value="Imprimir" class="btn btn-primary" />
  </form>
  </div>
</div>
