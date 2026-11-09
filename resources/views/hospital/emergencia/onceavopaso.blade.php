<style>
    .clinical-diagnosis-editor{overflow:hidden;border:1px solid #d8e6ef;border-radius:16px;background:#fff;box-shadow:0 12px 30px rgba(20,69,101,.10)}
    .clinical-diagnosis-editor .diagnosis-heading{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:22px 24px;color:#fff;background:linear-gradient(120deg,#12668d 0%,#128ab0 58%,#18a79f 100%)}
    .clinical-diagnosis-editor .diagnosis-title-wrap{display:flex;align-items:center;gap:14px}
    .clinical-diagnosis-editor .diagnosis-icon{display:inline-flex;width:48px;height:48px;flex:0 0 48px;align-items:center;justify-content:center;border:1px solid rgba(255,255,255,.32);border-radius:13px;background:rgba(255,255,255,.14);font-size:21px}
    .clinical-diagnosis-editor .diagnosis-kicker{margin:0 0 3px;color:#c9f7f4;font-size:11px;font-weight:800;letter-spacing:.10em;text-transform:uppercase}
    .clinical-diagnosis-editor .diagnosis-heading h3{margin:0;color:#fff;font-size:21px;font-weight:800}
    .clinical-diagnosis-editor .diagnosis-heading p{margin:4px 0 0;color:rgba(255,255,255,.84);font-size:13px}
    .clinical-diagnosis-editor .diagnosis-body{padding:22px 24px 24px}
    .clinical-diagnosis-editor .diagnosis-form-panel{padding:18px;border:1px solid #dce9f1;border-radius:13px;background:#f7fbfd}
    .clinical-diagnosis-editor .diagnosis-grid{display:grid;grid-template-columns:minmax(280px,1fr) minmax(190px,.42fr) auto;gap:14px;align-items:end}
    .clinical-diagnosis-editor .field-label{display:block;margin-bottom:7px;color:#183f5a;font-size:12px;font-weight:800;letter-spacing:.025em}
    .clinical-diagnosis-editor .input-shell{position:relative}
    .clinical-diagnosis-editor .input-shell>i{position:absolute;top:50%;left:13px;z-index:2;color:#5892ae;transform:translateY(-50%)}
    .clinical-diagnosis-editor .form-control{height:42px;border-color:#cbdde8;border-radius:9px;box-shadow:none}
    .clinical-diagnosis-editor .input-shell .form-control{padding-left:38px}
    .clinical-diagnosis-editor .form-control:focus{border-color:#1595b4;box-shadow:0 0 0 3px rgba(21,149,180,.12)}
    .clinical-diagnosis-editor .field-help{display:block;margin-top:6px;color:#6f8799;font-size:11px}
    .clinical-diagnosis-editor .btn-add-diagnosis{min-width:128px;height:42px;border:0;border-radius:9px;background:#087fac;color:#fff;font-weight:800;box-shadow:0 7px 16px rgba(8,127,172,.20)}
    .clinical-diagnosis-editor .btn-add-diagnosis:hover,.clinical-diagnosis-editor .btn-add-diagnosis:focus{background:#056b92;color:#fff}
    .clinical-diagnosis-editor .diagnosis-feedback{display:none;margin:14px 0 0;padding:10px 13px;border-radius:8px;font-size:12px}
    .clinical-diagnosis-editor .diagnosis-feedback.is-error{display:block;color:#9e2633;background:#fff0f2;border:1px solid #f4cbd1}
    .clinical-diagnosis-editor .diagnosis-feedback.is-success{display:block;color:#17683f;background:#ecfaf3;border:1px solid #c7ead8}
    .clinical-diagnosis-editor .diagnosis-list{margin-top:18px;border:1px solid #dce8ef;border-radius:12px;overflow:hidden}
    .clinical-diagnosis-editor .diagnosis-list-title{display:flex;align-items:center;justify-content:space-between;padding:12px 15px;background:#edf7fa;color:#173f59}
    .clinical-diagnosis-editor .diagnosis-list-title strong{font-size:14px}.clinical-diagnosis-editor .diagnosis-list-title span{color:#648297;font-size:11px}
    .clinical-diagnosis-editor table{margin:0}.clinical-diagnosis-editor table thead th{padding:11px 13px;border-bottom:1px solid #dce8ef;background:#f7fafc;color:#476477;font-size:10px;font-weight:800;letter-spacing:.055em;text-transform:uppercase}
    .clinical-diagnosis-editor table tbody td{padding:12px 13px;vertical-align:middle;color:#27485e;font-size:12px}
    .clinical-diagnosis-editor .diagnosis-code{color:#057eae;font-weight:800}
    .clinical-diagnosis-editor .diagnosis-type{display:inline-block;padding:4px 8px;border-radius:20px;background:#e7f5fa;color:#08789f;font-size:10px;font-weight:800}
    .clinical-diagnosis-editor .btn-remove-diagnosis{border-radius:7px}
    .clinical-diagnosis-editor .diagnosis-empty td{padding:24px 12px;text-align:center;color:#7a91a1}.clinical-diagnosis-editor .diagnosis-empty i{display:block;margin-bottom:7px;color:#9fc0d1;font-size:21px}
    .ui-autocomplete{overflow-x:hidden;overflow-y:auto;max-height:240px;z-index:2000!important;padding:5px 0;border:1px solid #cbdde8;border-radius:9px;background:#fff;box-shadow:0 10px 24px rgba(31,72,98,.16);list-style:none}
    @media(max-width:900px){.clinical-diagnosis-editor .diagnosis-grid{grid-template-columns:1fr}.clinical-diagnosis-editor .btn-add-diagnosis{width:100%}}
    @media(max-width:600px){.clinical-diagnosis-editor .diagnosis-heading,.clinical-diagnosis-editor .diagnosis-body{padding:17px}.clinical-diagnosis-editor .diagnosis-heading h3{font-size:18px}.clinical-diagnosis-editor .diagnosis-list{overflow-x:auto}.clinical-diagnosis-editor table{min-width:620px}}
</style>

<section class="clinical-diagnosis-editor" aria-labelledby="diagnosis-entry-title">
    <header class="diagnosis-heading">
        <div class="diagnosis-title-wrap">
            <span class="diagnosis-icon"><i class="fa fa-stethoscope"></i></span>
            <div>
                <div class="diagnosis-kicker">Emergencia · Formulario 008</div>
                <h3 id="diagnosis-entry-title">{{trans('pasos.DiagnosticosPresuntivos')}}</h3>
                <p>Registra el diagnóstico CIE-10 que sustenta la atención de este episodio.</p>
            </div>
        </div>
    </header>
    <div class="diagnosis-body">
        <form id="form_paso11" autocomplete="off">
            {{csrf_field()}}
            <input type="hidden" name="id_solicitud" id="id_solicitud" value="{{$id_sol}}">
            <input type="hidden" name="codigo" id="codigo">
            <div class="diagnosis-form-panel">
                <div class="diagnosis-grid">
                    <div>
                        <label class="field-label" for="cie10">{{trans('paso2.Diagnostico')}} CIE-10</label>
                        <div class="input-shell"><i class="fa fa-search"></i><input type="text" name="cie10" id="cie10" class="form-control" placeholder="Escriba el código o nombre del diagnóstico" aria-describedby="cie10-help"></div>
                        <span class="field-help" id="cie10-help">Escriba al menos dos caracteres y seleccione una opción del catálogo.</span>
                    </div>
                    <div>
                        <label class="field-label" for="pre_def">Clasificación</label>
                        <select name="pre_def" id="pre_def" class="form-control"><option value="PRESUNTIVO" selected>{{trans('paso2.PRESUNTIVO')}}</option><option value="DEFINITIVO">{{trans('paso2.DEFINITIVO')}}</option></select>
                    </div>
                    <div><button name="agregar_cie" id="agregar_cie" type="button" class="btn btn-add-diagnosis"><i class="fa fa-plus-circle"></i> {{trans('paso2.Agregar')}}</button></div>
                </div>
                <div id="diagnosis-feedback" class="diagnosis-feedback" role="status" aria-live="polite"></div>
            </div>
        </form>
        <div class="diagnosis-list">
            <div class="diagnosis-list-title"><strong><i class="fa fa-list-ul"></i> Diagnósticos registrados</strong><span>Vinculados al episodio actual</span></div>
            <div class="table-responsive"><table class="table table-hover" aria-label="Diagnósticos de ingreso"><thead><tr><th>Código</th><th>Descripción</th><th>Tipo</th><th class="text-center">Acciones</th></tr></thead><tbody id="tdiagnostico"><tr class="diagnosis-empty"><td colspan="4"><i class="fa fa-clipboard"></i>No hay diagnósticos de ingreso registrados.</td></tr></tbody></table></div>
        </div>
    </div>
</section>

<script src="{{asset('/js/jquery-ui.js')}}"></script>
<script>
(function(){
    var $form=$('#form_paso11'),$feedback=$('#diagnosis-feedback'),$button=$('#agregar_cie');
    function feedback(message,type){$feedback.removeClass('is-error is-success').addClass(type==='success'?'is-success':'is-error').text(message)}
    function clearFeedback(){$feedback.removeClass('is-error is-success').text('')}
    function removeEmptyState(){$('#tdiagnostico .diagnosis-empty').remove()}
    function emptyState(){if($('#tdiagnostico tr').length===0)$('#tdiagnostico').append('<tr class="diagnosis-empty"><td colspan="4"><i class="fa fa-clipboard"></i>No hay diagnósticos de ingreso registrados.</td></tr>')}
    function addRow(value,idHcproc){
        removeEmptyState();
        var row=document.createElement('tr');row.id='tdiag'+value.id;
        var code=document.createElement('td'),codeText=document.createElement('span');codeText.className='diagnosis-code';codeText.textContent=value.cie10||'';code.appendChild(codeText);
        var description=document.createElement('td');description.textContent=value.descripcion||'';
        var type=document.createElement('td'),badge=document.createElement('span');badge.className='diagnosis-type';badge.textContent=value.pre_def||'';type.appendChild(badge);
        var actions=document.createElement('td'),remove=document.createElement('button');actions.className='text-center';remove.type='button';remove.className='btn btn-xs btn-danger btn-remove-diagnosis';remove.title='Eliminar diagnóstico';remove.innerHTML='<i class="fa fa-trash"></i>';remove.onclick=function(){eliminar_ing(value.id,idHcproc)};actions.appendChild(remove);
        row.appendChild(code);row.appendChild(description);row.appendChild(type);row.appendChild(actions);document.getElementById('tdiagnostico').appendChild(row);
    }
    $('#cie10').autocomplete({source:function(request,response){$.ajax({url:"{{route('epicrisis.cie10_nombre')}}",headers:{'X-CSRF-TOKEN':$form.find('input[name=_token]').val()},data:{term:request.term},dataType:'json',type:'post',success:response,error:function(){feedback('No se pudo consultar el catálogo CIE-10.','error')}})},minLength:2,select:function(event,ui){$('#codigo').val(ui.item&&ui.item.id?ui.item.id:'');clearFeedback()}});
    $('#cie10').on('input',function(){$('#codigo').val('')});
    $('#cie10').on('change',function(){$.ajax({type:'post',url:"{{route('epicrisis.cie10_nombre2')}}",headers:{'X-CSRF-TOKEN':$form.find('input[name=_token]').val()},dataType:'json',data:{cie10:$('#cie10').val()},success:function(data){$('#codigo').val(data!=='0'&&data?data.id:'')}})});
    $button.on('click',function(){clearFeedback();if(!$.trim($('#cie10').val()))return feedback("{{trans('paso2.SeleccioneCIE10')}}",'error');if(!$('#codigo').val())return feedback('Seleccione una opción válida del catálogo CIE-10.','error');guardar_cie10_consulta()});
    window.guardar_cie10_consulta=function(){$button.prop('disabled',true).html('<i class="fa fa-spinner fa-spin"></i> Guardando...');$.ajax({type:'post',url:"{{route('hospital.agregar_cie10')}}",headers:{'X-CSRF-TOKEN':$form.find('input[name=_token]').val()},dataType:'json',data:$form.serialize(),success:function(data){if(!data||data.estado==='err')return feedback(data&&data.msn?data.msn:'No se pudo registrar el diagnóstico.','error');addRow(data,data.id_hcproc);$('#codigo,#cie10').val('');feedback('Diagnóstico registrado correctamente.','success')},error:function(){feedback('No se pudo guardar el diagnóstico. Revise los datos e inténtelo nuevamente.','error')},complete:function(){$button.prop('disabled',false).html('<i class="fa fa-plus-circle"></i> {{trans('paso2.Agregar')}}')}})};
    window.cargar_tabla_cie=function(idHcproc){$.ajax({url:"{{route('hospital.cargar_tabla_cie_hos')}}",dataType:'json',type:'post',headers:{'X-CSRF-TOKEN':$form.find('input[name=_token]').val()},data:{id_hcproc:idHcproc},success:function(data){$('#tdiagnostico').empty();$.each(data||[],function(index,value){addRow(value,idHcproc)});emptyState()},error:function(){feedback('No se pudo cargar el listado de diagnósticos.','error')}})};
    window.eliminar_ing=function(id,idHcproc){if(!window.confirm('¿Confirma que desea eliminar este diagnóstico del episodio?'))return;$.ajax({type:'delete',url:"{{url('cie10/eliminar')}}/"+id,data:{_token:'{{csrf_token()}}'},dataType:'json',success:function(){$('#tdiag'+id).remove();emptyState();feedback('Diagnóstico eliminado.','success')},error:function(){feedback('No se pudo eliminar el diagnóstico.','error')}})};
    cargar_tabla_cie({{$solicitud->id_hcproc}});
})();
</script>
