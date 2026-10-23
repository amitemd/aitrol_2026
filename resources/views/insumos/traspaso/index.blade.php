@section('traspaso.base')
<div class="container">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-12">
                <label> Traspaso Bodega </label>
            </div>
            <div class="col-md-12">
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bodega</th>
                            <th>Nombre</th>
                            <th>Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($a as $a)
                            
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection