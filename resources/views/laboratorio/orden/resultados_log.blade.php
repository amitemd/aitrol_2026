<section class="content">
  <div class="row">
    <div>

      <div class="box-header with-border">
        <table class="table table-hover" style="width: 100%;">
          <thead>
            <tr>
              <th>CEDULA</th>
              <th>USUARIO</th>
              <th># ORDEN</th>
              <th>DESCRIPCION</th>
              <th>FECHA</th>
              <th>IP</th>
            </tr>
          </thead>
          <tbody>
            @foreach($log as $value)
            <tr>
              <td width="15%"> {{$value->id_usuario}} </td>
              <td width="25%"> {{$value->usuario}} </td>
              <td width="15%"> {{$value->dato_ant1}} </td>
              <td width="25%"> {{$value->descripcion}} </td>
              <td width="10%"> {{$value->fecha}} </td>
              <td width="10%"> {{$value->ip_usuario}} </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</section>