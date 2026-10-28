@props([
    'success' => null,
    'error' => null,
    'warning' => null,
    'info' => null,
    'includeValidationErrors' => true,
    'loadLibrary' => true,
])

@php
    $validationMessages = $includeValidationErrors && isset($errors) && $errors->any()
        ? $errors->all()
        : [];
    $alertConfiguration = [
        'messages' => [
            'success' => $success ?? session('status') ?? session('success'),
            'error' => $error ?? session('error') ?? ($validationMessages !== [] ? implode("\n", $validationMessages) : null),
            'warning' => $warning ?? session('warning'),
            'info' => $info ?? session('info'),
        ],
        'labels' => [
            'success' => __('new_system.alerts.success'),
            'error' => $validationMessages !== [] ? __('new_system.alerts.review_information') : __('new_system.alerts.error'),
            'warning' => __('new_system.alerts.warning'),
            'info' => __('new_system.alerts.information'),
            'confirmation' => __('new_system.alerts.confirmation'),
            'confirm' => __('new_system.alerts.confirm'),
            'cancel' => __('new_system.alerts.cancel'),
        ],
    ];
    $cspNonce = request()->attributes->get('csp_nonce');
@endphp

<script @if ($cspNonce) nonce="{{ $cspNonce }}" @endif>
    window.AitrolAlertConfiguration = {{ Js::from($alertConfiguration) }};
</script>
@if ($loadLibrary)
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" @if ($cspNonce) nonce="{{ $cspNonce }}" @endif></script>
@endif
<script src="{{ asset('js/new-system-alerts.js') }}?v={{ filemtime(public_path('js/new-system-alerts.js')) }}" @if ($cspNonce) nonce="{{ $cspNonce }}" @endif></script>
