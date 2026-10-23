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
        <h1 class="box-title">{{trans('translab.Plan')}}</h1>
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
          <label>{{trans('translab.Evolucionporpaciente')}}</label>
          <div class="row">
           <div class="col-md-3">
              @foreach($users as $value)
              <input  type="text" disabled class="form-control" value="{{$value->nombre1}} {{$value->nombre2}} {{$value->apellido1}} {{$value->apellido2}} ">
             @endforeach
             </div>
             <button type="button" onclick ="location.href='{{route('hospital.formulario05',$id_paciente)}}'" class="btn btn-danger btn-sm"><i class="far fa-arrow-alt-circle-left"></i> {{trans('translab.Regresar')}}</button>
          </div>
          <br>
            <div class="col-md-12">
              <div class="row">
               <label>{{trans('translab.Buscadorporpaciente')}}</label>
                 <form action="{{route('hospital.resultado_plan',$id_paciente)}}" method="GET" id="formulario">
                    <div class="col-md-2">
                        {{ Form::text('medico_plan', null, ['class' => 'form-control', 'placeholder' => 'Nombre Medico']) }}
                    </div>
                      <div class="col-md-2">
                        {{ Form::text('descripcion_plan', null, ['class' => 'form-control', 'placeholder' => 'Descripci&oacute;n']) }}
                      </div>
                          <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> {{trans('translab.Buscar')}}</button>
                          </button>
                        </form>
                      </div>
                   </div>
                    <br>
                <tr>
                  <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >No:</th>
                  <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >{{trans('translab.Medico')}} </th>
                  <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >{{trans('translab.Preescripcion')}}</th>
                  <th width="20%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >{{trans('translab.Fechadeevolucion')}}</th>
                  <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending" >{{trans('translab.Accion')}}</th>
                </tr>
                @foreach($tratamiento as $value)
                <tr role="row">
                <td>{{$value->id}}</td>
                <td>{{$value->medico_plan}}</td>
                <td>{{$value->descripcion_plan}}</td>
                <td>{{$value->created_at}}</td>
                <td><button type="button" data-remote="{{ route('hospital.modal_editarplan',['id'=>$value->id])}}" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modaleditar">{{trans('translab.Editar')}}</button></td>
                </tr>
                @endforeach
              </thead>
            </table>
             <div class="row">
              <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('translab.Mostrando')}} 1 / {{count($tratamiento)}} de {{$tratamiento->total()}} {{trans('translab.registros')}}</div>
                </div>
                  <div class="col-sm-7">
                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                      {{ $tratamiento->links() }}
                    </div>
                  </div>
                 </div>
              </div>
            </div>
          </div>
        @endsection
