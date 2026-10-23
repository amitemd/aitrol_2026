@extends('servicios_generales.mantenimiento_excel.base')
@section('action-content')
<style>
    .stilos:focus {

        background: whitesmoke;
    }

    #estilos:hover {
        color: blanchedalmond;
    }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
          <div class="col-md-12">
                <div class="row">
                    <form method="POST" id="form" action="{{route('documento.buscar')}}">
                        {{ csrf_field() }}
                        <div class="form-group col-md-6">
                            <label for="fecha" class="col-md-4 texto">Nombre</label>
                            <div class="col-md-5">
                                <input type="text" name="nombre" id="nombre" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-sm-6">
                            <button type="submit" name="button" class="btn btn-primary">buscar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                    <div class="col-sm-12">
                        <table id="example" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead style="background-color: #337ab7;color:white;">
                                <tr role="row" id="cabezera">
                                    <th>Nombre</th>
                                    <th>Tiempo de Resultado</th>
                                    <th>Sugerencia otros nombres</th>
                                    <th>Sugerencia</th>
                                    <th>Acción</th>

                                </tr>
                            </thead>
                            <tbody id="cuerpo">
                                @foreach ($excel as $val)

                                <tr>
                                    <td>{{$val->nombre}}</td>
                                    <td><input onchange="guardar_tiempo({{$val->id }}, this.value)" style="width:99%" type="text"  name="id_tiempo_examen" id="id_tiempo_examen" value="{{$val->tiempos}}"></td>
                                    <td>{{$val->nombre_largo}}</td>
                                    <td>{{$val->sugerencia}}</td>
                                    <td> <a id="editar" class="btn btn-info btn-xs" href="{{ route('documento.excel_actualizar', ['id' => $val->id]) }}"><span>Editar</span></a></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="col-md-12 text-right">
                            {{ $excel->appends(request()->except('page'))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    function guardar_tiempo(id, tiempo_var){
    console.log(id, tiempo_var);
    $.ajax({
            //async: true,
            type: "POST",
            url: "{{route('examen_mt.update')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: "html",
            data: {
             'id_examen':id, 'tiempo':tiempo_var
            },
            success: function(data){
                //alert(data);
                //$("#area_trabajo").html(data);
                console.log(data);
            },
            error:  function(){
                alert('error al cargar');
            }
        });
    }
</script>  
@endsection
