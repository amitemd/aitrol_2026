  <html>
  <head>
    <title>ORDEN BIOPSIAS</title>
    <style type="text/css">
      #principal{
       width:800px;
      }

      @page { margin-top:25px;margin-bottom:30px; }

      #footer { margin-top: 110px;}
      #footer1 {  margin-top: 50px; }

      @font-face
        {font-family:"Cambria Math";
        panose-1:2 4 5 3 5 4 6 3 2 4;}
      @font-face
        {font-family:Calibri;
        panose-1:2 15 5 2 2 2 4 3 2 4;}
       /* Style Definitions */
       p.MsoNormal, li.MsoNormal, div.MsoNormal
        {margin-top:0cm;
        margin-right:0cm;
        margin-bottom:8.0pt;
        margin-left:0cm;
        line-height:107%;
        font-size:11.0pt;
        font-family:"Calibri",sans-serif;}
      .MsoChpDefault
        {font-family:"Calibri",sans-serif;}
      .MsoPapDefault
        {margin-bottom:8.0pt;
        line-height:107%;}

      div.WordSection1
      {
        page:WordSection1;
      }

      .parrafo2 p{
        margin: 0;
      }

      #bloq1{
        /*float:left;*/
        width:800px;
        padding-right:20px;
      }

      #bloq2{
        /*float:right;*/
        width:800px;
        padding-left:20px;
      }

      .table_wrapper{
        width:100%;margin:0px;padding:0px;

      }
    </style>
  </head>
  <body lang=ES-EC style="margin-top: 5px;margin-top:0px;padding-top:0px">
    <div id="principal" style="margin-top:0px;padding-top:0px">
      <table class="table_wrapper"   cellpadding="0" cellspacing="0" style="margin-top:0px;padding-top:0px">
        <tr>
          <td style="width:50%;border-right:1px solid dashed" valign="top">
            <div id="bloq1">
              @php
                  $date = $historia->fecha_atencion;
              @endphp
              <div id="header">
                <div style="float: left;width: 10%;text-align: left;">
                  <img style="margin: 0;width: 200px" src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}">
                </div>
                <div  style="text-align: center;">
                    <img style="margin: 0;width: 180px;">
                    <p style="font-size: 19px;"><b style="text-align: center;">@lang('hc4.instituto_ecuatoriano') <b><br><b>@lang('hc4.deenfermedadesdigestivas')</b></p>
                </div>
              </div>
              <br>
              <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
                <tbody>
                  <tr>
                    <td style="border-right: 1px solid;width: 103px;text-align: center;font-size: 19px;background-color: #4682B4;"><b style="text-align: center;color: white">@lang('hc4.ordenBiopsias')</b></td>
                  </tr>
                </tbody>
              </table>
                @php
                  $progromador = Auth::user()->id;
                  if(!is_null($paciente)){
                    $seguro = Sis_medico\Seguro::find($paciente->id_seguro);
                  }
                @endphp
                @if(!is_null($biop_first->created_at))
                  @php
                    $fecha = substr($biop_first->historiaclinica->agenda->fechaini,0,10);
                    $invert = explode( '-',$fecha);
                    $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0];
                  @endphp
                @endif
                <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
                  <tbody>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;@lang('hc4.fecha-'):</b></td>
                      <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$fecha_invert}}</td>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;@lang('hc4.cedula-')</b></td>
                      <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$paciente->id}}</td>
                      <td colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>@lang('hc4.ordenBiopsias')</b></td>
                    </tr>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;@lang('hc4.paciente-'):</b></td>
                      <td colspan="4" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif</td>
                    </tr>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>@lang('hc4.edad-'):</b></td>
                      <td colspan="2"style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$edad}}</td>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>@lang('hc4.seguro-'):</b></td>
                      <td colspan="1" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$seguro->nombre}}</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;background-color: #4682B4;color: white;border-color: currentColor;"><b>@lang('hc4.procedimiento-')</b></td>
                      </tr>
                      <tr>
                       <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;padding-left: 5px;padding-top: 5px"><?php echo $lista_proced ?></td>
                      </tr>
                    <tr>
                      <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;text-align: center;"><b>&nbsp;@lang('hc4.detallesFrascos')</b></td>
                    </tr>
                    @foreach($impr_biospsias as $value)
                    <tr>
                      <td colspan="2" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px;"><p style="width: 100%; border: none; margin:0">Fco {{$value->numero_frasco}}: {{$value->descripcion_frasco}}</p></td>
                      <td colspan="3" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px,"><p style="width: 100%; border: none; margin:0">Obs: {{$value->observacion}}</p></td>
                    </tr>
                    @endforeach
                      <tr>
                        <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;@lang('hc4.cuadroClinico-'):</b></td>
                      </tr>
                      <tr>
                        <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;padding-left: 5px;padding-top: 5px"><?php echo $hc_proced->cuadro_clinico_bp ?></td>
                      </tr>
                      <tr>
                        <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;@lang('hc4.diagnostico-')</b></td>
                      </tr>
                      <tr>
                       <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;padding-left: 5px;padding-top: 5px"><?php echo $hc_proced->diagnosticos_bp ?>
                       </td>
                      </tr>
                    

                  </tbody>
                </table>
                <div id="footer" style="text-align: center">
                  <p style="text-align: center;">
                    <hr style="width: 30%;margin-left: 202pt;margin:0 auto">
                    <label class="control-label" style="font-family: 'Helvetica general';font-size: 20px;">@lang('hc4.dr_(a)')
                    </label>
                  </p>
                </div>
                <div id="footer1">
                  <div style="float: left;font-size: 14px;width: 50%;text-align: center;">
                    Av.Juan Tanca Marengo, @lang('hc4.calle') 13E NE <br>
                    Torre Médico Vitalis 1 - Mezanine 3 <br>
                    @lang('hc4.telefono').: 042109180 - 042109180 <br>
                    Celular: 09993066407 - 0959777712 <br>
                    iecedgye@gmail.com / www.ieced.com.ec
                  </div>
                  <div style="font-size: 14px;text-align: center;">
                    Av.Juan Tanca Marengo, @lang('hc4.calle') 13E NE <br>
                    Torre Médica II - 4to piso # 408-406 <br>
                    @lang('hc4.telefono').: 042109180 - 042109180 <br>
                    Celular: 09993066407 - 0959777712 <br>
                    iecedgye@gmail.com / www.ieced.com.ec
                  </div>
                </div>
                <div id="footer2">
                  <p>
                  </p>
                </div>
            </div>
          </td>
          <td style="width:50%"  valign="top">
            <div id="bloq2">
              @php
                  $date = $historia->fecha_atencion;
              @endphp
              <div id="header">
                <div style="float: left;width: 10%;text-align: left;">
                  <img style="margin: 0;width: 200px" src="{{base_path().'/storage/app/logo/logo1391707460001.png'}}">
                </div>
                <div  style="text-align: center;">
                    <img style="margin: 0;width: 180px;">
                    <p style="font-size: 19px;"><b style="text-align: center;">@lang('hc4.instituto_ecuatoriano') <b><br><b>@lang('hc4.deenfermedadesdigestivas')</b></p>
                </div>
              </div>
              <br>
              <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
                <tbody>
                  <tr>
                    <td style="border-right: 1px solid;width: 103px;text-align: center;font-size: 19px;background-color: #4682B4;"><b style="text-align: center;color: white">@lang('hc4.ordenBiopsias')</b></td>
                  </tr>
                </tbody>
              </table>
                @php
                  if(!is_null($paciente)){
                    $seguro = Sis_medico\Seguro::find($paciente->id_seguro);
                  }
                @endphp
                @if(!is_null($biop_first->created_at))
                  @php
                    $fecha = substr($biop_first->created_at,0,10);
                    $invert = explode( '-',$fecha);
                    $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0];
                  @endphp
                @endif
                <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
                  <tbody>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;@lang('hc4.fecha-'):</b></td>
                      <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$fecha_invert}}</td>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;@lang('hc4.cedula-')</b></td>
                      <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$paciente->id}}</td>
                      <td colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>@lang('hc4.ordenBiopsias')</b></td>
                    </tr>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;@lang('hc4.paciente-'):</b></td>
                      <td colspan="4" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif</td>
                    </tr>
                    <tr>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>@lang('hc4.edad-'):</b></td>
                      <td colspan="2"style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$edad}}</td>
                      <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>@lang('hc4.seguro-'):</b></td>
                      <td colspan="1" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$seguro->nombre}}</td>
                    </tr>
                    <tr>
                        <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;background-color: #4682B4;color: white;border-color: currentColor;"><b>@lang('hc4.procedimientos-')</b></td>
                      </tr>
                      <tr>
                       <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;padding-left: 5px;padding-top: 5px"><?php echo $lista_proced ?></td>
                      </tr>
                    <tr>
                      <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;text-align: center;"><b>&nbsp;@lang('hc4.detallesFrascos')</b></td>
                    </tr>
                    @foreach($impr_biospsias as $value)
                    <tr>
                      <td colspan="2" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px;"><p style="width: 100%; border: none; margin:0">Fco {{$value->numero_frasco}}: {{$value->descripcion_frasco}}</p></td>
                      <td colspan="3" style="border-right: 2px solid;border-top: 1px solid;font-size: 16px;padding-left: 6px,"><p style="width: 100%; border: none; margin:0">Obs: {{$value->observacion}}</p></td>
                    </tr>
                    @endforeach
                      <tr>
                        <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;@lang('hc4.cuadroClinico-'):</b></td>
                      </tr>
                      <tr>
                        <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;padding-left: 5px;padding-top: 5px"><?php echo $hc_proced->cuadro_clinico_bp ?></td>
                      </tr>
                      <tr>
                        <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;@lang('hc4.diagnostico-')</b></td>
                      </tr>
                      <tr>
                       <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px;padding-left: 5px;padding-top: 5px"><?php echo $hc_proced->diagnosticos_bp ?></td>
                      </tr>
                     
                     
                  </tbody>
                </table>
                <div id="footer" style="text-align: center">
                  <p style="text-align: center;">
                    <hr style="width: 30%;margin-left: 202pt;margin:0 auto">
                    <label class="control-label" style="font-family: 'Helvetica general';font-size: 20px;">@lang('hc4.dr_(a)')
                    </label>
                  </p>
                </div>
                <div style="float: left;width: 50%;text-align: center;">
                  @if(!is_null($logo_emp))
                    <img width=850 height=170 src="{{base_path().'/storage/app/logo/'.$logo_emp->logo_receta}}" style="" align=center><br>
                  @endif  
                </div>
            </div>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>
