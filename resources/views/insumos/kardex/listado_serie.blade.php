 
@foreach($series as $serie)
    @php 
        $kardexn = Sis_medico\InvKardexNuevo::where('id_bodega',$serie->id_bodega)->where('serie',$serie->serie)->where('id_empresa',$serie->id_empresa)->first();
    @endphp
    @if(!is_null($kardexn))
    <h4>Producto: {{$serie->producto->nombre}} - Kardex por Usos Serie: {{$serie->serie}} - {{$serie->bodega->nombre}} - Usos: {{$serie->cantidad_usos_kardex}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <button class="btn btn-primary btn-xs" onclick="cargar_usos('{{$serie->serie}}');"><i class="fa fa-plus"></i></button></h4>
    <div class="box-body">
        <div class="box">
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row">
                    
                    <div id="mostrar_serie{{$serie->serie}}">

                    </div> 
                </div>
            </div>
        </div>
    </div> 
    @endif                           
@endforeach
                        
            


