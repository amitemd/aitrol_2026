<div id="load" style="position: relative;">
    <table class="table table-bordered">
        <thead class="thead-light">
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Descripcion</th>
        </tr>
        </thead>
        <tbody>
        @foreach($proc as $pro)
            <tr>
                <td width="50px">
                {{$pro->id}}</th>
                <td>{{$pro->descripcion }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

{!! $proc->render() !!}