@foreach (['Creado por', 'Creado el', 'Modificado por', 'Modificado el'] as $columnaAuditoria)
    <th scope="col" class="text-nowrap">{{ __($columnaAuditoria) }}</th>
@endforeach
