@extends('laboratorio.mantenimiento_tubos.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="">
                {{ csrf_field() }}
                
                <div class="col-md-8">
                    <h3 class="box-title"> Mantenimiento Tipo Tubo </h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('tipo_tubo.crear')}}" class="btn btn-primary" >Crear </a>
                </div>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                <tr>

                                         <th>Nombre</th>
                                        <th> Estado </th>
                                        <th> Color </th>
                                        <th> Editar </th>
                                       
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($mantenimientos_tubos as $mantenimiento_tubo)

                                    <tr>

                                        <td>{{$mantenimiento_tubo->nombre}}</td>
                                        <td>{{$mantenimiento_tubo->estado}}</td>
                                        <td style="background-color: {{$mantenimiento_tubo->color}}">{{$mantenimiento_tubo->color}}</td>

                                        <td>
                                            <a href="{{Route('tipo_tubo.editar', ['id'=>$mantenimiento_tubo->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                            <a href="{{Route('tipo_tubo.delete', ['id'=>$mantenimiento_tubo->id])}}" class="btn btn-danger"><i class="glyphicon glyphicon-trash "></i></a>

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