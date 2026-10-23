<section class="content" style="position: fixed;">

    <div class="box-header">
        <div class="col-md-7">
            <h5><b>{{trans('nomina.Decimo Cuarto Sueldo')}}</b></h5>
        </div>

    </div>
    <div class="row head-title">
        <div class="col-md-12 cabecera">
            <label class="color_texto" for="title">{{ $nomina->nombres }}</label>
        </div>
    </div>
    <div class="box-body dobra">
        <div class="form-group col-md-12">
            <div class="form-row">

                <div id="contenedor">
                    <div id="example2det_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                        <div class="row">
                            <div class="table-responsive col-md-12">
                                <table id="example2det" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2det_info" style="width: 100%">
                                    <thead>
                                        <tr class='well-dark'>
                                            <th width="30%" style="text-align: center;">{{trans('nomina.rol')}}</th>
                                            <th width="30%" style="text-align: center;">{{trans('nomina.anio')}}</th>
                                            <th width="30%" style="text-align: center;">{{trans('nomina.mes')}}</th>
                                            <th width="40%" style="text-align: center;">{{trans('nomina.valor')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>@php $acum = 0 @endphp

                                        @foreach ($roles1 as $value)
                                        @php $nrol = Sis_medico\Ct_Rol_Pagos::find($value->rid); @endphp
                                        <tr style="text-align: center;">
                                            <td>{{$nrol->id}}</td>
                                            <td>{{$nrol->anio}}</td>
                                            <td> @if($nrol->mes == '1') {{trans('nomina.enero')}}
                                                @elseif($nrol->mes == '2') {{trans('nomina.febrero')}}
                                                @elseif($nrol->mes == '3') {{trans('nomina.marzo')}}
                                                @elseif($nrol->mes == '4') {{trans('nomina.abril')}}
                                                @elseif($nrol->mes == '5') {{trans('nomina.mayo')}}
                                                @elseif($nrol->mes == '6') {{trans('nomina.junio')}}
                                                @elseif($nrol->mes == '7') {{trans('nomina.julio')}}
                                                @elseif($nrol->mes == '8') {{trans('nomina.agosto')}}
                                                @elseif($nrol->mes == '9') {{trans('nomina.septiembre')}}
                                                @elseif($nrol->mes == '10') {{trans('nomina.octubre')}}
                                                @elseif($nrol->mes == '11') {{trans('nomina.noviembre')}}
                                                @elseif($nrol->mes == '12') {{trans('nomina.diciembre')}}
                                                @endif
                                            </td>
                                            <td>{{$nrol->detalle->first()->dcuarto_acumulado_final}}</td>@php $acum += $nrol->detalle->first()->dcuarto_acumulado_final; @endphp
                                        </tr>
                                        @endforeach
                                        @foreach ($roles2 as $value)
                                        @php $nrol = Sis_medico\Ct_Rol_Pagos::find($value->rid); @endphp
                                        <tr style="text-align: center;">
                                            <td>{{$nrol->id}}</td>
                                            <td>{{$nrol->anio}}</td>
                                            <td> @if($nrol->mes == '1') {{trans('nomina.enero')}}
                                                @elseif($nrol->mes == '2') {{trans('nomina.febrero')}}
                                                @elseif($nrol->mes == '3') {{trans('nomina.marzo')}}
                                                @elseif($nrol->mes == '4') {{trans('nomina.abril')}}
                                                @elseif($nrol->mes == '5') {{trans('nomina.mayo')}}
                                                @elseif($nrol->mes == '6') {{trans('nomina.junio')}}
                                                @elseif($nrol->mes == '7') {{trans('nomina.julio')}}
                                                @elseif($nrol->mes == '8') {{trans('nomina.agosto')}}
                                                @elseif($nrol->mes == '9') {{trans('nomina.septiembre')}}
                                                @elseif($nrol->mes == '10') {{trans('nomina.octubre')}}
                                                @elseif($nrol->mes == '11') {{trans('nomina.noviembre')}}
                                                @elseif($nrol->mes == '12') {{trans('nomina.diciembre')}}
                                                @endif
                                            </td>
                                            <td>${{$nrol->detalle->first()->dcuarto_acumulado_final}}</td>@php $acum += $nrol->detalle->first()->dcuarto_acumulado_final; @endphp
                                        </tr>
                                        @endforeach
                                        <tr>
                                            <td style="text-align: center;"><b>TOTAL</b></td>
                                            <td></td>
                                            <td></td>
                                            <td style="text-align: center;">${{$acum}}</td>
                                        </tr>
                                    </tbody>
                           
                                    <tfoot>
                                    </tfoot>

                                </table>
                            </div>
                        </div>
                        <div class="row">

                        </div>
                    </div>

                </div>
            </div>


</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script type="text/javascript">





</script>