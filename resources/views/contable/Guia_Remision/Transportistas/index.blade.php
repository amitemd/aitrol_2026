@extends('contable.Guia_Remision.Transportistas.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="">
                {{ csrf_field() }}

                <div class="col-md-8">
                    <h3 class="box-title"> Mantenimiento Transportistas </h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('transportistas.crear')}}" class="btn btn-primary">Crear </a>
                </div>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                        <th>Identificación/RUC</th>
                                        <th> Razón Social </th>
                                        <th> Nombres </th>
                                        <th> Apellidos </th>
                                        <th> ID Empresa </th>
                                        <th> Nombre Comercial </th>
                                        <th> Ciudad </th>
                                        <th> Dirección </th>
                                        <th> Email </th>
                                        <th> Email 2 </th>
                                        <th> Teléfono 1</th>
                                        <th> Teléfono 2 </th>
                                        <th> Placa </th>
                                        <th> Tipo Documento </th>
                                        <th> RISE </th>
                                        <th> Contabilidad </th>
                                        <th> Contribuyente Especial </th>
                                        <th> Editar </th>

                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($mantenimientos_transportistas as $mt)
                                    <tr>

                                        <td>{{$mt->ci_ruc}}</td>
                                        <td>{{$mt->razon_social}}</td>
                                        <td>{{$mt->nombres}}</td>
                                        <td>{{$mt->apellidos}}</td>
                                        <td>{{$mt->id_empresa}}</td>
                                        <td>{{$mt->nombrecomercial}}</td>
                                        <td>{{$mt->ciudad}}</td>
                                        <td>{{$mt->direccion}}</td>
                                        <td>{{$mt->email}}</td>
                                        <td>{{$mt->email2}}</td>
                                        <td>{{$mt->telefono1}}</td>
                                        <td>{{$mt->telefono2}}</td>

                                        <td>{{$mt->placa}}</td>
                                        <td>@if($mt->tipo_documento == 4)
                                            RUC
                                            @elseif($mt->tipo_documento == 5)
                                            Cédula
                                            @elseif($mt->tipo_documento == 6)
                                            Pasaporte
                                            @elseif($mt->tipo_documento == '08')
                                            Identificación del Exterior
                                            @endif
                                        </td>
                                        <td>
                                            @if($mt->rise == 1)
                                            SI
                                            @else
                                            NO
                                            @endif
                                        </td>
                                        <td>@if($mt->contabilidad == 1)
                                            SI
                                            @else
                                            NO
                                            @endif
                                        </td>
                                        <td>@if($mt->contribuyente_especial == 1)
                                            SI
                                            @else
                                            NO
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{Route('transportistas.editar', ['id'=>$mt->ci_ruc])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                            
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
