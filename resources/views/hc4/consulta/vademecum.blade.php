<style>
    .v3-medicine-catalog { overflow: hidden; border-radius: 16px; background: #f5fafc; box-shadow: 0 18px 45px rgba(5, 69, 105, .20); }
    .v3-medicine-head { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 18px 22px; background: linear-gradient(110deg, #07547e 0%, #087eae 62%, #16aaa5 100%); color: #fff; }
    .v3-medicine-title { display: flex; align-items: center; gap: 12px; }
    .v3-medicine-title__icon { display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px; border: 1px solid rgba(255,255,255,.40); border-radius: 11px; background: rgba(255,255,255,.13); font-size: 18px; }
    .v3-medicine-title h3 { margin: 0 0 3px; font-size: 18px; font-weight: 700; }
    .v3-medicine-title p { margin: 0; font-size: 12px; opacity: .86; }
    .v3-medicine-close { border: 0; background: transparent; color: #fff; font-size: 25px; line-height: 1; opacity: .85; }
    .v3-medicine-search { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 12px; padding: 18px 22px; border-bottom: 1px solid #d7e7ee; background: #fff; }
    .v3-medicine-search__field { position: relative; }
    .v3-medicine-search__field i { position: absolute; top: 50%; left: 14px; z-index: 2; color: #1687aa; transform: translateY(-50%); }
    .v3-medicine-search__field input { width: 100%; height: 42px; padding: 8px 14px 8px 40px; border: 1px solid #bcd7e2; border-radius: 10px; box-sizing: border-box; box-shadow: none; }
    .v3-medicine-search__field input:focus { border-color: #159fb0; box-shadow: 0 0 0 3px rgba(21,159,176,.14); outline: 0; }
    .v3-medicine-search__button { min-width: 116px; height: 42px; border: 0; border-radius: 10px; background: #087eae; color: #fff; font-weight: 700; box-shadow: 0 6px 14px rgba(8,126,174,.22); }
    .v3-medicine-search__button:hover { background: #075f8c; }
    .v3-medicine-loading { padding: 30px; color: #087eae; text-align: center; }
    @media (max-width: 650px) {
        .v3-medicine-head { align-items: flex-start; }
        .v3-medicine-search { grid-template-columns: 1fr; }
        .v3-medicine-search__button { width: 100%; }
    }
</style>

<section class="v3-medicine-catalog">
    <header class="v3-medicine-head">
        <div class="v3-medicine-title">
            <span class="v3-medicine-title__icon"><i class="fa fa-medkit" aria-hidden="true"></i></span>
            <div><h3>Catálogo de medicinas</h3><p>Consulta presentaciones, dosis e indicaciones.</p></div>
        </div>
        <button type="button" class="v3-medicine-close" data-dismiss="modal" aria-label="Cerrar">&times;</button>
    </header>

    <form method="POST" id="buscador_modal_vade" class="v3-medicine-search" onsubmit="buscar_nuevo_modal(1); return false;">
        {{ csrf_field() }}
        <div class="v3-medicine-search__field">
            <i class="fa fa-search" aria-hidden="true"></i>
            <input type="text" name="texto" id="inputnombre" placeholder="Buscar por nombre o presentación" value="{{$texto}}" autocomplete="off">
        </div>
        <button type="submit" class="v3-medicine-search__button"><i class="fa fa-search" aria-hidden="true"></i> Buscar</button>
    </form>

    <div id="vademecum-table-container">
        @include('hc4.consulta.vademecum_interno', ['nombre' => $nombre, 'texto' => $texto])
    </div>
</section>

<script>
    function buscar_nuevo_modal(page) {
        var pagina = page || 1;
        var contenedor = $('#vademecum-table-container');
        contenedor.html('<div class="v3-medicine-loading"><i class="fa fa-circle-o-notch fa-spin"></i> Cargando medicinas...</div>');
        $.ajax({
            type: 'POST',
            url: "{{ asset('hc4/revisar/informacion/ventana/') }}",
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            data: {texto: $('#inputnombre').val(), page: pagina, _token: '{{ csrf_token() }}'},
            success: function (data) { contenedor.html(data); },
            error: function () { contenedor.html('<div class="v3-medicine-loading">No fue posible cargar el catálogo.</div>'); }
        });
    }

    $('#vademecum-table-container').off('click.vademecumPager').on('click.vademecumPager', '.js-vademecum-page', function () {
        if (!$(this).prop('disabled')) { buscar_nuevo_modal($(this).data('page')); }
    });
</script>
