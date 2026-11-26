@php $signature = $anesthesiaSignature ?? array(); @endphp
<div style="width:100%; text-align:center; font-family:Arial, Helvetica, sans-serif; line-height:1.2;">
    @if(!empty($signature['seal_image']))
        <img src="{{$signature['seal_image']}}" alt="Sello registrado" style="max-width:170px; max-height:72px;">
    @endif
</div>
