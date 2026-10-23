<div class="box-body">
    <div class="box">
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row">
                <div class="table-responsive col-md-12">
                    <table id="tbl_detalles" class="display compact responsive"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                        <thead>
                            <tr>
                                <th>{{trans('winsumos.serie')}}</th>
                                <th>{{trans('winsumos.producto')}}</th>
                                <th>{{trans('winsumos.bodega')}}</th>
                                <th>{{trans('winsumos.existencia')}}</th>
                                <th>{{trans('winsumos.lote')}}</th>
                                <th>{{trans('winsumos.egreso')}}</th>
                                <th>{{trans('winsumos.accion')}}</th>
                            </tr>
                        </thead>
                        <tbody>@php $bandera = 0; @endphp
                            @foreach($series as $serie)
                                @php $bandera = 1; @endphp
                                <tr id="section{{$serie->id}}">
                                    <td>{{$serie->serie}}</td>
                                    <td>{{$serie->producto->nombre}}</td>
                                    <td>{{$serie->bodega->nombre}}</td>
                                    <td>{{$serie->existencia}}</td>
                                    <td>{{$serie->lote}}</td>
                                    <td><input type="text" name="cant_eg{{ $serie->id }}" id="cant_eg{{ $serie->id }}" ></td>
                                    <td><button type="button" class="btn btn-primary btn-xs" onclick="ingresar_por_serie('{{ $serie->serie }}' , '{{ $serie->bodega->id }}' , '{{ $serie->id }}' )" > Agregar </button></td>
                                </tr>                                                                  
                            @endforeach
                            @if($bandera == 0)
                                <tr id="">
                                    <td colspan="6" style="font-size: 23px;">No se encontraron registros</td>
                                </tr> 
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


