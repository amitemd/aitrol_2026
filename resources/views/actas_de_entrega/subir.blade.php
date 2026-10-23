@extends('actas_de_entrega.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">Subir Acta</h3>
        </div>
        <div class="box-body">
            <form id="subir_acta" method="post" action="{{route('acta.guardar_acta')}}" enctype="multipart/form-data">
                {{ csrf_field() }}
            
                <input type="hidden" name="id" id="id" value="{{$id}}">


                <div class="box-body">
                    <div class="from-group col-xs-6" style="padding-left:15px;">
                        <label class="texto" for="archivo_actas">Agregar archivo</label>
                        <input type="file" name="archivo_actas" id="archivo_actas" class="form-control" required accept="aplication/vnd.openxmformats-officedocument.spreadsheetml.sheet" value="">
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group col-xs-6" style="padding-top: 30px; padding-left: 15px;">
                        <button type="submit" class="btn btn-primary">
                            Guardar
                        </button>
                    </div>
                </div>



            </form>
        </div>
        
    </div>
</section>
@endsection