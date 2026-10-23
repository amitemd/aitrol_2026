@extends('servicios_generales.limpieza_equipos.base')
@section('action-content')
@php $fechaHoy = date('Y-m-d'); @endphp
<style>
    .btn {
        font-size: 15px;
        font-weight: bold;
    }

    .salas:hover {
        background-color: #4192C2;
    }
</style>
<div class="modal fade" id="nuevo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="box-header">
                <form id="form_fecha" method="POST">
                    {{ csrf_field() }}
                    <div class="form-group col-md-4 col-xs-6">
                        <label for="fecha" class="col-md-3 control-label">Desde</label>
                        <div class="col-md-6">
                            <input type="date" class="form-control" name="fecha" value="{{$fechaHoy}}" id="fecha">
                        </div>
                        <div class="col-md-3">
                           <a href="{{route('limpieza_equipo.reporteexcel')}}" class="btn btn-success">Reportes</a>
                        </div>
                    </div>
                </form>

            </div>


            <div class="box-body">
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">

                    <table id="example2" class="table table-bordered table-hover dataTable">
                        <tbody>
                            @foreach($sala as $sala)
                            <div class="col-md-3" style="padding: 5px;">
                                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                                    <a id="boton_salas{{$sala->id}}" class="btn btn-primary" style="width: 100%; height: 60px; line-height: 40px; font-size: 20px; text-align: center" onClick="componentDidMount({{$sala->id}});">{{$sala->nombre_sala}}
                                    </a>

                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="box">
                        <div class="box-header">
                            <div class="pull-right box-tools">
                                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="demo">
                                    <i class="fa fa-minus"></i></button>
                            </div>
                        </div>
                        <div class="box-body" style="display: block;">
                            <div class="col-md-12" id="index_form"></div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': true,
            'lengthChange': true,
            'searching': true,
            'responsive': true,
            'ordering': true,
            'info': true,
            'autoWidth': false,
            'sInfoEmpty': false,
            'sInfoFiltered': false,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    });
    async function componentDidMount(id_sala) {
        var result = await this.index_limpieza(id_sala);
        document.getElementById("index_form").innerHTML = await result.text();
    }

    async function index_limpieza(id_sala) {
        var fechaBuscar = document.getElementById("fecha").value;
        var data = {
            fecha: fechaBuscar,
            sala_id: id_sala,
        };
        const resultado = await fetch("{{asset('buscar/sala/completo')}}", {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        });
        return resultado;

    }
</script>


@endsection
