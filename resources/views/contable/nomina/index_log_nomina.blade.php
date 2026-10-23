@extends('contable.nomina.base')
@section('action-content')
@php
$tipo_usuario = Auth::user()->id_tipo_usuario;
@endphp

<section class="content">

    <div class="box-body dobra">
        <div class="form-group col-md-12">
            <div class="table-responsive col-md-12">
                <table id="log_auditoria" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="log_auditoria_info" style="width: 100%">
                    <thead>
                        <tr class='well-dark'>
                            <th width="10%" style="text-align: center;"> N°</th>
                            <th width="10%" style="text-align: center;"> Fecha</th>
                            <th width="20%" style="text-align: center;"> Tipo </th>
                            <th width="40%" style="text-align: center;"> Detalle </th>
                            <th width="30%" style="text-align: center;"> Usuario</th>


                        </tr>
                    </thead>

                    <tbody>
                        @php $cont = 0; @endphp
                        @foreach ($log_nomina as $value)
                        <tr>
                            @php $cont++; @endphp
                            <td style="text-align: center;">{{$cont}}</td>
                            <td style="text-align: center;">{{$value->created_at}}</td>
                            <td style="text-align: center;">{{$value->tipo}}</td>
                            <td>{{$value->detalle}}</td>
                            <td style="text-align: center;">{{$value->usuario->apellido1}} {{$value->usuario->apellido2}} {{$value->usuario->nombre1}} {{$value->usuario->nombre2}}</td>

                        </tr>
                        @endforeach


                    </tbody>

                </table>
            </div>

        </div>

</section>

@endsection