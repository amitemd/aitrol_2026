@extends('layouts.adminlte')

@section('title', 'Anular documento')
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <form class="card" method="POST" action="{{ route($modulo.'.'.$seccion.'.anular', $documento->id) }}" data-ajax-form>
                @csrf
                <h1 class="card-header fs-5">Anular documento</h1>
                @include('comercial::errores')
                <div class="card-body">
                    <p><strong>{{ $documento->numero }}</strong> · {{ $documento->tercero }}</p>
                    @if ($documento->id_bodega)
                        <p class="text-body-secondary">Al anular se revertirán los movimientos de inventario asociados.</p>
                    @endif
                    <label class="form-label" for="com-motivo">Motivo de anulación</label>
                    <textarea class="form-control" id="com-motivo" name="motivo" rows="3" maxlength="250" required>{{ old('motivo') }}</textarea>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route($modulo.'.'.$seccion.'.index') }}" data-ajax-close>{{ __('Cancelar') }}</a>
                    <button class="btn btn-danger">Confirmar anulación</button>
                </div>
            </form>
        </div>
    </div>
@endsection
