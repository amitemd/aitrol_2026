@extends('preanestesico.anestesico_v3_base')
@section('anestesico-type', 'Transanestésico')
@section('anestesico-active', 'trans')
@section('anestesico-pdf-route'){{ route('formulario.018A', ['id' => $transanestesico->id]) }}@endsection
@section('anestesico-pre-route'){{ route('formato.rc_preanestesico_crear', ['id' => $transanestesico->id]) }}@endsection
@section('anestesico-trans-route'){{ url()->current() }}@endsection
@section('anestesico-post-route'){{ route('formato.rc_postanestesico_crear', ['id' => $transanestesico->id]) }}@endsection
