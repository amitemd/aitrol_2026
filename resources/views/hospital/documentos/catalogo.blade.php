<div class="clinical-doc-block" id="formularios-episodio" data-document-search-section>
    <div class="clinical-doc-block__heading">
        <h4 class="clinical-doc-section-title"><i class="fa fa-list-alt"></i>Formularios del episodio</h4>
        <span class="clinical-doc-badge">{{ count($formCatalog) }}</span>
    </div>
    <p class="clinical-form-help">Catálogo HCU del Ecuador y formularios hospitalarios complementarios. Use «Completar» para llenar un borrador y «Ver formato» para consultar su presentación. Complete los formularios «según indicación» solo cuando correspondan.</p>
    <div class="clinical-form-list" id="clinical-form-catalog" aria-label="Formularios HCU y hospitalarios del episodio" data-search-description="Buscar por código, nombre o especialidad">
        @php $currentFormGroup = null; @endphp
        @foreach($formCatalog as $form)
            @if(($form['group'] ?? 'Otros') !== $currentFormGroup)
                @php $currentFormGroup = $form['group'] ?? 'Otros'; @endphp
                <div class="clinical-form-group" data-document-search-group>{{ $currentFormGroup }}</div>
            @endif
            <div class="clinical-form-item" data-document-search="{{ mb_strtolower($form['code'].' '.$form['name'].' '.$form['context'].' '.($form['group'] ?? '').' '.($form['edition'] ?? '').' '.($form['status'] ?? '').' '.implode(' ', $form['aliases'] ?? []), 'UTF-8') }}">
                <div class="clinical-form-item__top">
                    <span class="clinical-form-code">{{ $form['code'] }}</span>
                    <div class="clinical-form-item__name">{{ $form['name'] }}</div>
                    <span class="clinical-form-edition">{{ $form['edition'] ?? 'HCU' }}</span>
                    <span class="clinical-form-status {{ $form['preview_url'] || $form['draft_preview_url'] ? 'is-available' : 'is-unavailable' }}">{{ $form['status'] }}</span>
                </div>
                <div class="clinical-form-item__bottom">
                    <span class="clinical-form-item__context">{{ $form['context'] }}@if($form['saved_count']) · {{ $form['saved_count'] }} borrador(es)@endif</span>
                    @if($form['preview_url'])
                        <button type="button" class="btn btn-primary btn-xs js-document-preview" data-preview-url="{{ $form['preview_url'] }}" data-preview-title="{{ $form['code'] }} · {{ $form['name'] }} · {{ $form['pdf_links'][0]['status'] ?? 'PDF' }}" data-form-pdf="1" data-form-code="{{ $form['code'] }}" @if($form['preview_is_official']) data-official-pdf="1" @endif><i class="fa fa-file-pdf-o"></i> {{ $form['preview_label'] }}</button>
                        <a class="btn btn-default btn-xs" href="{{ $form['preview_url'] }}" target="_blank" rel="noopener">Abrir PDF</a>
                        @foreach(array_slice($form['pdf_links'], 1) as $pdf)
                            <button type="button" class="btn btn-default btn-xs js-document-preview" data-preview-url="{{ $pdf['url'] }}" data-preview-title="{{ $form['code'] }} · {{ $form['name'] }} · PDF{{ $pdf['label'] ? ' · '.$pdf['label'] : '' }}" data-form-pdf="1" data-form-code="{{ $form['code'] }}" title="{{ $pdf['label'] ? 'Procedimiento '.$pdf['label'] : 'Ver PDF del episodio' }}"><i class="fa fa-file-pdf-o"></i> {{ $pdf['label'] ? 'PDF '.$pdf['label'] : 'Ver PDF' }}</button>
                        @endforeach
                    @endif
                    @if($form['draft_preview_url'])
                        <button type="button" class="btn btn-primary btn-xs js-document-preview" data-preview-url="{{ $form['draft_preview_url'] }}" data-preview-title="{{ $form['code'] }} · {{ $form['name'] }} · Borrador" data-form-pdf="1" data-form-code="{{ $form['code'] }}"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ver borrador</button>
                    @elseif(!$form['preview_url'] && $form['template_preview_url'])
                        <button type="button" class="btn btn-default btn-xs js-document-preview" data-preview-url="{{ $form['template_preview_url'] }}" data-preview-title="{{ $form['code'] }} · {{ $form['name'] }} · Formato sin llenar" data-form-template="1" data-form-code="{{ $form['code'] }}"><i class="fa fa-file-o" aria-hidden="true"></i> Ver formato</button>
                    @endif
                    @if($form['url'])
                        @if(!empty($form['embedded_url']))
                            <a class="btn btn-default btn-xs js-document-editor" href="{{ $form['url'] }}" target="_blank" rel="noopener" data-preview-url="{{ $form['embedded_url'] }}" data-preview-title="{{ $form['code'] }} · {{ $form['name'] }} · {{ $form['action'] }}"><i class="fa fa-{{ $form['can_complete'] ? 'pencil' : 'eye' }}" aria-hidden="true"></i> {{ $form['action'] }}</a>
                        @else
                            <a class="btn btn-default btn-xs" href="{{ $form['url'] }}" target="_blank" rel="noopener"><i class="fa fa-external-link" aria-hidden="true"></i> {{ $form['action'] }}</a>
                        @endif
                    @endif
                    @if($form['workflow_url'])<a class="btn btn-default btn-xs" href="{{ $form['workflow_url'] }}">Flujo quirúrgico</a>@endif
                </div>
                @if($form['hint'])<p class="clinical-form-item__hint">{{ $form['hint'] }}</p>@endif
            </div>
        @endforeach
    </div>
</div>
