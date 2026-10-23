<!DOCTYPE html>
@php
$data = date("Y-m-d");
$idusuario  = Auth::user()->id;
$usuarioNombre = Sis_medico\user::where('id',$idusuario)->first();
$fecha = date("Y-m-d H:i:s");
@endphp
@section('action-content')
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Cierre de Caja</title>
    <style>
        @page {
            margin: 20px 20px;
        }

        * {
            font-size: 14px;
            padding: 0px;

        }

        .table {
            border-collapse: collapse;
            padding: 1px;

        }

        .table,
        .td {
            border: 1px solid black;
            text-align: center;
            color: white;
        }

        .th {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            color: white;
            background-color: #0033cc;
        }

        .new_exl_cabecera {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
            font-weight: bold;
            font-size: 20px;
            color: black;
            background-color: #D1F2EB;
        }

        .new_exl_cuerpo {
            border: 1px solid black;
            text-align: center;
            padding: 8px;
            /*font-weight: bold;*/
            font-size: 16px;
            color: black;
        }

        .tabla_final_izquierda{
            border: 1px solid black;
            text-align: left;
            font-size: 16px;
            color: black;
        }

        .tabla_final_derecha{
            border: 1px solid black;
            text-align: right;
            font-size: 16px;
            color: black;
        }

        .pie {
            font-weight: bold;
            color: black;
            background-color: #b3c6ff;
        }

        table tr td {

            padding: 3px;

        }

        #container {
            margin: 15px 15px;
        }

        .estilos {
            font-weight: bold;
            color: black;
        }

        

        .saltoDePagina {
            display: block;
            page-break-before: always;
        }
    </style>
