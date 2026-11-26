<table style="width: 100%;">
        <tbody>
            <tr>
                <td><b>N.º</b></td>
                <td style="width: 33%;"><b> DIAGNÓSTICOS </b></td>
                <td><b> TIEMPO DE EVOLUCION </b></td>
                <td><b> TRATAMIENTO </b></td>
                <td><b>ORIGEN</b></td>
            </tr>
            @php
                $contador = 1;
                $antecedentesPaciente = $antecedentes_paciente ?? collect();
            @endphp

            @foreach ($antecedentesPaciente as $antecedentePaciente)
                <tr class="v3-preloaded-background">
                    <td>{{ $contador++ }}</td>
                    <td>
                        <input style="width: 98%;" type="text" value="{{ $antecedentePaciente->diagnostico }}" disabled>
                    </td>
                    <td>
                        <input style="width: 98%;" type="text" value="{{ $antecedentePaciente->tiempo_evolucion ?: 'No registrado previamente' }}" disabled>
                    </td>
                    <td>
                        <input style="width: 98%;" type="text" value="{{ $antecedentePaciente->tratamiento ?: 'No registrado previamente' }}" disabled>
                    </td>
                    <td>
                        <span class="v3-source-badge"><i class="fa fa-database" aria-hidden="true"></i> {{ $antecedentePaciente->origen ?? 'Historia médica' }}</span>
                    </td>
                </tr>
            @endforeach

            @if($antecedentesPaciente->isEmpty())
                <tr>
                    <td colspan="5" class="v3-empty-background">No registra antecedentes médicos.</td>
                </tr>
            @endif
        </tbody>
    </table>
