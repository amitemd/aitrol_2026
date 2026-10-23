@extends('sri_electronico.infotributaria.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="{{route('deinfotributaria.index')}}">
                {{ csrf_field() }}

                <div class="col-md-8">
                    <h3 class="box-title"> {{trans('infoTributaria.Mantenimiento')}}</h3>
                </div>


                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>{{trans('infoTributaria.ID_Maestro_Documentos')}}</th>
                                        <th>{{trans('infoTributaria.Empresa')}} </th>
                                        <th>{{trans('infoTributaria.Caja')}} </th>
                                        <th>{{trans('infoTributaria.Numero_Documento')}}</th>
                                        <th>{{trans('infoTributaria.Secuencial')}}</th>
                                        <th>{{trans('infoTributaria.Codigo_Sucursal')}}</th>
                                        <th>{{trans('infoTributaria.Codigo_Caja')}}</th>
                                        <th>{{trans('infoTributaria.Estado')}} </th>
                                        <th>{{trans('infoTributaria.Editar')}} </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($de_info_tributaria as $dit)
                                    @php
                                    $obtener_doc = Sis_medico\De_Maestro_Documentos::find($dit->id_maestro_documentos);
                                    @endphp
                                    <tr>
                                        <td>{{$dit->id}}</td>
                                        <td>
                                            {{$obtener_doc->nombre}}
                                        </td>
                                        <td>{{$dit->id_empresa}}</td>
                                        <td>{{$dit->id_caja}}</td>
                                        <td>{{$dit->numero_factura}}</td>
                                        <td>{{$dit->secuencial_nro}}</td>
                                        <td>{{$dit->cod_sucursal}}</td>
                                        <td>{{$dit->cod_caja}}</td>
                                        <td>
                                            @if($dit->estado == 0)
                                            {{trans('infoTributaria.Inactivo')}}
                                            @elseif($dit->estado == 1)
                                            {{trans('infoTributaria.Activo')}}
                                            @endif

                                        </td>
                                        <td>
                                            <a href="{{route('deinfotributaria.edit' ,['id'=>$dit->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
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