@extends('contable.conciliacion_bancaria.new_vistas.base')
@section('action-content')

<style>
    :root {
        --color: transparent linear-gradient(90deg, #2A79C6 0%, #115596 100%) 0% 0% no-repeat padding-box !important;
    }

    .emitido {
        background-color: #00a65a;
        font-weight: 600;
        color: #ffffff;
    }

    .pendiente {
        background-color: #f39c12;
        font-weight: 600;
        color: #ffffff;
    }

    .anulado {
        background-color: #dd4b39;
        font-weight: 600;
        color: #ffffff;
    }

    .swal2-icon.swal2-warning {
        font-size: 37px !important;
    }

    .swal2-icon-content {
        font-size: 102px;
    }

    .swal2-warning {
        margin: 16px !important;
    }

    .contenedor-valores {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 5px 20px;
        width: 100%;
        text-align: center;
    }

    .contenedor-valores>p {
        margin: 0px;
        font-weight: 600;
        color: white;
    }

    .grid-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(20em, 1fr));
        gap: 20px;
        margin-top: 0px;
        padding: 0px 10px;
    }

    .grid-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(5em, 1fr));
        gap: 5px;
        margin-top: 0px;
    }
</style>

<div class="modal fade bd-example-modal-lg rounded" id="ver_cedula" aria-labelledby="myModalLabelGuia" style="width: 100%;">
    <div class="modal-dialog modal-lg" style="width: 60%;">
        <div class="modal-content" id="cargar_modal" style="width: 100%;">

        </div>
    </div>
</div>

<div style="background-color: white; border-radius: 8px; padding: 15px">
    <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
        <h3 class="text-3xl font-semibold text-gray-800">Listado de Conciliaciones</h3>

        <div class="flex justify-end md:flex-row flex-col gap-5 place-items-center">
            <div class="text-center md:w-auto w-full">

            </div>
            <div class="text-center md:w-auto w-full">

            </div>
            <div class="text-end md:w-auto w-full">
                <a type="button" href="{{route('newconciliacionbancaria.conciliacion_create')}}" class="w-full md:w-auto bg-green-500 rounded text-white font-semibold px-4 py-2 transition-all duration-300  hover:bg-green-700"> <i class="fa fa-plus" aria-hidden="true"></i> Agregar</a>
            </div>
        </div>
    </div>
    <div class="col-md-12 mt-5" style="padding: 0px;">
        <table class='table table-bordered table-striped table-hover dataTable' id="example2">
            <thead>
                <tr>
                    <th>{{trans('contableM.institucion')}}</th>
                    <th>{{trans('contableM.anio')}}</th>
                    <th>{{trans('contableM.mes')}}</th>
                    <th>{{trans('contableM.saldoLibros')}}</th>
                    <th>{{trans('contableM.saldoBancos')}}</th>
                    <th>{{trans('contableM.creado')}}</th>
                    <th>{{trans('contableM.fcreacion')}}</th>
                    <th>{{trans('contableM.accion')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conciliaciones as $value)
                @php
                //dd($value);
                $libros = explode('|', $value->valores);
                @endphp
                <tr>
                    <td>{{$value->nombre}}</td>
                    <td>{{$value->anio}}</td>
                    <td>{{$value->mes}}</td>
                    <td>{{$libros[0] ?? '0.00'}}</td>
                    <td>{{$libros[1] ?? '0.00'}}</td>
                    <td>{{$value->usuariocrea}}</td>
                    <td>{{$value->created_at}}</td>
                    <td>
                        @if($value->estado == '1')
                        <a class="btn btn-xs btn-warning" type="button" onclick="verConciliacion('{{$value->id}}')"><i class="fa fa-eye"></i></a>
                        <a class="btn btn-xs btn-primary" type="button" onclick="ver_pdf('{{$value->id_caja_banco}}', '{{$value->anio}}', '{{$value->mes}}')"><i class="fa fa-file-pdf-o"></i></a>
                        <a class="btn btn-xs btn-success" type="button" onclick="excelDatosConciliacion('{{$value->id_caja_banco}}', '{{$value->anio}}', '{{$value->mes}}')"><i class="fa fa-file-excel-o"></i></a>
                        <a class="btn btn-xs btn-danger" type="button" onclick="anular('{{$value->anio}}','{{$value->mes}}','{{$value->id_caja_banco}}')"><i class="fa fa-trash"></i></a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="row">

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': true,
            'info': false,
            'autoWidth': false,
        });
    });

    function alertas(icon, title, msj) {
        Swal.fire({
            icon: icon,
            title: title,
            html: msj
        })
    }

    function anular(anio, mes, banco) {
        $.ajax({
            type: 'get',
            url: "{{url('contable/banco/anular/conciliacion')}}/" + anio + '/' + mes + '/' + banco,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            success: function(data) {
                alertas(data.status, data.status, data.msj);
                if (data.status == 'success') {
                    setTimeout(function() {
                        location.reload();
                    }, 200);
                }
            },
            error: function(data) {
                alertas(data.status, data.status, data.msj);
            }
        })
    }

    function ver_pdf(id, anio, mes = null) {
        var periodo = anio + '-' + mes;
        if (mes === null) {
            id = $('#instituciones').val();
            periodo = $('#fecha_periodo').val().split('-');
        } else {
            periodo = periodo.split('-');
        }
        window.open("{{route('newconciliacionbancaria.pdfDatosConciliacion')}}?periodo=" + periodo[0] + '-' + periodo[1] + "&banco=" + id, "_blank");
    }

    function excelDatosConciliacion(id, anio, mes = null) {
        var periodo = anio + '-' + mes;
        if (mes === null) {
            id = $('#instituciones').val();
            periodo = $('#fecha_periodo').val().split('-');
        } else {
            periodo = periodo.split('-');
        }
        window.open("{{route('newconciliacionbancaria.excelDatosConciliacion')}}?periodo=" + periodo[0] + '-' + periodo[1] + "&banco=" + id, "_blank");
    }

    function verConciliacion(id) {
        window.open("{{route('newconciliacionbancaria.conciliacion_create')}}?id=" + id, '_self');
    }
</script>

@endsection