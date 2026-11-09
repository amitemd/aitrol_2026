<section class="clinical-section">
    <h6 class="clinical-section-title"><i class="fa fa-shield"></i> Seguridad antes de iniciar</h6>
    <p class="clinical-help">Se exige identidad, indicación, consentimiento vigente y checklist. Una emergencia debe quedar justificada.</p>
    <div class="row">
        <div class="form-group col-md-6">
            <label>Indicación clínica <span class="required-mark">*</span></label>
            <textarea class="form-control" name="procedure_indication" rows="2" required minlength="5"></textarea>
        </div>
        <div class="form-group col-md-3">
            <label>Consentimiento</label>
            <select class="form-control" name="consent_template_id">
                <option value="">Seleccione...</option>
                @foreach($consentFormats as $format)<option value="{{ $format->id }}">{{ $format->nombre }}</option>@endforeach
            </select>
        </div>
        <div class="form-group col-md-3"><label>Versión</label><input class="form-control" name="consent_version" placeholder="Ej. 1.0"></div>
        <div class="form-group col-md-3"><label>Fecha y hora de firma</label><input type="datetime-local" class="form-control" name="consent_signed_at"></div>
        <div class="form-group col-md-3"><label>Firmado por</label><input class="form-control" name="consent_signed_by"></div>
        <div class="form-group col-md-3">
            <label>Calidad del firmante</label>
            <select class="form-control" name="consent_signer_role"><option value="PATIENT">Paciente</option><option value="REPRESENTATIVE">Representante</option></select>
        </div>
        <div class="form-group col-md-3"><label>Relación del representante</label><input class="form-control" name="consent_representative_relationship"></div>
        <div class="form-group col-md-4"><label><input type="checkbox" name="procedure_identity_verified" value="1" required> Identidad verificada</label></div>
        <div class="form-group col-md-4"><label><input type="checkbox" name="procedure_checklist_complete" value="1"> Checklist completo</label></div>
        <div class="form-group col-md-4"><label><input type="checkbox" name="procedure_emergency_exception" value="1"> Excepción por emergencia</label></div>
        <div class="form-group col-md-12"><label>Justificación de emergencia</label><textarea class="form-control" name="procedure_emergency_reason" rows="2"></textarea></div>
    </div>
</section>
