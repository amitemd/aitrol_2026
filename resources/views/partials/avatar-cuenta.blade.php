<span class="aitrol-avatar {{ $claseAvatar ?? '' }}" aria-hidden="true">
    <span>{{ $iniciales }}</span>
    <img data-aitrol-avatar @if ($fotoPerfil) src="{{ $fotoPerfil }}" @endif alt="" hidden>
</span>
