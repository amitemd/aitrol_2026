<div class="modal fade" id="editMaxPacientes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document" style="width: 60%;">
    <div class="modal-content" >

    </div>
  </div>
</div>
@if($general->estado == 1)
<div class="col-md-3 col-md-offset-9">
  <a id="agregar" href="{{ route('plan_cuentas.nuevo_padre', ['id' => $general->id_plan]) }}" data-toggle="modal" data-target="#editMaxPacientes" class="btn btn-primary btn-xs">Agregar Nuevo Elemento</a>
</div>
@endif
<br><br>
<table id="tabla_elementos" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
  <thead>
    <tr >
      <th  width="20%">Codigo</th>
      <th  width="40%">Nombre</th>
      <th  width="20%">Modificado</th>
      <th  width="20%">Usu. Modifica</th>
    </tr>
  </thead>
  <tbody>
    @if(count($cuenta) == 0)
    <tr>
      <td colspan="4">No Posee Subcuentas</td>
    </tr>
    @endif
    @foreach($cuenta as $value)
    <tr>
      <td>{{$value->plan}}</td>
      <td>{{$value->nombre}}</td>
      <td>{{$value->updated_at}}</td>
      <td>{{substr($value->modifica->nombre1, 0, 1)}}.{{$value->modifica->apellido1}}</td>
    </tr>
    @endforeach
  </tbody>
</table>
<script type="text/javascript">
  function nuevo() {
    cuenta = $('#id_cuenta').val();
    tipo = $('#tipo').val();
    if(tipo != 1){
      alert('La cuenta seleccionada no es un grupo');
    }else{
      $("#agregar").click();
    }
  }
  $('#editMaxPacientes').on('hidden.bs.modal', function(){
      $(this).removeData('bs.modal');
  });
</script>
