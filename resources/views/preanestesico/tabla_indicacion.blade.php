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
                <a onclick="eliminar_indicaciones({{ $indicacione->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
                <td>
                    <hr>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>