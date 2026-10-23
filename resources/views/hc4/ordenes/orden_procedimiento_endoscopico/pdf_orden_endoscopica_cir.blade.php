<!DOCTYPE html>
<html>
<head>
  
  <title>ORDEN ENDOSCOPICO</title>

  <style>
   
    @page { margin: 80px 70px; }
    #header { position: fixed; left: 0px; top: -40px; right: 0px; height: 150px; text-align: center; }
    #footer { position: fixed; left: 0px; bottom: 0px; right: 0px; height: 320px; }
    #footer1 { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 110px; }
    #footer2 { position: fixed; left: 0px; bottom: -50px; right: 0px; height: 255px; text-align: center; }
   
    /* Font Definitions */
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
      {page:WordSection1;}
    .parrafo2 p{
      margin: 0;
    }
  </style>    
</head>
<body lang=ES-EC style="margin-top: 5px;">
    
    <div id="header">
     
      <div style="float: left;width: 10%;text-align: left;">
        <img style="margin: 0;width: 250px;" src="{{base_path().'/storage/app/logo/logocir.png'}}">
      </div>
      <div  style="text-align: center;">
          <img style="margin: 0;width: 180px;">
          <p style="font-size: 19px;"><b style="text-align: center;">INSTITUTO ECUATORIANO <b><br><b>DE ENFERMEDADES DIGESTIVAS</b></p>
      </div>
    </div>
    <br>
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td style="border-right: 1px solid;width: 103px;text-align: center;font-size: 19px;background-color: #4682B4;"><b style="text-align: center;color: white">ORDEN DE PROCEDIMIENTO ENDOSC&Oacute;PICO</b></td>
        </tr>
      </tbody>
    </table>
      @php
        if(!is_null($paciente)){
          $seguro = Sis_medico\Seguro::find($paciente->id_seguro); 
        }
      @endphp
      @if(!is_null($orden_proc_endoscopico->fecha_orden))
        @php
          $fecha = substr($orden_proc_endoscopico->fecha_orden,0,10);
          $invert = explode( '-',$fecha);
          $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
        @endphp
      @endif
      @php
        if(!is_null($orden_proc_endoscopico->id)){
          $procedimiento_orden_tipo = \Sis_medico\Orden_Tipo::where('id_orden', $orden_proc_endoscopico->id)->get();
        }

        $texto1 = ""; 
        $texto2 = "";
        $texto3 = "";
        $texto4 = "";
        $texto5 = "";
        $texto6 = "";
       
        if(!is_null($procedimiento_orden_tipo)){ 
          foreach($procedimiento_orden_tipo as $value1)
          {
                                          
            if($value1->id_grupo_procedimiento == 1){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true;
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();
                if($mas == true){
                  $texto1 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                  $texto1 = $texto1.' + '.$nombre_procedimiento->nombre;
                }

              }
            }

                                           
            if($value1->id_grupo_procedimiento == 2){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true; 
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto2 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                  $texto2 = $texto2.' + '.$nombre_procedimiento->nombre;
                }

              }
            }

            
            if($value1->id_grupo_procedimiento == 3){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true; 
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto3 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                 $texto3 = $texto3.' + '.$nombre_procedimiento->nombre;
                }

              }
            }

                                            
            if($value1->id_grupo_procedimiento == 9){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true; 
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto4 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                   $texto4 = $texto4.' + '.$nombre_procedimiento->nombre;
                }

              }
            }
                                            
                                            
            if($value1->id_grupo_procedimiento == 10){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true; 
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto5 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                 $texto5 = $texto5.' + '.$nombre_procedimiento->nombre;
                }

              }
            }


            if($value1->id_grupo_procedimiento == 14){
              $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $value1->id)->get();

              $mas = true; 
              foreach($procedimiento_orden_proced as $value2)
              {
                $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

                if($mas == true){
                  $texto6 = $nombre_procedimiento->nombre;
                  $mas = false; 
                }
                else{
                 $texto6 = $texto6.' + '.$nombre_procedimiento->nombre;
                }

              }
            }

          }
        }   
      @endphp
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;FECHA</b></td>
          <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$fecha_invert}}</td>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;C.I</b></td>
          <td  colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;{{$paciente->id}}</td>
          <td colspan="1" style="border-right: 1px solid;font-size: 16px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>PROCEDIMIENTO ENDOSC&Oacute;PICO</b></td>
        </tr>
        <tr>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;PACIENTE:</b></td>{{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif  {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
          <td colspan="2" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;</td>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>EDAD:</b></td>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$edad}}</td>
        </tr>
        <tr>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px"><b>&nbsp;DOCTOR SOLICITANTE:</b></td>
          <td colspan="2" style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$doctor_solicitante->apellido1}} {{$doctor_solicitante->apellido2}} {{$doctor_solicitante->nombre1}} {{$doctor_solicitante->nombre2}}</td>
          <td style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;<b>SEGURO:</b></td>
          <td  style="border-right: 1px solid;border-top: 1px solid;font-size: 16px">&nbsp;{{$seguro->nombre}}</td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;ANTECEDENTES PATOLOGICOS</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 10px"><?php echo $paciente->antecedentes_pat?></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;ANTECEDENTES FAMILIARES</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 10px"><?php echo $paciente->antecedentes_fam?></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;ANTECEDENTES QUIRURGICOS</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 10px"><?php echo $paciente->antecedentes_quir?></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;MOTIVO</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 10px"><?php echo $orden_proc_endoscopico->motivo_consulta?></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;RESUMEN DE LA HISTORIA CL&Iacute;NICA</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 5px"><?php echo $orden_proc_endoscopico->resumen_clinico?></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;DIAGN&Oacute;STICO</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 5px"><?php echo $orden_proc_endoscopico->diagnosticos?></td>
        </tr>
      </tbody>
    </table>
    <br>
    @if($texto1 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;ENDOSCOPIAS DIGESTIVAS</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto1}}</td>
        </tr>
      </tbody>
    </table>
    <br>
    @endif
    @if($texto2 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
       <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;COLONOSCOPIA-PROCTOLOGIA</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto2}}</td>
        </tr>
      </tbody>
    </table>
    <br>
    @endif
    @if($texto3 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;INTESTINO DELGADO</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto3}}</td>
        </tr>
      </tbody>
    </table>
    <br>
    @endif
    @if($texto4 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;ECOENDOSCOPIAS</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto4}}</td>
        </tr>
      </tbody>
    </table>
    <br>
    @endif
    @if($texto5 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
      <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;CPRE</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto5}}</td>
        </tr>
      </tbody>
    </table>
    <br>
    @endif
    @if($texto6 != "")
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
      <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;BRONCOSCOPIA</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px">&nbsp;{{$texto6}}</td>
        </tr>
      </tbody>
    </table>
    <br> 
    @endif
    <table  style="border: 1px solid; width: 100%;border-collapse: collapse; font-size: 14px;">
      <tbody>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 19px;background-color: #4682B4;color: white;border-color: currentColor;"><b>&nbsp;OBSERVACI&Oacute;N M&Eacute;DICA</b></td>
        </tr>
        <tr>
          <td colspan="5" style="border-right: 1px solid;border-top: 1px solid;font-size: 18px;padding-left: 5px;padding-top: 10px">
            @if($orden_proc_endoscopico->necesita_valoracion=='SI') REQUIERE VALORACION CARDIOLÓGICA<BR> @endif
            <?php echo $orden_proc_endoscopico->observacion_medica?></td>
        </tr>
      </tbody>
    </table>
    <br>

    @php
      $nom = "";
      if(!is_null($firma)){
        $nom = "/storage/app/avatars/$firma->nombre";
      }
    @endphp

    @if($nom != "")
    <div id="footer" style="text-align: center">
      <p class=MsoNormal style='margin-bottom:0cm;margin-bottom:.0001pt;line-height:normal'><span style='font-size:6.0pt;mso-bidi-font-size:11.0pt'><o:p><img style='position: absolute;top: -12px;margin-left: 220pt;padding-left: 0px;' width=250 height=100 src="{{base_path().$nom}}" align=center hspace=12></o:p></span>
      </p>
    </div>
    @endif

    <div id="footer1" style="text-align: center!important;">
     
        <img style="width: 90%; margin-bottom: 10px;" src="{{base_path().'/storage/app/logo/footercir.jpg'}}" alt="footer_cir">
   
    </div>
    <div id="footer2" style="text-align: center">
      <p>
        <hr style="width: 30%;margin-left: 202pt">
        <label class="control-label" style="font-family: 'Helvetica general';font-size: 20px; margin-left: 30px!important;">Doctor (a)</label>
      </p>
    </div>
    
  </body>
</html>  