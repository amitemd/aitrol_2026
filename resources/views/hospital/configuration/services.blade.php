@extends('layouts.app-template-h')

@section('content')
<style>
    .hospital-service-settings { --navy:#073b66; --blue:#0877be; --teal:#0d9488; --ink:#17324d; --muted:#687b8d; --line:#dbe8f1; padding:.5rem 0 2rem; color:var(--ink); }
    .hospital-service-settings * { box-sizing:border-box; }
    .service-settings-hero { display:flex; align-items:center; justify-content:space-between; gap:1.5rem; padding:1.6rem 1.8rem; border-radius:18px; background:linear-gradient(115deg,#063b67,#0877ad 68%,#0d9488); box-shadow:0 14px 34px rgba(7,59,102,.14); color:#fff; }
    .service-settings-hero h1 { margin:0; color:#fff; font-size:1.65rem; font-weight:750; }
    .service-settings-hero p { margin:.45rem 0 0; color:rgba(255,255,255,.82); }
    .service-settings-company { min-width:230px; padding:.8rem 1rem; border:1px solid rgba(255,255,255,.25); border-radius:13px; background:rgba(255,255,255,.12); }
    .service-settings-company small { display:block; color:#bde9ee; font-weight:700; text-transform:uppercase; }
    .service-settings-company strong { display:block; margin-top:.2rem; color:#fff; }
    .service-settings-notice { margin:1rem 0; padding:.9rem 1rem; border-radius:12px; background:#e9f8f4; color:#176b5c; font-weight:650; }
    .service-settings-notice.warning { background:#fff4df; color:#8a5a06; }
    .service-settings-panel { margin-top:1rem; padding:1.35rem; border:1px solid var(--line); border-radius:18px; background:#fff; box-shadow:0 10px 30px rgba(18,69,116,.07); }
    .service-settings-head { display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:1rem; }
    .service-settings-head h2 { margin:0; color:var(--navy); font-size:1.2rem; }
    .service-settings-head p { margin:.3rem 0 0; color:var(--muted); }
    .service-settings-counter { padding:.4rem .7rem; border-radius:999px; background:#e8f7f5; color:#0b766d; font-size:.8rem; font-weight:750; }
    .service-settings-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:.85rem; }
    .service-setting { display:flex; align-items:center; gap:.9rem; min-height:92px; padding:1rem; border:1px solid var(--line); border-radius:14px; background:#fbfdff; transition:.2s ease; }
    .service-setting.is-disabled { opacity:.58; background:#f3f5f7; }
    .service-setting-icon { display:grid; width:44px; height:44px; flex:0 0 44px; place-items:center; border-radius:12px; background:#e8f4fb; color:var(--blue); font-size:1.15rem; }
    .service-setting-copy { min-width:0; flex:1; }
    .service-setting-copy strong { display:block; color:var(--ink); }
    .service-setting-copy span { display:block; margin-top:.2rem; color:var(--muted); font-size:.78rem; }
    .service-setting-toggle { position:relative; width:48px; height:27px; flex:0 0 48px; margin:0; }
    .service-setting-toggle input { position:absolute; opacity:0; }
    .service-setting-slider { position:absolute; inset:0; border-radius:999px; background:#c7d2dc; cursor:pointer; transition:.2s; }
    .service-setting-slider:before { content:''; position:absolute; width:21px; height:21px; left:3px; top:3px; border-radius:50%; background:#fff; box-shadow:0 2px 5px rgba(0,0,0,.22); transition:.2s; }
    .service-setting-toggle input:checked + .service-setting-slider { background:var(--teal); }
    .service-setting-toggle input:checked + .service-setting-slider:before { transform:translateX(21px); }
    .service-settings-actions { display:flex; justify-content:flex-end; gap:.7rem; margin-top:1.2rem; padding-top:1rem; border-top:1px solid var(--line); }
    .service-settings-actions a,.service-settings-actions button { display:inline-flex; align-items:center; gap:.45rem; padding:.72rem 1rem; border-radius:10px; font-weight:700; text-decoration:none; }
    .service-settings-actions a { border:1px solid var(--line); color:var(--navy); background:#fff; }
    .service-settings-actions button { border:0; color:#fff; background:var(--blue); cursor:pointer; }
    .dark-layout .service-settings-panel,.dark-layout .service-setting { border-color:#3b4a58; background:#24313d; }
    .dark-layout .service-setting.is-disabled { background:#1e2933; }
    .dark-layout .service-setting-copy strong,.dark-layout .service-settings-head h2 { color:#f5fbff; }
    .dark-layout .service-setting-copy span,.dark-layout .service-settings-head p { color:#b8c6d2; }
    @media(max-width:1050px){.service-settings-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
    @media(max-width:700px){.service-settings-hero,.service-settings-head{align-items:flex-start;flex-direction:column}.service-settings-company{width:100%}.service-settings-grid{grid-template-columns:1fr}.service-settings-actions{flex-direction:column}.service-settings-actions a,.service-settings-actions button{justify-content:center;width:100%}}
</style>

<main class="hospital-service-settings">
    <section class="service-settings-hero">
        <div>
            <h1><i class="fa fa-th-large" aria-hidden="true"></i> Servicios visibles en Hospital</h1>
            <p>Activa únicamente las áreas que esta empresa ofrece en su portada hospitalaria.</p>
        </div>
        <div class="service-settings-company">
            <small>Empresa seleccionada</small>
            <strong>{{ $company ? ($company->nombrecomercial ?: $company->razonsocial) : session('id_empresa') }}</strong>
        </div>
    </section>

    @if(session('success'))
        <div class="service-settings-notice" role="status"><i class="fa fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(!$configurationInstalled)
        <div class="service-settings-notice warning"><i class="fa fa-exclamation-triangle"></i> Ejecute las migraciones para habilitar el guardado por empresa. Mientras tanto se muestran todos los servicios.</div>
    @endif

    <form class="service-settings-panel" method="POST" action="{{ route('hospital.services.configuration.save') }}">
        {{ csrf_field() }}
        <div class="service-settings-head">
            <div><h2>Áreas disponibles</h2><p>Desactivar una opción la oculta solamente para la empresa seleccionada.</p></div>
            <span class="service-settings-counter"><span id="enabled-service-count">{{ $services->where('enabled', true)->count() }}</span> de {{ $services->count() }} activas</span>
        </div>

        <div class="service-settings-grid">
            @foreach($services as $service)
                <div class="service-setting {{ $service['enabled'] ? '' : 'is-disabled' }}">
                    <span class="service-setting-icon"><i class="fa {{ $service['icon'] }}" aria-hidden="true"></i></span>
                    <span class="service-setting-copy"><strong>{{ $service['label'] }}</strong><span>{{ $service['description'] }}</span></span>
                    <label class="service-setting-toggle" title="Mostrar u ocultar {{ $service['label'] }}">
                        <input class="js-service-toggle" type="checkbox" name="enabled[]" value="{{ $service['key'] }}" {{ $service['enabled'] ? 'checked' : '' }} {{ !$configurationInstalled ? 'disabled' : '' }}>
                        <span class="service-setting-slider"></span>
                    </label>
                </div>
            @endforeach
        </div>

        <div class="service-settings-actions">
            <a href="{{ route('hospital.index') }}"><i class="fa fa-arrow-left"></i> Volver a Hospital</a>
            <button type="submit" {{ !$configurationInstalled ? 'disabled' : '' }}><i class="fa fa-save"></i> Guardar configuración</button>
        </div>
    </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var toggles = document.querySelectorAll('.js-service-toggle');
    var counter = document.getElementById('enabled-service-count');
    function refresh() {
        var enabled = 0;
        Array.prototype.forEach.call(toggles, function (toggle) {
            var card = toggle.closest('.service-setting');
            if (toggle.checked) enabled++;
            if (card) card.classList.toggle('is-disabled', !toggle.checked);
        });
        if (counter) counter.textContent = enabled;
    }
    Array.prototype.forEach.call(toggles, function (toggle) { toggle.addEventListener('change', refresh); });
    refresh();
});
</script>
@endsection
