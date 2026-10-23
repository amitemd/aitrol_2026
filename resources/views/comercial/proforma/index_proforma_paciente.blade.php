@extends('comercial.proforma.base')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-header">
          
        </div>
        <div class="box-body">
            <form id="form_producto" method="post" action="">
                {{ csrf_field() }}
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    
                <h3 for="paciente" class="col-md-12 control-label">Paciente: <b>{{$paciente->nombre1}}</b> <b>{{$paciente->nombre2}}</b> <b>{{$paciente->apellido1}}</b> <b>{{$paciente->apellido2}}</b></h3>
                    <div class="row" id="listado">
                        
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>N.</th>
                                        <th>{{trans('proforma.observacion')}}</th>
                                        <th>{{trans('proforma.Seguro')}}</th>
                                        <th style="width: 15%;">{{trans('proforma.estado')}}</th>
                                        <th>{{trans('proforma.total')}}</th>
                                        <th>{{trans('proforma.accion')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proformas_paciente as $value)
                                    <tr>
                                        <td>{{$value->id}}</td>
                                        <td> {{$value->observacion}}</td>
                                        <td> {{$value->seguro->nombre}}</td>
                                        @if($value->estado == 1)
                                        <td style="background: #00a65a; font-weight: bold; text-align: center;">Emitida</td>
                                        @endif
                                        <td> {{$value->total}}</td> 
                                        <td>
                                            @if($value->estado == 1)
                                            <a target="_blank" href="{{route('comercial.proforma.pdf_proforma',['id' => $value->id])}}" class="btn btn-success"> <i class="fa fa-download"></i></a>
                                            @endif
                                        </td>

                                    </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($proformas_paciente->currentPage() - 1) * $proformas_paciente->perPage())}} / {{count($proformas_paciente) + (($proformas_paciente->currentPage() - 1) * $proformas_paciente->perPage())}} de {{$proformas_paciente->total()}} registros</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{ $proformas_paciente->appends(Request::only(['proveedor', 'observacion', 'secuencia_factura','ct_c.tipo_comprobante','fecha','id_asiento_cabecera','id','detalle','secuencia_f']))->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
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

