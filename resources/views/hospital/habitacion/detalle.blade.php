@extends('layouts.app-template-h')
@section('content')
<style>
  /*Importante en la modal*/
  .modal-backdrop {
    opacity: 0.0 !important;
  }
</style>

<div class="modal fade" id="modalprescripcion" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal fade" id="modalenfermeria" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalenfermeria" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>
<div class="modal fade" id="modalnew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="date">

    </div>
  </div>
</div>

<div class="modal fade" id="modalimagenes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" id="enferm">

    </div>
  </div>
</div>

<div class="content" id="area_cambiar">
  <div class="content-header">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-8 col-sm-6">
          <h3>{{trans('hospitalizacion.GESTI&Oacute;NDECUARTO')}}</h3>
        </div>
        <div class="col-4">
          <!--<button type="button" class="btn btn-success btn-sm" onclick="location.href='{{route('hospital.costo',['id'=>$paciente->id,'id_cama'=>$cama->id])}}'">
            Servicios Adicionales
          </button>-->
          <!--           <button class="btn btn-danger btn-sm" type="button"> <i class="fa fa-notes-medical"></i> </button>
          <button type="button" data-remote="{{ route('hospital.prescripcion',['id'=>$paciente->id])}}" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalprescripcion">Dr: Prescripcion
          </button> -->
          <!--button type="button" class="btn btn-warning btn-sm" onclick="modalinfermeria()">{{trans('hospitalizacion.IMAGENES')}}</button>
          <button type="button" class="btn btn-primary btn-sm" onclick="modalshow()">{{trans('hospitalizacion.CIRUGIA')}}</button-->
          <button type="submit" form="form1" value="submit" class="btn btn-danger btn-sm">{{trans('hospitalizacion.LIBERARCUARTO')}}</button>
          <button type="button" onclick="location.href='{{route('hospital.gcuartos')}}'" class="btn btn-primary btn-sm">{{trans('hospitalizacion.Regresar')}}</button>
        </div>
      </div>
    </div>
  </div>

  <!--COLLAPSE --->
  <!--DATOS PRINCIPALES DE PACIENTES-->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header with-border">
        <h1 class="card-title">{{trans('hospitalizacion.Datosprincipalesdepaciente')}}</h1>
        <a data-action="collapse" class=""><svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down">
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg></a>
      </div>
      <div class="card-content">
        <div class="card-body">
          <form action="{{route('hospital.eliminar')}}" id="form1" method="POST">
            {{ csrf_field() }}
            <div class="form-row">
              <div class="col-md-3 col-sm-6 col-3">
                <label for="fecha" class="col-form-label-sm">{{trans('hospitalizacion.FechadeIngreso:')}}</label>
              </div>
              <div class="col-sm-3 col-6">
                <input type="text" id="fecha" class="form-control form-control-sm" value="{{$hospitalizacion->fecha}}" readonly>
              </div>
              @php
              $fechaActual = date("Y-m-d H:i:s");
              $f1 = new DateTime($fechaActual);
              $f2 = new DateTime($hospitalizacion->fecha);

              $d = $f1->diff($f2);
              $dias = 0;
              if ($d->format('%d') > 0) {
              $dias = $dias + ($d->format('%d'));
              }
              if ($d->format('%m') > 0) {
              $dias = $dias + ($d->format('%m'));
              }
              if ($d->format('%y') > 0) {
              $dias = $dias + ($d->format('%y'));
              }
              @endphp
              <label class="col-3" style=" color:white;background-color: @if($dias <= 3) green @elseif($dias <= 7) yellow @else red @endif; text-align: center;">
                @php
                echo $dias;
                @endphp
                {{trans('hospitalizacion.diasingresados')}}</label>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="nombre" class="col-form-label-sm">{{trans('hospitalizacion.Paciente')}}</label>
                <input type="text" id="nombre" class="form-control form-control-sm" value="{{($paciente->apellido1).' '.($paciente->apellido2).' '.($paciente->nombre1).' '.($paciente->nombre2)}}" placeholder="Nombre de Paciente" readonly>
                <input id="id_tipo" name="id_tipo" type="hidden" value="{{$cama->habitacion->tipo->id}}">
                <input id="id_cama" name="id_cama" type="hidden" value="{{$cama->id}}">
                <input type="hidden" name="id_hospitalizacion" id="id_hospitalizacion" value="{{$hospitalizacion->id}}">

              </div>
              <div class="form-group col-md-2 col-sm-3">
                <label for="indetificacion" class="col-form-label-sm">{{trans('hospitalizacion.identificaci&oacute;n')}}</label>
                <input type="number" name="indetificacion" id="indetificacion" class="form-control form-control-sm" value="{{$paciente->id}}" placeholder="C.I:" readonly>
              </div>
              <div class="form-group col-md-2 col-sm-3">
                <label for="edad" class="col-form-label-sm">{{trans('hospitalizacion.Edad')}}</label>
                <input type="number" id="edad" class="form-control form-control-sm" value="" placeholder="Edad" readonly>
              </div>
              <div class="form-group col-md-2 col-sm-3">
                <label for="sexo" class="col-form-label-sm">{{trans('hospitalizacion.Sexo')}}</label>
                <input type="text" id="sexo" class="form-control form-control-sm" value="@if(($paciente->sexo) == 1) Masculino @else(($paciente->sexo) == 2) Femenino @endif" readonly>
              </div>
              <div class="form-group col-md-2 col-sm-3">
                <label for="cortesia" class="col-form-label-sm">{{trans('hospitalizacion.Cortes&iacute;a')}}</label>
                <input type="text" id="cortesia" class="form-control form-control-sm" placeholder="Cortesia" readonly>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="alergia" class="col-form-label-sm">{{trans('hospitalizacion.ALERGIAS')}}</label>
                <textarea class="form-control" id="alergia" name="alergia" rows="3" readonly> @foreach($paciente->a_alergias as $x) {{$x->principio_activo->nombre."\n"}} @endforeach </textarea>
              </div>
              <div class="form-group col-md-6">
                <label for="observacion" class="col-form-label-sm">{{trans('hospitalizacion.OBSERVACIONES')}}</label>
                <textarea class="form-control" id="observacion" name="observacion" rows="3" value="{{$hospitalizacion->detalle}}"></textarea>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="antpato" class="col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEPATOLÓGICOS')}}</label>
                <textarea class="form-control" id="antpato" name="antpato" rows="3" value="{{$paciente->antecedentes_pat}}" readonly></textarea>
              </div>
              <div class="form-group col-md-4">
                <label for="antfami" class="col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEFAMILIARES')}}</label>
                <textarea class="form-control" name="antfami" id="antfami" rows="3" value="{{$paciente->antecedentes_fam}}" readonly></textarea>
              </div>
              <div class="form-group col-md-4">
                <label for="antquiru" class="col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEQUIRURGICOS')}}</label>
                <textarea class="form-control" name="antquiru" id="antquiru" rows="3" value="{{$paciente->antecedentes_quir}}" readonly></textarea>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
  <!--Datos de filiacion-->
  <!--Evolucion por paciente-->
