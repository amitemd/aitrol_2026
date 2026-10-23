<div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';">
  @php
        
    if(!is_null($orden_proendoscopico->id_paciente)){

      $xdata = DB::table('agenda as a')
                ->where('a.id_paciente',$orden_proendoscopico->id_paciente)
                ->join('historiaclinica as h','h.id_agenda','a.id')
                ->join('seguros as s','s.id','h.id_seguro')
                ->join('empresa as em','em.id','a.id_empresa')
                ->where('a.espid','<>','10')
                ->select('h.*','s.nombre','em.nombre_corto')
                ->first();
    }

  @endphp
  @php
    if(!is_null($orden_proendoscopico->id_doctor)){
          $xdoctor = DB::table('users as us')->where('us.id',$orden_proendoscopico->id_doctor)->first();
    }
  @endphp
  @php
    $fecha = substr($orden_proendoscopico->fecha_orden,0,10);
    $invert = explode( '-',$fecha);
    $fecha_invert = $invert[2]."/".$invert[1]."/".$invert[0]; 
  @endphp
  <div class="col-md-12" style="padding: 1px;">
    <div class="row">
      <div class="col-md-8">
            @if(!is_null($fecha_invert))
              <span style="font-family: 'Helvetica general';font-size: 12px">FECHA:</span>
              <label for="fecha" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$fecha_invert}}</b>
              </label>
            @endif 
      </div>
    </div>
  </div>
  <div class="col-md-12" style="padding: 1px;">
    <div class="row">
      <div class="col-md-8">
        @if(!is_null($paciente)) 
          <span style="font-family: 'Helvetica general'; font-size: 12px">PACIENTE:</span>
          <label for="paciente" class="control-label" style="font-family: 'Helvetica general';font-size: 12px">
            <b>{{$paciente->apellido1}} {{$paciente->apellido2}} {{$paciente->nombre1}} {{$paciente->nombre2}}</b>
          </label>
        @endif 
      </div>
      <div class="col-md-4">
        <span style="font-family: 'Helvetica general';font-size: 12px">EDAD:</span>
        <label for="fecha" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$edad}}</b>
        </label>
      </div>
    </div>
  </div>
  <div class="col-md-12" style="padding: 1px;">
    <div class="row">
      <div class="col-md-8">
        @if(!is_null($ndoctor)) 
          <span style="font-family: 'Helvetica general';font-size: 12px">DOCTOR (a) SOLICITANTE:</span>
          <label for="doctor" class="control-label" style="font-family: 'Helvetica general';font-size: 12px"><b>{{$ndoctor->apellido1}} {{$ndoctor->apellido2}} {{$ndoctor->nombre1}}  {{$ndoctor->nombre2}}
                </b>
          </label>
        @endif 
      </div>
      <div class="col-md-4">
        @if(!is_null($xdata)) 
          <span style="font-family: 'Helvetica general';font-size: 12px">CONVENIO:</span>
          <label for="convenio" class="control-label" style="font-family: 'Helvetica general';font-size: 12px">
            <b> 
            {{$xdata->nombre}}-{{$xdata->nombre_corto}}
            </b>
          </label>
        @endif
      </div>
    </div>
  </div>
  <div class="col-md-12" style="padding: 1px;">
    <div class="row">
      <div class="col-md-12">
        <span style="font-family: 'Helvetica general';font-size: 12px">MOTIVO:</span>
      </div>
      <div class="col-12">
        <span><?php echo $orden_proendoscopico->motivo_consulta?></span>
      </div>
    </div>
  </div>
  <div class="col-md-12" style="padding: 1px;">
    <div class="row">
      <div class="col-md-12">
        <span style="font-family: 'Helvetica general';font-size: 12px">RESUMEN DE LA HISTORIA CL&IacuteNICA:</span>
      </div>
      <div class="col-12">
        <span><?php echo $orden_proendoscopico->resumen_clinico?></span>
      </div>
    </div>
  </div>
  <div class="col-md-12" style="padding: 1px;">
    <div class="row">
      <div class="col-md-12">
        <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
      </div>
      <div class="col-12">
        <span><?php echo $orden_proendoscopico->diagnosticos?></span>
      </div>
    </div>
  </div>

  @php
    if(!is_null($orden_proendoscopico->id)){
      $procedimiento_orden_tipo = \Sis_medico\Orden_tipo::where('id_orden', $orden_proendoscopico->id)->get();
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
  
  <div class="col-md-12" style="padding: 1px;">
    @if($texto1 != "")
      <div class="row">
        <div class="col-md-12">
          <div style="background-color: #004AC1; color: white">
            <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">ENDOSCOPIAS DIGESTIVAS 
            </label>
          </div>
        </div>
        <div class="col-12">
          <span>
            {{$texto1}}
          </span>
        </div>
      </div>
    @endif
  </div>
  <div class="col-md-12" style="padding: 1px;">
    @if($texto2 != "")
      <div class="row">
        <div class="col-md-12">
          <div style="background-color: #004AC1; color: white">
            <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">COLONOSCOPIA  
            </label>
          </div>
        </div>
        <div class="col-12">
          <span>
            {{$texto2}}
          </span>
        </div>
      </div>
    @endif
  </div>
  <div class="col-md-12" style="padding: 1px;">
    @if($texto3 != "")
      <div class="row">
        <div class="col-md-12">
          <div style="background-color: #004AC1; color: white">
            <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">INTESTINO DELGADO  
            </label>
          </div>
        </div>
        <div class="col-12">
          <span>
            {{$texto3}}
          </span>
        </div>
      </div>
    @endif
  </div>
  <div class="col-md-12" style="padding: 1px;">
    @if($texto4 != "")
      <div class="row">
        <div class="col-md-12">
          <div style="background-color: #004AC1; color: white">
            <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">ECOENDOSCOPIAS   
            </label>
          </div>
        </div>
        <div class="col-12">
          <span>
            {{$texto4}}
          </span>
        </div>
      </div>
    @endif
  </div>
  <div class="col-md-12" style="padding: 1px;">
    @if($texto5 != "")
      <div class="row">
        <div class="col-md-12">
          <div style="background-color: #004AC1; color: white">
            <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">CPRE  
            </label>
          </div>
        </div>
        <div class="col-12">
          <span>
            {{$texto5}}
          </span>
        </div>
      </div>
    @endif
  </div>
  <div class="col-md-12" style="padding: 1px;">
    @if($texto6 != "")
      <div class="row">
        <div class="col-md-12">
          <div style="background-color: #004AC1; color: white">
            <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">BRONCOSCOPIA  
            </label>
          </div>
        </div>
        <div class="col-12">
          <span>
            {{$texto6}}
          </span>
        </div>
      </div>
    @endif
  </div>
  <div class="col-md-12" style="padding: 1px;">
    <div class="row">
      <div class="col-md-12">
        <span style="font-family: 'Helvetica general';font-size: 12px">OBSERVACION M&EacuteDICA:</span>
      </div>
      <div class="col-12">
        <span><?php echo $orden_proendoscopico->observacion_medica?></span>
      </div>
    </div>
  </div>
</div>




