<div class="modal-content">
    <div class="modal-header" style="background: #3c8dbc;">
        <input type="hidden" name="empresacheck">
        <button style="line-height: 30px;" id="button" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:white;font-size:30pxt;font-weight:bold" class="modal-title">Editar un Horario</h3>
    </div>
    <div class="modal-body">
        <div class="col-md-12">
            <label for="">Seleccione lo que desea editar</label>
            <table>
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Fecha</th>
                            <th scope="col">Area</th>
                            <th scope="col">Observaciones</th>
                            <th scope="col">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($mantemientoHorario as $val)
                   
                        <tr>
                           <td>{{$val->created_at}}</td>
                           <td>{{$val->horario->nombre_sala}}</td>
                           <td>@if(!is_null($val->observaciones)){{$val->observaciones}} @else vacio @endif</td>
                           <td><a href="{{route('mantenimiento.editar_horario',['id'=>$val->id])}}" type="submit" class="btn btn-danger">Editar</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </table>
        </div>
    </div>
    <div class="modal-footer">

    </div>

</div>