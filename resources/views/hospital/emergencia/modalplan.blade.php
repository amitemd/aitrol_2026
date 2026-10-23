<div class="modal-content">
<div class="modal-header">
    <h5 class="modal-title" id="myModalDoctor">Resultados:</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  <table class="table table-hover">
    <thead class="thead-dark">
      <tr>
        <th scope="col">No</th>
        <th scope="col">Fecha</th>
        <th scope="col">Medico</th>
        <th scope="col">Descripcion</th>
      </tr>
    </thead>
    <tbody>
      @foreach($plan as $value)
      <tr>
        <th scope="row">1</th>
      <td>{{$value->created_at}}</td>
      <td>{{$value->medico_plan}}</td>
      <td>{{$value->descripcion_plan}}</td>
    </tr>
    @endforeach
    </tbody>
</table>
</div>