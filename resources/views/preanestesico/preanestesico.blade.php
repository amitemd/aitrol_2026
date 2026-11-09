@extends('preanestesico.anestesico_v3_base')
@section('anestesico-type', 'Preanestésico')
@section('anestesico-active', 'pre')
@section('anestesico-pdf-route'){{ route('formulario.018', ['id' => $pre_anestesico->id]) }}@endsection
@section('anestesico-pre-route'){{ url()->current() }}@endsection
@section('anestesico-trans-route'){{ route('formato.transanestesico_crear', ['id' => $pre_anestesico->anestesiologia->id]) }}@endsection
@section('anestesico-post-route'){{ route('formato.rc_postanestesico_crear', ['id' => $pre_anestesico->anestesiologia->id]) }}@endsection
