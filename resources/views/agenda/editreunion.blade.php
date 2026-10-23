<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span></button>
    @php $sala=Sis_medico\sala::find($agenda->id_sala) @endphp
    <h4 class="modal-title" id="myModalLabel">Reunión Programada en {{$sala->nombre_sala}}/{{Sis_medico\hospital::find($sala->id_hospital)->nombre_hospital}}</h4>
</div>
<div class="modal-body">
  <form method="post" action="{{route('agenda.updatereunion', ['id' => $agenda->id])}}">
    {{csrf_field()}}
    <div class="form-group col-md-12">
      <label for="observaciones" class="col-md-12 control-label">Motivo Suspensión</label>
      <input id="observaciones" type="text" class="form-control" name="observaciones" value="{{old('observaciones')}}" required>
    </div>  
    
                           
    <button type="submit" class="btn btn-primary">Suspender</button>


  </form>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>