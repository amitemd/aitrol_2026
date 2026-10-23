@extends('preanestesico.preanestesico')
@section('action-content')
<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="{{route('formato.rc_preanestesico_index')}}">
                {{ csrf_field() }}
                <div class="col-md-8">
                    <h3 class="box-title"> FORMATO PREANESTESICO</h3>
                </div>
                <div class="col-md-12" style="text-align: right">
                    <a href="{{route('formato.rc_preanestesico_crear')}}" class="btn btn-primary">{{('Crear')}} </a>

                </div>
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <tbody>
                                    
                                    @foreach ($pre_anestesico as $pre)
                                    
                                    <div><b>Tipo</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->tipo}}</textarea>
                                    </div>

                                    <div><b>ANESTÉSICO</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->anestesico}}</textarea>
                                    </div>
                                    <div> <b>QUIRÚRGICOS</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->quirurgicos}}</textarea>
                                    </div>
                                    <div> <b>ALÉRGICOS</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->alergicos}}</textarea>
                                    </div>
                                    <div> <b>TRANSFUSIONES</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->transfuciones}}</textarea>
                                    </div>
                                    <div> <b>HÁBITOS</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->habitos}}</textarea>
                                    </div>
                                    <div> <b>Antecendentes Patológicos Familiares</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->antecedentes}}</textarea>
                                    </div>
                                    <br>
                                    <label> VÍA AÉREA </label>
                                    <div><b>APERTURA BUCAL(cm)</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->apertura_bucal}}</textarea>
                                    </div>

                                    <div><b>DISTANCIA TIROMENTONEANA(cm)</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->distancia_tiromentoneana}}</textarea>
                                    </div>
                                    <div> <b>MALLAMPATI</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->mallanpati}}</textarea>
                                    </div>
                                    <div> <b>PROTRUSIÓN MANDIBULAR</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->protusion_mandibular}}</textarea>
                                    </div>
                                    <div> <b>PERIMETRO CERVICAL(cm)</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->perimetro_cervical}}</textarea>
                                    </div>
                                    <div> <b>MOVILIDAD CERVICAL(°)</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->movilidad_cervical}}</textarea>
                                    </div>
                                    <div> <b>HISTORIA DEINTUBACIÓN DIFICIL</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->intubacion_dificil}}</textarea>
                                    </div>
                                    <div> <b> PATOLOGÍA ASOCIADA A INTUBACIÓN DIFICIL</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->patologia}}</textarea>
                                    </div>
                                    <div> <b>TÓRAX</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->torax}}</textarea>
                                    </div>
                                    <div> <b>CORAZÓN</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->corazon}}</textarea>
                                    </div>
                                    <div> <b> PULMONES</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->pulmones}}</textarea>
                                    </div>
                                    <div> <b> ABDÓMEN</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->abdomen}}</textarea>
                                    </div>
                                    <div> <b> EXTREMIDADES</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->extremidades}}</textarea>
                                    </div>
                                    <div> <b> SISTEMA NERVIOSO CENTRAL</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->sistema_nervisoso}}</textarea>
                                    </div>
                                    <div> <b>EQUIVALENTE METABÓLICO (METS)</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->equivalente_metabolico}}</textarea>
                                    </div>
                                    <br>
                                    <label>ESCALA E ÍNDICES</label>
                                    <div> <b>ESTADO FISICO ASA</b>
                                        <br>
                                        <textarea style="width: 90%;" readonly>{{$pre->equivalente_metabolico}}</textarea>
                                    </div>
                                    <div>
                                        <a href="{{route('formato.rc_preanestesico_edit',['id' => $pre->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
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
                    url: "{{url('rc/preanestesico/delete/')}}/" + id,
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