<div class="box box-warning">
    <div class="row">
        <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                <thead>
                    <th>Codigo</th>
                    <th>Nombre</th>
                    <th>Acción</th>
                </thead>
                <tbody>
                    @foreach ($det_tarifario as $det)
                        <tr>
                            <td>@if(isset($det->ap_procedimento)) {{$det->ap_procedimento->codigo}} @endif</td>
                            <td>@if(isset($det->ap_procedimento)) {{$det->ap_procedimento->descripcion}} @endif</td>
                            <td>
                                <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>