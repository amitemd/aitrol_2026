@php
    $isEditing = isset($usuario);
    $companySelection = array_map('strval', old('company_ids', $selectedCompanies));
@endphp

@if ($errors->any())
    <div class="mb-6 rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
        <p class="font-semibold">Revisa la información ingresada:</p>
        <ul class="mt-2 list-disc space-y-1 pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid gap-6 lg:grid-cols-[minmax(0,1.4fr)_minmax(18rem,0.6fr)]">
    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-7">
        <h3 class="text-lg font-semibold text-slate-900">Información personal y acceso</h3>
        <div class="mt-6 grid gap-5 sm:grid-cols-2">
            <label class="flex flex-col gap-2">
                <span class="text-sm font-semibold text-slate-700">Identificación</span>
                <input type="text" name="id" value="{{ old('id', $usuario->id ?? '') }}" maxlength="13" required @disabled($isEditing) class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition disabled:bg-slate-100 disabled:text-slate-500 focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-sm font-semibold text-slate-700">Correo electrónico</span>
                <input type="email" name="email" value="{{ old('email', $usuario->email ?? '') }}" maxlength="191" required autocomplete="off" class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-sm font-semibold text-slate-700">Primer nombre</span>
                <input type="text" name="nombre1" value="{{ old('nombre1', $usuario->nombre1 ?? '') }}" maxlength="60" required class="rounded-xl border border-slate-200 px-4 py-3 text-sm uppercase outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-sm font-semibold text-slate-700">Segundo nombre</span>
                <input type="text" name="nombre2" value="{{ old('nombre2', $usuario->nombre2 ?? '') }}" maxlength="60" class="rounded-xl border border-slate-200 px-4 py-3 text-sm uppercase outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-sm font-semibold text-slate-700">Primer apellido</span>
                <input type="text" name="apellido1" value="{{ old('apellido1', $usuario->apellido1 ?? '') }}" maxlength="60" required class="rounded-xl border border-slate-200 px-4 py-3 text-sm uppercase outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-sm font-semibold text-slate-700">Segundo apellido</span>
                <input type="text" name="apellido2" value="{{ old('apellido2', $usuario->apellido2 ?? '') }}" maxlength="60" required class="rounded-xl border border-slate-200 px-4 py-3 text-sm uppercase outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-sm font-semibold text-slate-700">Contraseña {{ $isEditing ? '(opcional)' : '' }}</span>
                <input type="password" name="password" @required(! $isEditing) minlength="12" autocomplete="new-password" class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
                <span class="text-xs text-slate-500">Mínimo 12 caracteres.</span>
            </label>
            <label class="flex flex-col gap-2">
                <span class="text-sm font-semibold text-slate-700">Confirmar contraseña</span>
                <input type="password" name="password_confirmation" @required(! $isEditing) minlength="12" autocomplete="new-password" class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
            </label>
        </div>
    </div>

    <div class="flex flex-col gap-6">
        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold text-slate-900">Permisos</h3>
            <div class="mt-5 flex flex-col gap-4">
                <label class="flex flex-col gap-2">
                    <span class="text-sm font-semibold text-slate-700">Rol</span>
                    <select name="id_tipo_usuario" required class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
                        <option value="">Seleccione…</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected((string) old('id_tipo_usuario', $usuario->id_tipo_usuario ?? '') === (string) $role->id)>{{ $role->nombre }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="flex flex-col gap-2">
                    <span class="text-sm font-semibold text-slate-700">Estado</span>
                    <select name="estado" required class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm outline-none transition focus:border-cyan-400 focus:ring-4 focus:ring-cyan-100">
                        <option value="1" @selected((string) old('estado', $usuario->estado ?? 1) === '1')>Activo</option>
                        <option value="0" @selected((string) old('estado', $usuario->estado ?? 1) === '0')>Inactivo</option>
                    </select>
                </label>
            </div>
        </div>

        <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h3 class="text-lg font-semibold text-slate-900">Empresas asignadas</h3>
            <div class="mt-4 flex max-h-64 flex-col gap-2 overflow-y-auto pr-1">
                @foreach ($newSystemCompanies as $company)
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 p-3 transition hover:border-cyan-300 hover:bg-cyan-50/50">
                        <input type="checkbox" name="company_ids[]" value="{{ $company->id }}" @checked(in_array((string) $company->id, $companySelection, true)) class="mt-1 h-4 w-4 rounded border-slate-300 text-cyan-600 focus:ring-cyan-400">
                        <span>
                            <span class="block text-sm font-semibold text-slate-800">{{ $company->nombrecomercial }}</span>
                            <span class="block text-xs text-slate-500">{{ $company->id }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
    <a href="{{ route('new-system.users.index') }}" class="rounded-xl border border-slate-200 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50">Cancelar</a>
    <button type="submit" class="rounded-xl bg-slate-900 px-6 py-3 text-sm font-bold text-white transition hover:bg-cyan-600">{{ $isEditing ? 'Guardar cambios' : 'Crear usuario' }}</button>
</div>
