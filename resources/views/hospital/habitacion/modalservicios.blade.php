<div class="modal-content">
  <div class="modal-header">
    <h5 class="modal-title" id="myModalServicio">{{trans('hospitalizacion.ServicioAdicionales')}}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
    <form action="{{route('hospital.salvar')}}" method="POST" id="formulario">
        <div class="modal-body">
            {{ csrf_field() }}
            <div class="form-group">
                <label>{{trans('hospitalizacion.Paciente')}}</label>
                <input disabled value="{{$servicio->nombre1}} {{$servicio->nombre2}} {{$servicio->apellido1}} {{$servicio->apellido2}}" type="text" class="form-control" name="nombre" id="nombre">
            <input type="hidden" name="pacienterefri" id="pacienterefri" value="{{$servicio->id}}">
                <small class="form-text text-muted">{{trans('hospitalizacion.Nombredelpacienteasignadoaestahabitación')}}</small>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label>{{trans('hospitalizacion.Desayunos')}}</label>
                            <select id="desayunos"  name="desayunos" class="form-control">
                                <option selected>{{trans('hospitalizacion.Seleccionar...')}}</option>
                                <option>{{trans('hospitalizacion.salmónahumado')}}</option>
                                <option>{{trans('hospitalizacion.Pitaconguacamole')}}</option>
                                <option>{{trans('hospitalizacion.Bocadillovegetal')}}</option>
                                <option>{{trans('hospitalizacion.Avenaconmango')}}</option>
                                <option>{{trans('hospitalizacion.Crepsligeros')}}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label>{{trans('hospitalizacion.Cantidad')}}</label>
                            <input name="cantidad_desayuno" id="cantidad_desayuno" type="number" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label>{{trans('hospitalizacion.Precio')}}</label>
                            <input name="precio_desayuno" id="precio_desayuno" type="number" class="form-control">
                        </div>
                        
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label>{{trans('hospitalizacion.Almuerzo')}}</label>
                            <select name="almuerzo" class="form-control">
                            <option>{{trans('hospitalizacion.Ensaladaasiáticadepollo')}}</option>
                            <option>{{trans('hospitalizacion.Boldeburritos')}}</option>
                            <option>{{trans('hospitalizacion.Arrozfritoconverduras')}}</option>
                            <option>{{trans('hospitalizacion.Sopadetomate')}}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label>{{trans('hospitalizacion.Cantidad')}}</label>
                            <input name="cant_almuerzo" id="cant_almuerzo" type="number" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label>{{trans('hospitalizacion.Precio')}}</label>
                            <input name="precio_almuerzo" id="precio_almuerzo" type="number" class="form-control">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-7">
                            <label>{{trans('hospitalizacion.Cena')}}</label>
                            <select name="cena" class="form-control">
                            <option>{{trans('hospitalizacion.Cremadejudías')}}</option>
                            <option>{{trans('hospitalizacion.Salmónconverduritas')}}</option>
                            <option>{{trans('hospitalizacion.Pescadosalteado')}}</option>
                            <option>{{trans('hospitalizacion.Cuscúsconverduras')}}</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <label>{{trans('hospitalizacion.Cantidad')}}</label>
                            <input name="cantidad_cena" id="cantidad_cena" type="number" class="form-control">
                        </div>
                        <div class="form-group col-md-2">
                            <label>{{trans('hospitalizacion.Precio')}}</label>
                            <input  name="precio_cena" id="precio_cena"  type="number" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>{{trans('hospitalizacion.Bebidas')}}</label>
                        <select name="bebidas" class="form-control">
                            <option>{{trans('hospitalizacion.Jugosmoras')}}</option>
                            <option>{{trans('hospitalizacion.Jugosdenaraja')}}</option>
                        </select>
                    </div>
                    <div class="form-group">
                       <label for="exampleFormControlInput1">{{trans('hospitalizacion.Cantidad')}}</label>
                        <input name="cantidad_bebi" id="cantidad_bebi" type="number" class="form-control" placeholder="Cantidad">
                    </div>
                    <div class="form-group">
                       <label for="exampleFormControlInput1">{{trans('hospitalizacion.Precio')}}</label>
                        <input name="precio_bebi" id="precio_bebi" type="number" class="form-control" placeholder="Cantidad">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-secondary" data-dismiss="modal">{{trans('hospitalizacion.Cerrar')}}</button>
            <button type="submit" class="btn btn-danger">{{trans('hospitalizacion.Agregar')}}</button>
        </div>
    </form>
</div>