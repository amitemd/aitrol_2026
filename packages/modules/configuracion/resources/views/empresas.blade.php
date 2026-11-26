@extends('layouts.adminlte')

@section('title', 'Empresas')

@section('content')
    @include('partials.listado-cabecera', [
        'titulo' => 'Empresas', 'subtitulo' => 'Configuraciones generales', 'icono' => 'bi-buildings',
        'crearUrl' => route('configuracion.empresas.index', ['crear' => 1]), 'crearTexto' => 'Nueva empresa',
    ])
    <div class="app-content">
        <div class="container-fluid">
            @if (session('estado'))
                <div class="alert alert-success" role="status">{{ session('estado') }}</div>
            @endif
            @include('partials.listado-modal')
            <div class="row g-3 align-items-start">
                <div class="col-12">
                    <section class="card clientes-directorio" data-ajax-list>
                        @include('partials.listado-busqueda', ['listadoUrl' => route('configuracion.empresas.index'), 'placeholder' => 'Razón social, nombre comercial o RUC'])
                        <div class="small text-body-secondary px-3 py-2 border-bottom">{{ $empresas->total() }} empresas registradas</div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="clientes-tabla-cabecera"><tr><th scope="col">Razón social</th><th scope="col">RUC</th><th scope="col">Contacto</th></tr></thead>
                                <tbody>
                                    @forelse ($empresas as $empresa)
                                        <tr>
                                            <th scope="row">
                                                {{ $empresa->razon_social }}
                                                @if ($empresa->nombre_comercial)
                                                    <small class="d-block fw-normal text-body-secondary">{{ $empresa->nombre_comercial }}</small>
                                                @endif
                                            </th>
                                            <td class="text-nowrap">{{ $empresa->ruc }}</td>
                                            <td>
                                                @if ($empresa->email) <div>{{ $empresa->email }}</div> @endif
                                                @if ($empresa->telefono) <div>{{ $empresa->telefono }}</div> @endif
                                                @if (!$empresa->email && !$empresa->telefono) <span class="text-body-secondary">Sin contacto</span> @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="p-4 text-center text-body-secondary">Todavía no hay empresas registradas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($empresas->hasPages())
                            <div class="card-footer">{{ $empresas->links('pagination::bootstrap-5') }}</div>
                        @endif
                    </section>
                </div>
                @if (request()->boolean('crear') || $errors->any())
                <div class="col-lg-8">
                    <form class="card shadow-sm" method="POST" data-ajax-form action="{{ route('configuracion.empresas.store') }}">
                        @csrf
                        <div class="card-header"><h2 class="card-title mb-0">Crear empresa</h2></div>
                        <div class="card-body row g-3">
                            @foreach ([
                                'razon_social' => ['Razón social', 'text', 160, true],
                                'ruc' => ['RUC', 'text', 13, true],
                                'nombre_comercial' => ['Nombre comercial', 'text', 160, false],
                                'email' => ['Correo electrónico', 'email', 254, false],
                                'telefono' => ['Teléfono', 'tel', 30, false],
                                'direccion' => ['Dirección', 'text', 255, false],
                            ] as $campo => [$etiqueta, $tipo, $limite, $obligatorio])
                                <div @class(['col-12', 'col-md-6' => in_array($campo, ['email', 'telefono'], true)])>
                                    <label for="{{ $campo }}" class="form-label fw-semibold">{{ $etiqueta }}</label>
                                    <input id="{{ $campo }}" name="{{ $campo }}" type="{{ $tipo }}" maxlength="{{ $limite }}"
                                        class="form-control @error($campo) is-invalid @enderror" value="{{ old($campo) }}"
                                        @required($obligatorio) @if ($campo === 'ruc') inputmode="numeric" pattern="[0-9]{13}" @endif>
                                    @error($campo) <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            @endforeach
                        </div>
                        <div class="card-footer d-flex justify-content-end gap-2">
                            <a class="btn btn-outline-secondary" href="{{ route('configuracion.empresas.index') }}" data-ajax-close>Cancelar</a>
                            <button type="submit" class="btn btn-primary">Registrar empresa</button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection