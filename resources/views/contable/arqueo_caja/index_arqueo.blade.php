@extends('contable.arqueo_caja.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="row">
                @foreach($arqueos as $value)

                <div class="form-group col-md-3">
                    <h4><b>Fecha proceso:</b>{{$value->fecha_proceso}}</h4>
                </div>
                <div class="form-group col-md-3">
                    <h4><b>Valor Efectivo:</b>{{$value->valor_efectivo}}</h4>
                </div>
                <div class="form-group col-md-3">
                    <h4><b>Usuario:</b>{{$value->usuario}}</h4>
                </div>
                <div class="form-group col-md-3">
                    <h4><b>Caja:</b>{{$value->caja}}</h4>
                </div>

                <div class="form-group col-md-6">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row" id="listado_pro_agrup">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr>
                                            <th>Denominacion</th>
                                            <th> Cantidad </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detalles->where('valor','!=','0.00') as $detalle) <tr>
                                            <td>{{$detalle->denominacion}}</td>
                                            <td>{{$detalle->cantidad}} </td>
                                            </tr>
                                            @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group col-md-6">
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row" id="listado_pro_agrup">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr>
                                            <th>Denominacion</th>
                                            <th> Cantidad </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($detalles->where('valor','!=','0.00') as $detalle)

                                        <tr>
                                            <td>{{$detalle->denominacion}}</td>
                                            <td>{{$detalle->cantidad}} </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection