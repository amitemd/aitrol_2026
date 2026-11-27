<a href="{{ $href }}" class="dropdown-item d-flex gap-2 align-items-center"
    @if ($modal ?? true) data-ajax-open="{{ $titulo ?? $etiqueta }}" @endif
    @if (isset($workspace)) data-workspace-link="{{ $workspace }}" data-workspace-label="{{ $workspaceLabel ?? $etiqueta }}" @endif>
    <i class="bi {{ $icono ?? 'bi-pencil' }}" aria-hidden="true"></i>{{ __($etiqueta) }}
</a>
