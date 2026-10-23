
@if ($result=='[]')
    <script> Swal.fire("Error: ","No se encontro existencia en bodega.","error"); </script>
@else
    @foreach($result as $key=>$x)

    <div class="panel panel-default details">
        <div class="panel-heading">
            <div class="row titulo">
                @php 
                    $z= \Sis_medico\Producto::find($key);
                @endphp
                <label class="col-md-10">Codigo {{$z->codigo}} {{$z->nombre}}  </label>
                <div class="col-md-2" style="text-align: right;">
                    <button type="button" class="btn btn-danger des">
                        <i class="fa fa-remove"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="panel-body" style="padding:0;">
            <div class="col-md-12 table-responsive " style="padding:0 !important;">
                <table class="table table-bordered table-hover dataTable noacti"  role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width: 100%!important;">
                    <thead>
                        <tr>
                        
                            <th tabindex="0">Cantidad</th>
                            <th tabindex="0">Serie</th>
                            <th tabindex="0">Lote</th>
                            <th tabindex="0">Fecha Vence</th>
                            <th>Accion</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($x as $p)
                            <tr>
                                <td><input type="number" style="width: 80%;height:20px; text-align:center;" class="form-control cneto" name="cantidad[]"  required value="1" onchange="existenciax(this)"> <input type="hidden" name="existencia" class="existencia" value="{{$p['existencia']}}"> </td>
                                {{-- <td>1</td> --}}
                                <td style="width: 80%;height:20px;" >{{$p['serie']}} <input type="hidden" name="id[]" value="{{$z->id}}"> <input type="hidden" name="serie[]" value="{{$p['serie']}}"> <input type="hidden" name="lote[]" value="{{$p['lote']}}"> <input type="hidden" name="fecha_vence[]" value="{{$p['fecha_vence']}}"> </td>
                                <td style="width: 80%;height:20px;" >{{$p['lote']}}</td>
                                <td style="width: 80%;height:20px;" >{{$p['fecha_vence']}}</td>
                                <td> <button class="btn btn-danger" type="button" onclick="return $(this).parent().parent().remove()"> <i class="fa fa-trash"></i></button> </td>
                            </tr>
                    @endforeach
                    </tbody>
                </table>
                
            </div>
        </div>
        
    </div>
    @endforeach
@endif