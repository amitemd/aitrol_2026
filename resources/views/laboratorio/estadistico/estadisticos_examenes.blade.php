<div class="row">
  <div class="col-md-12">
    <div class="table-responsive">
      <h4 style="background-color: #009999;text-align: center;color: white;">PRODUCCION DE EXAMENES DEL AÑO {{$anio}}</h4>
      <table id="tabla_examenes" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="examplea_info" style="font-size: 11px;">
        <thead>
          <tr role="row">
            
            <th style="width: 60%">Exámenes</th>
            <th style="background-color: #1affff;width: 10%">Cantidad</th>
            <th style="background-color: #1affff:width: 10%">Valor</th>
          </tr>
        </thead>
        <tbody>
        @php $num = 0; @endphp
        @foreach($or_anio as $value)
          <tr role="row" >@php $num ++; @endphp
            
            <td style="text-align: left;">{{$value->nombre}}</td>
            <td>{{$value->cantidad}}</td>
            <td>$ {{number_format(round($value->valor,2))}}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </div>  
  <!--div class="col-md-12">
    <center>
      <canvas id="canvas3" style="max-width: 70%;height: 250px;"></canvas>
    </center>
  </div-->
</div>
<script type="text/javascript">
  $('#tabla_examenes').DataTable({
    'paging'      : true,
    'lengthChange': true,
    'searching'   : true,
    'ordering'    : true,
    'info'        : true,
    'autoWidth'   : false,
    'order'       : [[ 2, "desc" ]],
    "language": {
      "search": "Buscar:",
      "lengthMenu": "Mostrar _MENU_ Examenes" 
    },
    
  });
</script>