</div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>

<script type="text/javascript">
  jQuery('body').on('click', '[data-toggle="modal"]', function() {
    if (remoto_href != jQuery(this).data('remote')) {
      remoto_href = jQuery(this).data('remote');
      jQuery(jQuery(this).data('target')).removeData('bs.modal');

      jQuery(jQuery(this).data('target')).find('.modal-body').empty();
      jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
    }
  });

  function modalshow() {
    $.ajax({
      type: 'get',
      url: "{{ route('hospitalizacion.modal_quirofano')}}",
      data: {
        'id_tipo': $('#id_tipo').val(),
        'id_cama': $('#id_cama').val(),
        'id_paciente': $('#indetificacion').val(),
        'id_hospitalizacion': $('#id_hospitalizacion').val()
      },
      datatype: 'json',
      success: function(data) {
        $('#date').empty().html(data);
        $('#modalnew').modal();
      },
      error: function(data) {
        //console.log(data);
      }
    });
  }

  function modalinfermeria() {
    var form = $("#form1");
    $.ajax({
      type: 'get',
      url: "{{ route('cuartos.imagenes')}}",
      data: form.serialize(),
      datatype: 'json',
      success: function(data) {
        $('#enferm').empty().html(data);
        $('#modalimagenes').modal();
      },
      error: function(data) {
        //console.log(data);
      }
    });
  }
</script>

@endsection