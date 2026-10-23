  <div class="modal-header" style="background: #3c8dbc;">
    <input type="hidden" name="empresacheck">
    <button style="line-height: 30px;" type="button" class="close" id="boton" data-dismiss="modal">&times;</button>
    <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">Posibles asientos</h3>
  </div>
  <div class="modal-body">
    <table id="example" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
      <thead style="background-color: #337ab7;color:white;">
        <tr role="row" id="cabezera">
        <tr>
          <th scope="col" class="px-6 py-3">
            Asiento
          </th>
          <th scope="col" class="px-6 py-3">
            Fecha
          </th>
          <th scope="col" class="px-6 py-3">
            Observacion
          </th>
          <th scope="col" class="px-6 py-3">
            Valor
          </th>

        </tr>
        </tr>
      </thead>
      <tbody id="cuerpo">
        @foreach($cab as $val)
        <tr>
          <td>{{$val->id}}</td>
          <td>{{$val->fecha_asiento}}</td>
          <td>{{$val->observacion}}</td>
          <td>{{$val->valor}}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>