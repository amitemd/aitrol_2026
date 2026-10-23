@extends('layouts.app-template')
@section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      
        <h1 class="col-md-9">
         Ingreso de Planillas @yield('tipo-planilla', 'IESS')
        </h1>
      @php
        $ruta = Cookie::get('planillas');

      @endphp
        @if($ruta == 'generar')
          <a type="button" href="{{route('planilla.genera_planillas')}}" class="btn btn-success btn-sm">
                  <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
          </a>
        @else 
        <a type="button" href="{{route('planilla.planillas_generadas')}}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
        </a>
        @endif
      <!--ol class="breadcrumb">
        
        <li><a href="{{asset('/')}}" style="color: blue;"><i class="fa fa-home"></i> Inicio</a></li>
        <li><a href="{{route('agenda.agenda2')}}" style="color: blue;"></i> Agenda</a></li>
      </ol-->
    </section>
    @yield('action-content')
    <!-- /.content -->
  </div>
@endsection
