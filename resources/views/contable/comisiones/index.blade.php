@extends('contable.comisiones.base')
@section('action-content')

    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css" />

    </head>
    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: none;
            color: black !important;
            border-radius: 4px;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            background: none;
            color: black !important;
        }

        .pagination>li:first-child>a,
        .pagination>li:first-child>span,
        .pagination>li:last-child>a,
        .pagination>li:last-child>span {
            color: black !important;
        }
    </style>
    @php
        use Illuminate\Support\Facades\DB;
        
        $fecha_actual = date('Y-m-d');
        
        $fecha_desde = date('Y-m-d');
        
        $fecha_hasta = date('Y-m-d');
        
        if (isset($fecha_desde_buscador) and isset($fecha_hasta_buscador)) {
            $fecha_desde = $fecha_desde_buscador;
        
            $fecha_hasta = $fecha_hasta_buscador;
        }
        
        foreach ($resultado as $val) {
            Sis_medico\Http\Controllers\contable\NuevoReciboCobroController::calcular_comision($val->id);
        }
        
        $arrayActualizado = db::table('ct_orden_venta')
            ->join('comisiones_gastro', 'ct_orden_venta.doctor_referido', 'comisiones_gastro.id')
            ->join('agenda as ag','ag.id', 'ct_orden_venta.id_agenda')
            ->leftjoin('seguros', 'ct_orden_venta.id_seguro', 'seguros.id')
            ->where('ct_orden_venta.doctor_referido', '<>', null)
            ->where("ct_orden_venta.estado",1)
            ->whereBetween('ct_orden_venta.fecha_emision', [$fecha_desde . ' 00:00:00', $fecha_hasta . ' 23:59:59']);
        
        if (isset($doctor_nombre)) {
            $arrayActualizado = $arrayActualizado->where('ct_orden_venta.doctor_referido', $doctor_nombre);
        }
    $arrayActualizado = $arrayActualizado->select('ag.id_paciente as cedula_paciente','ct_orden_venta.fecha_emision', 'comisiones_gastro.nombre as nombreDoctor', 'ct_orden_venta.total as precio_original', 'ct_orden_venta.comision_total', 'ct_orden_venta.id', 'seguros.nombre')->paginate(10);
    @endphp
    <!-- Main content -->

    {{-- Modal --}}

    <div id="ex1" class="modal">
        <p>Thanks for clicking. That felt good.</p>
        <a href="#" rel="modal:close">Close</a>
    </div>

    <section class="content">
        <div class="box box-success">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-9">
                        <h3 class="box-title">Reporte de Comisiones</h3>
                    </div>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                @if (Session::has('error'))
                    <div class="alert alert-danger"><span
                            style="color:white;font-weight:bold">{{ Session::get('error') }}</span></div>
                @endif
                @if (Session::has('vacio_excel'))
                    <div class="alert alert-warning"><span
                            style="color:white;font-weight:bold">{{ Session::get('vacio_excel') }}</span></div>
                @endif

                @if (Session::has('carga_exitosa'))
                    <div class="alert alert-success"><span
                            style="color:white;font-weight:bold">{{ Session::get('carga_exitosa') }}</span></div>
                @endif

                <form action="{{ route('subir_excel.index') }}" enctype="multipart/form-data" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group row">
                        <label for="excel_comisiones" class="col-sm-1 col-form-label">Excel</label>

                        <div class="col-sm-3">
                            <input type="file" class="form-control" style="padding: 3px;" name="excel_comisiones"
                                required />
                        </div>
                        <div class="col-sm-2">
                            <select name="doctor_excel" id="doctor_excel" class="form-control" required>
                                <option value="">Seleccione</option>
                                @foreach ($doctor as $value)
                                    <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-success">Subir Excel</button>
                        </div>
                    </div>
                </form>
            </div>
            <hr>
            <form method="POST" action="{{ route('buscar.comisiones') }}">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-2">
                        <label><b>Fecha desde</b></label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control"
                            value="{{ $fecha_desde }}">
                    </div>
                    <div class="col-md-1">
                        <label><b>Fecha hasta</b></label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control"
                            value="{{ $fecha_hasta }}">
                    </div>
                    <div class="col-md-1">
                        <label><b>Doctor</b></label>
                    </div>
                    <div class="col-md-2">
                        <select name="doctor" id="doctor" class="form-control">
                            <option value="">Seleccione</option>
                            @foreach ($doctor as $value)
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary" type="submit">
                            Buscar
                        </button>
                    </div>
                </div>
            </form>

            <div style="text-align: center">
                <div id="example1_wrapper">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <table id="tabla_id" class="table table-bordered table-hover display nowrap" role="grid"
                                aria-describedby="example1_info" style="width:100%">
                                <thead>
                                    <th>Id</th>
                                        <th>Cédula del paciente</th>
                                        <th>Nombres del paciente</th>
                                    <th>
                                        Doctor
                                    </th>
                                    <th>
                                        Seguro
                                    </th>
                                    <th>
                                        Fecha Emisión
                                    </th>
                                    <th style="color:red">
                                        Valor Real(recibo de cobro)
                                    </th>
                                    <th>
                                        Comisión
                                    </th>
                                    <th>
                                        Detalle
                                    </th>
                                </thead>
                                <tbody>
                                    @foreach ($arrayActualizado as $value)
                                        <tr>
                                            <td>
                                                {{ $value->id }}
                                            </td>
                                            <td>
                                                {{$value->cedula_paciente}}
                                            </td>
                                            @php
                                                $paciente = \Sis_medico\Paciente::where('id', $value->cedula_paciente)->first();
                                            @endphp
                                            <td>
                                                {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}
                                            </td>
                                            <td>
                                                {{ $value->nombreDoctor }}
                                            </td>
                                            <td>
                                                {{ $value->nombre }}
                                            </td>
                                            <td>
                                                {{ $value->fecha_emision }}
                                            </td>
                                            <td>
                                                {{ "$" . number_format($value->precio_original, 2) }}
                                            </td>
                                            <td>
                                                {{ "$" . number_format($value->comision_total, 2) }}
                                            </td>
                                            <td>
                                                <a href="{{ route('detalle_examenes.comisiones', ['id' => $value->id]) }}"
                                                    class="btn btn-xs btn-primary">
                                                    Detalle
                                                </a>
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
        </div>
        </div>
    </section>
@endsection
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>


<script type="text/javascript">
    $(document).ready(function() {
        // show the alert
        setTimeout(function() {
            $(".alert").alert('close');
        }, 2000);
    });

    $(document).ready(function() {
        $('#tabla_id').DataTable({
            dom: 'Bfrtip',
            paging: true,
            searching: false,
            info: false,
            buttons: [{
                    extend: 'copyHtml5',
                    footer: true
                },
                {
                    extend: 'excelHtml5',
                    footer: true,
                }
            ],
        })
    });
</script>
