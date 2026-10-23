@extends('hospital.base')
@section('action-content')
 <div class="modal fade" id="modaleditar" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    </div>
  </div>
</div>
<style>
  .box{
    border-color: #FDFEFE; border-radius: 30px;
  }
  h3{
    font-family: 'Montserrat Bold';
  }
  h1, input{
    font-family: 'Montserrat Medium';
    color: white;
  }
</style>
<div class="col-md-12">
    <div class="box">
      <div class="box-header with-border"style="border-radius: 30px; background-image: linear-gradient(to right, #3352ff 0%, #051eff 100%); margin-bottom: 5px">
        <h1 class="box-title">{{trans('emergencia.TraspasosSalas')}}</h1>
          </div>
          <div class="col-md-12">
            @if(Session('success'))
            <div class="alert alert-success">
            {{session('success')}}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
           </button>
            </div>
          @endif
        </div>
      <div  class="table-responsive col-md-12">
        <table  id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
          <thead class="thead-light">
          <label>{{trans('emergencia.Evolucionporpaciente')}}</label>
          <div class="row">
           <div class="col-md-3">
              @foreach($users as $value)
              <input  type="text" disabled class="form-control" value="{{$value->nombre1}} {{$value->nombre2}} {{$value->apellido1}} {{$value->apellido2}} ">
             @endforeach
             </div>
             <button type="button" onclick ="location.href='{{route('hospital.formulario05',$id_paciente)}}'" class="btn btn-danger btn-sm"><i class="far fa-arrow-alt-circle-left"></i>{{trans('emergencia.Regresar')}} </button>
          </div>
          <br>
            <div class="col-md-12">
              <div class="row">
               <label>{{trans('emergencia.Buscadorporpaciente')}}</label>
                 <form action="{{route('hospital.salas_resultado',$id_paciente)}}" method="GET" id="formulario">
                    <div class="col-md-2">
                        {{ Form::text('area_salas', null, ['class' => 'form-control', 'placeholder' => 'Area']) }}
                    </div>
                      <div class="col-md-2">
                        {{ Form::text('medicina_salas', null, ['class' => 'form-control', 'placeholder' => 'Medicinas']) }}
                      </div>
                          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i>{{trans('emergencia.Buscar')}} </button>
                          </button>
                        </form>
                      </div>
                   </div>
                 <br>
                  <tr>
                    <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >No</th>
                    <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >{{trans('emergencia.Fecha')}}</th>
                    <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >{{trans('emergencia.Area')}}</th>
                    <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >{{trans('emergencia.Medicina')}}</th>
                    <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >{{trans('emergencia.Descripcion')}}</th>
                    <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >{{trans('emergencia.Medico')}}</th>
                    <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >{{trans('emergencia.Accion')}}</th>
                  </tr>
                  @foreach($medicamento as $value)
                  <tr role="row">
                  <td>{{$value->id}}</td>
                  <td>{{$value->created_at}}</td>
                  <td>{{$value->area_salas}}</td>
                  <td>{{$value->medicina_salas}}</td>
                  <td>{{$value->descripcion_salas}}</td>
                  <td>{{$value->medico_salas}}</td>
                  <td><button type="button" data-remote="{{ route('hospital.editar_salas',['id'=>$value->id])}}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modaleditar"><i class="fas fa-pencil-alt"></i>{{trans('emergencia.Editar')}} </button></td>
                  </tr>
                  @endforeach
                </thead>
              </table>
               <div class="row">
                    <div class="col-sm-5">
                      <div class="dataTables_info" id="example2_info" role="status" aria-live="polite"> {{trans('emergencia.Mostrando')}}  1 / {{count($medicamento)}} de {{$medicamento->total()}} {{trans('emergencia.registros')}} </div>
                      </div>
                        <div class="col-sm-7">
                          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                            {{ $medicamento->links() }}
                          </div>
                        </div>
                       </div>
                      </div>
                    </div>
                  </div>
              @endsection
