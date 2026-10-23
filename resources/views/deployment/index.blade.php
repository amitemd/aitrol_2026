@extends('layouts.app-template')

@section('content')
<style>
    .deployment-page {
        background: #f4f7fb;
        min-height: calc(100vh - 50px);
    }

    .deployment-page .content-header {
        padding: 28px 30px 14px;
    }

    .deployment-page .content-header h1 {
        color: #17324d;
        font-size: 28px;
        font-weight: 600;
        margin: 0;
    }

    .deployment-page .content-header p {
        color: #6b7c8f;
        margin: 8px 0 0;
    }

    .deployment-page .content {
        padding: 0 30px 30px;
    }

    .deployment-notice {
        align-items: flex-start;
        background: #eef8ff;
        border: 1px solid #c7e8fa;
        border-radius: 10px;
        color: #28536b;
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        padding: 15px 18px;
    }

    .deployment-notice i {
        color: #1597c9;
        font-size: 20px;
        margin-top: 2px;
    }

    .deployment-status-grid,
    .deployment-operation-grid {
        display: grid;
        gap: 16px;
    }

    .deployment-status-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        margin-bottom: 24px;
    }

    .deployment-status-card {
        background: #fff;
        border: 1px solid #e4ebf2;
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(31, 56, 82, .05);
        padding: 16px;
    }

    .deployment-status-card span {
        color: #718195;
        display: block;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: .08em;
        margin-bottom: 6px;
        text-transform: uppercase;
    }

    .deployment-status-card strong {
        color: #17324d;
        font-size: 17px;
        word-break: break-word;
    }

    .deployment-section-title {
        color: #17324d;
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 14px;
    }

    .deployment-operation-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .deployment-operation-card {
        background: #fff;
        border: 1px solid #e2e9f0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(31, 56, 82, .06);
        display: flex;
        flex-direction: column;
        min-height: 255px;
        padding: 20px;
    }

    .deployment-operation-icon {
        align-items: center;
        background: #edf5fb;
        border-radius: 10px;
        color: #1769aa;
        display: flex;
        font-size: 20px;
        height: 46px;
        justify-content: center;
        margin-bottom: 16px;
        width: 46px;
    }

    .deployment-operation-card h3 {
        color: #17324d;
        font-size: 18px;
        font-weight: 600;
        margin: 0 0 9px;
    }

    .deployment-operation-card p {
        color: #6c7d90;
        flex: 1;
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .deployment-confirmation {
        margin-bottom: 12px;
    }

    .deployment-confirmation label {
        color: #58697a;
        display: block;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .deployment-confirmation code {
        color: #b33b31;
    }

    .deployment-output {
        background: #12202e;
        border: 0;
        border-radius: 8px;
        color: #d7e6f3;
        font-family: Consolas, Monaco, monospace;
        font-size: 12px;
        line-height: 1.55;
        margin: 12px 0 0;
        max-height: 360px;
        overflow: auto;
        padding: 16px;
        white-space: pre-wrap;
    }

    @media (max-width: 1199px) {
        .deployment-status-grid,
        .deployment-operation-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .deployment-page .content-header,
        .deployment-page .content {
            padding-left: 15px;
            padding-right: 15px;
        }

        .deployment-status-grid,
        .deployment-operation-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="content-wrapper deployment-page">
    <section class="content-header">
        <h1><i class="fa fa-cogs"></i> Despliegue y mantenimiento</h1>
        <p>Operaciones administrativas de Laravel disponibles únicamente para superadministradores.</p>
    </section>

    <section class="content">
        <div class="deployment-notice">
            <i class="fa fa-info-circle" aria-hidden="true"></i>
            <div>
                <strong>Este panel no ejecuta Git.</strong>
                Primero actualice los archivos del servidor por el medio habitual. Después puede finalizar el despliegue o ejecutar una operación específica desde aquí.
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>No se ejecutó la operación.</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @php($result = session('deployment_result'))
        @if ($result)
            <div class="alert {{ $result['successful'] ? 'alert-success' : 'alert-danger' }}">
                <strong>{{ $result['title'] }}:</strong>
                {{ $result['successful'] ? 'operación completada.' : 'la operación terminó con errores.' }}
                <pre class="deployment-output">{{ $result['output'] }}</pre>
            </div>
        @endif

        <h2 class="deployment-section-title">Estado del servidor</h2>
        <div class="deployment-status-grid">
            <div class="deployment-status-card">
                <span>Entorno</span>
                <strong>{{ $status['environment'] }}</strong>
            </div>
            <div class="deployment-status-card">
                <span>Versiones</span>
                <strong>Laravel {{ $status['laravel'] }} · PHP {{ $status['php'] }}</strong>
            </div>
            <div class="deployment-status-card">
                <span>Aplicación</span>
                <strong class="text-{{ $status['maintenance'] ? 'danger' : 'success' }}">
                    {{ $status['maintenance'] ? 'En mantenimiento' : 'Disponible' }}
                </strong>
            </div>
            <div class="deployment-status-card">
                <span>Frontend compilado</span>
                <strong class="text-{{ $status['manifest'] ? 'success' : 'danger' }}">
                    {{ $status['manifest'] ? 'Manifest disponible' : 'Manifest faltante' }}
                </strong>
            </div>
            <div class="deployment-status-card">
                <span>Depuración</span>
                <strong class="text-{{ $status['debug'] ? 'warning' : 'success' }}">
                    {{ $status['debug'] ? 'Activada' : 'Desactivada' }}
                </strong>
            </div>
            <div class="deployment-status-card">
                <span>Base de datos</span>
                <strong>{{ $status['database'] }}</strong>
            </div>
            <div class="deployment-status-card">
                <span>Caché</span>
                <strong>{{ $status['cache'] }}</strong>
            </div>
            <div class="deployment-status-card">
                <span>Acceso</span>
                <strong>Superadministrador</strong>
            </div>
        </div>

        <h2 class="deployment-section-title">Operaciones disponibles</h2>
        <div class="deployment-operation-grid">
            @foreach ($operations as $operationKey => $operation)
                @php($deployUnavailable = $operationKey === 'deploy' && ! $status['manifest'])
                <article class="deployment-operation-card">
                    <div class="deployment-operation-icon">
                        <i class="fa {{ $operation['icon'] }}" aria-hidden="true"></i>
                    </div>
                    <h3>{{ $operation['label'] }}</h3>
                    <p>{{ $operation['description'] }}</p>

                    <form method="POST"
                          action="{{ route('deployment.execute') }}"
                          onsubmit="return window.confirm('¿Confirma que desea ejecutar esta operación?');">
                        @csrf
                        <input type="hidden" name="operation" value="{{ $operationKey }}">

                        @if ($operation['confirmation'])
                            <div class="deployment-confirmation">
                                <label for="confirmation-{{ $operationKey }}">
                                    Escriba <code>{{ $operation['confirmation'] }}</code> para confirmar
                                </label>
                                <input id="confirmation-{{ $operationKey }}"
                                       name="confirmation"
                                       type="text"
                                       class="form-control"
                                       autocomplete="off"
                                       spellcheck="false"
                                       required>
                            </div>
                        @endif

                        <button type="submit"
                                class="btn {{ $operation['button'] }} btn-block"
                                @disabled($deployUnavailable)>
                            <i class="fa {{ $operation['icon'] }}"></i>
                            {{ $deployUnavailable ? 'Compile el frontend primero' : $operation['label'] }}
                        </button>
                    </form>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
