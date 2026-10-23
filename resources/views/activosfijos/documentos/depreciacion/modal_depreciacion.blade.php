  <div class="modal-header" style="background: #3c8dbc;">
    <input type="hidden" name="empresacheck">
    <button style="line-height: 30px;" type="button" class="close" id="boton" data-dismiss="modal">&times;</button>
    <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">Detalle Depreciación</h3>
  </div>
  <div class="modal-body">
    <table id="example" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
      <thead style="background-color: #337ab7;color:white;">
        <tr role="row" id="cabezera">
          <th>ID ACTIVO</th>
          <th>NOMBRE ACTIVO</th>
          <th>VALOR DEP</th>
          <th>SALDO</th>
        </tr>
      </thead>
      <tbody id="cuerpo">
        @foreach($detalles as $val)
        <tr>
          <td>{{$val->activo_id}}</td>
          <td>@if(isset($val->activo)) {{$val->activo->nombre}} @endif</td>
          <td>{{$val->valordepreciacion}}</td>
          <td>{{$val->saldo}}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

