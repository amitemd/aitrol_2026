@php
    $examFieldGroups = \Sis_medico\Services\HospitalEpisodePhysicalExam::fieldGroups();
    $regionalExam = $examFieldGroups['Regional'];
    $systemicExam = $examFieldGroups['Sistémico'];
    $regionalCodes = array_keys($regionalExam);
    $systemicCodes = array_keys($systemicExam);
    $examHasValue = function ($option) use ($form008) {
        return trim((string) $form008->{$option['field']}) !== '';
    };
@endphp

<div style="margin-top: 10px">
    <table class="tabla1" cellspacing="0">
        <thead>
            <tr class="titulo">
                <th colspan="46">H. EXAMEN FÍSICO</th>
                <th class="tituloc3" colspan="19">MARCAR "X" CUANDO PRESENTE PATOLOGÍA Y DESCRIBA</th>
            </tr>
            <tr>
                <th class="subtitulo2" colspan="39">REGIONAL</th>
                <th class="subtitulo2" colspan="26">SISTÉMICO</th>
            </tr>
        </thead>
        <tbody>
            @for($row = 0; $row < 5; $row++)
                @php
                    $rowOptions = [
                        [$regionalCodes[$row], $regionalExam[$regionalCodes[$row]]],
                        [$regionalCodes[$row + 5], $regionalExam[$regionalCodes[$row + 5]]],
                        [$regionalCodes[$row + 10], $regionalExam[$regionalCodes[$row + 10]]],
                        [$systemicCodes[$row], $systemicExam[$systemicCodes[$row]]],
                        [$systemicCodes[$row + 5], $systemicExam[$systemicCodes[$row + 5]]],
                    ];
                @endphp
                <tr>
                    @foreach($rowOptions as [$code, $option])
                        <td class="subtitulo2" colspan="2">{{ $code }}</td>
                        <td class="subtitulo2" colspan="9">{{ mb_strtoupper($option['label'], 'UTF-8') }}</td>
                        <td class="campos" colspan="2">{{ $examHasValue($option) ? 'X' : '' }}</td>
                    @endforeach
                </tr>
            @endfor
            <tr>
                <td class="campos_left" height="10px" colspan="65">
                    @foreach($examFieldGroups as $group => $options)
                        @foreach($options as $code => $option)
                            @if($examHasValue($option))
                                <strong>{{ $code }}.</strong> {{ $form008->{$option['field']} }}<br>
                            @endif
                        @endforeach
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>
</div>
