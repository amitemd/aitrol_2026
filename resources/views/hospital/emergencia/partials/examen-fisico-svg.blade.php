<style>
    .clinical-body-map { grid-column:1 / -1; width:100%; margin-top:10px; padding:15px; border:1px solid #d4e6ed; border-radius:12px; background:linear-gradient(180deg,#f9fdfe 0%,#eef8fa 100%); }
    .clinical-body-map__heading { display:flex; align-items:center; gap:10px; margin-bottom:8px; }
    .clinical-body-map__heading h5 { margin:0 0 2px; color:#0b4f7d; font-size:14px; font-weight:800; }
    .clinical-body-map__heading p { margin:0; color:#688093; font-size:11px; }
    .clinical-body-map__icon { display:inline-flex; align-items:center; justify-content:center; flex:0 0 34px; width:34px; height:34px; border-radius:10px; background:#dff3f6; color:#087fab; }
    .clinical-body-map__canvas svg { display:block; width:100%; max-width:820px; height:auto; margin:0 auto; overflow:visible; }
    .clinical-body-map__status { min-height:34px; margin:8px 0 0; padding:8px 10px; border-radius:8px; background:#fff; color:#31566b; font-size:11px; font-weight:700; text-align:center; }
</style>

<aside class="clinical-body-map" aria-labelledby="clinical-body-map-title">
    <div class="clinical-body-map__heading">
        <span class="clinical-body-map__icon"><i class="fa fa-male" aria-hidden="true"></i></span>
        <div><h5 id="clinical-body-map-title">Mapa corporal</h5><p>Seleccione una zona para registrar el hallazgo.</p></div>
    </div>
    <div class="clinical-body-map__canvas" data-body-map-source="{{ asset('assets/hospital/examen-fisico-mapa.svg') }}">
        {!! file_get_contents(public_path('assets/hospital/examen-fisico-mapa.svg')) !!}
    </div>
    <p id="clinical-body-map-status" class="clinical-body-map__status" aria-live="polite">Escriba un hallazgo para colorear la región correspondiente.</p>
</aside>
