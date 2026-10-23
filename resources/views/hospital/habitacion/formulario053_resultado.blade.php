@extends('hospital.base')
@section('action-content')
@php
    foreach($log as $value){
    $paciente= $value->paciente;
    }
 @endphp

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<div class="content">

  <section class="content-header">
        <div class="row">
            <div class="col-md-10 col-sm-10">
                <h3>
                {{trans('hospitalizacion.FORMULARIO053')}}
                    <small>{{trans('hospitalizacion./HistorialMedico')}}</small>
                </h3>
            </div>
            <div class="col-2">
                <button type="button" onclick="location.href='{{route('hospital.formulario053',['id_cama'=>$id_cama,'id'=>$paciente->id])}}'" class="btn btn-danger btn-sm btn-block"><i class="far fa-arrow-alt-circle-left"></i>{{trans('hospitalizacion.Regresar')}} </button>
            </div>
        </div>
    </section>

  <div class="row">
    
    <!-- 4.-  -->
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">{{trans('hospitalizacion.Historial')}}</h3>
          <div class="box-tools pull-right">
            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
          </div>
        </div>
        <form id="formulario_053" action="{{route('hospital.formulario053_resultado',['id_cama'=>$id_cama,'id'=>$paciente->id])}}">
          {{ csrf_field() }}
          <div class="box-body">
            <div class="row">
              <div class="col-md-12">
                                @foreach($users as $value)
                                <input type="text" class="col-sm-4 form-control form-control-sm my-2" value="{{$value->nombre1}} {{$value->nombre2}} {{$value->apellido1}} {{$value->apellido2}}" readonly>
                                @endforeach
                                 <div class="row">
                                  <div class='col-sm-2'>
                                  <label>{{trans('hospitalizacion.Desde:')}}</label>
                                  <input type='text' name="fecha" id="fecha" class="form-control" id='datetimepicker6' />
                                 </div>
                                 <div class='col-sm-2'>
                                  <label>{{trans('hospitalizacion.Hasta:')}}</label>
                                  <input type='text' name="fechafin" id="fechafin" class="form-control" id='datetimepicker7' />
                                 </div>
                                  </div>
                                  <br>
                                <div class="table-responsive">
                                    <table  class="table table-bordered table-hover table-sm ">
                                        <thead>
                                            <tr>
                                                <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('hospitalizacion.No')}}</th>
                                                <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('hospitalizacion.Fecha')}}</th>
                                                <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('hospitalizacion.Notasdoctor')}}</th>
                                                <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">{{trans('hospitalizacion.Acci&oacute;n')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($variable1 as $value)
                                            <tr>
                                                <td>{{ $value->id }}</td>
                                                <td>{{ $value->created_at }}</td>
                                                <td>{{ $value->notas_doctor }}</td>
                                                <td style="text-align: center"><button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#modaleditar"><i class="fas fa-pencil-alt"></i>{{trans('hospitalizacion.Editar')}}</button></td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                              </div>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                    
                  </div>

                </div>
    <script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
    <script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
    <script type="text/javascript">
    $(function () {
    $('#datetimepicker6').datetimepicker({
    format: 'YYYY-MM-DD'
    });
    });
    $(function () {
    $('#datetimepicker7').datetimepicker({
    format: 'YYYY-MM-DD'
    });
    });
    </script>
    <script type="text/javascript">
    $(function () {
    $('#fecha').datetimepicker({
    format: 'YYYY/MM/DD',
    defaultDate: '{{$fecha}}'

    });
    $("#fecha").on("dp.change", function (e) {
    $('#fechafin').data("DateTimePicker").minDate(e.date);
    fecha_buscador();
    //alert('entra:inicio');
    });
    $('#fechafin').datetimepicker({
    format: 'YYYY/MM/DD',
    defaultDate: '{{$fechafin}}'
    });
    $("#fechafin").on("dp.change", function (e) {
    //alert('entra:fin');
    fecha_buscador();
    });
    });
</script>
<script type="text/javascript" >
    function fecha_buscador(){
      //alert('entra');
      $('#formulario_053').submit();
    }
</script>



@endsection
