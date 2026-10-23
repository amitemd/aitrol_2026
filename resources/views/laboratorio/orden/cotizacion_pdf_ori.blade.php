<!DOCTYPE html><!--VERSION CON RECARGO NO SUBIDA PILAS!!!!!!!-->
<html>
<head>
  <style>
    @page { margin: 200px 70px; }
    #header { position: fixed; left: 0px; top: -180px; right: 0px; height: 150px; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: -220px; right: 0px; height: 150px; }

  </style>    

</head>
<body>
  
  <script type="text/php">
        if (isset($pdf)) {
          $font = $fontMetrics->getFont("Arial", "bold");
          $pdf->page_text(555, 80, "{PAGE_NUM}/{PAGE_COUNT}", $font, 9, array(0, 0, 0));
        }
  </script>
  

    @php
      $recargo = $orden->recargo_p/100;  
      $descuento = $orden->descuento_p/100;
      $r = 1 + $recargo - ($descuento*$recargo);
      $pr = [10, 11, 12];
    @endphp 
    <div id="header">
        
      <div class="col-md-12" style="width: 1122px;border-bottom: 0.5px solid #009a98;border-top: 0.5px solid #ec6c25;">
       <img width="1125" src="{{base_path().'/public/imagenes/cotizacion_labs.png'}}" align=center>
      </div> 

      <div style="border-bottom: 1px solid #009a98;padding: 0px;width: 1122px;">
        <table style="font-size: 16px;color: #009a98;">
          <tbody>
            <tr role="row">
              <td width="50" style="background-color: #e5f5f5;height: 20px;"><b>PACIENTE</b></td>
              <td width="206" style="color: black;">{{$orden->paciente->nombre1}} @if($orden->pnombre2=='N/A'||$orden->paciente->nombre2=='(N/A)') @else{{ $orden->paciente->nombre2 }} @endif {{$orden->paciente->apellido1}} @if($orden->paciente->apellido2=='N/A'||$orden->paciente->apellido2=='(N/A)') @else{{ $orden->paciente->apellido2 }} @endif</td>
              <td width="50" style="background-color: #e5f5f5;"><b>CEDULA</b></td>
              <td width="40" style="color: black;">{{$orden->id_paciente}}</td>
              <td width="45" style="background-color: #e5f5f5;"><b>EDAD</b></td>
              <td width="40" style="color: black;">{{$age}}</td>         
              <td width="50" style="background-color: #e5f5f5;"><b>SEXO</b></td>
              <td width="40" >@if($orden->paciente->sexo=='1')Masculino @elseif($orden->paciente->sexo=='2') Femenino @endif</td>
              <td width="50" style="background-color: #e5f5f5;"></td>
            </tr>
            <tr role="row" style="background-color: #e5f5f5;">
              <td width="50" ></td>
              <td width="200"></td>
              <td width="50" ></td>
              <td width="40"></td>
              <td width="50" ></td>
              <td width="40"></td>         
              <td width="50" ></td>
              <td width="40"></td>
              <td width="50" ></td>
            </tr>  
            <tr role="row">
              <td style="background-color: #e5f5f5;height: 20px;"><b>ANÁLISIS</b></td>
              <td>&nbsp;</td>
              <td style="background-color: #e5f5f5;"><b>FECHA</b></td>
              <td style="color: black;">{{substr($orden->created_at,0,10)}}</td>
              <td style="background-color: #e5f5f5;"><b>HORA</b></td>
              <td style="color: black;">{{substr($orden->created_at,10,10)}}</td>         
              <td style="background-color: #e5f5f5;"><b>PÁGINA</b></td>
              <td>&nbsp;</td>
              <td width="50" style="background-color: #e5f5f5;"></td>
            </tr>
          </tbody>
        </table>
      </div>  
              
    </div> 

    <div id="footer">
      
      
    </div> 
  
  
    <div id="content"> 
    
      
      <br>
      <table style="font-size: 22px;width: 100%;">
        
        <tbody>
         
            <tr>
              <td><b>Seguro: </b></td>
              <td>{{$orden->seguro->nombre}}</td>
              <td><b>Nivel: </b></td>
              <td>@if($orden->id_nivel!=null){{$orden->nivel->nombre}}@endif</td>
              <td><b>Forma de Pago: </b></td>
              <td>@if(!is_null($orden->forma_de_pago)){{$orden->forma_de_pago->nombre}}@endif</td>  
            </tr>
            @if($orden->id_protocolo!=null)
            <tr>
              <td><b>Protocolo: </b></td>
              <td colspan="4" style="font-size: 17px;">@if($orden->id_protocolo!=null) {{$orden->protocolo->nombre}} @endif</td>  
            </tr>
            @endif
         
        </tbody>
      </table>
      <br>
      <table style="font-size: 18px;width: 100%;">
        <thead>
          <tr>
            <th style="width: 40%;">Examen</th>
            <th colspan="2" style="width: 40%;">Agrupador</th>
            <th style="width: 5%;">Cobertura</th>
            <th style="width: 15%;text-align: right;">Valor</th>
          </tr>   
        </thead>
        <tbody>
          @foreach($detalles as $detalle)
            @php
              $examen_agr = DB::table('examen_agrupador_sabana')->where('id_examen',$detalle->id_examen)->first();
              $agrupador = DB::table('examen_agrupador_labs')->where('id',$examen_agr->id_examen_agrupador_labs)->first();
            @endphp
            <tr>
              <td><?php echo $detalle->descripcion; ?></td>
              <td colspan="2">@if(!is_null($agrupador)){{$agrupador->nombre}} @endif</td>
              <td>@if(in_array($orden->id_protocolo,$pr)) @else{{$detalle->cubre}}@endif</td>
              <!--td style="text-align: right;">$ {{number_format(round($detalle->valor * $r ,2),2)}}</td-->
              <td style="text-align: right;">@if(in_array($orden->id_protocolo,$pr)) @else $ {{number_format(round($detalle->valor,2),2)}}@endif</td>  
            </tr>
          @endforeach
            <tr>
              <td style="text-align: right;"></td>
              <td style="text-align: right;"><b>Cantidad:</b></td>
              <td style="text-align: right;"><b>{{$orden->cantidad}}</b></td>
              <td style="text-align: right;"><b>SubTotal</b></td>
              <td style="text-align: right;"><b>$ {{round($orden->valor,2)}}</b></td>
            </tr>
            <tr>
              <td style="text-align: right;"></td>
              <td style="text-align: right;"></td>
              <td style="text-align: right;"></td>
              <td style="text-align: right;"><b>Descuento</b></td>
              <td style="text-align: right;"><b>(-)$ {{$orden->descuento_valor}}</b></td>
            </tr>
            <tr>
              <td style="text-align: right;"></td>
              <td style="text-align: right;"></td>
              <td style="text-align: right;"></td>
              <td style="text-align: right;"><b>Fee Administrativo</b></td>
              <td style="text-align: right;"><b>$ {{$orden->recargo_valor}}</b></td>
            </tr>
            <tr>
              <td style="text-align: right;"></td>
              <td style="text-align: right;"></td>
              <td style="text-align: right;"></td>
              <td style="text-align: right;"><b>Total</b></td>
              <td style="text-align: right;"><b>$ {{$orden->total_valor}}</b></td>
            </tr>
        </tbody>
      </table>
      
        
      

    </div>  
          
      
        
    
  

  

</body>
</html>  
