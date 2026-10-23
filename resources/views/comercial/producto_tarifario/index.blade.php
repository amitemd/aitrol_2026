@extends('comercial.producto_tarifario.base')
@section('action-content')

<style type="text/css">
 

    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 12px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
        opacity : 1;
    }
    .ui-autocomplete
    {
        opacity : 1;
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 470px !important;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }
    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
    .ui-widget-content a
    {
        color: #222222;
    }
</style>

<section class="content">
    <div class="box">
        <div class="box-header">
        </div>
        <div class="box-body">

            <form id="form_producto" method="post" action="{{route('prodtarifario.buscar_productos_contenido')}}">
                {{ csrf_field() }}    
                <!-- <div class="alert-danger"><span id="cant_ord"></span></div>
                    <div class="form-group col-md-4{{ $errors->has('id') ? ' has-error' : '' }}">
                        <label for="id" class="control-label">{{trans('prod_tar.productos')}}:</label>
                        <select id="producto" name="producto" class="form-control select2_productos" style="width:100%">
                        </select>
                    </div>
                </div> -->
                <div class="alert-danger"><span id="busca"></span></div>
                    <div class="form-group col-md-4{{ $errors->has('id') ? ' has-error' : '' }}">
                        <label for="producto" class="control-label">{{trans('prod_tar.productos')}}:</label>
                        <div class="input-group">
                            <input id="producto" class="form-control input-sm" type="text" name="producto" value="{{ old('producto') }}" required autofocus style="background-color: #fff0e6;">
                            @if ($errors->has('producto'))
                            <span class="help-block">
                                <strong>{{ $errors->first('producto') }}</strong>
                            </span>
                            @endif
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('producto').value = '';"></i>
                            </div> 
                        </div>
                    </div>
                    <div>
                        <div class="form-group col-md-2 ">
                            <div class="col-md-7">
                                <button type="submit" class="btn btn-primary" id="boton_buscar">
                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('prod_tar.buscar')}}</button>
                            </div>
                           
                        </div>
                        <div class="form-group col-md-2">
                            <div class="col-md-8">
                                <a href="{{route('prodtarifario.excel')}}" class="btn btn-success">Excel</a>
                            </div>
                        </div>

                        <div class="form-group col-md-2">
                            <div class="col-md-8">
                                <a href="{{route('prodtarifario.excel_producto_tarifario')}}" class="btn btn-warning">Excel</a>
                            </div>
                        </div>

                        <div class="form-group col-md-2">
                            <div class="col-md-8">
                                <a href="{{route('doctores_convenios.index')}}" class="btn btn-primary">Mantenimiento Doctores</a>
                            </div>
                        </div>
                    </div>
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row" id="listado">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr>
                                            <th>{{trans('prod_tar.codigo')}}</th>
                                            <th>{{trans('prod_tar.producto')}}</th>
                                            <th>{{trans('prod_tar.Valor')}}</th>
                                            <th>{{trans('prod_tar.accion')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($productos as $prod)
                                        <tr>
                                            <td>{{$prod->codigo}}</td>
                                            <td>{{$prod->nombre}} </td>
                                            <td>{{$prod->valor_total_paq}} </td>
                                            <td>
                                                <a href="{{ route('prodtarifario.index_tarifario', ['id_producto' => $prod->id])}}" class="btn btn-xs btn-warning">comercial</a>
                                                <a href="{{ route('comisiones.index', ['id_producto' => $prod->id])}}" class="btn btn-xs btn-danger">comisiones</a>
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

<script src="{{ asset ('/js/jquery-ui.js')}}"></script>
<script type="text/javascript">
    $('#example2').DataTable({
        'paging': false,
        'lengthChange': false,
        'searching': false,
        'ordering': true,
        'info': false,
        'autoWidth': false
    })

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
                return {
                    results: data
                };
            }
        }
    });

    const changeExcel = () => {
        document.getElementById("excel").value = 1;
    }

    $("#producto").autocomplete({
        
        source: function( request, response ) {
             //alert("hola");
            $.ajax({
                url:"{{route('prodtarifario.buscar_todos_productos')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data: {
                    term: request.term
                        },
                dataType: "json",
                type: 'post',
                success: function(data){
                    response(data);
                   // console.log(data);

                },
                
            })
        },
        minLength: 3,
        select: function(data, ui){
            //alert(data);
            //console.log(ui.item.label);
            $('#producto').val(ui.item.label);
        }
    } );

    var contador = 0;    

</script>

@endsection