@extends('preanestesico.anestesico_v3_base')
@section('anestesico-type', 'Postanestésico')
@section('anestesico-active', 'post')
@section('anestesico-pdf-route'){{ route('formato.pdf_019', ['id_historia' => $post_anestesico->hc_id_procedimiento]) }}@endsection
@section('anestesico-pre-route'){{ route('formato.rc_preanestesico_crear', ['id' => $post_anestesico->hc_anestesiologia->id]) }}@endsection
@section('anestesico-trans-route'){{ route('formato.transanestesico_crear', ['id' => $post_anestesico->hc_anestesiologia->id]) }}@endsection
@section('anestesico-post-route'){{ url()->current() }}@endsection
