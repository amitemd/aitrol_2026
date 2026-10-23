@extends('comercial.plantilla.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
        </div>
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="">
                {{ csrf_field() }}
                
                <!-- <div class="form-group col-md-2 ">
                    <div class="col-md-7">
                        <button type="submit" class="btn btn-primary" id="boton_buscar">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar </button>
                    </div>
                </div> -->

                <div class="form-group col-md-2" >
                    <div class="col-md-7">
                        <a href="{{route('proforma.crear_plantilla')}}" class="btn btn-info">Crear Plantilla</a>
                    </div>
                </div>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                         <th>Id</th>
                                        <th> Codigo </th>
                                        <th>Nombre</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($pro_agrupadores as $pro_agrupador)

                                    <tr>
                                        <td>{{$pro_agrupador->id}}</td>
                                        <td>{{$pro_agrupador->codigo}}</td>
                                        <td>{{$pro_agrupador->nombre}} </td>
                                        <td>
                                            <a href="{{route('proforma.index_plantilla_detalle' ,['id'=>$pro_agrupador->id])}}" class="btn btn-success"><i class="fa fa-plus"> DETALLE </i></a>
                                            <a href="{{route('proforma.editar_plantilla' ,['id'=>$pro_agrupador->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>




@endsection