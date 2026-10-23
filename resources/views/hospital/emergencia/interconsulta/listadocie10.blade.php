<div class="card card-primary">
    <div class="card-body">
        <div id="muestra" class="card-body table-responsive">
            <table class="table table-hover">
                <tr>
                    <td><b>{{trans('emergencia.Cie10')}}</b></td>
                    <td><b>{{trans('emergencia.Presuntivo/Definitivo')}}</b></td>
                    <td><b>{{trans('emergencia.Accion')}}</b></td>
                </tr>
                @foreach($cie10 as $cie)
                <tr>
                    @php 
                        $c3 = Sis_medico\Cie_10_3::find($cie->cie10); 
                        $c4 = Sis_medico\Cie_10_4::find($cie->cie10);
                        $texto = '';
                        if(!is_null($c3)){
                            $texto = $c3->descripcion;
                        }
                        if(!is_null($c4)){
                            $texto = $c4->descripcion;
                        }
                    @endphp
                    <td>{{$cie->cie10}}-{{$texto}}</td>
                    <td>{{$cie->presuntivo_definitivo}}</td>
                    <td>
                        <button type="button" onclick="eliminar_cie10('{{$cie->id}}')" class="btn btn-xs btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>
