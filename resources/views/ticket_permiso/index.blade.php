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
    <div class="modal fade" id="modal_historial" data-keyboard="false" tabindex=null role="dialog"
        aria-labelledby="myModalDoctor" aria-hidden="true">
        <div class="modal-dialog modal-xs" role="document">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal_dia" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            </div>
        </div>
    </div>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="box-title">{{trans('tecnicof.permitapplication')}} </h3>
                </div>
                    <div class="col-md-4 text-right">
                @if($rolUsuario=='20' || $rolUsuario=='1')
                            <div class="btn-group">
                                <a data-remote="{{ route('ticketpermisos.historial_dia') }}" class="btn btn-info"
                                    data-toggle="modal" data-target="#modal_dia">Historial del D&iacute;a</a>
                                <a class="btn btn-warning" style="margin: 0 5px;"
                                    href="{{ route('ticketpermisos.create') }}">{{ trans('tecnicof.permitcreation') }}</a>

                </div>
                @endif
                    </div>
            </div>
        </div>

        <!-- /.box-header -->
        <div class=" box-body">
            <div class="row">
                <form action="{{route('ticketpermisos.buscador')}}" method="POST">
                    {{ csrf_field() }}
                    <div class="col-md-3">
                        <label for="fecha" class="col-md-12 control-label">{{trans('tecnicof.from')}}</label>
                        <div class="col-md-8">
                                <input style="text-align: center;line-height:10px;" value="{{ $desde }}"
                                    type="date" name="desde" id="desde" class="form-control">

                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="fecha" class="col-md-12 control-label">{{trans('tecnicof.to')}}</label>
                        <div class="col-md-8">
                                <input style="text-align: center;line-height:10px;" value="{{ $hasta }}"
                                    type="date" name="hasta" id="hasta" class="form-control">

                        </div>
                    </div>
                    @if($rolUsuario == 1 || $rolUsuario == 20 )
                    <div class="col-md-3">
                        <label for="usuario" class="col-md-12 control-label">{{trans('tecnicof.id')}}</label>
                        <div class="col-md-8">
                                    <input type="text" name="id_usuario" id="id_usuario" class="form-control"
                                        value="{{ $cedula }}">
                            <div id="resultado">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                                <label for="nomusuario"
                                    class="col-md-12 control-label">{{ trans('tecnicof.nombres') }}</label>
                        <div class="col-md-12">
                                    <input type="text" name="nomusuario" id="nomusuario" class="form-control"
                                        value="{{ $usuarios }}">
                            <div id="resultado">
                            </div>
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
                        <div class="col-md-8">
                            <select class="form-control" name="estado" id="estado">
                                        <option @if ($estado == '0') selected @endif value="0">
                                            {{ trans('tecnicof.unattended') }}</option>
                                        <option @if ($estado == '1') selected @endif value="1">
                                            {{ trans('tecnicof.attended') }}</option>
                                        <option @if ($estado == '2') selected @endif value="2">
                                            {{ trans('tecnicof.all') }}</option>
                            </select>
                        </div>
                    </div>
                    @endif

                    <div class="col-md-2" style=" text-align: center;">
                        <br>
                            <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar"
                                onclick="changeExcel('b')">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                    </div>
                    <input type="hidden" name="excel" id="excel" value="0">
                    <div class="col-md-2" style=" text-align: center;">
                        <br>
                            <button type="submit" class="btn btn-info" onclick="changeExcel('e')"><i
                                    class="fa fa-file-excel-o" aria-hidden="true"> {{ trans('tecnicof.reports') }}</i>
                            </button>
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
                                    <th class="th">Fecha</th>
                                    @if($rolUsuario == 1 || $rolUsuario == 20 )<th class="th">{{trans('tecnicof.user')}}</th>@endif
                                    @if($rolUsuario != 1 || $rolUsuario != 20 ) <th class="th">{{trans('tecnicof.request')}}</th>@endif
                                    @if($rolUsuario != 1 || $rolUsuario != 20 ) <th class="th">{{trans('tecnicof.reason')}}</th>@endif
                                    @if($rolUsuario == 1 || $rolUsuario == 20 )<th class="th">{{trans('tecnicof.departament')}}</th>@endif
                                    <th class="th">APROBACION</th>
                                    <th class="th">Estado</th>
                                    @if($rolUsuario == 1 || $rolUsuario == 20 ) <th class="th">{{trans('tecnicof.action')}}</th> @endif
                                </tr>
                            </thead>
                            <tbody id="cuerpo">
                                @foreach($datos as $value)
                                <tr>
                                    <td>{{substr($value->fecha_registro,0,11)}}</td>
                                            @if ($rolUsuario == 1 || $rolUsuario == 20)
                                                <td style="text-align: left;">{{ $value->cedula }}- @if (!is_null($value->nombre))
                                                        {{ $value->nombre->nombre1 }} @if (!is_null($value->nombre->nombre2))
                                                            {{ $value->nombre->nombre2 }}
                                                            @endif {{ $value->nombre->apellido1 }} @if (!is_null($value->nombre->apellido2))
                                                                {{ $value->nombre->apellido2 }}
                                                            @endif
                                                        @endif
                                                </td>
                                            @endif
                                            <td>
                                                @if (!is_null($value->id))
                                                    {{ $value->id }}
                                                @endif
                                            </td>
                                            @if ($rolUsuario != 1 || $rolUsuario != 20)
                                                <td>{{ $value->tipo_permiso }}</td>
                                            @endif
                                            @if ($rolUsuario == 1 || $rolUsuario == 20)
                                                <td>{{ $value->departamento }}</td>
                                            @endif
                                            <td
                                                @if ($value->estado_solicitud == '0') bgcolor='#FF3636' 
                                            @elseif($value->estado_solicitud == '1') 
                                                bgcolor='#99EA65'
                                            @elseif($value->estado_solicitud == '-1') 
                                                bgcolor = '#E3F536';
                                                @elseif($value->estado_solicitud == '2') 
                                                bgcolor='#2e98e2' @endif>
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
                                            <td
                                                @if ($value->estado_solicitud == '0') bgcolor='#FF3636' 
                                                @elseif($value->estado_solicitud == '1') 
                                                    bgcolor='#99EA65'
                                                @elseif($value->estado_solicitud == '-1') 
                                                    bgcolor = '#E3F536';
                                                @elseif($value->estado_solicitud == '2') 
                                                    bgcolor = '#65b2ff'; @endif>
                                                @if ($value->estado_solicitud == '0')
                                                    {{ trans('tecnicof.notapproved') }}
                                                @elseif($value->estado_solicitud == '-1')
                                                    {{ trans('tecnicof.byabrrobar') }}
                                                @elseif($value->estado_solicitud == '2')
                                                    Devuelto
                                                @elseif($value->estado_solicitud == '1')
                                                    Aprobado
                                                @endif
                                            </td>
                                    @if($rolUsuario == 1 || $rolUsuario == 20 )
                                                <td>
                                                    <a class="btn btn-warning"
                                                        href="{{ route('ticketpermisos.editar', ['id' => $value->id]) }}"><i
                                                            class="glyphicon glyphicon-eye-open"
                                                            aria-hidden="true"></i></a>
                                                    <a class="btn btn-success" target="_blank"
                                                        href="{{ route('ticketpermisos.permisos_pdf', ['id' => $value->id]) }}"><i
                                                            class="fa fa-file-pdf-o " aria-hidden="true"></i></a>
                                        @if($value->ruta_archivo != null || $value->ruta_archivo != '')
                                                        <a target="_blank" class="btn btn-primary"
                                                            href="{{ route('ticketpermisos.ver_pdf', ['id' => $value->id]) }}"><i
                                                                class="fa fa-download" aria-hidden="true"></i></a>
                                        @endif
                                                </td>
                                            @endif
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
    function changeExcel(l) {
        if (l === 'e') {
            document.getElementById("excel").value = 1;
        }else{
            document.getElementById("excel").value = 0;
        }

    }
    document.getElementById("resultado").style.visibility = "hidden";
    $('.select2').select2({
        tags: false
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
        jQuery(document).ready(function() {
            jQuery('#modal_dia').on('hidden.bs.modal', function(e) {
                jQuery(this).removeData('bs.modal');
                jQuery(this).find('.modal-content').empty();
            })
        })
        jQuery(document).ready(function() {
            jQuery('#modal_historial').on('hidden.bs.modal', function(e) {
                jQuery(this).removeData('bs.modal');
                jQuery(this).find('.modal-content').empty();
            })
        })
</script>


@endsection