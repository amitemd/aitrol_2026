@extends('comercial.proforma.base')
@section('action-content')
    <link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.css') }}">
    <section class="content">
        <div class="box">
            <div class="box-header">
            </div>
            <div class="box-body">
                <form id="form_producto" method="post" action="">
                    {{ csrf_field() }}

                    <!-- <div class="form-group col-md-4 ">
                        <div class="row">
                            <div class="form-group col-md-10 ">
                                <label for="nombre" class="col-md-4 control-label">N. Proforma:</label>
                                <div class="col-md-8">
                                    <input type="text" name="num_prof" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-md-2 ">
                        <div class="col-md-7">
                            <button type="submit" class="btn btn-primary" id="boton_buscar">
                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{ trans('prod_tar.buscar') }}</button>
                        </div>
                    </div> -->

                    


                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row" id="listado">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid"
                                    aria-describedby="example2_info">
                                    <thead>
                                        <tr>
                                            <th>N.</th>
                                            
                                           
                                            <th>{{ trans('proforma.observacion') }}</th>
                                            <th>{{ trans('proforma.Seguro') }}</th>
                                            <th style="width: 15%;">{{ trans('proforma.estado') }}</th>
                                            <th>{{ trans('proforma.total') }}</th>
                                            <th>{{ trans('proforma.accion') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($proformas as $value)
                                            <tr>
                                                <td>{{ $value->id }}</td>
                                                <td> {{ $value->observacion }}</td>
                                                <td> {{ $value->seguro->nombre }}</td>
                                                @if ($value->estado == -1)
                                                    <td style="background: #f39c12; font-weight: bold; text-align: center;">
                                                        Proforma pendiente de emitir</td>
                                                @else
                                                    <td style="background: #00a65a; font-weight: bold; text-align: center;">
                                                        Emitida</td>
                                                @endif
                                                <td> {{ $value->total }}</td>
                                                <td>

                                                    
                                                        <a href="{{ route('comercial.proforma.editar_cotizacion', ['id' => $value->id]) }}"
                                                            class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                                </td>

                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                            {{-- <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                                        Mostrando {{ 1 + ($proformas->currentPage() - 1) * $proformas->perPage() }} /
                                        {{ count($proformas) + ($proformas->currentPage() - 1) * $proformas->perPage() }}
                                        de {{ $proformas->total() }} registros</div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                        {{ $proformas->appends(Request::only(['proveedor', 'observacion', 'secuencia_factura', 'ct_c.tipo_comprobante', 'fecha', 'id_asiento_cabecera', 'id', 'detalle', 'secuencia_f']))->links() }}
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script src="{{ asset('/plugins/datetimepicker/bootstrap-material-datetimepicker.js') }}"></script>
    <script src="{{ asset('/js/bootstrap-datetimepicker.js') }}"></script>
    <script type="text/javascript">
        $('#example2').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': false,
            'info': false,
            'autoWidth': false
        })

        
    </script>

@endsection
