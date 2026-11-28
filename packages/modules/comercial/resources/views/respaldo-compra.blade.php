@extends('layouts.adminlte')

@section('title', 'Subir archivo de respaldo')
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <form class="card" method="POST" enctype="multipart/form-data"
                action="{{ route('compras.documentos.respaldo.store', $documento['id']) }}" data-ajax-form>
                @csrf
                <h1 class="card-header fs-5">Subir archivo de respaldo</h1>
                @include('comercial::errores')
                <div class="card-body">
                    <p><strong>{{ $documento['numero'] }}</strong> · {{ $documento['tercero'] }}</p>
                    <label class="form-label" for="compra-respaldo">Selecciona un PDF o una imagen</label>
                    <input type="file" class="form-control" id="compra-respaldo" name="archivo"
                        accept=".pdf,.jpg,.jpeg,.png,.webp" aria-describedby="compra-respaldo-formatos" required>
                    <div class="form-text" id="compra-respaldo-formatos">PDF, JPG, PNG o WebP. Tamaño máximo de 10 MB.</div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a class="btn btn-outline-secondary" href="{{ route('compras.documentos.index') }}" data-ajax-close>Cancelar</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1" aria-hidden="true"></i> Subir archivo</button>
                </div>
            </form>
        </div>
    </div>
@endsection
