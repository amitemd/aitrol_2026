@extends('comercial.plantilla.base')
@section('action-content')
<link href="{{ asset("/bower_components/select2/dist/css/select2.min.css")}}" rel="stylesheet" type="text/css" />
<style type="text/css">
.select2 {
        width: 100% !important;
        background-color: #eafcff !important;
    }
</style>
<section class="content">
    <div class="box">
        <div class="box-header">
        </div>
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="">
                {{ csrf_field() }}

                <div class="header box-header with-border">
                    <div class="box-title col-md-9">
                        <b style="font-size: 22px;"> Nombre : {{$agrupador->nombre}}</b>
                    </div>
                </div>

                <label>Agregar Producto</label>
                
                <div class="form-group">
                    <select id="producto_nuevo" name="producto_nuevo" class="form-control select2_productos" required placeholder="{{trans('new_recibo.IngreseProducto')}}" onchange="seleccionar_producto();">     
                    </select>
                 </div>

                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado_pro_agrup">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                <thead>
                                    <tr>
                                         <th>Codigo </th>
                                        <th> Nombre </th>
                                       
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($agrup_detalles as $agrup_detalle)

                                    <tr>
                                        <td>{{$agrup_detalle->producto->codigo}}</td>
                                        <td>{{$agrup_detalle->producto->nombre}}</td>
                                        <td> 
                                            <a href="{{route('proforma.eliminar_producto_detalle', ['id'=>$agrup_detalle->id])}}" class="btn btn-danger"> <i class="fa fa-trash-o"></i></a>
                                        </td>
                                    </tr>
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

<script src="{{ asset ("/bower_components/select2/dist/js/select2.full.js") }}"></script>


<script type="text/javascript">
    //comentarioo
   
    $('.select2_productos').select2();
   
    $('.select2_productos').select2({
        placeholder: "Seleccione un producto...",
        allowClear: true,
        cache: true,
        ajax: {
            url: '{{route("importaciones.productos")}}',
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
                }
                return query;
            },
            processResults: function(data) {
                console.log(data);
                return {
                    results: data
                };
            }
        }
    });
    
    function seleccionar_producto() {
        var producto_nuevo = $('#producto_nuevo').val();

        $.ajax({
            type: 'post',
            url: "{{route('proforma.guardar_producto_detalle')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            datatype: 'json',
            data: {
                'producto_nuevo': producto_nuevo,
                'id'            : '{{$agrupador->id}}' ,
                
            },
            success: function(data) {
               location.reload();
            },
            error: function(data) {
                console.log(data);
                alert("No se pudo agregar el producto");
            }
        });

    }

  
</script>





@endsection
