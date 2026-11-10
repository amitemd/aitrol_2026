<aside
    id="aitrol-support-assistant"
    class="aitrol-support-assistant is-collapsed"
    data-state="idle"
    data-assistant-name="{{ trans('v3/support_assistant.name') }}"
    data-translations="{{ json_encode(trans('v3/support_assistant')) }}"
    data-src-idle="{{ asset('assets/avatars/avatar-soporte-esperando-cutout.png') }}"
    data-src-thinking="{{ asset('assets/avatars/avatar-soporte-pensando-cutout.png') }}"
    data-src-book="{{ asset('assets/avatars/avatar-soporte-libro-cutout.png') }}"
    data-src-support="{{ asset('assets/avatars/avatar-soporte-computadora-cutout.png') }}"
    data-user-id="{{ Auth::check() ? (string) Auth::id() : 'guest' }}"
    data-user-name="{{ Auth::check() ? trim((string) Auth::user()->nombre1) : '' }}"
    data-login-greeting="{{ session('aitrol_assistant_login_greeting') ? '1' : '0' }}"
    aria-label="{{ trans('v3/support_assistant.aria') }}"
>
    <button
        class="aitrol-assistant__help-trigger"
        type="button"
        aria-controls="aitrol-assistant-panel"
        aria-expanded="false"
        aria-label="Abrir ayuda de Walter"
        title="Ayuda"
    >
        <i class="fas fa-question" aria-hidden="true"></i>
        <span class="aitrol-assistant__help-label" aria-hidden="true">Ayuda</span>
    </button>

    <div class="aitrol-assistant__bubble" id="aitrol-assistant-status" role="status" aria-live="polite">
        {{ trans('v3/support_assistant.initial') }}
    </div>

    <section class="aitrol-assistant__panel" id="aitrol-assistant-panel" aria-hidden="true" aria-labelledby="aitrol-assistant-title">
        <button class="aitrol-assistant__close" type="button" aria-label="{{ trans('v3/support_assistant.close') }}">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
        <h2 id="aitrol-assistant-title">{{ trans('v3/support_assistant.name') }}</h2>
        <p id="aitrol-assistant-panel-status">{{ trans('v3/support_assistant.question') }}</p>
        <div class="aitrol-assistant__actions">
            <button type="button" data-assistant-demo="book">
                <i class="fas fa-book-open" aria-hidden="true"></i> {{ trans('v3/support_assistant.suggestion') }}
            </button>
            @if (Route::has('v3.support-tickets.create') || Route::has('gateway_tickets.create'))
                <a href="{{ Route::has('v3.support-tickets.create') ? route('v3.support-tickets.create') : route('gateway_tickets.create') }}" data-assistant-ticket>
                    <i class="fas fa-headset" aria-hidden="true"></i> {{ trans('v3/support_assistant.ticket') }}
                </a>
            @endif
        </div>
        <small>{{ trans('v3/support_assistant.help') }}</small>
    </section>

    <button
        class="aitrol-assistant__avatar"
        type="button"
        aria-controls="aitrol-assistant-panel"
        aria-expanded="false"
        title="{{ trans('v3/support_assistant.open') }}"
    >
        <img
            src="{{ asset('assets/avatars/avatar-soporte-esperando-cutout.png') }}"
            alt="{{ trans('v3/support_assistant.labels.idle') }}"
            width="512"
            height="512"
        >
    </button>
</aside>
