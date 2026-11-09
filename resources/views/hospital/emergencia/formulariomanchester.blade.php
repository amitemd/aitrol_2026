@extends('layouts.app-template-h')
@section('content')
<link rel="stylesheet" href="{{ asset('css/emergency-admission.css') }}?v=4">
<link rel="stylesheet" href="{{ asset('css/emergency-classification.css') }}?v=4">

<main class="ea-page">
    <a class="ea-back" href="{{ route('hospital.emergencia') }}"><i class="fas fa-arrow-left" aria-hidden="true"></i> Volver a Emergencia</a>
    <header class="ea-heading">
        <div><span class="ea-eyebrow">EMERGENCIA / NUEVO INGRESO</span><h1>Ingreso de paciente</h1><p>Guarde el ingreso con lo mínimo. El equipo podrá completar los datos y el triaje durante la atención.</p></div>
        <span class="ea-heading-icon" aria-hidden="true"><i class="fas fa-user-plus"></i></span>
    </header>

    @include('hospital.emergencia.admission_form')
</main>
@include('hospital.emergencia.registro_paciente')
<script defer src="{{ asset('js/jquery-ui.js') }}"></script>
<script defer src="{{ asset('js/emergency-patient-registration.js') }}?v=8"></script>
<script defer src="{{ asset('js/emergency-classification.js') }}?v=6"></script>
<script defer src="{{ asset('js/emergency-admission.js') }}?v=8"></script>
@endsection
