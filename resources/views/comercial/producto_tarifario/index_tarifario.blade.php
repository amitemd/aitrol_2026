@extends('comercial.producto_tarifario.base')
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
                            <h4><b>{{trans('prod_tar.producto')}}:</b> {{$producto->nombre}}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group col-md-2 ">
                <div class="col-md-7">
                    <a class="btn btn-primary" data-remote="{{route('prodtarifario.crear_tarifario',['id_producto' => $producto->id])}}" data-toggle="modal" data-target="#tarifario_nivel">Crear</a>
                </div>
            </div>

            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row" id="listado">
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                                <tr>
                                    <th>{{trans('prod_tar.seguro')}}</th>
                                    <th>{{trans('prod_tar.nivel')}}</th>
                                    <th>{{trans('prod_tar.valor')}}</th>
                                    <th>{{trans('prod_tar.accion')}}</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if($producto->valor_total_paq != null)
                                <tr>
                                    <td ><b> {{trans('prod_tar.particular')}} </b></td>
                                    <td></td>
                                    <td>{{$producto->valor_total_paq}}</td>
                                    <td><a class="btn btn-warning" data-remote="{{route('prodtarifario.edit_particular',['id' => $producto->id])}}" data-toggle="modal" data-target="#edit_particular"><i class="fa fa-edit"></i></a></td>
                                </tr>
                                @endif

                                @foreach($prod_tarifario as $prod_t)
                                @php
                                $nivel = Sis_medico\Nivel::where('id',$prod_t->nivel)->where('estado',1)->first();
                                @endphp

                                <tr>
                                    <td>{{$prod_t->seguro->nombre}}</td>
                                    <td>{{$nivel->nombre}}</td>
                                    <td>{{$prod_t->precio_producto}}</td>
                                    <td>
                                        <a class="btn btn-warning" data-remote="{{route('prodtarifario.edit_tarifario',['id' => $prod_t->id])}}" data-toggle="modal" data-target="#edit_tarifario"> <i class="fa fa-edit"></i></a>
                                        <a class="btn btn-danger" onclick="eliminar('{{$prod_t->id}}')"> <i class="fa fa-trash"></i></a>
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
            url: "{{ url('comercial/producto_tarifario/eliminar_tarifario/') }}/" + id,
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