<div class="text-left" id="emar-panel-{{$detalle->id}}" style="min-width: 270px;">
    <button type="button" class="btn btn-info btn-sm" onclick="alternarEmar('{{$detalle->id}}')">
        <i class="fa fa-medkit"></i> Administrar
    </button>
    <div id="emar-form-{{$detalle->id}}" class="mt-2 p-2 border rounded" style="display:none; background:#f7fbfd;">
        <div class="form-group mb-1">
            <label class="mb-0">Dosis aplicada</label>
            <input id="emar-dosis-{{$detalle->id}}" class="form-control form-control-sm" value="{{$detalle->dosis}}">
        </div>
        <div class="form-group mb-1">
            <label class="mb-0">Vía</label>
            <select id="emar-via-{{$detalle->id}}" class="form-control form-control-sm">
                <option value="">Seleccione</option>
                <option value="ORAL">Oral</option>
                <option value="INTRAVENOUS">Intravenosa</option>
                <option value="INTRAMUSCULAR">Intramuscular</option>
                <option value="SUBCUTANEOUS">Subcutánea</option>
                <option value="TOPICAL">Tópica</option>
                <option value="INHALATION">Inhalatoria</option>
                <option value="OTHER">Otra</option>
            </select>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6 mb-1">
                <label class="mb-0">Programada</label>
                <input id="emar-programada-{{$detalle->id}}" type="datetime-local" class="form-control form-control-sm" value="{{date('Y-m-d\TH:i')}}">
            </div>
            <div class="form-group col-md-6 mb-1">
                <label class="mb-0">Hora real</label>
                <input id="emar-real-{{$detalle->id}}" type="datetime-local" class="form-control form-control-sm" value="{{date('Y-m-d\TH:i')}}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-6 mb-1">
                <label class="mb-0">Resultado</label>
                <select id="emar-estado-{{$detalle->id}}" class="form-control form-control-sm" onchange="cambiarEstadoEmarClinico('{{$detalle->id}}')">
                    <option value="ADMINISTERED">Administrado</option>
                    <option value="OMITTED">Omitido</option>
                </select>
            </div>
            <div class="form-group col-md-6 mb-1">
                <label class="mb-0">Serie</label>
                <input id="emar-serie-{{$detalle->id}}" class="form-control form-control-sm">
            </div>
        </div>
        <div id="emar-omision-grupo-{{$detalle->id}}" class="form-group mb-1" style="display:none;">
            <label class="mb-0">Motivo de omisión</label>
            <textarea id="emar-omision-{{$detalle->id}}" class="form-control form-control-sm" rows="2"></textarea>
        </div>
        <div class="form-group mb-1">
            <label class="mb-0">Resultado u observación</label>
            <textarea id="emar-resultado-{{$detalle->id}}" class="form-control form-control-sm" rows="2"></textarea>
        </div>
        <label class="d-block mb-1">
            <input id="emar-paciente-{{$detalle->id}}" type="checkbox"> Identidad del paciente verificada
        </label>
        <label class="d-block mb-2">
            <input id="emar-medicamento-{{$detalle->id}}" type="checkbox"> Medicamento comparado con la orden
        </label>
        <button id="emar-guardar-{{$detalle->id}}" type="button" class="btn btn-primary btn-sm" onclick="registrarEmarClinico('{{$detalle->id}}')">
            <i class="fa fa-shield"></i> Confirmar administración
        </button>
    </div>
</div>
