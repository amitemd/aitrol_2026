@extends('contable.diario.base')
@section('action-content')


<section class="content">
    <div class="container">
        <table id="example2" class="table">
            <thead>
                <tr>
                    <th>Id Asiento</th>
                    <th>Cuenta</th>
                    <th>Detalle</th>
                </tr>
            </thead>

            <tbody>
                @foreach($data as $value)
                <tr>
                    <td> <a target="_blank" href="{{ route('librodiario.edit', ['id' => $value['id_asiento']]) }}">{{$value['id_asiento']}}</a> </td>
                    <td>
                        @if($value['estado'] == 0)
                        <label class="label label-danger">{{$value['cuenta']}}</label>
                        @elseif($value['estado'] == 2)
                        <label class="label label-danger">{{$value['cuenta']}}</label>
                        @endif
                    </td>
                    <td> {{$value['detalle']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</section>


@endsection