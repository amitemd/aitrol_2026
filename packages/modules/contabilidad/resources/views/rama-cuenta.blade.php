@php
    $hijas = $cuentasPorPadre->get($cuenta['id'], []);
    $subcuentasId = 'plan-subcuentas-'.$cuenta['id'];
    $iconoCuenta = $esRaiz ? ($iconosClase[$cuenta['clase']] ?? 'bi-diagram-3') : ($cuenta['auxiliar'] ? 'bi-file-earmark-text' : 'bi-folder2');
@endphp
<li class="plan-cuenta" data-plan-cuenta="{{ $cuenta['id'] }}" @if ($esRaiz) data-plan-clase="{{ $cuenta['clase'] }}" @endif @unless ($cuenta['visible_plan']) hidden @endunless>
    <div class="plan-cuenta-nodo" @if ($cuenta['tiene_hijas']) data-plan-rama @endif>
        <div class="plan-cuenta-identidad">
            @if ($cuenta['tiene_hijas'])
                <button class="plan-cuenta-expansion" type="button" data-plan-expandir aria-expanded="{{ $cuenta['expandida_plan'] ? 'true' : 'false' }}"
                    aria-controls="{{ $subcuentasId }}" aria-label="Subcuentas de {{ $cuenta['nombre'] }}">
                    <i class="bi bi-chevron-right" aria-hidden="true"></i>
                </button>
            @else
                <span class="plan-cuenta-terminal" aria-hidden="true"></span>
            @endif
            <span class="plan-cuenta-icono"><i class="bi {{ $iconoCuenta }}" aria-hidden="true"></i></span>
            <div class="plan-cuenta-nombre">
                <span class="plan-cuenta-codigo">{{ $cuenta['codigo'] }}</span>
                <span data-plan-nombre class="{{ $cuenta['auxiliar'] ? '' : 'fw-semibold' }}">{{ $cuenta['nombre'] }}</span>
            </div>
        </div>
        <div class="plan-cuenta-acciones">
            @unless ($cuenta['activo'])
                <span class="badge text-bg-secondary">Inactiva</span>
            @endunless
            @if ($cuenta['efectivo'])
                <span class="plan-cuenta-efectivo" title="Cuenta de efectivo"><i class="bi bi-cash" aria-hidden="true"></i><span class="visually-hidden">Cuenta de efectivo</span></span>
            @endif
            @component('partials.acciones-fila', ['ver' => route($ruta.'.show', $cuenta['id']), 'tituloVer' => 'Detalle de cuenta',
                'editar' => $cuenta['auxiliar'] && $puedeEditarAuxiliar ? route($ruta.'.editar.formulario', $cuenta['id']) : null])
                @if (!$cuenta['auxiliar'] && $cuenta['activo'] && $puedeCrearAuxiliar)
                    @include('partials.accion-fila-enlace', ['href' => route($ruta.'.create', ['id_padre' => $cuenta['id']]),
                        'etiqueta' => 'Agregar auxiliar', 'titulo' => 'Nueva cuenta auxiliar', 'icono' => 'bi-plus-lg'])
                @endif
            @endcomponent
        </div>
    </div>
    @if ($cuenta['tiene_hijas'])
        <ul class="plan-cuentas-ramas" id="{{ $subcuentasId }}" role="list" @unless ($cuenta['expandida_plan']) hidden @endunless>
            @foreach ($hijas as $hija)
                @include('contabilidad::rama-cuenta', ['cuenta' => $hija, 'esRaiz' => false])
            @endforeach
        </ul>
    @endif
</li>
