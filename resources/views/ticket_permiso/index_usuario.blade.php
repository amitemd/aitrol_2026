@extends('ticket_permiso.base')
@section('action-content')
@php
$rolUsuario = Auth::user()->id_tipo_usuario;
//dd($rolUsuario);
@endphp
<style>
    .stilos:focus {

        background: whitesmoke;
    }

    #estilos:hover {
        color: blanchedalmond;
    }

    th {

        text-align: center;
        font-size: 12px;
    }

    td {
        text-align: center;
        font-size: 12px;
    }
</style>
    <div class="modal fade" id="modal_documetacion" data-keyboard="false" tabindex=null role="dialog"
        aria-labelledby="myModalDoctor" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            </div>
        </div>
    </div>
<section class="content">
        @if (Session::has('error'))
            <div class="alert alert-danger"><span style="color:white;font-weight:bold">{{ Session::get('error') }}</span>
            </div>
        @endif
        @if (Session::has('success'))
            <div class="alert alert-success"><span style="color:white;font-weight:bold">{{ Session::get('success') }}</span>
            </div>
        @endif
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="box-title">{{trans('tecnicof.permitapplication')}} </h3>
                </div>
                
                <div class="col-md-4" style="text-align: right">
                        <a class="btn btn-success"
                            href="{{ route('ticketpermisos.create_usuario') }}">{{ trans('tecnicof.permitcreation') }}</a>
                </div>
            
            </div>
        </div>
            
        <!-- /.box-header -->
        <div class=" box-body">
        <div class="row">
                <form action="{{route('ticketpermisos.index_usuario')}}" method="post">
                    {{ csrf_field() }}
                    <div class="col-md-3">
                        <label for="fecha" class="col-md-12 control-label">{{trans('tecnicof.from')}}</label>
                        <div class="col-md-12">
                                <input style="text-align: center;line-height:10px;" value="{{ $desde }}"
                                    type="date" name="desde" id="desde" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha" class="col-md-12 control-label">{{trans('tecnicof.to')}}</label>
                        <div class="col-md-12">
                                <input style="text-align: center;line-height:10px;" value="{{ $hasta }}"
                                    type="date" name="hasta" id="hasta" class="form-control">
                         </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="tipos" class="col-md-12 control-label">{{trans('tecnicof.type')}}</label>
                        <div class="col-md-12">
                            <select class="form-control" name="permiso" id="permiso">
                                <option value="">{{trans('tecnicof.select')}}</option>
                                <option @if($permiso=='PERMISO POR FALLECIMIENTO' ) selected @endif value="PERMISO POR FALLECIMIENTO">{{trans('tecnicof.deathleave')}}</option>
                                <option @if($permiso=='PERMISO POR MATERNIDAD' ) selected @endif value="PERMISO POR MATERNIDAD">{{trans('tecnicof.maternityleave')}}</option>
                                <option @if($permiso=='PERMISO POR PATERNIDAD' ) selected @endif value="PERMISO POR PATERNIDAD">{{trans('tecnicof.paternityleave')}}</option>
                                <option @if($permiso=='CALAMIDAD DOMESTICA' ) selected @endif value="CALAMIDAD DOMESTICA">{{trans('tecnicof.domesticcalamity')}}</option>
                                <option @if($permiso=='OLVIDO DE MARCACION' ) selected @endif value="OLVIDO DE MARCACION">{{trans('tecnicof.forgetfulnessofmarking')}}</option>
                                <option @if($permiso=='PERMISO MEDICO' ) selected @endif value="PERMISO MEDICO">{{trans('tecnicof.medicalleave')}}</option>
                                <option @if($permiso=='PERMISO PERSONAL' ) selected @endif value="PERMISO PERSONAL">{{trans('tecnicof.personalleave')}}</option>
                                <option @if($permiso=='COMISION DE SERVICIOS' ) selected @endif value="COMSION DE SERVICIOS">{{trans('tecnicof.servicecommission')}}</option>
                                <option @if($permiso=='REPROGRAMAR VACACIONES' ) selected @endif value="REPROGRAMAR VACACIONES">{{trans('tecnicof.vacationrescheduling')}}</option>
                                <option @if($permiso=='ATRASOS' ) selected @endif value="ATRASOS">{{trans('tecnicof.delays')}}</option>
                                <option @if($permiso=='TELETRABAJO' ) selected @endif value="TELETRABAJO">{{trans('tecnicof.teletrabajo')}}</option>
                                <option @if($permiso=='CAPACITACION' ) selected @endif value="CAPACITACION">{{trans('tecnicof.capacitacion')}}</option>
                            </select>
                        </div>
                    </div> 
                    <div class="col-md-3">
                        <label for="tipos" class="col-md-12 control-label">{{trans('tecnicof.state')}}</label>
                        <div class="col-md-12">
                            <select class="form-control" name="estado" id="estado">
                                <option value=" ">{{trans('tecnicof.select')}}</option>
                                <option @if($estado=='0' ) selected @endif value="0">{{trans('tecnicof.unattended')}}</option>
                                <option @if($estado=='1' ) selected @endif value="1">{{trans('tecnicof.attended')}}</option>
                                <option @if($estado=='2' ) selected @endif value="2">{{trans('tecnicof.all')}}</option>
                            </select>
                        </div>
                    </div> 
                   
                    
                    <div class="col-md-2" style=" text-align: center;" >
                        <br>
                        <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                    </div>
                            
                </form>
            </div>
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                    <div class="col-sm-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid"
                                aria-describedby="example2_info">
                            <thead style="background-color: #337ab7;color:white;">
                                <tr role="row" id="cabezera">
                                    <th class="th">{{trans('tecnicof.date')}}</th>
                                    <th class="th">{{trans('tecnicof.user')}}</th>
                                    <th class="th">{{trans('tecnicof.request')}}</th>
                                    <th class="th">{{trans('tecnicof.reason')}}</th>
                                    <th class="th">{{trans('tecnicof.department')}}</th>
                                    <th class="th">{{trans('tecnicof.approved')}}</th>
                                    <th class="th">{{trans('tecnicof.state')}}</th>
                                    <th class="th">{{trans('tecnicof.action')}}</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpo">
                                @foreach($datos as $value)
                                <tr>
                                    <td>{{substr($value->fecha_registro,0,11)}}</td>
                                            <td style="text-align: left;">
                                                {{ $value->cedula }}-{{ $value->nombre->nombre1 }}
                                                {{ $value->nombre->nombre2 }} {{ $value->nombre->apellido1 }}
                                                {{ $value->nombre->apellido2 }}</td>
                                    <td>{{$value->id}}</td>
                                    <td>{{$value->tipo_permiso}}</td>
                                    <td>{{$value->departamento}}</td>
                                            <td
                                                @if ($value->estado_solicitud == 0) bgcolor='#F93333' 
                                    @elseif($value->estado_solicitud == 1) 
                                        bgcolor='#71CD1B' 
                                    @elseif($value->estado_solicitud == '-1') 
                                        bgcolor='#F9DB33' 
                                    @elseif($value->estado_solicitud == '2') 
                                        bgcolor='#2e98e2' @endif>
                                                @if ($value->estado_solicitud == 0)
                                                    {{ trans('tecnicof.notapproved') }}
                                                @elseif($value->estado_solicitud == '-1')
                                                    {{ trans('tecnicof.byabrrobar') }}
                                                @elseif($value->estado_solicitud == '2')
                                                    Devuelta
                                                @elseif($value->estado_solicitud == '1')
                                                    Aprobado
                                                @endif
                                            </td>
                                            <td>
                                                @if ($value->estado_solicitud == '0')
                                                    No aprobado
                                                @elseif($value->estado_solicitud == '2')
                                                    Devuelto
                                                @elseif($value->estado_solicitud == '-1')
                                                    Por aprobar
                                                @elseif($value->estado_solicitud == '1')
                                                    Aprobado
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-warning"
                                                    href="{{ route('ticketpermisos.editar_usuario', ['id' => $value->id]) }}"><i
                                                        class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                                                <a class="btn btn-success" target="_blank"
                                                    href="{{ route('ticketpermisos.permisos_pdf', ['id' => $value->id]) }}"><i
                                                        class="fa fa-file-pdf-o " aria-hidden="true"></i></a>
                                        @if($value->ruta_archivo != null || $value->ruta_archivo != '')
                                                    <a target="_blank" class="btn btn-primary"
                                                        href="{{ route('ticketpermisos.ver_pdf', ['id' => $value->id]) }}"><i
                                                            class="fa fa-download" aria-hidden="true"></i></a>
                                                @endif
                                                @if ($value->estado_solicitud == '2')
                                                    <span data-toggle="modal" data-target="#modal_documetacion"
                                                        data-remote="{{ route('ticketpermisos.devolver_modal', ['id' => $value->id]) }}">
                                                        <a class="btn btn-info" data-toggle="tooltip"
                                                            title="Subir Documentación"><i class="fa fa-file-pdf-o"
                                                                aria-hidden="true"></i></a>
                                                    </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script text="text/javascript">
        $(document).ready(function() {
            setTimeout(function() {
                $(".alert").alert('close');
            }, 2000);
        });
    
    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': true,
            'lengthChange': false,
            'searching': false,
            'responsive': false,
            'ordering': false,
            'info': false,
            'autoWidth': false,
            'sInfoEmpty': false,
            'sInfoFiltered': false,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    });
    function verifica_fechas() {
    if (Date.parse($("#fecha_desde").val()) > Date.parse($("#fecha_hasta").val())) {
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Verifique el rango de fechas y vuelva consultar'
      });
    }
  }
  
 
</script>


@endsection