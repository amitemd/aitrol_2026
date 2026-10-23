    <table style="width: 100%;">
        <tbody>
            @php
            $contador = 1;
            @endphp
            @foreach ($drogas as $rc)
            <tr>
                <td>{{ $contador++ }}</td>
                <td>
                    <input style="width: 98%;" type="text" value="{{ $rc->drogas }}" disabled>
                </td>
                <td>
                    <a onclick="eliminar_droga({{ $rc->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
                <td>
                    <hr>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>