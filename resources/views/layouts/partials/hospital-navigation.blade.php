<nav class="aitrol-hospital-nav" aria-label="Navegación hospitalaria">
    <a class="aitrol-hospital-nav__brand" href="{{ route('hospital.index') }}">
        <i class="fa fa-heartbeat" aria-hidden="true"></i> AITROL
    </a>
    <ul>
        <li>
            <a href="{{ route('hospital.index') }}" @if(request()->routeIs('hospital.index')) aria-current="page" @endif>
                <i class="fa fa-home" aria-hidden="true"></i> Inicio hospitalario
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.gcuartos') }}" @if(request()->routeIs('hospital.gcuartos', 'hospital.gcuartos_habitacion')) aria-current="page" @endif>
                <i class="fa fa-bed" aria-hidden="true"></i> Recepción y camas
            </a>
        </li>
        <li>
            <a href="{{ route('hospital.index') }}#hospital-services-title">
                <i class="fa fa-th-large" aria-hidden="true"></i> Servicios clínicos
            </a>
        </li>
    </ul>
</nav>
