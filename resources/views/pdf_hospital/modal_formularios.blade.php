
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">FORMULARIOS </h4>
</div>

<div class="modal-body">
    <div class="row" >
        
        @if(!is_null($anamesis))    
        <div class="col-md-4">  
            <a class="btn btn-success btn-xs" href="{{route('formulario.002',['id'=>$anamesis->id])}}@if($tipo == 'auditoria')?xtipo=auditoria @endif" target="_blank">F-002 Anamenesis</a>  
            <embed src="{{route('formulario.002',['id'=>$anamesis->id])}}@if($tipo == 'auditoria')?xtipo=auditoria @endif" type="application/pdf" width="100%" style="height:300px;" />
        </div>
        @endif
        @if(!is_null($epicrisis))    
        <div class="col-md-4">  
            <a class="btn btn-success btn-xs" href="{{route('formulario.006',['id'=>$epicrisis->id])}}@if($tipo == 'auditoria')?xtipo=auditoria @endif" target="_blank">F-006 Epicrisis</a>
            <embed src="{{route('formulario.006',['id'=>$epicrisis->id])}}@if($tipo == 'auditoria')?xtipo=auditoria @endif" type="application/pdf" width="100%" style="height:300px;" />
        </div>
        @endif
        @if(!is_null($protocolo)) 
        <div class="col-md-4"> 
            <form class="form-vertical" role="form" method="POST" action="{{ route('evolucion.nuevo_guardar_op')}}">
                {{ csrf_field() }}
                <input  type="hidden" class="form-control" name="protocolo" value="{{ $protocolo->id }}" >
                <input  type="hidden" class="form-control" name="tipo_anestesia" value="{{ $protocolo->tipo_anestesia }}" >
                <input  type="hidden" class="form-control" name="id_doctor_examinador2" value="{{ $protocolo->procedimiento->id_doctor_examinador }}" >
                <input  type="hidden" class="form-control" name="fecha_operacion" value="{{ $protocolo->fecha_operacion }}" >
                <input  type="hidden" class="form-control" name="id_doctor_ayudante_con" value="{{ $protocolo->procedimiento->id_doctor_ayudante_con }}" >
                <input  type="hidden" class="form-control" name="hora_ini" value="{{ $protocolo->hora_inicio }}" >

                <button type="submit" formtarget="_blank" formaction="{{ route('evolucion.nuevo_guardar_op')}}" class="btn btn-success btn btn-xs" id="new_guardar_op">F-005 Evolucion</button>
            </form>
            <div class="alert alert-info" style="margin-top:8px;">Use el botón <b>F-005 Evolución</b> para generar el documento. La vista previa automática fue deshabilitada para evitar modificaciones clínicas mediante una petición GET.</div>
        </div>
         
        <div class="col-md-4">  
            <a class="btn btn-success btn-xs" href="{{route('formulario.12B',['id'=>$protocolo->id])}}" target="_blank">F-012 Informe</a>
            <embed src="{{route('formulario.12B',['id'=>$protocolo->id])}}" type="application/pdf" width="100%" style="height:300px;" />
        </div>
         
        <div class="col-md-4"> 
            <form class="form-vertical" role="form" method="POST" action="{{ route('formulario.017_aud')}}">
                {{ csrf_field() }}
                <input  type="hidden" class="form-control" name="protocolo" value="{{ $protocolo->id }}" >
                <input  type="hidden" class="form-control" name="tipo_anestesia" value="{{ $protocolo->tipo_anestesia }}" >
                <input  type="hidden" class="form-control" name="id_doctor_examinador2" value="{{ $protocolo->procedimiento->id_doctor_examinador }}" >
                <input  type="hidden" class="form-control" name="fecha_operacion" value="{{ $protocolo->fecha_operacion }}" >
                <input  type="hidden" class="form-control" name="id_doctor_ayudante_con" value="{{ $protocolo->procedimiento->id_doctor_ayudante_con }}" >
                <input  type="hidden" class="form-control" name="hora_ini" value="{{ $protocolo->hora_inicio }}" >

                <button type="submit" formtarget="_blank" formaction="{{ route('formulario.017_aud')}}" class="btn btn-success btn btn-xs" id="new_guardar_op">F-017 Protocolo</button>
            </form>
            <embed src="{{ route('formulario.017_aud')}}?protocolo={{ $protocolo->id }}&tipo_anestesia={{ $protocolo->tipo_anestesia }}&id_doctor_examinador2={{ $protocolo->procedimiento->id_doctor_examinador }}&fecha_operacion={{ $protocolo->fecha_operacion }}&id_doctor_ayudante_con={{ $protocolo->procedimiento->id_doctor_ayudante_con }}&hora_ini={{ $protocolo->hora_inicio }}" type="application/pdf" width="100%" style="height:300px;" />
        </div>
        @if(!is_null($biopsia))
        <div class="col-md-4">  
            <a class="btn btn-success btn-xs" href="{{route('nuevoArchivoPlano.formulario_013_pdf', ['id'=>$protocolo->id_hc_procedimientos])}}" target="_blank">F-013A Solic.Patológico</a>
            <embed src="{{route('nuevoArchivoPlano.formulario_013_pdf', ['id'=>$protocolo->id_hc_procedimientos])}}" type="application/pdf" width="100%" style="height:300px;" />
        </div>
        @endif
        @if(!is_null($infobiopsia))
        <div class="col-md-4">  
            <a class="btn btn-success btn-xs" href="{{route('formulario.013B', ['id'=>$protocolo->id_hc_procedimientos])}}" target="_blank">F-013B Infor.Patológico</a>
            <embed src="{{route('formulario.013B', ['id'=>$protocolo->id_hc_procedimientos])}}" type="application/pdf" width="100%" style="height:300px;" />
        </div>
        @endif
        @if(!is_null($solicitud))
        <div class="col-md-4">  
            <a class="btn btn-success btn-xs" href="{{route('hospital.nuevo.formulario008_pdf', ['id' => $solicitud->id])}}" target="_blank">F-008 Emergencia</a>
            <embed src="{{route('hospital.nuevo.formulario008_pdf', ['id' => $solicitud->id])}}" type="application/pdf" width="100%" style="height:300px;" />
        </div>
        <div class="col-md-4">  
            <a class="btn btn-success btn-xs" href="{{route('formulario.001', ['id' => $solicitud->id])}}" target="_blank">F-001 Admision</a>
            <embed src="{{route('formulario.001', ['id' => $solicitud->id])}}" type="application/pdf" width="100%" style="height:300px;" />
        </div>
        @endif
        
        @endif
    </div> 
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script type="text/javascript">
     
</script>
