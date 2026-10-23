@extends('layouts.app-template-h')
@section('content')

<div class="content">

  <section class="content-header">
    <div class="row">
      <div class="col-md-9 col-sm-9">
        <h3>
          {{trans('emergencia.IngresoporEmergencia')}}
          <small>{{trans('emergencia.Pordia')}}</small>
        </h3>
      </div>
      <div class="col-3">
        <a href="{{route('hospital.formulariomanchester') }}" class="btn btn-sm btn-warning mb-2">{{trans('emergencia.NuevoTriaje')}}</a>
        <a href="{{route('hospital.ingreso008',['id_solicitud' => '0']) }}" class="btn btn-sm btn-danger mb-2"><i class="fas fa-user-plus"></i>{{trans('emergencia.NuevoIngreso')}}</a>

      </div>
    </div>
  </section>
  <div class="card card-primary">
    <div class="card-header with-border">
      <h3 class="card-title">{{trans('emergencia.OrdendeAtencionEmergencia')}}</h3>
      <div class="card-tools pull-right">
        <button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      </div>
    </div>

    <div class="card-body">
      <div id="muestra" class="card-body table-responsive">
        <table class="table table-hover">
          <thead class="table-primary">
            <tr>
              <th scope="col">{{trans('emergencia.Fecha')}}</th> <!-- trans-->
              <th scope="col">{{trans('emergencia.CedulaPaciente')}}</th>
              <th scope="col">{{trans('emergencia.NombredelPaciente')}}</th>
              <th scope="col">{{trans('emergencia.TipodEmergencia')}}</th>
              <th scope="col">{{trans('emergencia.Priorid')}}</th>
              <th scope="col">{{trans('emergencia.TiempodeEspera')}}</th>
              <th scope="col">{{trans('emergencia.FormulariodeIngreso')}}</th>
            </tr>
          </thead>



          @foreach($ho_triaje as $ho_t)
          @if(!is_null($ho_t))

          @php

          $fec=new DateTime($ho_t->fecha_ingreso);
          $fec2=new DateTime(date('Y-m-d H:i:s'));
          $diff = $fec->diff($fec2);
          $duracion = $diff->days * 24 * 60;
          $duracion += $diff->h * 60;
          $duracion += $diff->i;
          @endphp

          <tr>
            <td>{{ $ho_t->fecha_ingreso}}</td>
            <td>{{ $ho_t->id_paciente }}</td>
            <td>{{ $ho_t->nombre1 }} {{ $ho_t->nombre2 }} {{ $ho_t->apellido1 }} {{ $ho_t->apellido2 }}</td>
            <td>{{ $ho_t->nombre_emergencia}}</td>
            <td @if($ho_t->prioridad == 5) style="background-color: #1F57A9; color: white" @elseif($ho_t ->prioridad == 4)
              style="background-color: #0DD119; color: white" @elseif($ho_t->prioridad == 3) style="background-color: #FFE400; color: white" @elseif($ho_t->prioridad == 2) style="background-color: #FF6C00; color: white" @elseif($ho_t->prioridad == 1) style="background-color: #FF0800; color: white" @endif> {{$ho_t->nombre_prioridad}}
            </td>
            <td style="text-align:center;">
              @if($ho_t->prioridad == 5) @if($duracion > 120) <button class="btn btn-danger" type="button"> Tiempo Expirado</button> @endif <br> >120 Minutos
              @elseif($ho_t ->prioridad == 4) @if($duracion >= 120) <button class="btn btn-danger" type="button"> Tiempo Expirado</button> @endif <br> 120 Minutos
              @elseif($ho_t->prioridad == 3) @if($duracion >= 60) <button class="btn btn-danger" type="button"> Tiempo Expirado</button> @endif <br> 60 Minutos
              @elseif($ho_t->prioridad == 2) @if($duracion >= 10) <button class="btn btn-danger" type="button"> Tiempo Expirado</button> @endif <br> 10 Minutos
              @elseif($ho_t->prioridad == 1) Inmediato
              @endif

            </td>
            <td>
              <a href="{{route('hospital.resultadomanchester',$ho_t->id)}}" class="btn btn-sm btn-success"><i class="far fa-file-alt"></i>{{trans('emergencia.DatosTriaje')}}</a>
              <a href="{{route('hospital.ingreso008',['id_solicitud' => $ho_t->id_solicitud]) }}" class="btn btn-sm btn-danger"><i class="fas fa-user-plus"></i>{{trans('emergencia.NuevoIngreso')}}</a>
            </td>
          </tr>
          @endif
          @endforeach
        </table>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="card card-primary">
        <div class="card-header with-border">
          <h3 class="card-title">{{trans('emergencia.ListadePacienteIngresadoaEmergencia')}}</h3>

          <div class="card-tools pull-right">
            <button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <!-- /.card-header -->
        <div class="card-body">
          <div id="muestra" class="card-body table-responsive">
            <table class="table table-hover">
              <thead class="table-primary">
                <tr>
                  <!-- <th>#</th> -->
                  <th scope="col">{{trans('emergencia.FechayHoradeingreso')}}</th>
                  <th scope="col">{{trans('emergencia.CedulaPaciente')}}</th>
                  <th scope="col">{{trans('emergencia.NombredelPaciente')}}</th>
                  <th scope="col">{{trans('emergencia.FormulariodeIngresoyEvoluciones')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($ho_solicitud as $ho_s)
                <tr>
                  <td>{{$ho_s->fecha_ingreso}}</td>
                  <td>{{$ho_s->id_paciente}}</td>
                  <td>{{$ho_s->apellido1}} {{$ho_s->apellido2}} {{$ho_s->nombre1}} {{$ho_s->nombre2}}</td>
                  <td>
                    <a href="{{route('hospital.detallep',['id_paciente' => $ho_s->id])}}" class="btn btn-sm btn-primary"><i class="far fa-file-alt"></i> {{trans('emergencia.Atencion')}}</a>
                    <a href="{{route('hospital.formulario008_pdf', ['id_paciente' => $ho_s->id])}}" target="_blank" class="btn btn-sm btn-primary"><i class="fa fa-download"></i> {{trans('emergencia.Formulario008')}}</a>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
        <!-- /.card-body -->
        <div class="card-footer clearfix">

        </div>
      </div>
      <!-- /.card -->
    </div>

  </div>
</div>

@endsection
