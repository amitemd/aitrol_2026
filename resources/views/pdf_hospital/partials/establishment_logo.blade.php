@php
    $establishmentLogoPath = app(\Sis_medico\Services\CompanyPdfLogoService::class)->dataUri($empresa ?? null);
    $establishmentName = !empty($empresa) && !empty($empresa->nombrecomercial)
        ? $empresa->nombrecomercial
        : 'ESTABLECIMIENTO DE SALUD';
    $establishmentLogoRowHeight = isset($logoRowHeight) ? max(24, (int) $logoRowHeight) : 72;
    $establishmentLogoHeight = isset($logoHeight) ? max(18, (int) $logoHeight) : 62;

@endphp

@if(!empty($imageOnly))
    @if(!is_null($establishmentLogoPath))
        <img src="{{ $establishmentLogoPath }}" alt="Logo de {{ $establishmentName }}" style="height: {{$establishmentLogoHeight}}px; width:auto; max-width: 320px; object-fit: contain; object-position:left center;">
    @else
        <span style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold; color: #17324d;">{{ $establishmentName }}</span>
    @endif
@else
<table cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 4px; border-collapse: collapse;">
    <tr>
        <td style="height: {{$establishmentLogoRowHeight}}px; border: none; padding: 3px 12px; text-align: left; vertical-align: middle;">
            @if(!is_null($establishmentLogoPath))
                <img src="{{ $establishmentLogoPath }}" alt="Logo de {{ $establishmentName }}" style="height: {{$establishmentLogoHeight}}px; width:auto; max-width: 320px; object-fit: contain; object-position:left center;">
            @else
                <div style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold; color: #17324d;">
                    {{ $establishmentName }}
                </div>
            @endif
        </td>
    </tr>
</table>
@endif
