@extends('insumos.plantilla_control.base')
@section('action-content')
<style>
    th {
        text-align: center;
    }

    td {
        text-align: center;
    }   
    
    .contenido-section{
        font-family: 'Roboto', sans-serif;
    }

    
</style>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
<!-- Main content -->
<section class="content contenido-section">
    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="box-title">Lista Plantillas Control</h3>
                </div>
                <div class="col-sm-6">
                    <a class="btn btn-primary" href="{{route('plantilla_procedimiento.crear')}}">Agregar Plantilla</a>
                </div>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <div class="row">
                <div class="col-sm-6"></div>
                <div class="col-sm-6"></div>
            </div>
            <!--AQUI VA EL BUSCADOR-->
            <form method="POST"  action="{{route('plantilla_procedimiento.buscar')}}">
                {{ csrf_field() }}
                <div class="row">
                    <div class="form-group col-md-4 ">
                        <label for="nombre" class="col-md-4 control-label">Nombre:</label>
                        <div class="col-md-7">
                            <input id="nombre" value="{{$nombre}}" type="text" class="form-control input-sm" name="nombre">
                        </div>
                    </div>
                    <div class="form-group col-md-2 ">
                        <button type="submit" class="btn btn-primary"> <span class="glyphicon glyphicon-search"> Buscar </span></button>
                    </div>
                </div>

            </form>

            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                    <div class="table-responsive col-md-12">
                        <table id="frmpro" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                            <thead>
                                <tr role="row">

                                    <th width="10%" class="sorting_asc" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="ascending">Código</th>
                                    <th width="30%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Nombre</th>
                                    <th width="10%" class="sorting" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Email: activate to sort column descending" aria-sort="sorting">Estado</th>
                                    <th width="20%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Action: activate to sort column ascending">Acción</th>

                                </tr>
                            </thead>
                            <tbody>

                                @foreach($plantillas as $value)
                                
                                <tr>
                                    <td>{{$value->codigo}}</td>
                                    <td>{{$value->nombre}}</td>
                                    <td>@if($value->estado==1) Activo @else Inactivo @endif</td>
                                    <td>
                                        <a href="{{route('plantilla_procedimiento.edit',['id' =>$value->id])}}" class="btn btn-warning ">
                                            Actualizar
                                        </a>
                                        <a href="{{route('plantilla_procedimiento.item_lista',['id' =>$value->id])}}" class="btn btn-success ">
                                            Items
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                        <div class="col-md-12">
                            <div class="text-right">
                                {{ $plantillas->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- /.box-body -->
</section>
<!-- /.content -->
@endsection