<section class="adr-card">
    @if($insurances->isEmpty())
        <div class="adr-card-body"><div class="alert alert-warning">No hay seguros registrados para configurar documentos requeridos.</div></div>
    @else
        <div class="adr-card-head">
            <div class="adr-card-title">
                <i class="fas fa-clipboard-check"></i>
                <div>
                    <h2>Configuración de documentos</h2>
                    <p>Activa los requisitos que deberá completar Recepción.</p>
                </div>
            </div>
            <span class="adr-count">{{ $requirements->count() }} de {{ $documents->count() }} activos</span>
        </div>
        <div class="adr-card-body">
            <form id="adrFilter" method="get" class="adr-controls">
                <div class="adr-field">
                    <label for="adrInsurance"><i class="fas fa-shield-alt"></i> Seguro</label>
                    <select id="adrInsurance" class="form-control" name="seguro">
                        @foreach($insurances as $item)
                            <option value="{{ $item->id }}" {{ $insuranceId === (int)$item->id ? 'selected' : '' }}>{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="adr-scope" aria-label="Tipo de atención">
                    @foreach(array(0 => 'Consulta', 1 => 'Procedimiento') as $key => $label)
                        <a class="btn btn-outline-primary js-adr-scope {{ $scope === $key ? 'active' : '' }}"
                            href="{{ route('v3.agenda-document-requirements.index', array_merge(array('seguro' => $insuranceId, 'proc_consul' => $key), $navigationQuery)) }}">
                            <i class="fas {{ $key === 0 ? 'fa-user-md' : 'fa-procedures' }}"></i> {{ $label }}
                        </a>
                    @endforeach
                </div>
                <input type="hidden" name="proc_consul" value="{{ $scope }}">
                @foreach($navigationQuery as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
            </form>
            <div class="adr-note">
                <span><i class="fas fa-info-circle"></i> La configuración corresponde al seguro seleccionado y se guarda con cada cambio.</span>
                <span class="adr-save-status" role="status" aria-live="polite">
                    <i class="fas fa-cloud"></i> Guardado automático
                </span>
            </div>
            <form id="adrRequirementsForm" method="post" action="{{ route('v3.agenda-document-requirements.update') }}">
                {{ csrf_field() }}
                <input type="hidden" name="seguro" value="{{ $insuranceId }}">
                <input type="hidden" name="proc_consul" value="{{ $scope }}">
                @foreach($navigationQuery as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <div class="table-responsive adr-table-wrap">
                    <table class="table adr-table">
                        <thead>
                            <tr>
                                <th style="width:70px">Usar</th>
                                <th>Documento</th>
                                <th class="adr-required-column" style="width:130px">Obligatorio (*)</th>
                                <th style="width:160px">Tipo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($documents as $document)
                                @php($requirement = $requirements->get($document->id))
                                <tr class="{{ $requirement ? 'is-selected' : '' }}">
                                    <td class="text-center">
                                        <label class="adr-check" title="Usar {{ $document->nombre }}">
                                            <input class="adr-checkbox js-adr-use" type="checkbox"
                                                name="documentos[{{ $document->id }}][id]" value="{{ $document->id }}"
                                                aria-label="Usar {{ $document->nombre }}" {{ $requirement ? 'checked' : '' }}>
                                            <span aria-hidden="true"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <div class="adr-document">
                                            <span class="adr-document-icon"><i class="far fa-file-alt"></i></span>
                                            <div>
                                                <b>{{ $document->nombre }}</b>
                                                @if($document->tipo_documento)<br><span class="adr-code">{{ $document->tipo_documento }}</span>@endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center adr-required-column">
                                        <label class="adr-check" title="Marcar {{ $document->nombre }} como obligatorio">
                                            <input class="adr-checkbox js-adr-required" type="checkbox"
                                                name="documentos[{{ $document->id }}][obligatorio]" value="1"
                                                aria-label="{{ $document->nombre }} obligatorio"
                                                {{ $requirement && $requirement->obligatorio ? 'checked' : '' }}
                                                {{ !$requirement ? 'disabled' : '' }}>
                                            <span aria-hidden="true"></span>
                                        </label>
                                    </td>
                                    <td>
                                        <span class="adr-type">
                                            <i class="fas {{ $scope === 0 ? 'fa-user-md' : 'fa-procedures' }}"></i>
                                            {{ $scope === 0 ? 'Consulta' : ($scope === 1 ? 'Procedimiento' : 'Ambos') }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">No hay documentos disponibles para este tipo de atención.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    @endif
</section>
