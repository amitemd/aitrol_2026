<style>
    .cabera_titulo {
        text-align: center;
        font-size: 1.4rem;
        background: #3d7ba8;
        color: white;
    }
    .estilo_box{
        margin-bottom: 5px;
        background: #3c8dbc;
        color: white;
        border-radius: 5px;
    }
    .box.box-primary {
        border-top-color: transparent!important;
    }
</style>

@php
$acumt = 0; $cabeceras = array(1=>'HONORARIOS MEDICOS', 2=>'MEDICINAS VALOR AL ORIGEN', 3=>'INSUMOS VALOR AL ORIGEN', 4=>'IMAGEN (*)', 5=>'SERVICIOS INSTITUCIONALES', 0=>'OTROS'); $acum_honorarios = 0;
@endphp

@if ($detalles!='[]')
@foreach ($cabeceras as $key => $row)
<div class="table-responsive col-md-12">
    <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
        <div class="box-header estilo_box">
            <div class="col-md-12" style="padding-left: 0px;">
                <h3 class="box-title">
                    <b style="color:white">{{ $row }}</b>
                </h3>
            </div>
            <!-- tools box -->
            <div class="pull-right box-tools">
                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="cabecera">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
            <!-- /. tools -->
        </div>
        <div class="box-body" style="padding: 5px;">
            <table style="border: 1px solid;width:100%!important;">
                <thead>
                    <tr style="border: 1px solid;">
                        <th width="8%">{{trans('contableM.fecha')}}</th>
                        <th width="8%">C&oacute;digo</th>
                        <th width="10%">Serie</th>
                        <th width="35%">Descripci&oacute;n</th>
                        <th width="35%">Descripci&oacute;n Contable</th>
                        <th width="15%">Cant.</th>
                        <th width="15%">Costo Uni.</th>
                        <th width="15%">{{trans('contableM.total')}}</th>
                    </tr>
                </thead>
                @php $acumtotal = 0; @endphp

                <tbody>
                    @if($key == '1')
                    @foreach($detalle_pdf as $dpdf)
                    @php 
                        $acum_honorarios = $acum_honorarios + $dpdf->valor; 
                        
                    @endphp
                    <tr>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{substr($dpdf->created_at,0,10)}}</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;"></td>
                        <td style="border-right: 1px solid;border-top: 1px solid;"></td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{$dpdf->descripcion}}</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;"></td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">1</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;"> {{ $dpdf->valor }} </td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;"> {{ $dpdf->valor }} </td>
                    </tr>
                    @endforeach

                    @endif
                    @if ($detalles!='[]')
                    @foreach ($detalles as $item)
                    @php 
                        $nom_contable = "";
                        if ($item->tipo_plantilla==null) {
                            $item->tipo_plantilla=0;
                        } 

                            if(isset($item->producto)){
                                $producto_insumo = Sis_medico\Ct_productos_insumos::where('id_insumo', $item->producto->id)->first();
                                if(!is_null($producto_insumo)){
                                    if(isset($producto_insumo->ct_producto)){
                                        $nom_contable=$producto_insumo->ct_producto->nombre;
                                    }else{
                                        $nom_contable = "<span style='color:red; font-weight:bold;'> NO ENCUENTRA EL PRODUCTO CONTABLE</span>";
                                    }
                                }else{
                                    $nom_contable = "<span style='color:red; font-weight:bold;'> NO ENCUENTRA EL LIGUE CON CONTABLE</span>";
                                }
                            }else{
                                $nom_contable = "<span style='color:red; font-weight:bold;'> NO ENCUENTRA EL PRODUCTO</span>";
                            }
                        
                    @endphp

                    @if (isset($item->producto) and $item->producto->tipo == $key)
                    @if ($item->check==1)
                    <tr>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{date('d/m/Y', strtotime($item->created_at))}}</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;">{{$item->codigo}}</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;">{{$item->serie}}</td>

                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">
                            @php

                            if(Auth::user()->id == "0957258056"){
                            //    dd($item);
                            }

                            if(isset($item->producto)){
                            $producto_contable = $item->producto->producto_contable();
                            
                            } else {
                            $producto_contable ='[]';
                            }

                            @endphp
                            @if($producto_contable!='[]' or $producto_contable!=null or count($producto_contable) > 0)
                           
                                @if(isset($item->producto))
                                    {{$item->producto->nombre}}
                                @endif
                            @endif

                        </td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">@php echo $nom_contable; @endphp</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{$item->cantidad}}</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($item->precio, 2, '.', ' ')}}</td>
                        @php $subt = $item->cantidad * $item->precio; @endphp

                        @php $imp = 0; $iva = 0; $porcent =0;

                        $total = ($subt+$porcent+$iva);
                        @endphp

                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format($total, 2, '.', ' ')}}</td>
                    </tr>
                    @php $acumtotal += $total; $acumt += $total; @endphp
                    @endif

                    @endif
                    @endforeach
                    @endif
                </tbody>

                <tfoot>
                    @if($key == '1')
                    <tr>
                        <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align: right;" colspan="6">TOTAL {{$row}}</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align: right;" colspan="2">{{ number_format($acum_honorarios, 2, '.', ' ') }}</td>
                    </tr>
                    @else
                    <tr>
                        <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align: right;" colspan="6">TOTAL {{$row}}</td>
                        <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align: right;" colspan="2">{{ number_format($acumtotal, 2, '.', ' ') }}</td>
                    </tr>
                    @endif
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endforeach

