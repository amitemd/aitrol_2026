@extends('contable.configuracion_pdf.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item active">Configuraciones Pdf</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header">
            <div class="col-md-9">
                <h5><b>CONFIGURACIONES PDF</b></h5>
            </div>
            <div class="col-md-1 text-right">
                <button type="button" onclick="location.href='{{route('configuraciones_pdf')}}'" class="btn btn-success btn-gray">
                    <i aria-hidden="true"></i>Agregar Configuraciones Pdf
                </button>
            </div>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto">LISTADO TIPO AMBIENTE</label>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="form-row">
                <div id="contenedor">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr class="well-dark">
                                            <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Numero</th>
                                            <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Empresa</th>
                                            <th width="25%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Detalle</th>
                                            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha</th>
                                            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Autorización</th>
                                            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                                            <th width="10%" class="" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Accion</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($confi as $value)
                                        @php

                                        $nombre = Sis_medico\Empresa::where('id',$value->id_empresa)->first();

                                        @endphp
                                        <tr class="well">
                                            <td>{{$value->id}}</td>
                                            <td>{{$nombre->nombrecomercial}}</td>
                                            <td>
                                                {{$value->detalle}}
                                            </td>
                                            <td style="text-align: center;">
                                                {{$value->fech_autorizacion}}</td>
                                            <td>
                                                {{$value->autorizacion}}
                                            </td>
                                            <td>
                                                {{$value->estado}}
                                            </td>
                                            <td style="text-align: center;">
                                                @if (($value->estado )==1)
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <a href="{{ route('editar_pdfconfi', ['id' => $value->id])}}" class="btn btn-success btn-gray">
                                                    <i class="glyphicon glyphicon-edit" aria-hidden="true"></i>
                                                </a>
                                                @elseif(($value->estado)==0)

                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div class="row">
                                    <div class="col-sm-5">
                                        <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($confi->currentPage() - 1) * $confi->perPage())}} / {{count($confi) + (($confi->currentPage() - 1) * $confi->perPage())}} de {{$confi->total()}} registros
                                        </div>
                                    </div>
                                    <div class="col-sm-7">
                                        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                            {{$confi->appends(Request::only(['codigo','confi']))->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            tags: false
        });
    });
</script>


@endsection