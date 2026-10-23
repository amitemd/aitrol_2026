<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="myModalCosto">{{trans('hospitalizacion.CostodeAlojamiento')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{ route('hospital.costo_alojamiento')}}" method="post">
        <div class="modal-body">
            {{ csrf_field() }}
            <div class="form-group row">
                <label class="col-sm-2 col-form-label" style="font-size: 13px;">{{trans('hospitalizacion.Paciente')}}</label>
                <div class="col-sm-5">
            <input type="text" class="form-control form-control-sm" name="paciente" id="paciente" disabled value="{{$nombre->nombre1}} {{$nombre->nombre2}} {{$nombre->apellido1}} {{$nombre->apellido2}}">
                </div>
                <input type="text" name="fecha" id="fecha" class="col-md-2 form-control form-control-sm">
                <label class="col-sm-2 col-form-label" style="font-size: 12px;"><?php  $fechaActual = date('d-m-Y'); echo  $fechaActual  ?></label>
            </div>
            <table class="table table-sm table-bordered">
                <thead class="table-primary">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{trans('hospitalizacion.Categoria')}}</th>
                        <th scope="col">{{trans('hospitalizacion.Habitación')}}</th>
                        <th scope="col">{{trans('hospitalizacion.Díasdehospedaje')}}</th>
                        <th scope="col">{{trans('hospitalizacion.Preciounitario')}}</th>
                        <th scope="col">{{trans('hospitalizacion.Total')}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">1</th>
                        <td>{{trans('hospitalizacion.Simple')}}</td>
                        <td>102</td>
                        <td>7</td>
                        <td>$80</td>
                        <td>$560.00</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-secondary" data-dismiss="modal">{{trans('hospitalizacion.Cerrar')}}</button>
        </div>
    </form>
</div>
