<?php
$data = config('data.metodopago');
$json_data = json_encode($data);

$dataTarjeta = config('data.tiposTarjeta');
$json_dataTarjeta = json_encode($dataTarjeta);

$dataBanco = config('data.tiposBancos');
$json_dataBanco = json_encode($dataBanco);


$dataBodega = config('data.bodegas');
$json_dataBodega = json_encode($dataBodega);

?>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ ucwords(strtolower(config('data.titulo'))) }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('v3/admin') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">{{ ucwords(strtolower(config('data.titulo'))) }}</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ config('data.titulo') }}</h3>
                        <div>
                     
                            <?php
                            if (config('data.empresa')->egreso_kardex == 1) {
                            ?>
                                <div style="position: absolute; right: 5px;top:7px">
                                    Kardex <input type="checkbox" name="kardex" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <div class="card-body hero-factura">
                        <input type="hidden" id="arrayMetodoPago" value="<?php echo htmlspecialchars($json_data); ?>">
                        <input type="hidden" id="arrayMetodoTarjeta" value="<?php echo htmlspecialchars($json_dataTarjeta); ?>">
                        <input type="hidden" id="arrayMetodoBanco" value="<?php echo htmlspecialchars($json_dataBanco); ?>">
                        <input type="hidden" id="arrayBodegas" value="<?php echo htmlspecialchars($dataBodega); ?>">
                        <input type="hidden" id="idClienteEd" value="{{isset(config('data.conglomeradaRegistro')['ventas']) ? config('data.conglomeradaRegistro')['ventas']->id_cliente : ''}}">
                        <form id="frm_conglomerada">
                            {{ csrf_field() }}
                            <input type="hidden" id="id" name="id" value="{{ isset(config('data.conglomeradaRegistro')['ventas']) ? config('data.conglomeradaRegistro')['ventas']->id : '' }}">
                            <input type="hidden" id="action" value="{{ url('v3/') }}/{{ config('data.controlador') }}/guardarCruceValores" />
                            <input type="hidden" id="d" name="d" />
                            <input type="hidden" value="0" id="electronica" name="electronica" />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">FACTURA CONGLOMERADA</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="fecha">Fecha asiento</label>
                                                    <input type="date" name="fecha" id="fecha" class="form-control" value="{{ isset(config('data.conglomeradaRegistro')['ventas'])? config('data.conglomeradaRegistro')['ventas']->fecha : date('Y-m-d') }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="orden_venta">Orden de venta</label>
                                                    <input type="text" name="orden_venta" id="orden_venta" class="form-control" value="{{ isset(config('data.conglomeradaRegistro')['ventas'])? config('data.conglomeradaRegistro')['ventas']->orden_venta : '' }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="sucursal_empre">Sucursal</label>
                                                    <select class="form-control select2" style="width: 100%;" id="sucursal_empre" name="sucursal_empre">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        foreach (config('data.sucursales') as $row) {
                                                            echo '<option ' . (isset(config('data.conglomeradaRegistro')['ventas']->sucursal) ? (config('data.conglomeradaRegistro')['ventas']->sucursal == $row->codigo_sucursal ? 'selected' : '') : '') . ' value="' . $row->id . '">' . $row->codigo_sucursal . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="punto_emision" class="font-normal">P. Emision
                                                        (Empresa)</label>
                                                    <select class="form-control select2" style="width: 100%;" id="punto_emision" name="punto_emision">
                                                        <option value="">--Seleccione--</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title"></h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label class="font-normal" for="divisas">Divisas</label>
                                                    <select class="form-control select2" style="width: 100%;" id="divisas" name="divisas">
                                                        <option value="">--Seleccione--</option>
                                                        <?php
                                                        if (config('data.divisas') != '' && count(config('data.divisas')) > 0) {
                                                            foreach (config('data.divisas') as $value) {
                                                                echo '<option ' . (isset(config('data.conglomeradaRegistro')["ventas"]) ? (config('data.conglomeradaRegistro')["ventas"]->divisas == $value->id ? 'selected' : '') : '') . ' value="' . $value->id . '">' . $value->descripcion . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="font-normal" for="recaudador">Recaudador</label>
                                                    <select class="form-control select2" style="width: 100%;" id="recaudador" name="recaudador">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        foreach (config('data.recaudador') as $row) {
                                                            echo '<option ' . (isset(config('data.conglomeradaRegistro')['ventas']) ? (config('data.conglomeradaRegistro')['ventas']->id_recaudador == $row->id ? 'selected' : '') : '') . ' value="' . $row->id . '">' . $row->nombre1 . ' ' . $row->apellido1 . ' ' . $row->apellido2 . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="font-normal" for="usuario_vendedor">Vendedor</label>
                                                    <select class="form-control select2" style="width: 100%;" id="usuario_vendedor" name="usuario_vendedor">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        foreach (config('data.vendedor') as $row) {
                                                            echo '<option ' . (isset(config('data.conglomeradaRegistro')['ventas']) ? (config('data.conglomeradaRegistro')['ventas']->ci_vendedor == $row->id ? 'selected' : '') : '') . ' value="' . $row->id . '">' . $row->nombre1 . ' ' . $row->apellido1 . ' ' . $row->apellido2 . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Datos cliente</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <input id="tipo_cliente" type="hidden" class="form-control" name="tipo_cliente">
                                            <input type="hidden" value="0.15" name="ivareal" id="ivareal">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="nombre_cliente">Identificación</label>
                                                    <select id="identificacion" name="identi" class="js-data-identi form-control" style="width: 100%;" autocomplete="off">
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="nombre">Nombre</label>
                                                    <input id="nombre_cliente" type="text" class="form-control" name="nombre_cliente" onchange="reloadCliente()">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="direccion_cliente">Dirección</label>
                                                    <input id="direccion_cliente" type="text" class="form-control" name="direccion_cliente">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="ciudad_cliente">Ciudad</label>
                                                    <input id="ciudad_cliente" type="text" class="form-control" name="ciudad_cliente">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="ciudad_cliente">Mail</label>
                                                    <input id="mail_cliente" type="text" class="form-control" name="mail_cliente">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="ciudad_cliente">Telefono</label>
                                                    <input id="telefono_cliente" type="text" class="form-control" name="telefono_cliente">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Datos generales</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">

                                            <div class="row">
                                                <div class="col-md-10">
                                                    <label class="font-normal" for="concepto">Concepto</label>
                                                    <input id="concepto" type="text" class="form-control" name="concepto" value="{{ isset(config('data.conglomeradaRegistro')['ventas'])? config('data.conglomeradaRegistro')['ventas']->concepto : '' }}">

                                                </div>

                                                <div class="col-md-2">
                                                    <label class="font-normal" for="dias_plazo">Dias Plazo</label>
                                                    <input name="dias_plazo" style="text-align: right" type="text" class="form-control" id="dias_plazo" value="{{ isset(config('data.conglomeradaRegistro')['ventas']) ? config('data.conglomeradaRegistro')['ventas']->dias_plazo : '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary collapsed-card">
                                        <div class="card-header">
                                            <h3 class="card-title">Información Paciente</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">

                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="identificacion_paciente">Identificación</label>
                                                    @if(!isset(config('data.conglomeradaRegistro')['ventas']))
                                                    <input type="hidden" name="identificacion_paciente" id="identificacion_paciente">
                                                    <select id="identi_paciente" name="identi_paciente" class="js-data-paciente form-control" style="width: 100%;" autocomplete="off">
                                                    </select>
                                                    @else
                                                    <input name="identificacion_paciente" type="text" class="form-control" id="identificacion_paciente" value="{{ isset(config('data.conglomeradaRegistro')['ventas']) ? config('data.conglomeradaRegistro')['ventas']->id_paciente : '' }}">
                                                    @endif
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="font-normal" for="nombre_paciente">Nombre Paciente</label>
                                                    <input name="nombre_paciente" class="form-control" type="text" id="nombre_paciente" value="{{ isset(config('data.conglomeradaRegistro')['ventas']) ? config('data.conglomeradaRegistro')['ventas']->nombres_paciente : '' }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="id_seguro">Seguro</label>
                                                    <select class="form-control" name="id_seguro" id="id_seguro">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        foreach (config('data.seguros') as $row) {
                                                            echo '<option' . (isset(config('data.conglomeradaRegistro')['ventas']) ? (config('data.conglomeradaRegistro')['ventas']->seguro_paciente == $row->id ? ' selected' : '') : '') . ' value="' . $row->id . '">' . $row->nombre . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="font-normal" for="procedimiento">Procedimiento</label>
                                                    <input type="text" class="form-control" name="procedimiento" id="procedimiento" value="{{ isset(config('data.conglomeradaRegistro')['ventas']) ? config('data.conglomeradaRegistro')['ventas']->procedimientos : '' }}">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="font-normal" for="fecha_procedimiento">Fecha
                                                        Procedimiento</label>
                                                    <input type="date" name="fecha_procedimiento" id="fecha_procedimiento" class="form-control" value="{{ isset(config('data.conglomeradaRegistro')['ventas']) ? date('Y-m-d', strtotime(config('data.conglomeradaRegistro')['ventas']->fecha_procedimiento)) : '' }}">
                                                </div>

                                                <div class="col-md-3">
                                                    <label class="font-normal" for="tipo_consulta">Tipo
                                                        Consula</label>
                                                    <select class="form-control" name="tipo_consulta" id="tipo_consulta">
                                                        <option value=""> --- Tipo --- </option>
                                                        <option {{ isset(config('data.conglomeradaRegistro')['ventas']) && config('data.conglomeradaRegistro')['ventas']->tipo_consulta == "1" ? 'selected' : '' }} value="1">Consulta</option>
                                                        <option {{ isset(config('data.conglomeradaRegistro')['ventas']) && config('data.conglomeradaRegistro')['ventas']->tipo_consulta == "2" ? 'selected' : '' }} value="2">Procedimiento</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Datos a facturar</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            {{-- Campos Hidden --}}

                                            <input type="hidden" name="valor_totalPagos" id="valor_totalPagos" value="0">
                                            @if(isset(config('data.conglomeradaRegistro')['ventas']))

                                            @if(config('data.conglomeradaRegistro')['ventas']->tipo == 'VENFA-CO' || config('data.conglomeradaRegistro')['ventas']->omni == 1)

                                            @php

                                            $data= \Sis_medico\v3\contable\DetalleVentaConglomerada::where('id_ct_ventas',config('data.conglomeradaRegistro')['ventas']->id)->get()->toArray();
                                            if(config('data.conglomeradaRegistro')['ventas']->omni==1){
                                            $data= \Sis_medico\v3\contable\Ct_Detalle_Venta_Omni::where('id_ct_ventas',config('data.conglomeradaRegistro')['ventas']->id)->get()->toArray();
                                            }

                                            $productos= \Sis_medico\v3\contable\Productos::where('id_empresa',config('data.conglomeradaRegistro')['ventas']->id_empresa)->get();
                                            $result = array();
                                            $key="id_paciente";
                                            foreach ($data as $val) {
                                            if (array_key_exists($key, $val)) {
                                            $result[$val[$key]][] = $val;
                                            }else{
                                            $result[""][] = $val;
                                            }
                                            }
                                            @endphp

                                            <div class="col-md-12" id="items" style=" height:400px; overflow-y:auto; margin-top: 10px;margin-bottom:10px">
                                                @foreach($result as $s=>$z)
                                                <div class="panel panel-default">
                                                    <div class="panel-heading">
                                                        @php
                                                        $ek= \Sis_medico\v3\contable\Pacientes::where('id',$s)->first();
                                                        @endphp
                                                        @if(!is_null($ek))
                                                        <label>{{$ek->apellido1}} {{$ek->apellido2}} {{$ek->nombre1}} {{$ek->nombre2}} </label>
                                                        <div class="panel-body" style="padding:0;">
                                                            <div class="col-md-12 table-responsive " style="padding:0 !important;">
                                                                <table class="table table-bordered table-hover dataTable noacti" role="grid" aria-describedby="example2_info" style="margin-top:0 !important;">
                                                                    <thead>
                                                                        <tr class="well-dark">
                                                                            <th width="35%" tabindex="0">Descripci&oacute;n del Producto</th>
                                                                            <th width="10%" tabindex="0">Cantidad</th>
                                                                            <th width="10%" tabindex="0">Precio</th>
                                                                            <th width="10%" tabindex="0">Cobrar Seguro</th>
                                                                            <th width="10%" tabindex="0">% Desc</th>
                                                                            <th width="10%" tabindex="0">Descuento</th>
                                                                            <th width="10%" tabindex="0">Precio Neto</th>
                                                                            <th width="5%" tabindex="0">IVA</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="entrega">

                                                                        @foreach($z as $a=>$x)
                                                                        @php

                                                                        $precioNeto = $x['precio'] * $x['cantidad'];

                                                                        @endphp
                                                                        <tr>
                                                                            <td style="max-width:100px;">
                                                                                <input type="hidden" class="codigo_producto" />
                                                                                <input type="hidden" name="verid[]" class="verid" value="1">
                                                                                @php
                                                                                $func= DB::table('ct_productos')->where('codigo',$x['id_ct_productos'])->first();

                                                                                @endphp
                                                                                <input type="text" class="form-control" value="{{$func->nombre}}" disabled>
                                                                                <textarea wrap="hard" rows="3" class="form-control px-1 desc_producto mt-1" disabled placeholder="Detalle del producto">{{$x['detalle']}}</textarea>
                                                                                <input type="hidden" name="iva[]" class="iva" value="{{$x['check_iva'] ? $x['check_iva'] : 0}}" />
                                                                            </td>
                                                                            <td>
                                                                                <input class="form-control text-right cneto" disabled type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="@if(!is_null($x['cantidad'])) {{$x['cantidad']}} @endif" required>
                                                                            </td>
                                                                            <td>
                                                                                @php
                                                                                $precio= $x['precio'];
                                                                                if(is_null($precio)){
                                                                                $precio=0.00;
                                                                                }
                                                                                @endphp
                                                                                <input type="text" class="form-control pneto" disabled onkeypress="return isNumberKey(event)" value="{{$precio}}">

                                                                            </td>
                                                                            <td>
                                                                                <input class="form-control text-right copago" disabled type="text" onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" readonly>
                                                                            </td>
                                                                            <td>
                                                                                <input class="form-control text-right pdesc" type="text" disabled onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                                                                <input class="form-control text-right maxdesc" type="hidden" disabled onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
                                                                            </td>
                                                                            <td>
                                                                                <input class="form-control text-right desc" type="text" disabled onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="0" required>
                                                                            </td>
                                                                            <td>
                                                                                <input class="form-control px-1 text-right" type="text" disabled onkeypress="return isNumberKey(event)" onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{$precioNeto}}" required>
                                                                            </td>
                                                                            <td>
                                                                                <input class="form luffy" disabled type="checkbox" @if($x['check_iva']>0) checked="checked" @endif name="valoriva[]" value="@if($x['check_iva']!=null){{$x['check_iva']}}@endif">
                                                                            </td>
                                                                        </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>

                                            <div style="display: flex;justify-content:right;align-items:flex-end">
                                                <div class="col-md-2">
                                                    <div class="table-responsive wrap">
                                                        <table class="table table-bordered table-hover dataTable">
                                                            <tfoot class='well'>
                                                                <tr>
                                                                    <td colspan="2" class="text-right">Subtotal 12%</td>
                                                                    <td id="subtotal_12" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas'])){{number_format(config('data.conglomeradaRegistro')['ventas']->subtotal_12,2)}}@endif</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class="text-right">Subtotal 0%</td>
                                                                    <td id="subtotal_0" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas'])){{number_format(config('data.conglomeradaRegistro')['ventas']->subtotal_0,2)}}@endif</td>
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class="text-right">Descuento</td>
                                                                    <td id="descuento" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas'])){{number_format(config('data.conglomeradaRegistro')['ventas']->descuento,2)}}@endif</td>
                                                                    <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class="text-right">Base Imponible</td>
                                                                    <td id="base" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas'])){{number_format(config('data.conglomeradaRegistro')['ventas']->base_imponible,2)}}@endif</td>
                                                                    <input type="hidden" name="base1" id="base1" class="hidden">
                                                                </tr>
                                                                <tr>
                                                                    <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                                                                    <td id="tarifa_iva" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas'])){{number_format(config('data.conglomeradaRegistro')['ventas']->impuesto,2)}}@endif</td>
                                                                    <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                                                                </tr>
                                                                <tr>

                                                                    <td colspan="2" class="text-right">Transporte</td>
                                                                    <td id="transporte" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas'])){{number_format(config('data.conglomeradaRegistro')['ventas']->transporte,2)}}@endif</td>
                                                                    <input type="hidden" name="transporte1" id="transporte1" class="hidden">
                                                                </tr>
                                                                <tr>

                                                                    <td colspan="2" class="text-right"><strong>Total</strong></td>
                                                                    <td id="total" class="text-right px-1"><strong>@if(isset(config('data.conglomeradaRegistro')['ventas'])){{number_format(config('data.conglomeradaRegistro')['ventas']->total_final,2)}}@endif</strong></td>
                                                                    <input type="hidden" name="total1" id="total1" class="hidden">
                                                                </tr>
                                                            </tfoot>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>

                                            @else



                                            <div class="table-responsive wrap">
                                                <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
                                                    <thead>
                                                        <tr class='well-dark'>
                                                            <th width="55%" class="" tabindex="0">Descripci&oacute;n del Producto</th>
                                                            <th width="10%" class="" tabindex="0">Cantidad</th>
                                                            <th width="10%" class="" tabindex="0">Precio</th>
                                                            <th width="10%" class="" tabindex="0">Cobrar Seguro</th>
                                                            <th width="10%" class="" tabindex="0">% Desc</th>
                                                            <th width="10%" class="" tabindex="0">Descuento</th>
                                                            <th width="10%" class="" tabindex="0">Precio Neto</th>
                                                            <th width="5%" class="" tabindex="0">IVA</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="agregar_cuentas">
                                                        @if(config('data.conglomeradaRegistro')['ventas']->tipo=='VENFA-CO')
                                                        @foreach($detalle_venfaco as $key => $value)
                                                        @php
                                                        $ct_prods = DB::table('ct_productos')->where('codigo',$value->id_ct_productos)->first();
                                                        //dd($value);
                                                        @endphp
                                                        <tr class="well" style="position: relative;">
                                                            <td style="width: 10%;">@if(!is_null($value->nombre)){{$value->id_ct_productos}} | {{$value->nombre}}@endif
                                                                <span class="detalle_producto">@if(!is_null($ct_prods))<br />{{$ct_prods->descripcion}}@endif</span>
                                                            </td>

                                                            <td style="width: 5%; text-align: center;">@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                                                            <td style="width: 5%; text-align: center;">@if(!is_null($value->precio)){{number_format($value->precio, 2)}}@endif</td>
                                                            <td style="width: 5%; text-align: center;"></td>
                                                            <td style="width: 5%; text-align: center;"></td>
                                                            <td style="width: 5%; text-align: center;">@if(isset($value->descuento)){{number_format($value->descuento, 2)}}@endif</td>
                                                            <td style="width: 5%; text-align: center;">@if(!is_null($value->cantidad)){{number_format($value->cantidad* $value->precio, 2)}}@endif</td>
                                                            <td style="width: 5%; text-align: center;">

                                                                <input type="checkbox" disabled />

                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                        @else
                                                        @php
                                                        $obs="";
                                                        if(count($detalle_venta)>0){
                                                        $obs = $detalle_venta[0]->codigo;
                                                        $cont= sizeof($detalle_venta);
                                                        $last_key = end(($detalle_venta));
                                                        }
                                                        @endphp
                                                        @foreach($detalle_venta as $key => $value)
                                                        <tr class="well" style="position: relative;">
                                                            <td style="width: 10%;">@if(!is_null($value->nombre)){{$value->id_ct_productos}} | {{$value->nombre}}@endif
                                                                <span class="detalle_producto">@if(!is_null($value->detalle))<br />{{$value->detalle}}@endif</span>
                                                            </td>
                                                            <td style="width: 5%; text-align: center;">@if(!is_null($value->cantidad)){{$value->cantidad}}@endif</td>
                                                            <td style="width: 5%; text-align: center;">@if(!is_null($value->precio)){{number_format($value->precio, 2)}}@endif</td>
                                                            <td style="width: 5%; text-align: center;">@if(!is_null($value->copago)){{number_format($value->copago, 2)}}@endif</td>
                                                            <td style="width: 5%; text-align: center;">@if(!is_null($value->descuento_porcentaje)){{$value->descuento_porcentaje}}@endif</td>
                                                            <td style="width: 5%; text-align: center;">@if(!is_null($value->descuento)){{number_format($value->descuento, 2)}}@endif</td>

                                                            @if($value->copago > 0)
                                                            <td style="width: 5%; text-align: center;">@if(!is_null($value->extendido)){{number_format($value->extendido / $value->cantidad, 2)}}@endif</td>
                                                            @else
                                                            <td style="width: 5%; text-align: center;">@if(!is_null($value->cantidad)){{number_format($value->cantidad* $value->precio, 2)}}@endif </td>
                                                            @endif

                                                            <td style="width: 5%; text-align: center;">
                                                                @if(($value->check_iva))<input type="checkbox" checked disabled />
                                                                @else <input type="checkbox" disabled />@endif

                                                            </td>
                                                        </tr>
                                                        @if($cont>1)
                                                        @if ($value->codigo != $last_key[1]->codigo)
                                                        <tr>
                                                            <td colspan="5">
                                                                {{$value->codigo}}
                                                            </td>
                                                        </tr>
                                                        @endif
                                                        @else
                                                        @if ($value->codigo !="")
                                                        <tr>
                                                            <td colspan="5">
                                                                {{$value->codigo}}
                                                            </td>
                                                        </tr>
                                                        @endif
                                                        @endif
                                                        @endforeach
                                                        @endif

                                                    <tfoot class='well'>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td colspan="2" class="text-right">Subtotal 12%</td>
                                                            <td id="subtotal_12" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas']->id)){{number_format(config('data.conglomeradaRegistro')['ventas']->subtotal_12,2)}}@endif</td>

                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td colspan="2" class="text-right">Subtotal 0%</td>
                                                            <td id="subtotal_0" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas']->id)){{number_format(config('data.conglomeradaRegistro')['ventas']->subtotal_0,2)}}@endif</td>

                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td colspan="2" class="text-right">Descuento</td>
                                                            <td id="descuento" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas']->id)){{number_format(config('data.conglomeradaRegistro')['ventas']->descuento,2)}}@endif</td>
                                                            <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td colspan="2" class="text-right">Base Imponible</td>
                                                            <td id="base" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas']->id)){{number_format(config('data.conglomeradaRegistro')['ventas']->base_imponible,2)}}@endif</td>
                                                            <input type="hidden" name="base1" id="base1" class="hidden">
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                                                            <td id="tarifa_iva" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas']->id)){{number_format($ventas->impuesto,2)}}@endif</td>
                                                            <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td colspan="2" class="text-right">Transporte</td>
                                                            <td id="transporte" class="text-right px-1">@if(isset(config('data.conglomeradaRegistro')['ventas']->id)){{number_format(config('data.conglomeradaRegistro')['ventas']->transporte,2)}}@endif</td>
                                                            <input type="hidden" name="transporte1" id="transporte1" class="hidden">
                                                        </tr>
                                                        <tr>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td colspan="2" class="text-right"><strong>Total</strong></td>
                                                            <td id="total" class="text-right px-1"><strong>@if(isset(config('data.conglomeradaRegistro')['ventas']->id)){{number_format(config('data.conglomeradaRegistro')['ventas']->total_final,2)}}@endif</strong></td>
                                                            <input type="hidden" name="total1" id="total1" class="hidden">
                                                        </tr>
                                                    </tfoot>
                                                    </tbody>
                                                </table>
                                            </div>
                                            @endif

                                            @endif
                                            @if(!isset(config('data.conglomeradaRegistro')['ventas']))
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <label class="font-normal" for="fecha_desde">Desde</label>
                                                    <input type="date" class="form-control dates" name="fecha_desde" id="fecha_desde">

                                                </div>
                                                <div class="col-md-2">
                                                    <label class="font-normal" for="fecha_hasta">Hasta</label>
                                                    <input type="date" class="form-control dates" name="fecha_hasta" id="fecha_hasta">
                                                </div>


                                                <div class="col-md-3">
                                                    <label class="font-normal" for="seguro_f">Seguro</label>
                                                    <select class="form-control select2" style="width: 100%;" id="seguro_f" name="seguro_f">
                                                        <option value="">-- Seleccione --</option>
                                                        <?php
                                                        foreach (config('data.seguros') as $row) {
                                                            echo '<option value="' . $row->id . '">' . $row->nombre . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-2">
                                                    <label class="font-normal" for="tiposervicio_f">Tipo
                                                        Servicio</label>
                                                    <select class="form-control select2" style="width: 100%;" id="tiposervicio_f" name="tiposervicio_f">
                                                        <option value="">-- Seleccione --</option>
                                                        <option value="1">Consulta</option>
                                                        <option value="2">Procedimiento</option>
                                                        <option value="3">Todos</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="font-normal" for="boton_buscar">Buscar</label>
                                                    <div>
                                                        <button type="button" class="btn btn-success w-60" onclick="buscarOrdenes()">
                                                            Buscar
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="col-md-1">
                                                    <label>Ambulatorio</label>
                                                    <div class="input-group">
                                                        <input type="checkbox" id="amBu" name="amBu" style="margin-left:10px;">
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(!isset(config('data.conglomeradaRegistro')['ventas']))
                            <div class="col-md-12" id="items" style="height: auto; overflow-y:auto;margin-top:10px;margin-bottom:10px">
                            </div>

                            <div style="display: flex;justify-content:right;align-items:flex-end">
                                <div class=" col-md-2">
                                    <table class="table table-bordered table-hover dataTable">
                                        <tfoot class='well'>
                                            <tr>
                                                <td colspan="2" class="text-right">Subtotal 12%</td>
                                                <td colspan="2" id="subtotal_12" class="text-right">0.00</td>
                                                <input type="hidden" name="subtotal_121" id="subtotal_121" class="hidden">
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-right">Subtotal 0%</td>
                                                <td id="subtotal_0" class="text-right">0.00</td>
                                                <input type="hidden" name="subtotal_01" id="subtotal_01" class="hidden">
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-right">Descuento</td>
                                                <td id="descuento" class="text-right">0.00</td>
                                                <input type="hidden" name="descuento1" id="descuento1" class="hidden">
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-right">Subtotal sin Impuesto</td>
                                                <td id="base" class="text-right">0.00</td>
                                                <input type="hidden" name="base1" id="base1" class="hidden">
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-right">Tarifa Iva 12%</td>
                                                <td id="tarifa_iva" class="text-right">0.00</td>
                                                <input type="hidden" name="tarifa_iva1" id="tarifa_iva1" class="hidden">
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-right"><strong>Total</strong></td>
                                                <td id="total" class="text-right">0.00</td>
                                                <input type="hidden" name="total1" id="total1" class="hidden">
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="text-right"><strong>Por Cobrar
                                                        Seguro</strong>
                                                </td>
                                                <td id="copagoTotal" class="text-right">0.00</td>
                                                <input type="hidden" name="totalc" id="totalc" class="hidden">
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            @endif
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-outline card-primary">
                                        <div class="card-header">
                                            <h3 class="card-title">Metodos de Pagos</h3>
                                            <div class="card-tools">
                                                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <input name="contador_pago" id="contador_pago" type="hidden" value="0">
                                                <table id="example1" role="grid" class="table table-bordered table-hover dataTable" aria-describedby="example1_info" style="margin-top:0 !important">
                                                    <thead>
                                                        <tr>
                                                            @if(!isset(config('data.conglomeradaRegistro')['ventas']))
                                                            <th width="10%" style="text-align: center;">Metodo</th>
                                                            <th width="10%" style="text-align: center;">Fecha</th>
                                                            <th width="15%" style="text-align: center;">Tipo
                                                                Tarjeta</th>
                                                            <th width="10%" style="text-align: center;">Número</th>
                                                            <th width="15%" style="text-align: center;">Banco</th>
                                                            <th width="10%" style="text-align: center;">Cuenta</th>
                                                            <th width="10%" style="text-align: center;">Girado a
                                                            </th>
                                                            <th width="10%" style="text-align: center;">Valor</th>
                                                            <th width="5%" style="text-align: center;">
                                                                <button class="btn btn-warning" id="btn_pago" type="button">
                                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                                </button>
                                                            </th>
                                                            @else
                                                            <th width="20%" style="text-align: center;">Tipo</th>
                                                            <th width="10%" style="text-align: center;">Fecha</th>
                                                            <th width="10%" style="text-align: center;">Número</th>
                                                            <th width="20%" style="text-align: center;">Banco</th>
                                                            <th width="10%" style="text-align: center;">Cuenta</th>
                                                            <th width="10%" style="text-align: center;">Girado a</th>
                                                            <th width="10%" style="text-align: center;">Valor</th>
                                                            @endif
                                                        </tr>
                                                    </thead>
                                                    <tbody id="agregar_pago">
                                                        @foreach(config('data.conglomeradaRegistro')['forma_pago'] as $value)
                                                        <tr class='well'>
                                                            <td width="20%" style="text-align: center;">
                                                                <select class="form-control" name="id_tip_pago[]" disabled>
                                                                    <option value="">Seleccione</option>
                                                                    @foreach(config('data.listasBancos') as $val)
                                                                    <option value="{{$value->id}}" {{ $val->id == $value->tipo ? 'selected' : ''}}>{{$val->nombre}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td width="10%" style="text-align: center;">{{$value->fecha}}</td>
                                                            <td width="10%" style="text-align: center;">{{$value->numero}}</td>
                                                            <td width="20%" style="text-align: center;">
                                                                <select class="form-control" name="id_tip_pago[]" disabled>
                                                                    <option value="">Seleccione</option>
                                                                    @foreach(config('data.listasBancos') as $val)
                                                                    <option value="{{$value->id}}" {{ $val->id == $value->banco ? 'selected' : ''}}>{{$val->nombre}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td width="10%" style="text-align: center;">
                                                                {{$value->cuenta}}
                                                            </td>
                                                            <td width="10%" style="text-align: center;">
                                                                {{$value->giradoa}}
                                                            </td>
                                                            <td width="10%" style="text-align: center;">{{$value->valor}}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="display: flex;justify-content:center;gap:10px;">
                                <button type="button" class="btn btn-danger" onclick="regresar()">
                                    <i class="fa fa-long-arrow-left" aria-hidden="true"></i> Regresar
                                </button>
                                @if(!isset(config('data.conglomeradaRegistro')['ventas']))
                                <button type="button" class="btn btn-success" id="guardarConglomerada">
                                    <i class="fa fa-floppy-o" aria-hidden="true"></i> Guardar
                                </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
