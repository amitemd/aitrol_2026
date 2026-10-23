@extends('preanestesico.preanestesico')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="{{route('formatoconsent.index')}}">
                {{ csrf_field() }}
                <div class="col-md-8">
                    <h3 class="box-title"> FORMATO PREANESTESICO</h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('formato.preanestesico_crear')}}" class="btn btn-primary">{{('Crear')}} </a>

                </div>
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <tbody>
                                    @foreach ($antecedentes as $pre)

                                        <div><b>Diagnostico</b>
                                            <br>
                                            <textarea style="width: 90%;"  readonly>{{$pre->diagnostico}}</textarea>
                                        </div>
                                        <div> <b> Tiempo de Evolución </b>
                                            <br>
                                            <textarea style="width: 90%;" readonly>{{$pre->tiempo_evolucion}}</textarea>
                                        </div>
                                        <div> <b> Tratamiento </b>
                                            <br>
                                            <textarea  style="width: 90%;" readonly>{{$pre->tratamiento}}</textarea>
                                        </div>
                                        <div>
                                            <a href="{{route('formato.preanestesico_edit',['id' => $pre->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                            <a onclick="eliminar({{$pre->id}})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                        </div>
                                        <br>
                             
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

</section>
<script>
    function eliminar(id) {
        Swal.fire({
            title: `Estas seguro de eliminar`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    url: "{{url('formato/preanestesico/delete/')}}/" + id,
                    dataType: 'json',
                    success: function(data) {
                        location.reload();
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        });
    }
</script>
@endsection