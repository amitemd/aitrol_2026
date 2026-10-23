@extends('plantillas_labs.mantenimiento_derivados.base')
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

            <form id="form_producto" method="post" action="{{route('examderivados.buscar_examenes_contenido')}}">
                {{ csrf_field() }}    

                <div class="alert-danger"><span id="busca"></span></div>
                    <div class="form-group col-md-4{{ $errors->has('id') ? ' has-error' : '' }}">
                        <label for="examen" class="control-label">Examenes:</label>
                        <div class="input-group">
                            <input id="examen" class="form-control input-sm" type="text" name="examen" value="{{ old('examen') }}" required autofocus style="background-color: #fff0e6;">
                            @if ($errors->has('examen'))
                            <span class="help-block">
                                <strong>{{ $errors->first('examen') }}</strong>
                            </span>
                            @endif
                            <div class="input-group-addon">
                                <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('examen').value = '';"></i>
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
                      
                    </div>
                    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                        <div class="row" id="listado">
                            <div class="table-responsive col-md-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                                    <thead>
                                        <tr>
                                            <th>Id </th>
                                            <th> Examen</th>
                                      
                                            <th> Acción </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($examenes as $exam)
                                        <tr>
                                            <td>{{$exam->id}}</td>
                                            <td>{{$exam->nombre}} </td>
                                
                                            <td>
                                                <a href="{{route('examderivados.index_derivados', ['id_examen' => $exam->id])}}" class="btn btn-warning"><i class="fa fa-plus"></i></a>
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
        placeholder: "Seleccione un examen...",
        allowClear: true,
        cache: true,
        ajax: {
            url: '{{route("mantenimiento.examderivados.examenes")}}',
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

    $("#examen").autocomplete({
        
        source: function( request, response ) {
             //alert("hola");
            $.ajax({
                url:"{{route('examderivados.buscar_todos_examenes')}}",
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
            $('#examen').val(ui.item.label);
        }
    } );

    var contador = 0;    

</script>

@endsection
