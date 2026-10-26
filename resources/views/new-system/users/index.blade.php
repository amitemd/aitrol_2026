<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold text-cyan-700">Seguridad y accesos</p>
            <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">Usuarios del sistema</h2>
            <p class="mt-2 text-sm text-slate-500">Administra los usuarios compartidos con AITROL legado.</p>
        </div>
        <a href="{{ route('new-system.users.create') }}" class="inline-flex justify-center rounded-xl bg-slate-900 px-4 py-3 text-sm font-bold text-white transition hover:bg-cyan-600">Crear usuario</a>
    </div>

    <form method="GET" action="{{ route('new-system.users.index') }}" class="mt-6 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row">
        <label class="flex-1">
            <span class="sr-only">Buscar usuarios</span>
            <input type="search" name="buscar" value="{{ $search }}" placeholder="Buscar por cédula, correo, nombre o apellido…" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
        </label>
        <button type="submit" class="rounded-xl bg-cyan-500 px-5 py-3 text-sm font-bold text-white transition hover:bg-cyan-600">Buscar</button>
        @if ($search !== '')
            <a href="{{ route('new-system.users.index') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-center text-sm font-semibold text-slate-600 hover:bg-slate-50">Limpiar</a>
        @endif
    </form>

    <div class="mt-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-left">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold tracking-wider text-slate-500 uppercase">
                        <th class="px-5 py-4">Usuario</th>
                        <th class="px-5 py-4">Identificación</th>
                        <th class="px-5 py-4">Rol</th>
                        <th class="px-5 py-4">Empresas</th>
                        <th class="px-5 py-4">Estado</th>
                        <th class="px-5 py-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $usuario)
                        <tr class="text-sm text-slate-700 transition hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-900">{{ trim($usuario->nombre1.' '.$usuario->nombre2.' '.$usuario->apellido1.' '.$usuario->apellido2) }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $usuario->email }}</p>
                            </td>
                            <td class="px-5 py-4 font-mono text-xs">{{ $usuario->id }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ (int) $usuario->id_tipo_usuario === 1 ? 'bg-violet-50 text-violet-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ (int) $usuario->id_tipo_usuario === 1 ? 'Superadministrador' : 'Rol '.$usuario->id_tipo_usuario }}
                                </span>
                            </td>
                            <td class="px-5 py-4">{{ $usuario->companies_count }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-2 text-xs font-semibold {{ (int) $usuario->estado === 1 ? 'text-emerald-700' : 'text-red-700' }}">
                                    <span class="h-2 w-2 rounded-full {{ (int) $usuario->estado === 1 ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                    {{ (int) $usuario->estado === 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <a href="{{ route('new-system.users.edit', $usuario) }}" class="inline-flex rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-cyan-300 hover:text-cyan-700">Editar</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-500">No se encontraron usuarios.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">
        {{ $users->links() }}
    </div>
</x-new-system.layout>
