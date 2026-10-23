@extends('control_sintoma.base')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title rojo mr-4">Buscador</h3>
            <div class="box-tools pull-right">
                <a class="label label-primary" href="{{route('create_control')}}">Formulario</a>
            </div>
            <form method="POST" action="{{route('buscar_control')}}">
                {{ csrf_field() }}
                <div class="col-md-12">
                    <div class="col-md-1">
                        <label for="">Desde</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="desde" id="desde">
                    </div>

                    <div class="col-md-1">
                        <label for="">Hasta</label>
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="hasta" id="hasta">
                    </div>
                    <div class="col-md-1">
                        <label for="">Usuario</label>
                    </div>
                    <div class="col-md-3">
                        <div class="col-md-10">
                            <select name="usuarios" class="js-data-example-ajax form-control">

                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" id="oks" class="btn btn-primary">
                            Buscar
                        </button>
                        <input type="hidden" value="0" name="excel" id="excel">
                        <a onclick="excel()" class="btn btn-success">
                            Excel
                        </a>
                    </div>
                </div>
            </form>

        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead style="background-color: #337ab7;color:white;">
                            <tr role="row" id="cabezera">
                                <th>Fecha</th>
                                <th>Nombres y Apellidos</th>
                                <th>Sexo</th>
                                <th>Edad</th>
                                <th>Temperatura</th>
                                <th>Acción</th>

                            </tr>
                        </thead>
                        <tbody id="cuerpo">
                            @foreach($user as $value)

                            <tr>
                                <td>{{$value->fecha_registro}}</td>
                                <td>{{$value->usuario->nombre1}} {{$value->usuario->apellido1}}</td>
                                <td>@if($value->sexo == 1) mujer @else hombre @endif</td>
                                <td>{{$value->edad}}</td>
                                <td>{{$value->temperatura}}</td>
                                <td> <a class="btn btn-primary" href="{{route('edit_control',['id'=>$value->id])}}">
                                        Editar
                                    </a> </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer text-right">
            {{ $user->links() }}
        </div>
        <!-- box-footer -->
    </div>
</section>
<link href="{{url('css/stylescontrolsintoma.css')}}" rel="stylesheet" type="text/css">
<script type="text/javascript">
    function excel() {
        document.querySelector("#excel").value = 1;
        document.querySelector("#excel").value = 0;
        document.querySelector("#oks").click();
    }

    $(document).ready(function() {
        var studentSelect = $('.js-data-example-ajax');
        $('.js-data-example-ajax').select2({
            tags: true,
            tokenSeparators: [','],
            minimumInputLength: 4,
            ajax: {
                url: "{{route('buscarusuario_control')}}",
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    }
                },
                processResults: function(data, page) {

                    return {
                        results: $.map(data, function(item) {
                            console.log(item);
                            return {
                                text: item.nombreappe,
                                id: item.id
                            }
                            var option = new Option(item.nombreappe, item.id, true, true);
                            studentSelect.append(option).trigger('change');
                        })
                    };
                },
            },
        });
    });
</script>
@endsection