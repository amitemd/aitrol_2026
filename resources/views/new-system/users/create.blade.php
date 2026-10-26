<x-new-system.layout
    :title="$title"
    :user="$newSystemUser"
    :companies="$newSystemCompanies"
    :current-company="$newSystemCurrentCompany"
>
    <form method="POST" action="{{ route('new-system.users.store') }}">
        @csrf
        @include('new-system.users._form')
    </form>
</x-new-system.layout>
