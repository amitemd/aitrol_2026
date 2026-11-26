<table style="width: 100%;">
        <tbody>
            @php
            $contador = 1;
            @endphp

            @foreach ($indicaciones as $indicacione)
            <tr>
                <td>{{ $contador++ }}</td>
                <td>
                    <input style="width: 100%;" type="text" value="{{ $indicacione->indicaciones }}" disabled>
                </td>
                <td>
                    <span class="v3-source-badge"><i class="fa fa-user-md" aria-hidden="true"></i> {{ $indicacione->origen ?? 'Área médica' }}</span>
                </td>
            </tr>
            @endforeach
            @if($indicaciones->isEmpty())
                <tr><td colspan="3" class="v3-empty-background">No registra indicaciones médicas.</td></tr>
            @endif
        </tbody>
    </table>
