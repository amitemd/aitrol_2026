<!DOCTYPE html>
@section('action-content')
<html lang="en">
    <head>
        <title>Nota de Entrega</title>
        <style>

            #page_pdf {
                width: 100%;
                /*margin: 15px auto 10px auto;*/
                margin: 0 0;
                float: left;
            }

            .negrita{
                font-weight: bold;
                font-size: 25px;
            }


            .titulo {
                background: #eee;
                padding: 5px;
            }

            .center {
                text-align: center;
            }


            .th {
                font-size: 10pt;
                text-align: left;
            }

            * {
                font-size: 18px;
            }

            .table {
                border-collapse: collapse;
                font-family: sans-serif !important;
                width: 100%;
            }

            .celda1{
                text-align: left;
                height: 2%;
                font-size: 20px;
                padding: 5px;
                font-size: 22px;
            }

            .celda2{
                text-align: right;
                height: 2%;
                font-size: 20px;
                padding: 5px;
                font-size: 22px;
            }

            .subcelda{
                width:  14%;
                height:  2%;
            }
            footer {
                position: fixed; 
                bottom: -60px; 
                left: 0px; 
                right: 0px;
                height: 50px; 
                text-align: left;
                line-height: 35px;
            }
        </style> 
    </head>
    @php
        $idusuario  = Auth::user()->id;
        $usuarioNombre = Sis_medico\user::where('id',$idusuario)->first();
        $fecha = date("d-m-Y H:i:s");
        $p_unit         = 0;
        $p_total        = 0;
        $subtotal_0     = 0;
        $subtotal_12    = 0;
        $descuento      = 0;
        $subtotal       = 0;
        $iva            = 0;
        $valor_total    = 0;
    @endphp
    <body >
       
        @php $empresa_nombre = Sis_medico\Empresa::where('prioridad', '1')->where('estado', 1)->first();   @endphp
        <div id="page_pdf" style="font-family: Arial">
            <!-- @if(is_null($agenda->id_empresa))
            <strong style="font-size:22px; font-family: sans-serif !important;"> FARMACIA INSTITUTO FARMACEUTICO DE ENFERMEDADES DIGESTIVAS</strong>
            @endif -->
            <table class="table"  width="100%;" >
                <thead>
                    @if(is_null($agenda->id_empresa) || $agenda->id_empresa == $empresa_nombre->id)
                        <tr>
                            <td >
                                @if(!is_null($empresa_nombre->id))
                                    @if(!is_null($empresa_nombre->logo))
                                        <img src="{{base_path().'/storage/app/logo/'.$empresa_nombre->logo}}" style="width:285px;height: 140px;">
                                    @endif
                                @endif
                            </td>
                            <td style="width:40%; text-align: center;">
                                        <strong style="font-size:30px">@if(!is_null($empresa_nombre->id)) {{$empresa_nombre->nombrecomercial}} @endif</strong><br>
                                        <span style="font-size:25px; text-align: center; font-weight: bold;">RUC.: </span><span style="font-size:25px; text-align: center;">@if(!is_null($empresa_nombre->id)) {{$empresa_nombre->id}} @endif</span><br>
                                        <span style="font-size:20px; text-align: center; font-weight: bold;">Dir.: </span><span style="font-size:20px; text-align: center;">@if(!is_null($empresa_nombre->id)) {{$empresa_nombre->direccion}} @endif</span>
                            </td>
                            <td style="width:60%; text-align: center;">
                                <b style="font-size: 35px;">NOTA DE ENTREGA</b> <br>
                                <b style="font-size: 30px">No. </b><span style="font-size:30px; color: red;">@if(!is_null($nota_entrega->numero)) {{$nota_entrega->numero}} @endif</span>
                            </td>
                        </tr>
                            @else
                                @if($agenda->id_empresa == '1307189140001')
                        <tr>
                            <td style="width:60%; text-align: center;">
                                <strong style="font-size: 32px; font-family: sans-serif !important;">FARMACIA "Dr. Carlos Robles Medranda"</strong><br>
                                <span style="font-size:25px; text-align: center; font-weight: bold;">RUC.: </span><span style="font-size:25px; text-align: center;">@if(!is_null($agenda->id_empresa)) {{$agenda->empresa->id}} @endif</span><br>
                                <span style="font-size:20px; text-align: center; font-weight: bold;">Dir.: </span><span style="font-size:20px; text-align: center;">@if(!is_null($agenda->id_empresa)) {{$agenda->empresa->direccion}} @endif</span>
                            </td>
                            <td style="width:30%; text-align: center;">
                                <b style="font-size: 35px;">NOTA DE ENTREGA</b> <br>
                                <b style="font-size: 30px">No. </b><span style="font-size:30px; color: red;">@if(!is_null($nota_entrega->numero)) {{$nota_entrega->numero}} @endif</span>
                            </td>
                        </tr>
                                @endif
                    @endif
                </thead>
            </table>
            <br>
            <table  class="table" width="100%;" style="border:1px black; solid;">
                <thead style="text-align:left;">
                    <tr>
                        <td><span class="negrita">Cliente:</span> <span style="font-size: 20px">INSTITUTO ECUATORIANO DE SEGURIDAD SOCIAL IESS</span></td>
                        <td><span class="negrita">Ruc:</span> <span style="font-size: 20px">1760004650001</span></td>
                    </tr>
                    <tr>
                        <td>
                            <span class="negrita">Dir:</span> <span style="font-size: 20px">Olmedo 401 A y boyaca</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <span class="negrita">Telf:</span> <span style="font-size: 20px">2400057</span>
                        </td>
                        <td><span class="negrita">Fecha:</span> <span style="font-size: 20px">{{substr($nota_entrega->created_at, 0, 10)}}</span></td>
                    </tr>
                    <tr>
                        @php $paciente= Sis_medico\Paciente::find($agenda->id_paciente); @endphp
                        <td><span class="negrita">Paciente:</span> <span style="font-size: 20px"> @if(!is_null($paciente)) {{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}} @endif</span> </td>
                        <td><span class="negrita">Cédula:</span> <span style="font-size: 20px">@if(!is_null($agenda->id_paciente)) {{$agenda->id_paciente}} @endif</span></td>
                    </tr>
                </thead>
            </table><br>
            <table class="tabless" width="100%;" style="">
                <thead style="text-align:center;">
                <tr>
                    <td style="border:1px solid black; border-radius: 20px 0px 0px 0px; font-weight: bold;" width="52%" > ARTICULO </td>
                    <td style="border:1px solid black; font-weight: bold;" width="12%"> CANTIDAD </td>
                    <td style="border:1px solid black; font-weight: bold;" width="12%"> P.UNIT. </td>
                    <td style="border:1px solid black; font-weight: bold;" width="12%"> P.CAN-MED </td>
                    <td style="border:1px solid black; font-weight: bold; border-radius: 0px 20px 0px 0px" width="12%"> V.TOTAL </td>
                </tr>
                </thead>
                <tbody>
                @foreach($nota_entrega_detalle as $val)
                        @php
                                $p_total = $val->cantidad * $val->precio;
                            $subtotal_0 = $subtotal_0 + $p_total; 
                            $subtotal = $subtotal_0;
                            $valor_total = $subtotal_0;
                        @endphp
                    <tr>
                        <td style="border:1px solid black; " class="celda1"> @if(!is_null($val->nombre_medicina)) {{$val->nombre_medicina}} @endif</td>
                        <td style="border:1px solid black; " class="celda2"> @if(!is_null($val->cantidad)) {{$val->cantidad}} @endif </td>
                                <td style="border:1px solid black; " class="celda2"> @if(!is_null($val->precio)) ${{$val->precio}} @endif </td>
                                <td style="border:1px solid black; " class="celda2"> @if(!is_null($val->precio)) ${{$val->precio}} @endif </td>
                            <td style="border:1px solid black; " class="celda2"> @if(!is_null($p_total)) ${{number_format($p_total,4)}} @endif </td>
                    </tr>
                @endforeach
                @if(!is_null($nota_entrega_detalle))
                        @if(count($nota_entrega_detalle) < 20)
                            @for($i = count($nota_entrega_detalle); $i <= 18; $i++)
                            <tr>
                                <td style="border:1px solid black; " class="celda1"> </td>
                                <td style="border:1px solid black; " class="celda2"> </td>
                                <td style="border:1px solid black; " class="celda2"> </td>
                                <td style="border:1px solid black; " class="celda2"> </td>
                                <td style="border:1px solid black; " class="celda2"> </td>
                            </tr>
                        @endfor
                    @endif
                @endif
                <tr>
                    <td style="border:1px solid black; border-radius: 0px 0px 0px 20px" class="celda1">  </td>
                    <td style="border:1px solid black; " class="celda1">  </td>
                    <td style="border:1px solid black; " class="celda1">  </td>
                    <td style="border:1px solid black; " class="celda1">  </td>
                    <td style="border:1px solid black; border-radius: 0px 0px 20px 0px" class="celda1"> </td>
                </tr>
                </tbody>
            </table>
            <table class="tabless" width="100%;" style="">
                <thead style="text-align:right;">
                <tr>
                    <td></td><td></td><td></td>
                    <td style="border:1px solid black; " class="subcelda"> SUBTOTAL 0% </td>
                        <td style="border:1px solid black; font-size: 22px" class="subcelda"> @if(!is_null($subtotal_0)) ${{number_format($subtotal_0,4)}} @endif </td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
                    <td style="border:1px solid black; " class="subcelda"> SUBTOTAL 15% </td>
                        <td style="border:1px solid black; font-size: 22px" class="subcelda"> @if(!is_null($subtotal_12)) ${{number_format($subtotal_12,4)}} @endif </td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
                    <td style="border:1px solid black; " class="subcelda"> DESCUENTO </td>
                        <td style="border:1px solid black; font-size: 22px" class="subcelda"> @if(!is_null($descuento)) ${{number_format($descuento,4)}} @endif </td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
                    <td style="border:1px solid black; " class="subcelda"> SUBTOTAL </td>
                        <td style="border:1px solid black; font-size: 22px" class="subcelda"> @if(!is_null($subtotal)) ${{number_format($subtotal,4)}} @endif </td>
                </tr>
                <tr>
                    <td></td><td></td><td></td>
                    <td style="border:1px solid black; " class="subcelda"> IVA </td>
                        <td style="border:1px solid black; font-size: 22px" class="subcelda"> @if(!is_null($iva)) ${{number_format($iva,4)}} @endif </td>
                </tr>
                <tr>
                    <td width="38%" style="text-align: left !important; "><span style="font-size: 25px;">Firma Autorizada: _______________</span></td>
                    <td width="38%" style="text-align: left !important; "><span style="font-size: 25px;">Recibi Conforme: _______________</span></td>
                    <td></td>
                    <td style="border:1px solid black; " class="subcelda"> VALOR TOTAL </td>
                        <td style="border:1px solid black; font-size: 22px" class="subcelda"> @if(!is_null($valor_total)) ${{number_format($valor_total,4)}} @endif </td>
                </tr>
                </thead>
            </table>
        </div>
       <!--  <footer>
                <label style="font-size: 18px; color: black;"><b style="font-size: 18px;">Impreso por:</b>{{$usuarioNombre->nombre1}} {{$usuarioNombre->apellido1}} {{$usuarioNombre->apellido2}} &nbsp;&nbsp;<b style="font-size: 18px;">Fecha:</b> {{$fecha}}
        </footer> -->
            
       
           
    </body>
</html>
