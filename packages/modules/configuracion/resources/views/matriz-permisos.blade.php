<div class="table-responsive border rounded">
    <table class="table align-middle mb-0">
        <thead><tr><th scope="col">Módulo u opción</th>
            @foreach (['Leer', 'Escribir', 'Actualizar', 'Eliminar'] as $titulo)<th scope="col">{{ $titulo }}</th>@endforeach
        </tr></thead>
        <tbody>
            @foreach ($opciones as $opcion => $acciones)
                <tr>
                    <th scope="row" class="fw-normal">{{ $opcion }}</th>
                    @foreach (['leer', 'escribir', 'actualizar', 'eliminar'] as $accion)
                        @php($permiso = $acciones->get($accion))
                        <td>
                            @if ($permiso && isset($directos))
                                <select name="directos[{{ $permiso->id }}]" class="form-select form-select-sm" aria-label="{{ $opcion }} · {{ $accion }}">
                                    @php($valor = old('directos.'.$permiso->id, isset($directos[$permiso->id]) ? (string) (int) $directos[$permiso->id] : ''))
                                    <option value="" @selected($valor === '')>Heredar</option>
                                    <option value="1" @selected((string) $valor === '1')>Permitir</option>
                                    <option value="0" @selected((string) $valor === '0')>Denegar</option>
                                </select>
                            @elseif ($permiso)
                                <input type="checkbox" class="form-check-input" name="permisos[]" value="{{ $permiso->id }}"
                                    aria-label="{{ $opcion }} · {{ $accion }}" @checked(in_array($permiso->id, old('permisos', $seleccion)))>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>