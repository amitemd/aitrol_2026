@extends('sri_electronico.de_maestro_documentos.base')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="{{route('demaestrodoc.index')}}">
                {{ csrf_field() }}
                <div class="col-md-8">
                    <h3 class="box-title"> {{trans('maestroDocumentos.Mantenimiento_Documentos_Maestros')}}</h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('demaestrodoc.create')}}" class="btn btn-primary">{{trans('maestroDocumentos.Crear')}} </a>

                </div>
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{{trans('maestroDocumentos.Nombre')}}</th>
                                        <th>{{trans('maestroDocumentos.Codigo')}}</th>
                                        <th>{{trans('maestroDocumentos.Estado')}} </th>
                                        <th>{{trans('maestroDocumentos.Editar')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($de_maestro_documentos as $dmd)

                                    <tr>
                                        <td>{{$dmd->id}}</td>
                                        <td>{{$dmd->nombre}}</td>
                                        <td>{{$dmd->codigo}}</td>
                                        <td>
                                            @if($dmd->estado == 0)
                                            {{trans('maestroDocumentos.Inactivo')}}
                                            @elseif($dmd->estado == 1)
                                            {{trans('maestroDocumentos.Activo')}}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{route('demaestrodoc.edit' ,['id'=>$dmd->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
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