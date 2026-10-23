<style>
    .centro {
        text-align: center;
    }
</style>

<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid">
                <div class="box-header with-border">
                </div>
                <!-- /.box-header -->
                {{-- <div class="box-body">
                <div class="col-md-4">
                  <dl> 
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                <dd>&nbsp; {{$empresa->nombrecomercial}}</dd>
                </dl>
            </div>
            <div class="col-md-4">
                <h4 style="text-align: center;">Credito Acreedores</h4>
                <h4 style="text-align: center;">Periodo {{$desde}} - {{$hasta}}</h4>
            </div>
            <div class="col-md-4">
                <dl>
                    <dd style="text-align:right">{{$empresa->direccion}} &nbsp; <i class="fa fa-building"></i></dd>
                    <dd style="text-align:right">Telf: {{$empresa->telefono1}} - {{$empresa->telefono2}}&nbsp;<i class="fa fa-phone"></i> </dd>
                    <dd style="text-align:right"> {{$empresa->email}} &nbsp;<i class="fa fa-envelope-o"></i></dd>
                </dl>
            </div>
        </div> --}}

        <div class="box-body">
            <div class="col-md-1">
                <dl>
                    <dd><img src="{{asset('/logo').'/'.$empresa->logo}}" alt="Logo Image" style="width:80px;height:80px;" id="logo_empresa"></dd>
                    {{-- <dd>&nbsp; {{$empresa->nombrecomercial}}</dd> --}}
                </dl>
            </div>
            <div class="col-md-3">
                <dl>
                    <dd><strong>{{$empresa->nombrecomercial}}</strong></dd>
                    <dd>&nbsp; {{$empresa->id}}</dd>
                </dl>
            </div>
            <div class="col-md-4">
                <h4 style="text-align: center;">Informe de Saldos</h4>
                <h5 style="text-align: center;"> @if($desde!=null) Desde {{date("d-m-Y", strtotime($desde))}} Hasta - {{date("d-m-Y", strtotime($hasta))}} @else Al {{date("d-m-Y", strtotime($hasta))}} @endif</h5>
            </div>
            <div class="col-md-4">
            </div>
            <div class="row">
                <div class="table-responsive col-md-12">
                    <div class="content">
                        <table id="example2" class="table table-striped" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                            <thead>
                                <tr class='well-dark'>
                                    <th width="10%" class="centro" tabindex="0" aria-controls="example2" rowspan="2">Fecha</th>
                                    <th width="40%" class="centro" tabindex="0" aria-controls="example2" rowspan="2">Numero</th>
                                    <th width="20%" class="centro" tabindex="0" aria-controls="example2" colspan="1">Detalle</th>
                                    <th width="20%" class="centro" tabindex="0" aria-controls="example2" colspan="1">Valor</th>
                                    <th width="20%" class="centro" tabindex="0" aria-controls="example2" colspan="1">Total</th>
                                </tr>
                            </thead>
                            <tbody class="centro">
                                @php
                                $suma=0;
                                @endphp
                                @foreach($ct_credito_acreedores as $value)
                                @php
                                $asiento_cabecera = \Sis_medico\Ct_Asientos_Cabecera::where('id', $value->id_asiento_cabecera)->where('estado', '1')->first();
                                //dd($asiento_cabecera);
                                $suma +=$value->valor_contable;
                                @endphp
                                <tr>
                                    <td>{{substr($asiento_cabecera->fecha_asiento, 0, 11)}}</td>
                                    <td>{{$asiento_cabecera->fact_numero}}</td>
                                    <td>{{$asiento_cabecera->observacion}}</td>
                                    <td>{{$value->valor_contable}}</td>
                                    <td>{{$value->valor_contable}}</td>
                                </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td></td>
                                    <td><label>Total: {{$suma}}</label></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>