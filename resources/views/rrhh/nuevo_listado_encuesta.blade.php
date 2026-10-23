@extends('rrhh.resultados.base')
@section('action-content')


<!-- Main content -->
<section class="content">


    <div class="box">
        <div class="box-header">
            <div class="row">
                <div class="col-sm-8">
                    <h3 class="box-title">{{trans('encuestas.listaresultadosdeencuesta')}}</h3>
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
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <form id="form_buscador" method="POST" action="{{ route('tiposugerencia.listado_detalle',['id' => $id])}}">
                {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-3">
                                <label for="anio" class="texto col-md-2 control-label">Año:</label>
                                <div class="col-md-9">
                                    @php $x = date('Y'); @endphp
                                    <select id="anio" name="anio" class="form-control">
                                        @for($i=2021;$i<=$x;$i++) <option @if($anio==$i) selected @endif value='{{$i}}'>{{$i}}</option>;
                                            @endfor

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                @php
                                $meses = array('Seleccione' ,'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio','Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
                                @endphp
                                <label for="mes" class="texto col-md-2 control-label">Mes:</label>
                                <div class="col-md-9">

                                    <select id="mes" name="mes" class="form-control">
                                        @foreach($meses as $key => $imes)
                                        <option @if($mes==$key) selected @endif value='{{$key}}'>{{$imes}}</option>;
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="col-md-8">
                                    <button type="submit" class="btn btn-primary"> <i class="fa fa-search"></i> Buscar</button>
                                </div>
                            </div>
                        </div>

                        <br> <br>

                        <div class="col-sm-12">


                            @foreach($encuesta as $value)
                            @php
                            $master_encuesta = Sis_medico\Master_encuesta::find($value->id_area);
                            @endphp
                            <div class="box box-success">
                                <div class="box-header with-border">
                                    <h4>{{trans('encuestas.encuestas')}} N°{{$value->id}} / {{$master_encuesta->descripcion}}</h4>
                                </div>
                                <div class="box-body">
                                    @foreach($grupopregunta as $grupo)
                                    @php
                                    $preguntas= $value->complementos->where('id_grupo',$grupo->id);
                                    @endphp

                                    @if($grupo->id ==1)
                                    <br>
                                    <table border="2">
                                        <thead>
                                            <tr role="row">
                                                <th width="20%" style=" font-size: 16px; vertical-align: middle;border-color:black; text-align: center;">Criterios de evaluacion</th>
                                                <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">{{trans('encuestas.excelente')}}</th>
                                                <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">{{trans('encuestas.muybueno')}}</th>
                                                <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">{{trans('encuestas.bueno')}}</th>
                                                <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">{{trans('encuestas.nibuenonimalo')}} </th>
                                                <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">{{trans('encuestas.malo')}}</th>
                                                <th width="5%" style="padding: 0 14px;border-color:black;text-align: center;">{{trans('encuestas.muymalo')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($preguntas as $pregunta)

                                            <tr role="row">
                                                <td style="align-content: center">{{$pregunta->pregunta->nombre}}</td>
                                                <td align="center">@if($pregunta->valor=='5') x @else &nbsp;@endif</td>
                                                <td align="center">@if($pregunta->valor=='4') x @else &nbsp;@endif</td>
                                                <td align="center">@if($pregunta->valor=='3.5') x @else &nbsp;@endif</td>
                                                <td align="center">@if($pregunta->valor=='3') x @else &nbsp;@endif</td>
                                                <td align="center">@if($pregunta->valor=='2.5') x @else &nbsp;@endif</td>
                                                <td align="center">@if($pregunta->valor=='1') x @else &nbsp;@endif</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @endif

                                    @if($grupo->id ==2)
                                    <div class="col-md-12">
                                        <br>
                                        @foreach($preguntas as $pregunta)
                                        <div class="col-md-6" style="vertical-align: middle; border-color: black; font-size: 15px;">{{$pregunta->pregunta->nombre}}</div>
                                        <div class="col-md-6" style="text-align: center; border-color: black;vertical-align: middle;font-size: 15px;">{{$pregunta->valor}}min</div>
                                        @endforeach
                                    </div>
                                    @endif

                                    @if($grupo->id ==4)
                                    <div class="col-md-12">
                                        <br>
                                        @foreach($preguntas as $pregunta)
                                        <div class="col-md-6" style="vertical-align: middle; border-color: black; font-size: 15px;">{{$pregunta->pregunta->nombre}}</div>
                                        <div class="col-md-6" style="text-align: center; border-color: black;vertical-align: middle;font-size: 15px;">{{$pregunta->valor}}</div>
                                        @endforeach
                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </form>

            </div>
        </div>
        <!-- /.box-body -->
    </div>
</section>
<!-- /.content -->
</div>

<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>

<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">
    $(document).ready(function($) {

        $('#fecha').datetimepicker({
            format: 'YYYY-MM-DD',


            defaultDate: '',

        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY-MM-DD',


            defaultDate: '',

        });
        $("#fecha").on("dp.change", function(e) {
            buscar();
        });

        $("#fecha_hasta").on("dp.change", function(e) {
            buscar();
        });

        $(".breadcrumb").append('<li class="active">Órdenes</li>');

        $('#example2').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': true,
            'info': false,
            'autoWidth': false
        })



    });

    $('#doctor').on('hidden.bs.modal', function() {
        location.reload();
        $(this).removeData('bs.modal');
    });


    function buscar() {
        var obj = document.getElementById("boton_buscar");
        obj.click();
    }
</script>

@endsection