</head>
<body>
    @php
        $fecha_actual = date('Y-m-d');
        $empresa= Sis_medico\Empresa::where('prioridad_labs', '1')->first();
        $empresa_matriz = Sis_medico\Empresa::where('prioridad', '1')->first();
    @endphp
    @foreach($cabecera_excel as $cabecera )
            @php
            $usuario= Sis_medico\user::find($cabecera->id_usuariocrea);
            @endphp
            <div>
                <div >
                <img src="{{base_path().'/public/imagenes/login2.png'}}" style="width:160px;height: 90px; margin-left: 11px;"><br>
                <span >
                    @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @endif <br>
                    @if(isset($empresa_matriz)) {{$empresa_matriz->nombrecomercial}} @endif
                </span>
                @yield('action-content')
                </div>
            </div><br>

    <table style="width: 100%;">
        <tr style="background-color: #D1F2EB">
            <td colspan="7">
                <center><label style="font-size: 35px; color: black;">REPORTE CAJA GENERAL LABS</label></center>
            </td>
            </tr>
            <tr>
                <td></td>
                <td style="font-size: 22px; font-weight: bold;">USUARIO:</td>
                <td style="font-size: 20px">{{$usuario->nombre1}} {{$usuario->apellido1}}</td>
                <td></td>
                <td style="font-size: 22px; font-weight: bold;">DESDE:</td>
                <td style="font-size: 20px">{{substr($cabecera->created_at,0,10)}}</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="font-size: 22px; font-weight: bold;">FECHA DE REPORTE:</td>
                <td style="font-size: 20px">{{$fecha_actual}}</td>
                <td></td>
                <td style="font-size: 22px; font-weight: bold;">HASTA:</td>
                <td style="font-size: 20px">{{substr($cabecera->created_at,0,10)}}</td>
                <td></td>
            </tr>
        </table>  
        <table class="table" style="width: 100%; border: none !important;">
            <thead>
                <tr>
                    <th class="new_exl_cabecera">
                        #
                    </th>
                    <th class="new_exl_cabecera">
                        orden
                    </th>
                    <th class="new_exl_cabecera">
                        Fecha
                    </th>
                    <th class="new_exl_cabecera">
                    <br> Seguro - </br>
                    <br> Nivel   </br>
                    </th>
                    <th class="new_exl_cabecera">
                        Paciente
                    </th>
                    <th class="new_exl_cabecera">
                        Efectivo
                    </th>
                    <th class="new_exl_cabecera">
                        Cheque
                    </th>
                    <th class="new_exl_cabecera">
                        Deposito
                    </th>
                    <th class="new_exl_cabecera">
                        Trans
                    </th>
                    <th class="new_exl_cabecera">
                        Credito
                    </th>
                    <th class="new_exl_cabecera">
                        Debito
                    </th>
                    <th class="new_exl_cabecera">
                        P.Pago
                    </th>
                <th class="new_exl_cabecera">
                    Online
                </th>
                <th class="new_exl_cabecera">
                        Oda
                    </th>
                    <th class="new_exl_cabecera">
                        Comprobante
                    </th>
                    <!-- <th class="new_exl_cabecera">
                        Usuario
                    </th> -->
                    <th class="new_exl_cabecera">
                        Observacion
                    </th>
                </tr>
            </thead>
            <tbody>
                @php $cant = 1; @endphp
            @php $ac_efectivo = 0; $ac_cheque = 0; $ac_deposito = 0; $ac_transf = 0; $ac_tar_cre = 0; $ac_tar_deb = 0; $ac_ppago = 0; $ac_ponline = 0; @endphp
                    @foreach($records as $val)
                        @php $seguro = Sis_medico\Seguro::find($val->id_seguro);  @endphp
                        @if($seguro->tipo != 0)
                            @if(in_array($seguro->id, array(4, 33)) == false)
                                @if($val->id_usuariocrea == $cabecera->id_usuariocrea)
            @if($seguro->id == $val->id_seguro)
                                    @php 
                                        $eorden = Sis_medico\Examen_Orden::find($val->id); 
                                        $cierre_caja = Sis_medico\CierreCaja::find($val->id_caja);
                                        $efectivo = $eorden->detalle_forma_pago->where('id_tipo_pago','1')->sum('valor');
                                        $cheque = $eorden->detalle_forma_pago->where('id_tipo_pago','2')->sum('valor');
                                        $deposito = $eorden->detalle_forma_pago->where('id_tipo_pago','3')->sum('valor');
                                        $transferencia = $eorden->detalle_forma_pago->where('id_tipo_pago','5')->sum('valor');
                                        $tarjeta_credito1 = $eorden->detalle_forma_pago->where('id_tipo_pago','4')->sum('p_fi');
                                        $tarjeta_credito2 = $eorden->detalle_forma_pago->where('id_tipo_pago','4')->sum('valor');
                                        $tarjeta_credito = $tarjeta_credito1 + $tarjeta_credito2;

                                        $tarjeta_debito1 = $eorden->detalle_forma_pago->where('id_tipo_pago','6')->sum('p_fi');
                                        $tarjeta_debito2 = $eorden->detalle_forma_pago->where('id_tipo_pago','6')->sum('valor');
                                        $tarjeta_debito = $tarjeta_debito1 + $tarjeta_debito2;

                                        $ppago = $eorden->detalle_forma_pago->where('id_tipo_pago','7')->sum('valor');

            if($eorden->pago_online == 1){
                $ponline = $eorden->total_valor;
            }else{
                $ponline = 0;
            }
                                        $ac_efectivo += $efectivo; $ac_cheque += $cheque; $ac_deposito += $deposito; $ac_transf += $transferencia; 
            $ac_tar_cre += $tarjeta_credito; $ac_tar_deb += $tarjeta_debito; $ac_ppago += $ppago; $ac_ponline += $ponline;
                                    @endphp
                                    <tr>
                                        <td class="new_exl_cuerpo">
                                            {{$cant}}
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            {{$eorden->id}}
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            {{substr($eorden->fecha_orden,0,10)}}
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            <br> {{$eorden->seguro->nombre}} </br>
                                            <br>  @if($eorden->id_nivel != null) {{$eorden->nivel->nombre}} @endif </br>
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            <br> {{$eorden->paciente->apellido1}} {{$eorden->paciente->apellido2}} </br>
                                            <br>{{$eorden->paciente->nombre1}} {{$eorden->paciente->nombre2}} </br>
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            {{ number_format($efectivo, 2, ',', ' ')}}
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            {{ number_format($cheque, 2, ',', ' ')}}
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            {{ number_format($deposito, 2, ',', ' ')}}
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            {{ number_format($transferencia, 2, ',', ' ')}}
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            {{ number_format($tarjeta_credito, 2, ',', ' ')}}
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            {{ number_format($tarjeta_debito, 2, ',', ' ')}}
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            {{ number_format($ppago, 2, ',', ' ')}}
                                        </td>
                                        <td class="new_exl_cuerpo">
                    {{ number_format($ponline, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                                            0.00
                                        </td>
                                        <td class="new_exl_cuerpo">
                                            @if($eorden->comprobante != null){{$eorden->comprobante}}@else <span style="color:red"> Sin Facturar</span> @endif
                                        </td>
                                        <!-- <td class="new_exl_cuerpo">
                                            {{substr($cierre_caja->crea->nombre1,0,1)}}&nbsp;{{$cierre_caja->crea->apellido1}}
                                        </td> -->
                                        <td class="new_exl_cuerpo">
                                            {{$cierre_caja->observacion}}
                                        </td>
                                    </tr>
                                    @php $cant ++ @endphp
                                @endif
                            @endif
                        @endif
            @endif
                    @endforeach  
                <tr>
                    <td class="new_exl_cabecera">
                        
                    </td>
                    <td class="new_exl_cabecera">
                        TOTAL
                    </td>
                    
                    <td class="new_exl_cabecera">
                        
                    </td>
                    <td class="new_exl_cabecera">
                    
                    </td>
                    <td class="new_exl_cabecera">
                        
                    </td>
                    <td class="new_exl_cabecera">
                        {{ number_format($ac_efectivo, 2, ',', ' ')}}
                    </td>
                    <td class="new_exl_cabecera">
                        {{ number_format($ac_cheque, 2, ',', ' ')}}
                    </td>
                    <td class="new_exl_cabecera">
                        {{ number_format($ac_deposito, 2, ',', ' ')}}
                    </td>
                    <td class="new_exl_cabecera">
                        {{ number_format($ac_transf, 2, ',', ' ')}}
                    </td>
                    <td class="new_exl_cabecera">
                        {{ number_format($ac_tar_cre, 2, ',', ' ')}}
                    </td>
                    <td class="new_exl_cabecera">
                        {{ number_format($ac_tar_deb, 2, ',', ' ')}}
                    </td>
                    <td class="new_exl_cabecera">
                    {{ number_format($ac_ppago, 2, ',', ' ')}}
                    </td>
                    <td class="new_exl_cabecera">
                    {{ number_format($ac_ponline, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                        0.00
                    </td>
                    
                    <td class="new_exl_cabecera">
                        
                    </td>
                    <!-- <td class="new_exl_cabecera">
                        
                    </td> -->
                    <td class="new_exl_cabecera">
                        
                    </td>
                </tr>  
            </tbody>
        </table>
        <div style="width: 100%; border: none !important;text-align:left;margin-top:2%">
          <label style="font-size: 18px; color: black;"><b style="font-size: 18px;">NOMBRE:</b>{{$usuarioNombre->nombre1}} {{$usuarioNombre->nombre2}} {{$usuarioNombre->apellido1}} {{$usuarioNombre->apellido2}} &nbsp;&nbsp;<b style="font-size: 18px;">FECHA:</b> {{$fecha}}</center>
        </div><br>
        <div class="saltoDePagina"></div>
    @endforeach
       
        <div>
            <div >
            <img src="{{base_path().'/public/imagenes/login2.png'}}" style="width:160px;height: 90px; margin-left: 11px;"><br>
            <span >
                @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @endif <br>
                @if(isset($empresa_matriz)) {{$empresa_matriz->nombrecomercial}} @endif
            </span>
            @yield('action-content')
            </div>
        </div><br>
    <div style="width: 100%; border: none !important; background-color: #D1F2EB; "> 
        <center><label style="font-size: 35px; color: black;">REPORTE CAJA DE HUMANA - DIA: {{$fechaBusqueda}}</label></center>
    </div><br>

    <table class="table" style="width: 100%; border: none !important;">
        <thead>
            <tr>
                <th class="new_exl_cabecera">
                    #
                </th>
                <th class="new_exl_cabecera">
                    orden
                </th>
                <th class="new_exl_cabecera" >
                    Fecha
                </th>
                <th class="new_exl_cabecera">
                    <br> Seguro </br>
                    <br> Nivel  </br>
                </th>
                <th class="new_exl_cabecera">
                    Paciente
                </th>
                <th class="new_exl_cabecera">
                    Efectivo
                </th>
                <th class="new_exl_cabecera">
                    Cheque
                </th>
                <th class="new_exl_cabecera">
                    Deposito
                </th>
                <th class="new_exl_cabecera">
                    Trans
                </th>
                <th class="new_exl_cabecera">
                    Credito
                </th>
                <th class="new_exl_cabecera">
                    Debito
                </th>
                <th class="new_exl_cabecera">
                    P.Pago
                </th>
                <th class="new_exl_cabecera">
                    Online
                </th>
                <th class="new_exl_cabecera">
                    Oda
                </th>
               
                <th class="new_exl_cabecera">
                    Comprobante
                </th>
                <th class="new_exl_cabecera">
                    Usuario
                </th>
                <th class="new_exl_cabecera">
                    Observacion
                </th>
            </tr>
        </thead>
        <tbody>
            @php $cant = 1; @endphp
            @php $ac_efectivo = 0; $ac_cheque = 0; $ac_deposito = 0; $ac_transf = 0; $ac_tar_cre = 0; $ac_tar_deb = 0; $ac_ppago = 0; $ac_ponline = 0; @endphp


            @foreach($records as $val)
                @if($val->id_seguro == '4')
                    @php 
                        $eorden = Sis_medico\Examen_Orden::find($val->id); 
                        $cierre_caja = Sis_medico\CierreCaja::find($val->id_caja);
                        $efectivo = $eorden->detalle_forma_pago->where('id_tipo_pago','1')->sum('valor');
                        $cheque = $eorden->detalle_forma_pago->where('id_tipo_pago','2')->sum('valor');
                        $deposito = $eorden->detalle_forma_pago->where('id_tipo_pago','3')->sum('valor');
                        $transferencia = $eorden->detalle_forma_pago->where('id_tipo_pago','5')->sum('valor');
                        $tarjeta_credito1 = $eorden->detalle_forma_pago->where('id_tipo_pago','4')->sum('p_fi');
                        $tarjeta_credito2 = $eorden->detalle_forma_pago->where('id_tipo_pago','4')->sum('valor');
                        $tarjeta_credito = $tarjeta_credito1 + $tarjeta_credito2;

                        $tarjeta_debito1 = $eorden->detalle_forma_pago->where('id_tipo_pago','6')->sum('p_fi');
                        $tarjeta_debito2 = $eorden->detalle_forma_pago->where('id_tipo_pago','6')->sum('valor');
                        $tarjeta_debito = $tarjeta_debito1 + $tarjeta_debito2;

                        $ppago = $eorden->detalle_forma_pago->where('id_tipo_pago','7')->sum('valor');

            if($eorden->pago_online == 1){
                $ponline = $eorden->total_valor;
            }else{
                $ponline = 0;
            }

            $ac_efectivo += $efectivo; $ac_cheque += $cheque; $ac_deposito += $deposito; $ac_transf += $transferencia;
            $ac_tar_cre += $tarjeta_credito; $ac_tar_deb += $tarjeta_debito; $ac_ppago += $ppago; $ac_ponline += $ponline;
            @endphp
            <tr>
                <td class="new_exl_cuerpo">
                    {{$cant}}
                </td>
                <td class="new_exl_cuerpo">
                    {{$eorden->id}}
                </td>
                <td class="new_exl_cuerpo">
                    {{substr($eorden->fecha_orden,0,10)}}
                </td>
                <td class="new_exl_cuerpo">
                    @if($eorden->seguro = $eorden->id_nivel) {{$eorden->nivel->nombre}} @endif
                </td>
                <td class="new_exl_cuerpo">
                    <br> {{$eorden->paciente->apellido1}} {{$eorden->paciente->apellido2}} </br>
                    <br>{{$eorden->paciente->nombre1}} {{$eorden->paciente->nombre2}} </br>
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($efectivo, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($cheque, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($deposito, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($transferencia, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($tarjeta_credito, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($tarjeta_debito, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($ppago, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($ponline, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    0.00
                </td>

                <td class="new_exl_cuerpo">
                    @if($eorden->comprobante != null){{$eorden->comprobante}}@else <span style="color:red"> Sin Facturar</span> @endif
                </td>
                <td class="new_exl_cuerpo">
                    {{substr($cierre_caja->crea->nombre1,0,1)}}{{$cierre_caja->crea->apellido1}}
                </td>
                <td class="new_exl_cuerpo">
                    {{$cierre_caja->observacion}}
                </td>
            </tr>
            @php $cant ++ @endphp
            @endif
            @endforeach
                
           
            <tr>
                <td class="new_exl_cabecera">
                    
                </td>
                <td class="new_exl_cabecera">
                    TOTAL
                </td>
                
                <td class="new_exl_cabecera">
                    
                </td>
                <td class="new_exl_cabecera">
                   
                </td>
                <td class="new_exl_cabecera">
                    
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_efectivo, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_cheque, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_deposito, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                     {{ number_format($ac_transf, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_tar_cre, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_tar_deb, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                   {{ number_format($ac_ppago, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_ponline, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    0.00
                </td>
                
                <td class="new_exl_cabecera">
        
                </td>
                <td class="new_exl_cabecera">
                    
                </td>
                <td class="new_exl_cabecera">
                    
                </td>
            </tr>  
        </tbody>
    </table>
    <div style="width: 100%; border: none !important;text-align:left;margin-top:2%">
        <label style="font-size: 18px; color: black;"><b style="font-size: 18px;">NOMBRE:</b>{{$usuarioNombre->nombre1}} {{$usuarioNombre->nombre2}} {{$usuarioNombre->apellido1}} {{$usuarioNombre->apellido2}} &nbsp;&nbsp;<b style="font-size: 18px;">FECHA:</b> {{$fecha}}</center>
    </div><br>
    <div class="saltoDePagina"></div>

        <div>
            <div >
            <img src="{{base_path().'/public/imagenes/login2.png'}}" style="width:160px;height: 90px; margin-left: 11px;"><br>
            <span >
                @if(isset($empresa)) {{$empresa->id}} {{$empresa->nombrecomercial}} @endif <br>
                @if(isset($empresa_matriz)) {{$empresa_matriz->nombrecomercial}} @endif
            </span>
            @yield('action-content')
            </div>
        </div><br>
    <div style="width: 100%; border: none !important; background-color: #D1F2EB; "> 
        <center><label style="font-size: 35px; color: black;">REPORTE DE CAJA DE PAQUETES EMPRESARIALES - DIA: {{$fechaBusqueda}}</label></center>
    </div><br>

    <table class="table" style="width: 100%; border: none !important;">
        <thead>
            <tr>
                <th class="new_exl_cabecera">
                    #
                </th>
                <th class="new_exl_cabecera">
                    orden
                </th>
                <th class="new_exl_cabecera" >
                    Fecha
                </th>
                <th class="new_exl_cabecera">
                   <br> Seguro </br>
                   <br> Nivel  </br>
                </th>
                <th class="new_exl_cabecera">
                    Paciente
                </th>
                <th class="new_exl_cabecera">
                    Efectivo
                </th>
                <th class="new_exl_cabecera">
                    Cheque
                </th>
                <th class="new_exl_cabecera">
                    Deposito
                </th>
                <th class="new_exl_cabecera">
                    Trans
                </th>
                <th class="new_exl_cabecera">
                    Credito
                </th>
                <th class="new_exl_cabecera">
                    Debito
                </th>
                <th class="new_exl_cabecera">
                    P.Pago
                </th>
                <th class="new_exl_cabecera">
                    Online
                </th>
                <th class="new_exl_cabecera">
                    ODA
                </th>
               
                <th class="new_exl_cabecera">
                    Comprobante
                </th>
                <th class="new_exl_cabecera">
                    Usuario
                </th>
                <th class="new_exl_cabecera">
                   Observacion
                </th>
            </tr>
        </thead>
        <tbody>
            @php $cant = 1; @endphp
            @php $ac_efectivo = 0; $ac_cheque = 0; $ac_deposito = 0; $ac_transf = 0; $ac_tar_cre = 0; $ac_tar_deb = 0; $ac_ppago = 0; $ac_ponline = 0; @endphp

            @foreach($records as $val)
                @if($val->id_seguro == '33')
                    @php 
                        $eorden = Sis_medico\Examen_Orden::find($val->id); 
                        $cierre_caja = Sis_medico\CierreCaja::find($val->id_caja);
                        $efectivo = $eorden->detalle_forma_pago->where('id_tipo_pago','1')->sum('valor');
                        $cheque = $eorden->detalle_forma_pago->where('id_tipo_pago','2')->sum('valor');
                        $deposito = $eorden->detalle_forma_pago->where('id_tipo_pago','3')->sum('valor');
                        $transferencia = $eorden->detalle_forma_pago->where('id_tipo_pago','5')->sum('valor');
                        $tarjeta_credito1 = $eorden->detalle_forma_pago->where('id_tipo_pago','4')->sum('p_fi');
                        $tarjeta_credito2 = $eorden->detalle_forma_pago->where('id_tipo_pago','4')->sum('valor');
                        $tarjeta_credito = $tarjeta_credito1 + $tarjeta_credito2;

                        $tarjeta_debito1 = $eorden->detalle_forma_pago->where('id_tipo_pago','6')->sum('p_fi');
                        $tarjeta_debito2 = $eorden->detalle_forma_pago->where('id_tipo_pago','6')->sum('valor');
                        $tarjeta_debito = $tarjeta_debito1 + $tarjeta_debito2;

                        $ppago = $eorden->detalle_forma_pago->where('id_tipo_pago','7')->sum('valor');

            if($eorden->pago_online == 1){
                $ponline = $eorden->total_valor;
            }else{
                $ponline = 0;
            }

            $ac_efectivo += $efectivo; $ac_cheque += $cheque; $ac_deposito += $deposito; $ac_transf += $transferencia;
            $ac_tar_cre += $tarjeta_credito; $ac_tar_deb += $tarjeta_debito; $ac_ppago += $ppago; $ac_ponline += $ponline;
            @endphp
            <tr>
                <td class="new_exl_cuerpo">
                    {{$cant}}
                </td>
                <td class="new_exl_cuerpo">
                    {{$eorden->id}}
                </td>
                <td class="new_exl_cuerpo">
                    {{substr($eorden->fecha_orden,0,10)}}
                </td>
                <td class="new_exl_cuerpo">
                    {{$eorden->seguro->nombre}}
                    @if($eorden->id_nivel != null) {{$eorden->nivel->nombre}} @endif
                </td>
                <td class="new_exl_cuerpo">
                    <br> {{$eorden->paciente->apellido1}} {{$eorden->paciente->apellido2}} </br>
                    <br>{{$eorden->paciente->nombre1}} {{$eorden->paciente->nombre2}} </br>
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($efectivo, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($cheque, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($deposito, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($transferencia, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($tarjeta_credito, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($tarjeta_debito, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($ppago, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    {{ number_format($ponline, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cuerpo">
                    0.00
                </td>

                <td class="new_exl_cuerpo">
                    @if($eorden->comprobante != null){{$eorden->comprobante}}@else <span style="color:red"> Sin Facturar</span> @endif
                </td>
                <td class="new_exl_cuerpo">
                    {{substr($cierre_caja->crea->nombre1,0,1)}}{{$cierre_caja->crea->apellido1}}
                </td>
                <td class="new_exl_cuerpo">
                    {{$cierre_caja->observacion}}
                </td>
            </tr>
            @php $cant ++ @endphp
            @endif
            @endforeach
                
            
            <tr>
                <td class="new_exl_cabecera">
                    
                </td>
                <td class="new_exl_cabecera">
                    TOTAL
                </td>
                
                <td class="new_exl_cabecera">
                    
                </td>
                <td class="new_exl_cabecera">
                   
                </td>
                <td class="new_exl_cabecera">
                    
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_efectivo, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_cheque, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_deposito, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                     {{ number_format($ac_transf, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_tar_cre, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_tar_deb, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                   {{ number_format($ac_ppago, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    {{ number_format($ac_ponline, 2, ',', ' ')}}
                </td>
                <td class="new_exl_cabecera">
                    0.00
                </td>
                
                <td class="new_exl_cabecera">
                    
                </td>
                <td class="new_exl_cabecera">
                    
                </td>
                <td class="new_exl_cabecera">
                    
                </td>
            </tr>  
        </tbody>
    </table>
    <div style="width: 100%; border: none !important;text-align:left;margin-top:2%">
        <label style="font-size: 18px; color: black;"><b style="font-size: 18px;">NOMBRE:</b>{{$usuarioNombre->nombre1}} {{$usuarioNombre->nombre2}} {{$usuarioNombre->apellido1}} {{$usuarioNombre->apellido2}} &nbsp;&nbsp;<b style="font-size: 18px;">FECHA:</b> {{$fecha}}</center>
    </div><br>

</body>



</html>