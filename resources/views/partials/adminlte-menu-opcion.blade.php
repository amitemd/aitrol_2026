@if ($item->ruta === null)
    @php
        $hijos = collect($navegacion[$item->id] ?? [])->filter(fn ($hijo) => $hijo->ruta === null || Route::has($hijo->ruta));
        $abierto = $hijos->contains(fn ($hijo) => $hijo->ruta !== null && request()->routeIs($hijo->ruta));
    @endphp
    <li @class(['nav-item', 'menu-open' => $abierto])>
        @if ($hijos->isNotEmpty())
            <a href="#" @class(['nav-link', 'active' => $abierto]) aria-expanded="{{ $abierto ? 'true' : 'false' }}">
                <i class="nav-icon bi {{ $item->icono }}" aria-hidden="true"></i>
                <p class="text-wrap text-break pe-3">{{ __($item->nombre) }} <i class="nav-arrow bi bi-chevron-right" aria-hidden="true"></i></p>
            </a>
            <ul class="nav nav-treeview">
                @foreach ($hijos as $hijo)
                    @include('partials.adminlte-menu-opcion', ['item' => $hijo])
                @endforeach
            </ul>
        @else
            <div class="nav-link">
                <i class="nav-icon bi {{ $item->icono }}" aria-hidden="true"></i>
                <p class="text-wrap text-break">{{ __($item->nombre) }}</p>
            </div>
        @endif
    </li>
@elseif (Route::has($item->ruta))
    @include('partials.adminlte-menu-enlace', ['enlace' => $item])
@endif
