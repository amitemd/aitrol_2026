@extends('layouts.adminlte')

@section('title', 'Importar retención recibida')
@section('content')
    <div class="app-content py-3">
        <div class="container-fluid">
            <form class="card" method="POST" enctype="multipart/form-data" action="{{ route('ventas.retenciones.importar') }}" data-ajax-form>
                @csrf
                <h1 class="card-header fs-5">Importar retención recibida</h1>
                @include('comercial::errores')
                <div class="card-body">
                    @if ($origen)
                        <input type="hidden" name="id_documento_origen" value="{{ $origen->id }}">
                        <p>Factura {{ $origen->numero }} · {{ $origen->tercero }}</p>
                    @else
                        <p>La retención se vinculará a la factura de venta indicada en el comprobante.</p>
                    @endif
                    <p class="text-body-secondary">Adjunta el XML o ingresa la clave para consultar el SRI.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="retencion-xml" class="form-label">Archivo XML</label>
                            <input id="retencion-xml" type="file" name="xml_retencion" class="form-control" accept=".xml,application/xml,text/xml">
                        </div>
                        <div class="col-md-6">
                            <label for="retencion-clave" class="form-label">Clave de acceso</label>
                            <input id="retencion-clave" name="clave_acceso" class="form-control" inputmode="numeric"
                                maxlength="49" pattern="[0-9]{49}" placeholder="49 dígitos" value="{{ old('clave_acceso') }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <a href="{{ route('ventas.retenciones.create', $origen ? ['origen' => $origen->id] : []) }}" class="btn btn-outline-secondary" data-ajax-modal-page>Volver al registro manual</a>
                    <button class="btn btn-primary" type="submit">Importar retención</button>
                </div>
            </form>
        </div>
    </div>
@endsection
