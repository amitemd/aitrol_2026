@extends('layouts.adminlte')

@section('title', 'Empresas')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <p class="text-body-secondary mb-1">Configuraciones generales</p>
            <h1 class="fs-3 mb-0">Empresas</h1>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            @if (session('estado'))
                <div class="alert alert-success" role="status">{{ session('estado') }}</div>
            @endif
            <div class="row g-3 align-items-start">
                <div class="col-xl-7">
                    <section class="card shadow-sm">
                        <div class="card-header"><h2 class="card-title mb-0">Empresas registradas</h2></div>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead><tr><th scope="col">Razón social</th><th scope="col">RUC</th><th scope="col">Contacto</th></tr></thead>
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
                <div class="col-xl-5">
                    <form class="card shadow-sm" method="POST" action="{{ route('configuracion.empresas.store') }}">
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
                        <div class="card-footer text-end"><button type="submit" class="btn btn-primary">Registrar empresa</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection