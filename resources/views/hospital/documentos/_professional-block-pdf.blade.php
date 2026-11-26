@php
    $professionalBlock = $professionalBlock ?? [];
    $professionalValue = function ($key, $fallback = '') use ($professionalBlock) {
        return isset($professionalBlock[$key]) ? $professionalBlock[$key] : $fallback;
    };
    $professionalBlockCompact = !empty($professionalBlockCompact);
    $professionalBlockTitle = $professionalBlockTitle ?? 'DATOS DEL PROFESIONAL RESPONSABLE';
@endphp
<style>
    .clinical-professional-block { width: 100%; margin-top: 1.5mm; border-collapse: collapse; table-layout: fixed; page-break-inside: avoid; }
    .clinical-professional-block th,
    .clinical-professional-block td { border: .6pt solid #555; padding: .65mm .8mm; vertical-align: middle; }
    .clinical-professional-block .professional-band { background: #d0ccff; color: #111b36; font-size: 6.8pt; font-weight: bold; text-align: left; }
    .clinical-professional-block .professional-label { background: #cdffcd; font-size: 5.2pt; font-weight: bold; text-align: center; }
    .clinical-professional-block .professional-value { height: 5mm; font-size: 6.2pt; text-align: center; }
    .clinical-professional-block .professional-visual { height: 20mm; padding: .8mm; text-align: center; vertical-align: middle; }
    .clinical-professional-block .professional-visual img { display: inline-block; max-width: 42mm; max-height: 18mm; width: auto; height: auto; }
    .clinical-professional-block .professional-visual img.professional-qr { max-width: 17mm; max-height: 17mm; }
    .clinical-professional-block .professional-electronic-label { display: block; margin-top: .4mm; font-size: 4.5pt; font-weight: bold; }
    .clinical-professional-block .professional-electronic-signature,
    .clinical-professional-block .professional-electronic-signature tr,
    .clinical-professional-block .professional-electronic-signature td { border: 0; padding: 0; }
    .clinical-professional-block .professional-electronic-signature { width: 100%; table-layout: fixed; }
    .clinical-professional-block .professional-electronic-signature .electronic-qr-cell { width: 18mm; text-align: center; vertical-align: middle; }
    .clinical-professional-block .professional-electronic-signature .electronic-text-cell { padding-left: 1mm; text-align: left; vertical-align: middle; font-size: 4.1pt; line-height: 4.8pt; }
    .clinical-professional-block .professional-electronic-signature .electronic-name { font-weight: bold; }
    .clinical-professional-block .professional-electronic-signature .electronic-status { font-weight: bold; }
    .clinical-professional-block .professional-signature-fingerprint { margin-top: .5mm; font-size: 3.7pt; font-weight: bold; text-align: center; }
    .clinical-professional-block.is-compact .professional-value { height: 3.6mm; }
    .clinical-professional-block.is-compact .professional-visual { height: 12mm; }
    .clinical-professional-block.is-compact .professional-visual img { max-height: 10.5mm; }
    .clinical-professional-block.is-compact .professional-visual img.professional-qr { max-width: 10.5mm; max-height: 10.5mm; }
    .clinical-professional-block.is-compact .professional-electronic-label { font-size: 3.3pt; }
</style>
<table class="clinical-professional-block {{ $professionalBlockCompact ? 'is-compact' : '' }}">
    <tr><th colspan="12" class="professional-band">{{ $professionalBlockTitle }}</th></tr>
    <tr>
        <th colspan="2" class="professional-label">FECHA</th>
        <th colspan="2" class="professional-label">HORA</th>
        <th colspan="8" class="professional-label">NOMBRES Y APELLIDOS</th>
    </tr>
    <tr>
        <td colspan="2" class="professional-value">{{ $professionalValue('date') }}</td>
        <td colspan="2" class="professional-value">{{ $professionalValue('time') }}</td>
        <td colspan="8" class="professional-value">{{ $professionalValue('name') }}</td>
    </tr>
    <tr>
        <th colspan="3" class="professional-label">NÚMERO DE DOCUMENTO DE IDENTIFICACIÓN</th>
        <th colspan="4" class="professional-label">FIRMA</th>
        <th colspan="5" class="professional-label">SELLO</th>
    </tr>
    <tr>
        <td colspan="3" class="professional-visual">{{ $professionalValue('identification') }}</td>
        <td colspan="4" class="professional-visual">
            @if($professionalValue('electronic') && $professionalValue('qr_source'))
                <table class="professional-electronic-signature">
                    <tr>
                        <td class="electronic-qr-cell">
                            <img class="professional-qr" src="{{ $professionalValue('qr_source') }}" alt="QR de validación de firma electrónica">
                        </td>
                        <td class="electronic-text-cell">
                            Validar la firma escaneando el QR<br>
                            Firmado electrónicamente por:<br>
                            <span class="electronic-name">{{ $professionalValue('name') }}</span><br>
                            {{ $professionalValue('specialty') }}<br>
                            <span class="electronic-status">{{ $professionalValue('signature_status') }}</span>
                        </td>
                    </tr>
                </table>
                @if($professionalValue('professional_register'))
                    <div class="professional-signature-fingerprint">REGISTRO PROFESIONAL: {{ $professionalValue('professional_register') }}</div>
                @endif
                @if($professionalValue('fingerprint'))
                    <div class="professional-signature-fingerprint">HUELLA: {{ strtoupper(substr($professionalValue('fingerprint'), 0, 24)) }}</div>
                @endif
            @elseif($professionalValue('signature_image'))
                <img src="{{ $professionalValue('signature_image') }}" alt="Firma manuscrita del profesional">
            @endif
        </td>
        <td colspan="5" class="professional-visual">
            @if($professionalValue('seal_image'))
                <img src="{{ $professionalValue('seal_image') }}" alt="Sello del profesional">
            @endif
        </td>
    </tr>
</table>
