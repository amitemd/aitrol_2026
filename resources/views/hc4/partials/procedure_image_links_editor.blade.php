@php
    $linksImagenesEdicion = $protocolo && $protocolo->relationLoaded('imagenLinks')
        ? $protocolo->imagenLinks->pluck('url')->values()
        : collect();
    if ($linksImagenesEdicion->isEmpty() && trim((string) optional($protocolo)->link_imagenes) !== '') {
        $linksImagenesEdicion = collect([trim((string) $protocolo->link_imagenes)]);
    }
    $listaLinksId = 'hc4_links_imagenes_' . $uniqueId;
    $eventoCambioLink = $onChange ?? '';
@endphp

<style>
    #area_trabajo .hc4-image-links-heading { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:6px; }
    #area_trabajo .hc4-image-links-heading label { margin:0; }
    #area_trabajo .hc4-image-links-list { display:flex; flex-direction:column; gap:7px; }
    #area_trabajo .hc4-image-link-row { display:flex; align-items:center; gap:7px; min-width:0; }
    #area_trabajo .hc4-image-link-field { flex:1 1 auto; min-width:0; }
    #area_trabajo .hc4-image-link-readonly {
        display:block; width:100%; min-height:38px; padding:8px 12px; overflow:hidden;
        border:1px solid #cbdde6; border-radius:7px; color:#087fa7; background:#fff;
        text-decoration:underline; text-overflow:ellipsis; white-space:nowrap;
    }
    #area_trabajo .hc4-image-link-input { width:100%; height:38px; border:1px solid #cbdde6; border-radius:7px; box-shadow:none; }
    #area_trabajo .hc4-image-link-action {
        display:inline-flex; flex:0 0 36px; align-items:center; justify-content:center;
        width:36px; height:36px; padding:0; border:1px solid #0b8eaf; border-radius:5px;
        color:#16778e; background:#fff; font-size:14px;
    }
    #area_trabajo .hc4-image-link-action--remove { color:#c43d4b; border-color:#d66a75; }
</style>

<div class="col-12 hc4-procedure-link-edit-section" style="padding-top:12px">
    <div class="hc4-image-links-heading">
        <label style="font-family:'Helvetica general';">Links de imágenes</label>
        <button type="button" class="hc4-image-link-action" title="Agregar otro enlace" aria-label="Agregar otro enlace" data-onchange="{{$eventoCambioLink}}" onclick="hc4AgregarLinkImagenes('{{$listaLinksId}}', this.getAttribute('data-onchange'))">
            <i class="fa fa-plus" aria-hidden="true"></i>
        </button>
    </div>

    <div id="{{$listaLinksId}}" class="hc4-image-links-list">
        @foreach($linksImagenesEdicion as $linkImagen)
            <div class="hc4-image-link-row">
                <div class="hc4-image-link-field">
                    <input type="hidden" name="link_imagenes[]" value="{{$linkImagen}}" onchange="{{$eventoCambioLink}}">
                    <a class="hc4-image-link-readonly" href="{{$linkImagen}}" target="_blank" rel="noopener noreferrer" title="Abrir enlace en una nueva pestaña">{{$linkImagen}}</a>
                </div>
                <button type="button" class="hc4-image-link-action hc4-image-link-action--paste" title="Pegar o reemplazar enlace" aria-label="Pegar o reemplazar enlace" onclick="hc4PegarLinkImagenes(this)"><i class="fa fa-clipboard" aria-hidden="true"></i></button>
                <button type="button" class="hc4-image-link-action" title="Copiar enlace" aria-label="Copiar enlace" onclick="hc4CopiarLinkImagenes(this)"><i class="fa fa-copy" aria-hidden="true"></i></button>
                <button type="button" class="hc4-image-link-action hc4-image-link-action--remove" title="Eliminar enlace" aria-label="Eliminar enlace" onclick="hc4EliminarLinkImagenes(this); {{$eventoCambioLink}}"><i class="fa fa-trash" aria-hidden="true"></i></button>
            </div>
        @endforeach

        @if($linksImagenesEdicion->isEmpty())
            <div class="hc4-image-link-row">
                <div class="hc4-image-link-field">
                    <input type="url" name="link_imagenes[]" class="form-control hc4-image-link-input" placeholder="Use el botón Pegar para agregar el enlace" autocomplete="off" readonly onchange="{{$eventoCambioLink}}">
                </div>
                <button type="button" class="hc4-image-link-action hc4-image-link-action--paste" title="Pegar enlace" aria-label="Pegar enlace" onclick="hc4PegarLinkImagenes(this)"><i class="fa fa-clipboard" aria-hidden="true"></i></button>
                <button type="button" class="hc4-image-link-action" title="Copiar enlace" aria-label="Copiar enlace" onclick="hc4CopiarLinkImagenes(this)"><i class="fa fa-copy" aria-hidden="true"></i></button>
                <button type="button" class="hc4-image-link-action hc4-image-link-action--remove" title="Quitar fila" aria-label="Quitar fila" onclick="hc4EliminarLinkImagenes(this)"><i class="fa fa-trash" aria-hidden="true"></i></button>
            </div>
        @endif
    </div>
