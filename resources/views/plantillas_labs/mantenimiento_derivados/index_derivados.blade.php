@extends('plantillas_labs.mantenimiento_derivados.base')
@section('action-content')
<div class="modal fade" id="tarifario_nivel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade" id="edit_tarifario" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<div class="modal fade" id="edit_particular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

<section class="content">
    <div class="box">
        <div class="box-header">
        </div>
        <div class="box-body">
            <div class="form-group col-md-8 ">
                <div class="row">
                    <div class="form-group col-md-10 ">
                        <div class="col-md-12">
                            <h4><b>Examen:</b> {{$examenes->nombre}}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-2 ">
                <div class="col-md-7">
                    <a class="btn btn-primary" data-remote="{{route('examderivados.crear_derivado',['id_examen' => $examenes->id])}}" data-toggle="modal" data-target="#tarifario_nivel">Crear</a>
                </div>
            </div>

            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row" id="listado">
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                                <tr>
                                    <th>Id </th>
                                    <th>Derivado</th>
                                    <th>{{trans('prod_tar.valor')}}</th>
                                    <th>{{trans('prod_tar.accion')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($examen_derivado as $exam_d) {
                                ?>
                                    <tr>
                                        <td>{{$exam_d->id}}</td>
                                        <td>{{$exam_d->tipo_derivado->nombre}}</td>
                                        <td>{{$exam_d->valor}}</td>
                                        <td>
                                            <a class="btn btn-warning" data-remote="{{route('examderivados.edit_derivado',['id_examen' => $exam_d->id])}}" data-toggle="modal" data-target="#edit_tarifario"> <i class="fa fa-edit"></i></a>
                                            <a class="btn btn-danger" onclick="eliminar('{{$exam_d->id}}')"> <i class="fa fa-trash"></i></a>
                                        </td>
                                    </tr>

                                <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $('#example2').DataTable({
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': true,
        'info': false,
        'autoWidth': false
    })

    $('#tarifario_nivel').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    $('#edit_tarifario').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    $('#edit_particular').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });



    function eliminar(id) {

        $.ajax({
            type: 'get',
            url: "{{ url('mantenimiento_examenes_derivados/eliminar_derivado/') }}/" + id,
            datatype: 'json',
            success: function(data) {
                console.log(data);
                location.reload();

            },
            error: function(data) {

            }
        });
    }
</script>
@endsection