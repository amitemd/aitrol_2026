<div class="col-12 hc4-procedure-safety" style="border:1px solid #b9d8e8;background:#f4fbff;padding:14px;margin:15px 0;">
    <h4><i class="fa fa-shield"></i> Seguridad antes de iniciar el procedimiento</h4>
    <p class="text-muted">El primer guardado exige identidad, indicación, consentimiento vigente y checklist. Toda excepción de emergencia queda auditada.</p>
    <div class="row">
        <div class="form-group col-md-6"><label>Indicación clínica</label><textarea class="form-control" name="procedure_indication" rows="2"></textarea></div>
        <div class="form-group col-md-3"><label>Consentimiento</label><select class="form-control hc4-endoscopy-single-select" name="consent_template_id" data-placeholder="Buscar consentimiento"><option value="">Seleccione...</option>@foreach($consentFormats as $format)<option value="{{$format->id}}">{{$format->nombre}}</option>@endforeach</select></div>
        <div class="form-group col-md-3"><label>Versión</label><input class="form-control" name="consent_version" placeholder="Ej. 1.0"></div>
        <div class="form-group col-md-3"><label>Fecha y hora de firma</label><input type="datetime-local" class="form-control" name="consent_signed_at"></div>
        <div class="form-group col-md-3"><label>Firmado por</label><input class="form-control" name="consent_signed_by"></div>
        <div class="form-group col-md-3"><label>Calidad del firmante</label><select class="form-control hc4-endoscopy-single-select" name="consent_signer_role" data-placeholder="Buscar calidad"><option value="PATIENT">Paciente</option><option value="REPRESENTATIVE">Representante</option></select></div>
        <div class="form-group col-md-3"><label>Relación del representante</label><input class="form-control" name="consent_representative_relationship"></div>
        <div class="form-group col-md-4"><label class="hc4-safety-check-label"><input class="hc4-safety-checkbox" type="checkbox" name="procedure_identity_verified" value="1"> Identidad verificada</label></div>
        <div class="form-group col-md-4"><label class="hc4-safety-check-label"><input class="hc4-safety-checkbox" type="checkbox" name="procedure_checklist_complete" value="1"> Checklist completo</label></div>
        <div class="form-group col-md-4"><label class="hc4-safety-check-label"><input class="hc4-safety-checkbox" type="checkbox" name="procedure_emergency_exception" value="1"> Excepción por emergencia</label></div>
        <div class="form-group col-md-12"><label>Justificación de emergencia</label><textarea class="form-control" name="procedure_emergency_reason" rows="2"></textarea></div>
    </div>
</div>
