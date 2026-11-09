<div class="clinical-doc-block" id="reportes-episodio" data-document-search-section>
    <div class="clinical-doc-block__heading">
        <h4 class="clinical-doc-section-title"><i class="fa fa-file-pdf-o"></i>Reportes del episodio</h4>
        <span class="clinical-doc-badge">{{ count($reportCatalog) }}</span>
    </div>
    <p class="clinical-form-help">PDF clínicos vinculados a registros de este paciente y episodio.</p>
    @if(count($reportCatalog))
        <div class="clinical-form-list clinical-report-list" aria-label="Reportes PDF del episodio">
            @php $previousGroup = null; @endphp
            @foreach($reportCatalog as $report)
                @if($previousGroup !== $report['group'])
                    <div class="clinical-report-group" data-document-search-group>{{ $report['group'] }}</div>
                    @php $previousGroup = $report['group']; @endphp
                @endif
                <div class="clinical-form-item" data-document-search="{{ mb_strtolower($report['code'].' '.$report['name'].' '.$report['group'].' '.($report['label'] ?? '').' '.($report['status'] ?? ''), 'UTF-8') }}">
                    <div class="clinical-form-item__top">
                        <span class="clinical-form-code">{{ $report['code'] }}</span>
                        <div class="clinical-form-item__name">{{ $report['name'] }}</div>
                        @if($report['label'])<span class="clinical-form-status is-available">{{ $report['label'] }}</span>@endif
                        @unless($report['official'])<span class="clinical-form-status is-unavailable">{{ $report['status'] }}</span>@endunless
                    </div>
                    <div class="clinical-form-item__bottom">
                        <button type="button" class="btn btn-primary btn-xs js-document-preview"
                            data-preview-url="{{ $report['url'] }}"
                            data-preview-title="{{ $report['code'] }} · {{ $report['name'] }} · PDF{{ $report['label'] ? ' · '.$report['label'] : '' }}"
                            data-form-pdf="1" data-prefetch-pdf="1" data-form-code="{{ $report['code'] }}" @if($report['official']) data-official-pdf="1" @endif>
                            <i class="fa fa-file-pdf-o"></i> {{ $report['preview_label'] }}
                        </button>
                        <a class="btn btn-default btn-xs" href="{{ $report['url'] }}" target="_blank" rel="noopener">Abrir PDF</a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="clinical-doc-empty">Aún no hay reportes PDF vinculados a este episodio.</div>
    @endif
</div>
