<table class="signatures professional-signatures">
    <tr>
        <td><div class="compact-line">{{ $professionalBlock['name'] ?? '' }}</div>Profesional responsable</td>
        <td><div class="compact-line">{{ $professionalBlock['identification'] ?? '' }}</div>Cédula de ciudadanía</td>
        <td>
            <div class="compact-line professional-inline-visual">
                @if(!empty($professionalBlock['electronic']) && !empty($professionalBlock['qr_source']))
                    <img class="professional-inline-qr" src="{{ $professionalBlock['qr_source'] }}" alt="Firma electrónica verificable">
                @elseif(!empty($professionalBlock['signature_image']))
                    <img src="{{ $professionalBlock['signature_image'] }}" alt="Firma manuscrita del profesional">
                @endif
            </div>
            Firma
        </td>
        <td>
            <div class="compact-line professional-inline-visual">
                @if(!empty($professionalBlock['seal_image']))<img src="{{ $professionalBlock['seal_image'] }}" alt="Sello del profesional">@endif
            </div>
            Sello
        </td>
    </tr>
</table>
