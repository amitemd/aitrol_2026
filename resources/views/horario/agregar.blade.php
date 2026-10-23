<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
  <h4 class="modal-title" id="myModalLabel">Agregar Horario Recurrente</h4>
</div>
<div class="modal-body">
  <form method="post" action="{{route('horario.agregar_enviar2')}}" >
    {{csrf_field()}}
    <input type="hidden" name="inicio" value="{{$start}}">
    <input type="hidden" name="fin" value="{{$end}}">
    <div class="form-group col-md-6">
      <label for="" class="col-md-12 control-label">Inicio</label>
      <label for="" class="col-md-12 control-label">{{$inicio}}</label>
    </div> 
    <div class="form-group col-md-6">
      <label for="" class="col-md-12 control-label">Fin</label>
      <label for="" class="col-md-12 control-label">{{$fin}}</label> 
    </div> 

    <div class="form-group {{ $errors->has('tipo') ? ' has-error' : '' }}">
      <label for="tipo" class="col-md-4 control-label">Tipo de Horario</label>
      <div class="col-md-6">
        <select id="tipo" name="tipo" class="form-control" onchange="edad();">
          <option value="0">Todos</option> 
          <option value="1" >Consulta</option>
          <option value="2" >Procedimiento</option>
        </select>
      </div>
    </div>                            
    <button type="submit" class="btn btn-primary">Guardar</button>
  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
</div>