
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
            <form class="form-vertical" role="form" method="POST" action="{{ route('evolucion.nuevo_guardar_op')}}@if($tipo == 'auditoria')?xtipo=auditoria @endif">
                {{ csrf_field() }}
                <input  type="hidden" class="form-control" name="protocolo" value="{{ $protocolo->id }}" >
                <input  type="hidden" class="form-control" name="tipo_anestesia" value="{{ $protocolo->tipo_anestesia }}" >
                <input  type="hidden" class="form-control" name="id_doctor_examinador2" value="{{ $protocolo->procedimiento->id_doctor_examinador }}" >
                <input  type="hidden" class="form-control" name="fecha_operacion" value="{{ $protocolo->fecha_operacion }}" >
                <input  type="hidden" class="form-control" name="id_doctor_ayudante_con" value="{{ $protocolo->procedimiento->id_doctor_ayudante_con }}" >
                <input  type="hidden" class="form-control" name="hora_ini" value="{{ $protocolo->hora_inicio }}" >

                <button type="submit" formtarget="_blank" formaction="{{ route('evolucion.nuevo_guardar_op')}}@if($tipo == 'auditoria')?xtipo=auditoria @endif" class="btn btn-success btn btn-xs" id="new_guardar_op">F-006 Evolucion</button>
            </form>
            <embed src="{{ route('evolucion.nuevo_guardar_op')}}?protocolo={{ $protocolo->id }}&tipo_anestesia={{ $protocolo->tipo_anestesia }}&id_doctor_examinador2={{ $protocolo->procedimiento->id_doctor_examinador }}&fecha_operacion={{ $protocolo->fecha_operacion }}&id_doctor_ayudante_con={{ $protocolo->procedimiento->id_doctor_ayudante_con }}&hora_ini={{ $protocolo->hora_inicio }}@if($tipo == 'auditoria')&xtipo=auditoria @endif" type="application/pdf" width="100%" style="height:300px;" />
        </div>
        @endif
        @if(!is_null($protocolo)) 
        <div class="col-md-4">  
            <a class="btn btn-success btn-xs" href="{{route('formulario.12B',['id'=>$protocolo->id])}}@if($tipo == 'auditoria')?xtipo=auditoria @endif" target="_blank">F-012 INFORME</a>
            <embed src="{{route('formulario.12B',['id'=>$protocolo->id])}}@if($tipo == 'auditoria')?xtipo=auditoria @endif" type="application/pdf" width="100%" style="height:300px;" />
        </div>
        @endif
        @if(!is_null($protocolo)) 
        <div class="col-md-4"> 
            <form class="form-vertical" role="form" method="POST" action="{{ route('formulario.017')}}">
                {{ csrf_field() }}
                
                @if($tipo == 'auditoria') 
                <input  type="hidden" class="form-control" name="protocolo" value="{{ $protocolo->id }}" >
                <input  type="hidden" class="form-control" name="xtipo" value="auditoria" >
                @else
                <input  type="hidden" class="form-control" name="protocolo" value="{{ $protocolo->id }}" >
                @endif
                <input  type="hidden" class="form-control" name="tipo_anestesia" value="{{ $protocolo->tipo_anestesia }}" >
                <input  type="hidden" class="form-control" name="id_doctor_examinador2" value="{{ $protocolo->procedimiento->id_doctor_examinador }}" >
                <input  type="hidden" class="form-control" name="fecha_operacion" value="{{ $protocolo->fecha_operacion }}" >
                <input  type="hidden" class="form-control" name="id_doctor_ayudante_con" value="{{ $protocolo->procedimiento->id_doctor_ayudante_con }}" >
                <input  type="hidden" class="form-control" name="hora_ini" value="{{ $protocolo->hora_inicio }}" >

                <button type="submit" formtarget="_blank" formaction="{{ route('formulario.017')}}" class="btn btn-success btn btn-xs" id="new_guardar_op">F-017 Protocolo</button>
            </form>
            <embed src="{{ route('formulario.017')}}?protocolo={{ $protocolo->id_protocolo_org }}&tipo_anestesia={{ $protocolo->tipo_anestesia }}&id_doctor_examinador2={{ $protocolo->procedimiento->id_doctor_examinador }}&fecha_operacion={{ $protocolo->fecha_operacion }}&id_doctor_ayudante_con={{ $protocolo->procedimiento->id_doctor_ayudante_con }}&hora_ini={{ $protocolo->hora_inicio }}" type="application/pdf" width="100%" style="height:300px;" />
        </div>
            
            <div class="col-md-4">  
                <a class="btn btn-success btn-xs" href="{{route('formulario.013A',['id'=>$protocolo->id_hc_procedimientos])}}" target="_blank">F-013 SOLICITUD</a>
                <embed src="{{route('formulario.013A',['id'=>$protocolo->id_hc_procedimientos])}}" type="application/pdf" width="100%" style="height:300px;" />
            </div>
            
            @if(!is_null($biopsia))
            <div class="col-md-4">  
                <a class="btn btn-success btn-xs" href="{{route('formulario.013B',['id'=>$biopsia->id_hc_procedimiento])}}" target="_blank">F-013 INFORME</a>
                <embed src="{{route('formulario.013B',['id'=>$biopsia->id_hc_procedimiento])}}" type="application/pdf" width="100%" style="height:300px;" />
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
