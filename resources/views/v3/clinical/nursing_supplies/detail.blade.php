@include('v3.clinical.operations._styles')
<div class="co-page ns-page">
<section class="co-hero"><div class="co-hero__main"><div class="co-hero__icon"><i class="fas fa-clipboard-check"></i></div><div><div class="co-eyebrow">ENFERMERÍA · V3</div><h1>Ingreso de insumos y equipos usados</h1><p>{{trim($agenda->patient_lastname1.' '.$agenda->patient_lastname2.' '.$agenda->patient_name1.' '.$agenda->patient_name2)}} · {{$agenda->id_paciente}}</p></div></div><a class="btn btn-light" href="{{route('v3.clinical-operations.nursing')}}"><i class="fas fa-arrow-left mr-1"></i> Regresar</a></section>
<section class="co-card"><header class="co-head"><div class="co-heading"><i class="fas fa-user-injured"></i><div><h2>Datos de la atención</h2><p>Información clínica utilizada para el registro y la trazabilidad.</p></div></div></header><div class="supply-summary"><div><span>Fecha y hora</span><strong>{{date('d/m/Y H:i',strtotime($agenda->fechaini))}}</strong></div><div><span>Seguro</span><strong>{{$agenda->insurance_name?:'Sin seguro'}}</strong></div><div><span>Doctor</span><strong>{{trim($agenda->doctor_lastname.' '.$agenda->doctor_name)?:'Sin asignar'}}</strong></div><div><span>Sala</span><strong>{{$agenda->room_name?:'Sin sala'}}</strong></div></div></section>
<div id="nursingSupplyWorkspace">@include('v3.clinical.nursing_supplies._workspace')</div>
</div>
<style>
.supply-summary{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:12px;padding:18px 20px}
.supply-summary div{min-height:70px;padding:13px 15px;border:1px solid #d6e5ec;border-radius:12px;background:linear-gradient(180deg,#fff,#f7fbfc)}
.supply-summary span{display:block;color:#728b9c;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase}
.supply-summary strong{display:block;margin-top:5px;color:#123f5c;line-height:1.3}
.ns-command-card{overflow:hidden}
.ns-workflow-badges{display:flex;align-items:center;justify-content:flex-end;gap:7px;flex-wrap:wrap}
.ns-status{display:inline-flex;align-items:center;gap:6px;padding:7px 10px;border-radius:999px;font-size:11px;font-weight:800;white-space:nowrap}
.ns-status--success{background:#e3f8ec;color:#127345}.ns-status--open{background:#e5f4fb;color:#086d96}.ns-status--locked{background:#fff0dc;color:#9a5900}
.ns-command-grid{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(260px,.65fr);gap:14px;padding:16px 20px}
.ns-warehouse-form{padding:15px;border:1px solid #d5e5ed;border-radius:12px;background:#f8fbfc}
.ns-warehouse-form label{display:block;margin:0 0 8px;color:#214c66;font-size:11px;font-weight:800;letter-spacing:.03em;text-transform:uppercase}
.ns-warehouse-form small{display:block;margin-top:7px;color:#678193}
.ns-inline-control,.ns-scan-bar{display:grid;grid-template-columns:minmax(180px,1fr) auto;gap:9px;align-items:center}
.ns-help{display:flex;align-items:center;gap:12px;padding:15px;border:1px solid #cce6ef;border-radius:12px;background:#edf9fc}
.ns-help__icon,.ns-section-title__icon{display:grid;place-items:center;flex:0 0 42px;width:42px;height:42px;border-radius:11px;background:#d7f2f8;color:#0789ad;font-size:18px}
.ns-help strong{display:block;color:#124968}.ns-help p{margin:3px 0 0;color:#607d8e;font-size:12px;line-height:1.45}
.ns-two-columns{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(320px,.75fr);gap:14px}
.ns-two-columns>.co-card{display:flex;flex-direction:column;min-height:100%}
.ns-equipment-card .co-table-wrap{flex:1}
.ns-scan-bar{padding:14px 18px;border-top:1px solid #dfeaf0;background:#f8fbfc}
.ns-scan-bar--supply{grid-template-columns:minmax(240px,1fr) auto}
.ns-scan-bar small{grid-column:1/-1;color:#a26500;font-weight:600}
.ns-observation{display:flex;flex:1;flex-direction:column;gap:12px;padding:16px 20px}
.ns-observation textarea{flex:1;min-height:154px;resize:vertical}
.ns-observation__footer{display:flex;align-items:center;justify-content:space-between;gap:12px}.ns-observation__footer small{color:#718a99}
.ns-section-title{display:flex;align-items:center;justify-content:space-between;gap:12px;margin:20px 2px 10px;padding:0 4px}
.ns-section-title>div{display:flex;align-items:center;gap:11px}.ns-section-title h2{margin:0;color:#123f5c;font-size:18px;font-weight:800}.ns-section-title p{margin:2px 0 0;color:#6c8595;font-size:12px}
.ns-procedure{overflow:hidden;border-top:3px solid #0b91ad}
.ns-procedure--locked{border-top-color:#ee9a22}.ns-procedure__head{align-items:flex-start}
.ns-head-actions{display:flex;align-items:center;justify-content:flex-end;gap:8px;flex-wrap:wrap}
.ns-plan-state{display:flex;align-items:center;gap:9px;min-width:158px;padding:7px 10px;border-radius:10px}
.ns-plan-state>i{font-size:16px}.ns-plan-state span,.ns-plan-state strong,.ns-plan-state small{display:block}.ns-plan-state strong{font-size:11px;text-transform:uppercase}.ns-plan-state small{margin-top:1px;font-size:10px}
.ns-plan-state--open{background:#e6f7ed;color:#146e44}.ns-plan-state--locked{background:#fff0dc;color:#934f00}
.ns-inline-alert{display:inline-flex;margin-left:6px;padding:2px 7px;border-radius:999px;background:#ffe7e7;color:#b52626;font-weight:700}
.ns-category{border-top:1px solid #e0ebf0}.ns-category__title{display:flex;justify-content:space-between;align-items:center;padding:9px 16px;background:#eef7fa;color:#135372;font-size:11px;font-weight:800;letter-spacing:.03em;text-transform:uppercase}
.ns-category__title small{font-weight:600;color:#648294;text-transform:none}
.ns-table{margin:0}.ns-table td,.ns-table th{vertical-align:middle!important}.ns-table tbody tr:hover{background:#f5fbfd}
.ns-quantity{display:inline-grid;place-items:center;min-width:28px;height:25px;padding:0 7px;border-radius:999px;background:#e5f4fb;color:#075f82;font-weight:800}
.ns-row-actions{display:flex;justify-content:flex-end;gap:6px;align-items:center}.ns-row-actions form{display:flex;gap:5px;margin:0;align-items:center}.ns-row-actions select{min-width:128px}
.ns-empty{padding:27px}.ns-empty-state{display:flex;align-items:center;justify-content:center;gap:12px}.ns-empty-state>i{font-size:24px;color:#d68a13}.ns-empty-state strong,.ns-empty-state span{display:block}.ns-empty-state span{margin-top:2px;color:#6c8595}
.ns-readonly-note{padding:13px 18px;border-top:1px solid #f0d5d5;background:#fff6f6;color:#a63b3b;font-weight:700}
.js-ns-busy{opacity:.62;pointer-events:none}
@media(max-width:1050px){.supply-summary{grid-template-columns:repeat(2,minmax(0,1fr))}.ns-command-grid,.ns-two-columns{grid-template-columns:1fr}.ns-head-actions{justify-content:flex-start}.ns-table{min-width:760px}}
@media(max-width:700px){.supply-summary{grid-template-columns:1fr}.ns-command-grid{padding:12px}.ns-workflow-badges{justify-content:flex-start}.ns-inline-control,.ns-scan-bar,.ns-scan-bar--supply{grid-template-columns:1fr}.ns-inline-control button,.ns-scan-bar button{width:100%}.ns-head-actions .btn{flex:1 1 180px}.ns-plan-state{width:100%}.ns-section-title{align-items:flex-start}.ns-section-title>.co-count{display:none}}
</style>
<script>
(function(){
    var workspace=document.getElementById('nursingSupplyWorkspace');
    if(!workspace)return;
    function notice(type,message){if(window.Swal&&Swal.fire){if(type==='success')return Swal.fire({toast:true,position:'top-end',icon:type,title:message,showConfirmButton:false,timer:1400,timerProgressBar:true});return Swal.fire({icon:type,title:'No se pudo completar',text:message,confirmButtonColor:'#0789bd'});}window.alert(message);return Promise.resolve();}
    function focusField(key){if(!key)return;var target=key==='equipment'?document.getElementById('nsEquipmentCode'):document.getElementById('nsSupply'+String(key).replace('supply-',''));if(target&&!target.disabled){target.focus();target.select();}}
    async function submitForm(form){
        if(form.classList.contains('js-ns-busy'))return;
        if(form.classList.contains('js-ns-confirm')&&!window.confirm(form.getAttribute('data-confirm')||'¿Continuar con esta operación?'))return;
        var focus=form.getAttribute('data-focus')||'';var button=form.querySelector('button[type="submit"]');
        form.classList.add('js-ns-busy');if(button)button.disabled=true;
        try{
            var response=await fetch(form.action,{method:(form.method||'POST').toUpperCase(),body:new FormData(form),credentials:'same-origin',headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
            var data;try{data=await response.json();}catch(parseError){throw new Error(response.status===419?'La sesión venció. Recargue la página e ingrese nuevamente.':'El servidor devolvió una respuesta no válida.');}
            if(!response.ok||data.status!=='ok')throw new Error(data.message||'La operación no pudo completarse.');
            if(data.html)workspace.innerHTML=data.html;
            notice('success',data.message||'Operación completada.');focusField(focus);
        }catch(error){await notice('error',error.message||'Ocurrió un error inesperado.');}
        finally{if(document.body.contains(form)){form.classList.remove('js-ns-busy');if(button)button.disabled=false;}}
    }
    workspace.addEventListener('submit',function(event){var form=event.target.closest('.js-ns-async');if(!form)return;event.preventDefault();submitForm(form);});
    workspace.addEventListener('change',function(event){var form=event.target.closest('form[data-auto-submit="1"]');if(form)submitForm(form);});
})();
</script>
