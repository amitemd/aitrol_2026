@php
    $establishmentLogoPath = null;
    $establishmentName = !empty($empresa) && !empty($empresa->nombrecomercial)
        ? $empresa->nombrecomercial
        : 'ESTABLECIMIENTO DE SALUD';

    if (!empty($empresa) && !empty($empresa->logo)) {
        $logoCandidate = base_path('storage/app/logo/' . basename($empresa->logo));
        if (is_file($logoCandidate)) {
            $establishmentLogoPath = $logoCandidate;
        }
    }
@endphp

<table cellpadding="0" cellspacing="0" style="width: 100%; margin-bottom: 4px; border-collapse: collapse;">
    <tr>
        <td style="height: 72px; border: 1px solid #000; text-align: center; vertical-align: middle;">
            @if(!is_null($establishmentLogoPath))
                <img src="{{ $establishmentLogoPath }}" alt="Logo de {{ $establishmentName }}" style="height: 62px; max-width: 320px;">
            @else
                <div style="font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold; color: #17324d;">
                    {{ $establishmentName }}
                </div>
            @endif
        </td>
    </tr>
</table>
