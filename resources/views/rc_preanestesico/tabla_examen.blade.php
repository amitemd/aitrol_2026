<table style="width: 100%;">
        <tbody>
            @php
            $contador = 1;
            @endphp

            @foreach ($examen as $ex)
            <tr>
                <td>{{ $contador++ }}</td>
                <td>
                    <input style="width: 98%;" type="text" value="{{ $ex->nombre }}" disabled>
                </td>
                <td>
                    <input style="width: 98%;" type="text" value="{{$ex->resultado}}" disabled>
                </td>
                <td>
                    <span class="v3-source-badge"><i class="fa fa-flask" aria-hidden="true"></i> {{ $ex->origen ?? 'Área médica' }}</span>
                </td>
            </tr>
            @endforeach
            @if($examen->isEmpty())
                <tr><td colspan="4" class="v3-empty-background">No registra exámenes ni resultados médicos.</td></tr>
            @endif
        </tbody>
    </table>
