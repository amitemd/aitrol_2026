<li class="nav-item">
    <a href="{{ route($enlace->ruta) }}" @class(['nav-link', 'active' => request()->routeIs($enlace->ruta)])
        @if (request()->routeIs($enlace->ruta)) aria-current="page" @endif>
        <i class="nav-icon bi {{ $enlace->icono }}" aria-hidden="true"></i>
        <p class="text-wrap text-break">{{ $enlace->nombre }}</p>
    </a>
</li>
