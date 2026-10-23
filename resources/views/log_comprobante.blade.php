<div class="col-md-12">
  <div class="table-responsive col-md-12">
    <span><b>Log de Envio</b></span>
    <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
      <thead>
        <tr class="well-dark">
          <th >Fecha</th>
          <th >Proceso</th>
          <th >Mensaje</th>
          <th >Informacion Adicional</th>
        </tr>
      </thead>
      <tbody>

        @foreach($envio->details->log as $value)
        <tr class="well">
          <td>{{$value->fecha}}</td>
          <td>{{$value->proceso}}</td>
          <td>{{$value->mensaje}}</td>
          <td>{{$value->detalle}}</td>
        </tr>
        @endforeach
      </tbody>
      <tfoot>
      </tfoot>
    </table>
  </div>
</div>
