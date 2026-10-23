<table style="width: 100%;">
        <tbody>
            <tr>
                <td colspan="5" style="font-size: 14px;" class="bg-purple"><b>ANTECEDENTES PATOLÓGICOS PERSONALES</b></td>
            </tr>
            <tr>
                <td></td>
                <td style="width: 33%;"><b> DIAGNÓSTICOS </b></td>
                <td><b> TIEMPO DE EVOLUCION </b></td>
                <td colspan="2"><b> TRATAMIENTO </b></td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <input style="width: 98%;" type="text" name="diagnosticos" id="diagnosticos" required>
                </td>
                <td>
                    <input style="width: 98%;" type="text" name="tiempoEvolucion" id="tiempoEvolucion" required>
                </td>
                <td>
                    <input style="width: 98%;" type="text" name="tratamiento" id="tratamiento" required>
                </td>
                <td style="text-align: center;">
                    <button class="btn btn-primary bg-green" id="boton_enviar" onclick="guardar();" type="button">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                    </button>
                </td>
            </tr>
            <tr>
                <td colspan="4">
                    <hr>
                </td>
            </tr>

            @php
            $contador = 1;
            
            @endphp

            @foreach ($rc_antecedentes as $rc)

            <tr>
                <td>{{ $contador++ }}</td>
                <td>
                    <input style="width: 98%;" type="text" value="{{ $rc->diagnostico }}" disabled>
                </td>
                <td>
                    <input style="width: 98%;" type="text" value="{{$rc->tiempo_evolucion}}" disabled>
                </td>
                <td>
                    <input style="width: 98%;" type="text" value="{{$rc->tratamiento}}" disabled>
                </td>
                <td>
                    <a onclick="eliminar({{ $rc->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
                <td>
                    <hr>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>