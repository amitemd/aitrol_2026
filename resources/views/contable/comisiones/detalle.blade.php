@extends('contable.comisiones.base')
@section('action-content')
    <style type="text/css">
        .table>tbody>tr>td,
        .table>tbody>tr>th {
            padding: 0.4%;
            text-align: center;
        }
    </style>
    <!-- Main content -->
    <section class="content">
        <div class="box box-success">
            <div class="box-header">
                <div class="row">
                    <div class="col-md-9">
                        <h3 class="box-title">Reporte de Comisiones del Doctor : {{ $cabecera->nombreDoctorComision->nombre }}
                        </h3>
                    </div>
                    <div class="col-md-3 text-right">
                        <button class="btn btn-danger" onclick="history.back()">
                            Regresar
                        </button>
                    </div>
                </div>
            </div>
            <div id="example1_wrapper">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-hover dataTable" role="grid"
                                aria-describedby="example1_info">
                                <thead>
                                    <th>
                                        Producto
                                    </th>

                                    <th style="color:red">
                                        Valor Real(recibo de cobro)
                                    </th>
                                    <th>
                                        Comisión
                                    </th>
                                  
                                </thead>
                                <tbody>

                                    @foreach($detalle as $value)
                                  
                                        <tr>
                                            <td>
                                                {{$value->producto->nombre}}
                                            </td>

                                            <td>

                                                {{'$' .number_format($value->precio,2)}}
                                            </td>

                                            <td>
                                                {{'$' .number_format($value->comision,2)}}
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
