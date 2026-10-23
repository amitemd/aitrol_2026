<div class="box-body" style="font-size: 11px;font-family: 'Helvetica general3';">
  @php
        
    if(!is_null($orden_procedfuncional->id_paciente)){

      $xdata = DB::table('agenda as a')
                ->where('a.id_paciente',$orden_procedfuncional->id_paciente)
                ->join('historiaclinica as h','h.id_agenda','a.id')
                ->join('seguros as s','s.id','h.id_seguro')
                ->join('empresa as em','em.id','a.id_empresa')
                ->where('a.espid','<>','10')
                ->select('h.*','s.nombre','em.nombre_corto')
                ->first();
    }

  @endphp
  @php
    if(!is_null($orden_procedfuncional->id_doctor)){
          $xdoctor = DB::table('users as us')->where('us.id',$orden_procedfuncional->id_doctor)->first();
    }
  @endphp
  @php
    $fecha = substr($orden_procedfuncional->fecha_orden,0,10);
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
        <span><?php echo $orden_procedfuncional->motivo_consulta?></span>
      </div>
    </div>
  </div>
  <div class="col-md-12" style="padding: 1px;">
    <div class="row">
      <div class="col-md-12">
        <span style="font-family: 'Helvetica general';font-size: 12px">RESUMEN DE LA HISTORIA CL&IacuteNICA:</span>
      </div>
      <div class="col-12">
        <span><?php echo $orden_procedfuncional->resumen_clinico?></span>
      </div>
    </div>
  </div>
  <div class="col-md-12" style="padding: 1px;">
    <div class="row">
      <div class="col-md-12">
        <span style="font-family: 'Helvetica general';font-size: 12px">DIAGNOSTICO:</span>
      </div>
      <div class="col-12">
        <span><?php echo $orden_procedfuncional->diagnosticos?></span>
      </div>
    </div>
  </div>
  @php
    if(!is_null($orden_procedfuncional->id)){
      $procedimiento_orden_tipo = \Sis_medico\Orden_tipo::where('id_orden', $orden_procedfuncional->id)->where('id_grupo_procedimiento','19')
      ->first();
    }

    
    $texto = ""; 
    if(!is_null($procedimiento_orden_tipo)){ 
     
      $procedimiento_orden_proced = \Sis_medico\Orden_Procedimiento::where('id_orden_tipo', $procedimiento_orden_tipo->id)->get();

      $mas = true;
      foreach($procedimiento_orden_proced as $value2)
      {
        $nombre_procedimiento = \Sis_medico\Procedimiento::where('id', $value2->id_procedimiento)->first();

        if($mas == true){
          $texto = $nombre_procedimiento->nombre;
          $mas = false; 
        }
        else{
         $texto = $texto.' + '.$nombre_procedimiento->nombre;
        }
      }
        
    }
  @endphp
  <div class="col-md-12" style="padding: 1px;">
    @if($texto != "")
      <div class="row">
        <div class="col-md-12">
          <div style="background-color: #004AC1; color: white">
            <label style="font-family: 'Helvetica general';" for="id_procedimiento" class="col-md-12 control-label">PROCEDIMIENTOS FUNCIONALES
            </label>
          </div>
        </div>
        <div class="col-12">
          <span>
            {{$texto}}
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
        <span><?php echo $orden_procedfuncional->observacion_medica?></span>
      </div>
    </div>
  </div>
</div>




