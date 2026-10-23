@extends('hospital_admin.base')
@section('action-content')

<div class="row">
    <div class="col-md-12">
        <!-- Collapsable Menú -->
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#listaplatos" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="listaplatos">
                <h6 class="m-0 font-weight-bold text-primary">Datos de Paciente</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="listaplatos">
                <div class="card-body">
                  <form method="POST" action="{{ route('hospital_admin.editar',$agenda->id)}}"  role="form">
                    {{ csrf_field() }}
                    <div class="form-row">
                      <div class="form-group col-md-3">
                        <label>Cedula</label>
                        <input type="text" class="form-control" id="nombre" value="{{$agenda->id_paciente}}" disabled>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Nombres Apellidos</label>
                        <input type="text" class="form-control" id="nombre" value="{{$agenda->paciente->nombre1}} {{$agenda->paciente->nombre2}} {{$agenda->paciente->apellido1}} {{$agenda->paciente->nombre2}}" disabled>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Fecha de inicio</label>
                        <input type="text" class="form-control" id="fechaini" value="{{$agenda->fechaini}}" disabled>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Fecha final</label>
                        <input type="text" class="form-control" id="fechafin" value="{{$agenda->fechafin}}" disabled>
                      </div>
                    </div>
                    <div class="form-row">
                      <div class="form-group col-md-3">
                        <label>Opercación</label>
                        <input type="text" class="form-control" id="operacion" value="{{$agenda->observaciones}}" disabled>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Doctor</label>
                        <input type="text" class="form-control" value="{{$agenda->doctor->nombre1}} {{$agenda->doctor->nombre2}} {{$agenda->doctor->apellido1}} {{$agenda->doctor->apellido2}}"  id="doctor" disabled >
                      </div>
                      <div class="form-group col-md-3">
                        <label>Estado</label>
                        <select class="custom-select mr-sm-2" name="estado" id="estado">
                          <option selected>OPCION...</option>
                          <option value="1">PREPARACION</option>
                          <option value="2">OPERACION</option>
                          <option value="3">RECUPERACION</option>
                        </select>
                      </div>
                      <div class="form-group col-md-3">
                        <label>Costo</label>
                        <input type="text" class="form-control" id="fechafin" value="{{$agenda->costo}}" name="costo" >
                      </div>
                    </div>
                    <a type="button" href="{{ route('hospital_admin.gestionqui')}}" class="btn btn-danger"><i class="far fa-trash-alt"></i> Cancelar</a>
                    <button type="submit" class="btn btn-warning"><i class="far fa-edit"></i> Editar</button>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection