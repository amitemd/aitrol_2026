<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    <div class="mb-6">
        <p class="text-sm font-semibold text-cyan-700">Administración de usuarios</p>
        <h2 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ trim($usuario->nombre1.' '.$usuario->apellido1) }}</h2>
        <p class="mt-2 text-sm text-slate-500">Actualiza el acceso sin alterar la información histórica del usuario.</p>
    </div>

    <form method="POST" action="{{ route('new-system.users.update', $usuario) }}">
        @csrf
        @method('PUT')
        @include('new-system.users._form')
    </form>
</x-new-system.layout>