</div>

<script>
    window.hc4AgregarLinkImagenes = window.hc4AgregarLinkImagenes || function(listaId, eventoCambio) {
        var lista = document.getElementById(listaId);
        if (!lista) { return; }

        var fila = document.createElement('div');
        fila.className = 'hc4-image-link-row';
        fila.innerHTML = '<div class="hc4-image-link-field">' +
            '<input type="url" name="link_imagenes[]" class="form-control hc4-image-link-input" placeholder="Use el botón Pegar para agregar el enlace" autocomplete="off" readonly>' +
            '</div>' +
            '<button type="button" class="hc4-image-link-action hc4-image-link-action--paste" title="Pegar enlace" aria-label="Pegar enlace" onclick="hc4PegarLinkImagenes(this)"><i class="fa fa-clipboard" aria-hidden="true"></i></button>' +
            '<button type="button" class="hc4-image-link-action" title="Copiar enlace" aria-label="Copiar enlace" onclick="hc4CopiarLinkImagenes(this)"><i class="fa fa-copy" aria-hidden="true"></i></button>' +
            '<button type="button" class="hc4-image-link-action hc4-image-link-action--remove" title="Quitar fila" aria-label="Quitar fila" onclick="hc4EliminarLinkImagenes(this)"><i class="fa fa-trash" aria-hidden="true"></i></button>';

        var input = fila.querySelector('input');
        if (eventoCambio) { input.setAttribute('onchange', eventoCambio); }
        lista.appendChild(fila);
        input.focus();
    };

    window.hc4EliminarLinkImagenes = window.hc4EliminarLinkImagenes || function(control) {
        var fila = control && control.closest ? control.closest('.hc4-image-link-row') : null;
        if (fila) { fila.remove(); }
    };

    window.hc4ConvertirLinkImagenes = window.hc4ConvertirLinkImagenes || function(input) {
        var url = input && input.value ? input.value.trim() : '';
        if (!url || !input.checkValidity()) { return; }

        var fila = input.closest('.hc4-image-link-row');
        var campo = input.closest('.hc4-image-link-field');
        if (!fila || !campo) { return; }

        var oculto = document.createElement('input');
        oculto.type = 'hidden';
        oculto.name = 'link_imagenes[]';
        oculto.value = url;
        if (input.getAttribute('onchange')) {
            oculto.setAttribute('onchange', input.getAttribute('onchange'));
        }

        var enlace = document.createElement('a');
        enlace.className = 'hc4-image-link-readonly';
        enlace.href = url;
        enlace.target = '_blank';
        enlace.rel = 'noopener noreferrer';
        enlace.title = 'Abrir enlace en una nueva pestaña';
        enlace.textContent = url;

        campo.innerHTML = '';
        campo.appendChild(oculto);
        campo.appendChild(enlace);

    };

    window.hc4PegarLinkImagenes = window.hc4PegarLinkImagenes || function(control) {
        var fila = control.closest('.hc4-image-link-row');
        var input = fila.querySelector('input[type="url"]');
        var oculto = fila.querySelector('input[type="hidden"][name="link_imagenes[]"]');
        var asignar = function(texto) {
            var url = (texto || '').trim();
            if (!url) { return; }

            var validador = document.createElement('input');
            validador.type = 'url';
            validador.value = url;
            if (!validador.checkValidity()) {
                window.alert('Pegue un enlace válido que comience con http:// o https://');
                return;
            }

            if (input) {
                input.value = url;
                input.dispatchEvent(new Event('change', { bubbles:true }));
                hc4ConvertirLinkImagenes(input);
                return;
            }

            var enlace = fila.querySelector('.hc4-image-link-readonly');
            if (oculto && enlace) {
                oculto.value = url;
                enlace.href = url;
                enlace.textContent = url;
                oculto.dispatchEvent(new Event('change', { bubbles:true }));
            }
        };
        if (navigator.clipboard && navigator.clipboard.readText) {
            navigator.clipboard.readText().then(asignar).catch(function() {
                var texto = window.prompt('Pegue el link de imágenes:');
                if (texto !== null) { asignar(texto); }
            });
        } else {
            var texto = window.prompt('Pegue el link de imágenes:');
            if (texto !== null) { asignar(texto); }
        }
    };

    window.hc4CopiarLinkImagenes = window.hc4CopiarLinkImagenes || function(control) {
        var fila = control.closest('.hc4-image-link-row');
        var enlace = fila.querySelector('a[href]');
        var input = fila.querySelector('input[type="url"]');
        var url = enlace ? enlace.href : (input ? input.value.trim() : '');
        if (!url) { return; }
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url);
        } else {
            var temporal = document.createElement('textarea');
            temporal.value = url;
            document.body.appendChild(temporal);
            temporal.select();
            document.execCommand('copy');
            temporal.remove();
        }
    };
</script>
