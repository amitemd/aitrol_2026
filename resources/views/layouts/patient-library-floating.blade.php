@if(Route::has('patient-library.index') && app(\Sis_medico\Services\PatientLibraryAccess::class)->allows(Auth::user(), $tenantActual ?? null))
<style>
.patient-library-float{position:fixed;right:22px;bottom:190px;z-index:1035;display:flex;width:56px;height:52px;align-items:center;justify-content:center;padding:0;border:1px solid #e4bc67;border-radius:14px;background:#fff3cf;color:#654713!important;box-shadow:0 6px 22px #19364930;text-decoration:none!important;cursor:grab;touch-action:none;user-select:none;transition:box-shadow .18s,background-color .18s}.patient-library-float:hover{background:#ffedb7;box-shadow:0 9px 25px #19364940}.patient-library-float:active,.patient-library-float.is-dragging{cursor:grabbing}.patient-library-float:focus-visible{outline:3px solid #197cad;outline-offset:4px}.patient-library-float svg{width:33px;height:29px;flex:none;pointer-events:none}.patient-library-float__tooltip{position:absolute;right:calc(100% + 10px);top:50%;width:max-content;max-width:190px;padding:7px 10px;border-radius:7px;background:#263238;color:#fff;box-shadow:0 4px 12px #0003;font:600 12px/1.25 Arial,sans-serif;opacity:0;pointer-events:none;transform:translateY(-50%) translateX(4px);transition:opacity .15s,transform .15s;white-space:nowrap}.patient-library-float__tooltip:after{content:"";position:absolute;left:100%;top:50%;margin-top:-5px;border:5px solid transparent;border-left-color:#263238}.patient-library-float.tooltip-on-right .patient-library-float__tooltip{right:auto;left:calc(100% + 10px);transform:translateY(-50%) translateX(-4px)}.patient-library-float.tooltip-on-right .patient-library-float__tooltip:after{right:100%;left:auto;border-right-color:#263238;border-left-color:transparent}.patient-library-float:hover .patient-library-float__tooltip,.patient-library-float:focus-visible .patient-library-float__tooltip{opacity:1;transform:translateY(-50%) translateX(0)}.patient-library-float.is-dragging .patient-library-float__tooltip{display:none}@media(max-width:650px){.patient-library-float{right:12px;bottom:145px}}@media(prefers-reduced-motion:reduce){.patient-library-float,.patient-library-float__tooltip{transition:none}}@media print{.patient-library-float{display:none}}
</style>
<a class="patient-library-float" href="{{ route('patient-library.index') }}" aria-label="Abrir biblioteca de pacientes">
    <svg viewBox="0 0 36 30" fill="none" aria-hidden="true"><path d="M2 7a3 3 0 0 1 3-3h8l4 4h14a3 3 0 0 1 3 3v14H2Z" fill="#edbc58"/><path d="M3 12h31l-4 14H2Z" fill="#ffd988" stroke="#b98523" stroke-width="1.5"/><path d="M16 15v8m-4-4h8" stroke="#805b1c" stroke-width="2"/></svg>
    <span class="patient-library-float__tooltip" role="tooltip">Biblioteca de pacientes</span>
</a>
<script>
(function () {
    var button = document.querySelector('.patient-library-float');
    if (!button) return;

    var storageKey = 'aitrol-patient-library-position-v1';
    var dragging = false;
    var moved = false;
    var startX = 0;
    var startY = 0;
    var startLeft = 0;
    var startTop = 0;

    function clamp(value, minimum, maximum) {
        return Math.max(minimum, Math.min(value, maximum));
    }

    function place(left, top) {
        var margin = 8;
        var maxLeft = Math.max(margin, window.innerWidth - button.offsetWidth - margin);
        var maxTop = Math.max(margin, window.innerHeight - button.offsetHeight - margin);
        button.style.left = clamp(left, margin, maxLeft) + 'px';
        button.style.top = clamp(top, margin, maxTop) + 'px';
        button.style.right = 'auto';
        button.style.bottom = 'auto';
        button.classList.toggle('tooltip-on-right', left < window.innerWidth / 2);
    }

    function restore() {
        try {
            var saved = JSON.parse(window.localStorage.getItem(storageKey));
            if (saved && isFinite(saved.left) && isFinite(saved.top)) place(saved.left, saved.top);
        } catch (error) {}
    }

    function save() {
        try {
            var rect = button.getBoundingClientRect();
            window.localStorage.setItem(storageKey, JSON.stringify({left: rect.left, top: rect.top}));
        } catch (error) {}
    }

    button.addEventListener('mousedown', function (event) {
        if (event.button !== 0) return;
        var rect = button.getBoundingClientRect();
        dragging = true;
        moved = false;
        startX = event.clientX;
        startY = event.clientY;
        startLeft = rect.left;
        startTop = rect.top;
        button.classList.add('is-dragging');
        event.preventDefault();
    });

    document.addEventListener('mousemove', function (event) {
        if (!dragging) return;
        var deltaX = event.clientX - startX;
        var deltaY = event.clientY - startY;
        if (Math.abs(deltaX) > 3 || Math.abs(deltaY) > 3) moved = true;
        if (moved) place(startLeft + deltaX, startTop + deltaY);
    });

    document.addEventListener('mouseup', function () {
        if (!dragging) return;
        dragging = false;
        button.classList.remove('is-dragging');
        if (moved) save();
    });

    button.addEventListener('click', function (event) {
        if (moved) {
            event.preventDefault();
            moved = false;
        }
    });

    window.addEventListener('resize', function () {
        if (button.style.left) {
            var rect = button.getBoundingClientRect();
            place(rect.left, rect.top);
            save();
        }
    });

    restore();
})();
</script>
@endif