<div class="table-responsive col-md-12">
    <div class="box box-primary collapsed-box" style="margin-bottom: 5px;">
        <div class="box-header estilo_box">
            <div class="col-md-12" style="padding-left: 0px;">
                <h3 class="box-title">
                    <b style="color:white">EQUIPOS ESPECIALES</b>
                </h3>
            </div>
            <!-- tools box -->
            <div class="pull-right box-tools">
                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="cabecera">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
            <!-- /. tools -->
        </div>
        <div class="box-body" style="padding: 5px;">
    <table style="border: 1px solid;width:100%;!important">
        <thead>
            <tr>
                <th width="10%">{{trans('contableM.fecha')}}</th>
                <th width="10%">C&oacute;digo</th>
                <th width="50%">Descripci&oacute;n</th>
                <th width="10%">{{trans('contableM.cantidad')}}</th>
                <th width="10%">Costo Uni.</th>
                <th width="10%">{{trans('contableM.total')}}</th>
            </tr>
        </thead>

        <tbody>
            @if ($equipos2!='[]')
            @foreach ($equipos2 as $item_equipos)
            @if (isset($item_equipos))
            <tr>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{date('d/m/Y', strtotime($item_equipos->created_at))}}</td>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 12px;">{{$item_equipos->serie}}</td>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{$item_equipos->nombre}} </td>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;">{{1}}</td>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format(0, 2, '.', '')}}</td>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 14px;text-align: right;">{{number_format(0, 2, '.', '')}}</td>
            </tr>
            @endif
            @endforeach
            @endif
        </tbody>

        <tfoot>
            <tr>
                <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align: right;" colspan="5">TOTAL EQUIPOS ESPECIALES</td>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align: right;">{{number_format(0, 2, '.', '')}}</td>
            </tr>
        </tfoot>
    </table>
    </div>
    </div>
</div>

<br>
<div class="table-responsive col-md-12">
    <table style="border: 1px solid; width: 100%;">
        <tfoot>
            <tr>
                <td style="border-right: 1px solid;border-top: 1px solid; font-size: 16px;font-weight: bold;text-align:right" colspan="6">TOTAL </td>
                <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;text-align:right; width: 10%;">{{number_format(($acumt + $acum_honorarios), 2, '.', ' ') }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@else
<h3>PLANILLA NO APROBADA</h3>
@endif