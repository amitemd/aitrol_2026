@php $signature = $anesthesiaSignature ?? array(); @endphp
<div style="width:100%; text-align:center; font-family:Arial, Helvetica, sans-serif; line-height:1.1;">
    @if(!empty($signature['electronic']))
        <table cellpadding="0" cellspacing="0" style="width:auto; margin:0 auto; border-collapse:collapse; border:0;">
            <tr>
                <td style="border:0; width:54px; padding:1px; text-align:center; vertical-align:middle;">
                    @if(!empty($signature['qr_source']))
                        <img src="{{$signature['qr_source']}}" alt="QR de validación" style="width:50px; height:50px;">
                    @endif
                </td>
                <td style="border:0; padding:1px 3px; text-align:left; vertical-align:middle; font-size:7px;">
                    Validar la firma escaneando el QR<br>
                    Firmado electrónicamente por:<br>
                    <b style="font-size:8px;">{{$signature['name']}}</b><br>
                    {{$signature['specialty']}}<br>
                    <b>{{$signature['status']}}</b>
                </td>
            </tr>
        </table>
        @if(!empty($signature['register']))
            <div style="font-size:6px;">REGISTRO PROFESIONAL: {{$signature['register']}}</div>
        @endif
        @if(!empty($signature['fingerprint']))
            <div style="font-size:6px; font-weight:bold;">HUELLA: {{strtoupper(substr($signature['fingerprint'], 0, 24))}}</div>
        @endif
    @elseif(!empty($signature['signature_image']))
        <img src="{{$signature['signature_image']}}" alt="Firma registrada" style="max-width:150px; max-height:65px;"><br>
        <b style="font-size:8px;">{{$signature['name']}}</b>
    @endif
</div>
