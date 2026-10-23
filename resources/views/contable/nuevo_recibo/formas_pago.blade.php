<link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .vtdobra {
        background-color: #eafcff;
    
        padding: 0;
    }
    .vtdobra2 {
        background-color: #eafcff;
    
        padding: 0;
    }
    .select2 {
        width: 100% !important;
        background-color: #eafcff !important;
    }

</style>

@if( $orden->pagos->sum('valor') < $orden->total  ) <span style="color: blue;"> Valor por pagar: {{  $orden->total - $orden->pagos->sum('valor') }}</span>@endif @if( $orden->pagos->count() > 0 ) @if( $orden->pagos->sum('valor') > $orden->total  ) <span style="color: red;"><b>*** Valor Pagado es mayor que el valor del Recibo</b> </span> @endif @endif

<div class="panel panel-default">
    <div class="panel-body" style="padding:0;">
        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
            <thead>
                <tr class='well' style="color: black;">
                    <th width="20%" tabindex="0">Metodos</th>
                    <th width="10%" tabindex="0">Fecha</th>
                    <th width="10%" tabindex="0">Tipo</th>
                    <th width="10%" tabindex="0">Número</th>
                    <th width="10%" tabindex="0">Banco</th>
                    <th width="10%" tabindex="0">Cuenta</th>
                    <!--th width="10%" tabindex="0">Girado</th-->
                    <!--th width="5%" tabindex="0">Valor</th-->
                    <th width="10%" tabindex="0">Valor</th>
                    <th width="10%" tabindex="0">Acción</th>
                </tr>
            </thead>
            <tbody >
                @foreach($detalles as $detalle)
                    <tr>
                        <td>
                            <div class="form-group">
                                {{ $detalle->metodo->nombre }}
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                {{ $detalle->fecha }}
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                @if($detalle->tarjeta != null) {{ $detalle->tarjeta->nombre }} @endif
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                {{ $detalle->numero }}
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                @if($detalle->ct_banco != null) {{ $detalle->ct_banco->nombre }} @endif
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                {{ $detalle->cuenta }}
                            </div>
                        </td>
                        <td>
                            <div class="form-group">
                                {{ number_format($detalle->valor, 2, ',', ' ') }}
                            </div>
                        </td>
                        <td><button type="button" class="btn btn-danger" onclick="eliminar_forma_pago('{{ $detalle->id }}')">Eliminar</button></td>   
                    </tr>
                @endforeach
                <tr>
                    <td>
                        <div class="form-group">
                            <select id="fp_metodo_nuevo" name="fp_metodo_nuevo" required placeholder="Seleccione Metodo de Pago" onchange="actualizar_metodos_pago()" > 
                                @if($orden->pagos->count() > 0)<option value="">Seleccione Metodo de Pago</option>@endif    
                                @foreach($tipo_pago as $tp)
                                    <option value="{{ $tp->id }}">{{ $tp->nombre }}</option>
                                @endforeach    
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <input type="date" class="form-control vtdobra" name="fp_fecha_nueva" id="fp_fecha_nueva" value="{{ date('Y-m-d') }}">
                        </div>
                    </td>
                    <td>
                        <div class="form-group" style="display: none;" id="div_fp_tarjetanueva">
                            <select id="fp_tarjetanueva" class="form-control vtdobra" name="fp_tarjetanueva" required placeholder="Seleccione La Tarjeta" > 
                                <option value="">Seleccione La Tarjeta</option>    
                                @foreach($tipo_tarjeta as $tt)
                                    <option value="{{ $tt->id }}">{{ $tt->nombre }}</option>
                                @endforeach    
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group" style="display: none;" id="div_fp_numero_nuevo">
                            <input type="text" class="form-control vtdobra" name="fp_numero_nuevo" id="fp_numero_nuevo" >
                        </div>
                    </td>
                    <td>
                        <div class="form-group" style="display: none;" id="div_fp_banco">
                            <select id="fp_banco" class="form-control vtdobra" name="fp_banco" required placeholder="Seleccione Banco" > 
                                <option value="">Seleccione Banco</option>    
                                @foreach($lista_banco as $lb)
                                    <option value="{{ $lb->id }}">{{ $lb->nombre }}</option>
                                @endforeach    
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group" style="display: none;" id="div_fp_cuenta_nueva">
                            <input type="text" class="form-control vtdobra" name="fp_cuenta_nueva" id="fp_cuenta_nueva"  >
                        </div>
                    </td>
                    <!--td>
                        <div class="form-group">
                            <input type="text" class="form-control vtdobra" name="fp_girado_nuevo" id="fp_girado_nuevo" >
                        </div>
                    </td-->
                    <td>
                        <div class="form-group">
                            <input type="text" class="form-control vtdobra" name="fp_valor_nuevo" id="fp_valor_nuevo" @if($orden->pagos->count() == 0) value="{{ $orden->total }}" @endif onkeypress="return soloNumeros(this);" >
                        </div>
                    </td> 
                    <td>
                        <button type="button" class="btn btn-info" onclick="seleccionar_metodo()">Guardar</button>
                    </td>  
                </tr>
            </tbody>
        </table>

    </div>

</div>

<script type="text/javascript">
    
    function actualizar_metodos_pago(){
        var fp_metodo_nuevo = $('#fp_metodo_nuevo').val();

        if(fp_metodo_nuevo > 2 && fp_metodo_nuevo < 7){
            $('#div_fp_tarjetanueva').show();
            $('#div_fp_numero_nuevo').show();
            $('#div_fp_banco').show();
            $('#div_fp_cuenta_nueva').show();
        }else if( fp_metodo_nuevo == 2 ){
            $('#div_fp_tarjetanueva').hide();
            $('#div_fp_numero_nuevo').show();
            $('#div_fp_banco').show();
            $('#div_fp_cuenta_nueva').show();
            $('#fp_tarjetanueva').val('');
            $('#fp_numero_nuevo').val('');
            $('#fp_banco').val('');
            $('#fp_cuenta_nueva').val('');
        }else{
            $('#div_fp_tarjetanueva').hide();
            $('#div_fp_numero_nuevo').hide();
            $('#div_fp_banco').hide();
            $('#div_fp_cuenta_nueva').hide();
            $('#fp_tarjetanueva').val('');
            $('#fp_numero_nuevo').val('');
            $('#fp_banco').val('');
            $('#fp_cuenta_nueva').val('');
        }
    }    
</script>





