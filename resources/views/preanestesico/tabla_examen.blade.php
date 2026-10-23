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
                    <a onclick="eliminar_examen({{ $ex->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                </td>
                <td> 
                    <hr>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>