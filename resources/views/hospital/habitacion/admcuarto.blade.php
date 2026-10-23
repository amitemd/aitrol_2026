@extends('layouts.app-template-h')
@section('content')
<style>

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
  @php
    foreach($log as $value){
    $paciente= $value->paciente;
    }
  @endphp
  @php
    foreach($log as $value){
    $nombre= $value->paciente;
    }
   @endphp
<div class="content" id="area_cambiar">
  <div class="content-header">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-8 col-sm-6">
          <h3>{{trans('hospitalizacion.GESTI&Oacute;NDECUARTO')}}</h3>
        </div>
        <div class="col-4">
          <button type="button" class="btn btn-primary btn-sm" onclick="location.href='{{route('hospital.formulario053',['id_cama'=>$id_cama,'id'=>$paciente->id])}}'">
          {{trans('hospitalizacion.Formulario053')}}
          </button>
          <!--<button type="button" class="btn btn-success btn-sm" onclick="location.href='{{route('hospital.costo',['id'=>$paciente->id,'id_cama'=>$id_cama])}}'">
            Servicios Adicionales
          </button>-->
          <button type="button"
                  data-remote="{{ route('hospital.prescripcion',['id'=>$paciente->id])}}"
                  class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalprescripcion">{{trans('hospitalizacion.Dr:Prescripcion')}}
          </button>
          <button type="submit" form="form1" value="submit" class="btn btn-danger btn-sm">{{trans('hospitalizacion.LIBERARCUARTOaaa')}}</button> 
          <button type="button" onclick ="location.href='{{route('hospital.gcuartos')}}'" class="btn btn-primary btn-sm">{{trans('hospitalizacion.Regresar')}}</button>
        </div>
      </div>
    </div>
  </div>

  <!--COLLAPSE --->
  <!--DATOS PRINCIPALES DE PACIENTES-->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header with-border">
        <h1 class="card-title">{{trans('hospitalizacion.Datosprincipalesdepacientesss')}}</h1>
        <a data-action="collapse" class=""><svg xmlns="http://www.w3.org/2000/svg" width="16px" height="16px" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-chevron-down"><polyline points="6 9 12 15 18 9"></polyline>
        </svg></a>
      </div>
      <div class="card-content">
        <div class="card-body">
          <form action="{{route('hospital.eliminar')}}" id="form1" method="POST">
            {{ csrf_field() }}
            <div class="form-row">
              <div class="col-md-3 col-sm-6 col-3">
                <label for="fecha" class = "col-form-label-sm">Fecha de Ingreso:</label>
              </div>
              <div class="col-sm-3 col-6">
                <input type="text" id="fecha" class="form-control form-control-sm" value="{{$fecha->updated_at}}" readonly>
              </div>
              @php
                $fechaActual = date("Y-m-d H:i:s");
                $f1          = new DateTime($fechaActual);
                $f2          = new DateTime($fecha->updated_at);
                
                $d    = $f1->diff($f2);
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
                <label for="nombre" class = "col-form-label-sm">{{trans('hospitalizacion.Paciente')}}</label>
                <input type="text" id = "nombre" class = "form-control form-control-sm" value="{{($paciente->apellido1).' '.($paciente->apellido2).' '.($paciente->nombre1).' '.($paciente->nombre2)}}" placeholder = "Nombre de Paciente" readonly>
                <input  name="id_tipo" type="hidden" value="{{$id_habitacion}}">
                <input  name="id_cama" type="hidden" value="{{$id_cama}}">

              </div>
              <div class="form-group col-md-2 col-sm-3">
                <label for="indetificacion" class = "col-form-label-sm">{{trans('hospitalizacion.identificaci&oacute;n')}}</label>
                <input type="number" id = "indetificacion" class = "form-control form-control-sm" value="{{$paciente->id}}" placeholder = "C.I:" readonly >
              </div>
              <div class="form-group col-md-2 col-sm-3">
                <label for="edad" class = "col-form-label-sm">{{trans('hospitalizacion.Edad')}}</label>
                <input type="number" id = "edad" class = "form-control form-control-sm" value = "" placeholder = "Edad" readonly>
              </div>
              <div class="form-group col-md-2 col-sm-3">
                <label for="sexo" class = "col-form-label-sm">{{trans('hospitalizacion.Sexo')}}</label>
                <input type="text" id = "sexo" class = "form-control form-control-sm" value = "@if(($paciente->sexo) == 1) Masculino @else(($paciente->sexo) == 2) Femenino @endif" readonly>
              </div>
              <div class="form-group col-md-2 col-sm-3">
                <label for="cortesia" class = "col-form-label-sm">{{trans('hospitalizacion.Cortes&iacute;a')}}</label>
                <input type="text" id = "cortesia" class = "form-control form-control-sm" placeholder = "Cortesia" readonly>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="alergia" class = "col-form-label-sm">{{trans('hospitalizacion.ALERGIAS')}}</label>
                <textarea class="form-control" id="alergia" name="alergia" rows="3" value="{{$paciente->alergias}}" readonly></textarea>
              </div>
              <div class="form-group col-md-6">
                <label for="observacion" class = "col-form-label-sm">{{trans('hospitalizacion.OBSERVACIONES')}}</label>
                <textarea class="form-control" id="observacion" name="observacion" rows="3" value="{{$paciente->observacion}}"></textarea>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-4">
                <label for="antpato" class = "col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEPATOLÓGICOS')}}</label>
                <textarea class="form-control" id="antpato" name="antpato" rows="3" value="{{$paciente->antecedentes_pat}}" readonly></textarea>
              </div>
              <div class="form-group col-md-4">
                <label for="antfami" class = "col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEFAMILIARES)}}</label>
                <textarea class = "form-control" name="antfami" id="antfami" rows="3" value="{{$paciente->antecedentes_fam}}" readonly></textarea>
              </div>
              <div class="form-group col-md-4">
                <label for="antquiru" class = "col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEQUIRURGICOS)}}</label>
                <textarea class = "form-control" name="antquiru" id="antquiru" rows="3" value="{{$paciente->antecedentes_quir}}" readonly></textarea>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
  <!--Datos de filiacion-->
  <div class="col-md-12">
    <div class="card">
      <div class="card-header with-border">
        <h1 class="card-title">{{trans('hospitalizacion.Datosdefiliaci&oacute;n')}}</h1>
      </div>
      <div class="card-body">
        <div class="form-row">
          <div class="form-group col-md-3 col-sm-3">
              <label for="seguro" class = "col-form-label-sm">{{trans('hospitalizacion.SEGURO')}}</label>
              <input type="text" id = "seguro" value="" class = "form-control form-control-sm" value = "{{$paciente->id_seguro}}" placeholder = "Seguro" readonly>
          </div>
          <div class="form-group col-md-3 col-sm-3">
              <label for="estcivil" class = "col-form-label-sm">{{trans('hospitalizacion.ESTADOCIVIL')}}</label>
              <input type="text" id = "estcivil" class = "form-control form-control-sm" value = "@if(($paciente->estadocivil) == 1) soltero @elseif(($paciente->estadocivil) == 2) casado @elseif(($paciente->estadocivil) == 3) viduo @elseif(($paciente->estadocivil) == 4) divorciado @elseif(($paciente->estadocivil) == 5) union libre @elseif(($paciente->estadocivil) == 6) union de hecho @endif" placeholder = "Estado Civil" readonly>
          </div>
          <div class="form-group col-md-3 col-sm-3">
              <label for="telefono" class = "col-form-label-sm">{{trans('hospitalizacion.TELEFONO')}}</label>
              <input type="number" id = "telefono" class = "form-control form-control-sm" value = "{{$paciente->telefono1}}" placeholder = "telefono" readonly>
          </div>
          <div class="form-group col-md-3 col-sm-3">
              <label for="celular" class = "col-form-label-sm">{{trans('hospitalizacion.CELULAR')}}</label>
              <input type="number" id = "celular" class = "form-control form-control-sm" value = "{{$paciente->telefono2}}" placeholder = "Celular" readonly>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-3 col-sm-6">
              <label for="ciudadpro" class = "col-form-label-sm">{{trans('hospitalizacion.CIUDADDEPROCEDENCIA)}}</label>
              <input type="text" id = "ciudadpro" class = "form-control form-control-sm" value = "{{$paciente->ciudad}}" placeholder = "Ciudad de procedencia" readonly>
          </div>
          <div class="form-group col-md-3 col-sm-6">
              <label for="fechadenacimi" class = "col-form-label-sm">{{trans('hospitalizacion.FECHADENACIMIENTO)}}</label>
              <input type="text" id = "fechadenacimi" class = "form-control form-control-sm" value = "{{$paciente->fecha_nacimiento}}" placeholder = "Fecha de nacimiento" readonly>
          </div>
          <div class="form-group col-md-3 col-sm-6">
              <label for="ciudadnacimi" class = "col-form-label-sm">{{trans('hospitalizacion.CIUDADDENACIMIENTO)}}</label>
              <input type="text" id = "ciudadnacimi" class = "form-control form-control-sm" value = "{{$paciente->lugar_nacimiento}}" placeholder = "Ciudad de nacimiento" readonly>
          </div>
          <div class="form-group col-md-2 col-sm-6">
              <label for="religion" class = "col-form-label-sm">{{trans('hospitalizacion.RELIGIÓN)}}</label>
              <input type="text" id = "religion" class = "form-control form-control-sm" value = "{{$paciente->religion}}" placeholder = "Religión" readonly>
          </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3 col-sm-4">
                <label for="ocupacion" class = "col-form-label-sm">{{trans('hospitalizacion.OCUPACIÓN)}}</label>
                <input type="text" id = "ocupacion" class = "form-control form-control-sm" value = "{{$paciente->ocupacion}}" placeholder = "Ocupacion" readonly>
            </div>
            <div class="form-group col-md-3 col-sm-4">
                <label for="trabajo" class = "col-form-label-sm">{{trans('hospitalizacion.TRABAJO)}}</label>
                <input type="text" id = "trabajo" class = "form-control form-control-sm" value = "{{$paciente->trabajo}}" placeholder = "Trabajo" readonly>
            </div>
            <div class="form-group col-md-3 col-sm-4">
                <label for="sanguineo" class = "col-form-label-sm">{{trans('hospitalizacion.GRUPOSANGUINEO)}}</label>
                <input type="text" id = "sanguineo" class = "form-control form-control-sm" value = "{{$paciente->gruposanguineo}}" readonly>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="email" class = "col-form-label-sm">{{trans('hospitalizacion.}CORREO ELECTR&Oacute;NICO)}</label>
                <input type="email" id = "email" class = "form-control form-control-sm" placeholder = "Email" readonly>
            </div>
            <div class="form-group col-md-3">
                <label for="direccion" class = "col-form-label-sm">{{trans('hospitalizacion.DIRECCIÓN}}</label>
                <input type="text" id = "direccion" class = "form-control form-control-sm" value = "{{$paciente->direccion}}" placeholder = "Dirección" readonly>
            </div>
            <div class="form-group col-md-6">
                <label for="observaciones" class = "col-form-label-sm">{{trans('hospitalizacion.OBSERVACIONES}}</label>
                <textarea class="form-control" id="observaciones" name="observacion" rows="3" value = "{{$paciente->observacion}}"></textarea>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-3">
                <input type="text" id = "transfusion" class = "form-control form-control-sm" value = "{{$paciente->transfusion}}" placeholder = "Transfusiones" readonly>
            </div>
            <div class="form-group col-md-3">
                <label for="habito" class = "col-form-label-sm">{{trans('hospitalizacion.H&Aacute;BITOS}}</label>
                <input type="text" id = "habito" class = "form-control form-control-sm" placeholder = "Hábito" readonly>
            </div>
            <div class="form-group col-md-3">
                <label for="referencia" class = "col-form-label-sm">{{trans('hospitalizacion.REFERENCIA}}</label>
                <input type="text" id = "referencia" class = "form-control form-control-sm" placeholder = "Referencia del paciente" readonly>
            </div>
            <div class="form-group col-md-3">
                <label for="vacuna" class = "col-form-label-sm">{{trans('hospitalizacion.VACUNA}}</label>
                <input type="text" id = "vacuna" class = "form-control form-control-sm" value = "{{$paciente->vacuna}}" placeholder = "Vacuna" readonly>
            </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
              <label for="antpatof" class = "col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEPATOLÓGICOS}}</label>
              <textarea class="form-control" id="antpatof" name="antpatof" rows="3" value="{{$paciente->antecedentes_pat}}"></textarea>
          </div>
          <div class="form-group col-md-4">
              <label for="antfamif" class = "col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEFAMILIARES}}</label>
              <textarea class = "form-control" name="antfamif" id="antfamif" rows="3" value="{{$paciente->antecedentes_fam}}"></textarea>
          </div>
          <div class="form-group col-md-4">
              <label for="antquiruf" class = "col-form-label-sm">{{trans('hospitalizacion.ANTECEDENTEQUIRURGICOS}}</label>
              <textarea class = "form-control" name="antquiruf" id="antquiruf" rows="3" value="{{$paciente->antecedentes_quir}}"></textarea>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--Evolucion por paciente-->

</div>

<script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js'></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>

<script type="text/javascript">
  jQuery('body').on('click', '[data-toggle="modal"]', function() {
    if(remoto_href != jQuery(this).data('remote')) {
      remoto_href = jQuery(this).data('remote');
      jQuery(jQuery(this).data('target')).removeData('bs.modal');

      jQuery(jQuery(this).data('target')).find('.modal-body').empty();
      jQuery(jQuery(this).data('target') + ' .modal-content').load(jQuery(this).data('remote'));
    }
	});
</script>

@endsection
