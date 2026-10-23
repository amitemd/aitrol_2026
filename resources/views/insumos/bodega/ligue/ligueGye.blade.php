@extends('insumos.bodega.base')
@section('action-content')
<div class="col-md-12" style="margin-top: 20px; background: #ffffff; padding: 20px; border-radius: 22px;">
    <div class="col-md-6">
        <p style="font-weight: 700; font-size: 20px;">Ligue de Bodegas de Guayaquil</p>
    </div>
    <div class="col-md-6">
        <div style="display: flex; justify-content: flex-end;">
            <a class="btn btn-danger" href="{{ url('/bodega') }}">
                <i class="fa fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>
    <div style="margin-top: 20px; display: flex; justify-content: center" class="col-md-12">
        <div class="col-md-8 col-sm-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">Ligue de Bodegas de Guayaquil</th>
                    </tr>
                    <tr>
                        <th class="text-center">Bodega Local</th>
                        <th class="text-center">Bodega Guayaquil</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bodegas as $value)
                    <tr>
                        <td style="text-align: center;">
                            <p>{{$value->nombre}}</p>
                        </td>
                        <td style="text-align: center;">
                            <select onchange="ligueBodega(`{{$value->id}}`, this.value)" id="bodega_{{$value->id}}" class="form-control">
                                <option value="">Seleccione</option>
                                @foreach($apiBodegas['detalles'] as $bodgye)
                                <option @if(isset($arrBodegas[$value->id]) and $arrBodegas[$value->id] == $bodgye['id']) selected @endif value="{{$bodgye['id']}}">{{$bodgye['nombre']}}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    const ligueBodega = (id, value) => {
        $.ajax({
            url: `{{route('bodega.ligueGyeStore')}}`,
            type: 'POST',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': `{{ csrf_token() }}`
            },
            data: {
                id_local: id,
                id_bodega_gye: value
            },
            success: function(data) {
                if(data.status == "success"){
                    alertas('success', 'Exito...', data.msj)
                    return ;
                }else{
                    alertas('error', 'Error...', data.msj)
                    return ;
                }
            }, error(data){
                alertas('error', 'Error', 'Error al ligar las bodegas')
                return ;
            }
        })
    }
</script>

@endsection