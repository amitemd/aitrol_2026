@extends('hospital_admin.base')
@section('action-content')

<style>
.item {
    border: 1px solid #ccc;
    width: 100px;
    text-align: center;
    padding: 5px;
    margin: 5px;
}
</style>

<div class="row">
    <div class="col-md-12">
        <!-- Collapsable Menú -->
        <div class="card shadow mb-4">
            <!-- Card Header - Accordion -->
            <a href="#collapseCardCrearMenu" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardCrearMenu">
                <h6 class="m-0 font-weight-bold text-primary">Crear Menú</h6>
            </a>
            <!-- Card Content - Collapse -->
            <div class="collapse show" id="collapseCardCrearMenu">
                <div class="card-body">

                    <form action="{{ route('hospital_admin.guardarplato')}}" method="post">
                        {{ csrf_field() }}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label >Nombre del Plato</label>
                                <input type="text" class="form-control" name="nombre" id="nombre">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Costo</label>
                                <input type="number" class="form-control" name="costo" id="costo">
                            </div>
                            <div class="form-group col-md-3">
                                <label>Categoria</label>
                                <select name="tipo" id="tipo" class="form-control">
                                    <option value="1">Desayuno</option>
                                    <option value="2">Almuerzo</option>
                                    <option value="3">Merienda</option>
                                    <option value="4">Refrigerio</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label>Ingredientes</label>
                                <input type="text" class="form-control" id="item"  placeholder="Nombres de los ingrediente">
                                <div id="lista"></div>

                                <div id="items"></div>

                                <div class="form-group row my-2">
                                    <small  class="col-sm-9 form-text text-muted">Agregar ingredientes al plato</small>
                                    <div class="col-sm-3">
                                    <button type="button" onclick="agregar()" class="btn btn-success float-right"><i class="fas fa-carrot"></i> nuevo ingrediente</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a type="button" href="{{ route('hospital_admin.listamenu')}}" class="btn btn-danger"><i class="far fa-trash-alt"></i> Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-utensils"></i> Crear Plato</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">

    //Autocomplete
    $(document).ready(function(){
        $('#item').keyup(function(){
            var query = $(this).val();
            console.log(query);
            if(query != ''){
                var _token = $('input[name="token"]').val();
                $.ajax({
                    url:"{{ route('autocomplete.fetch')}}",
                    method:"get",
                    data:{query:query, _token:_token},
                    success:function(data){
                        $('#lista').fadeIn();
                        //console.log(data);
                        $('#lista').html(data);
                    }
                });
            }
        });
    });


    //AGREGAR UN NUEVO INGREDIENTE A MI TABLA
    function agregar(){
        var valor = $("#item").val();
        console.log($('input[name=_token]').val());
        $.ajax({
            type: 'post',
            url: "{{ route('hospital_admin.ingrediente')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                data:{'valor':valor},
                success: function(data){
                    console.log("data",data);
                    if(data==""){
                        $.ajax({
                            type: 'post',
                            url: "{{ route('hospital_admin.agregaing')}}",
                                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                                data:{'valor':valor},
                                success: function(data){                                
                                    console.log("nuevo");
                                    
                                    var nuevo = "<div class='item'>"
                                                +valor+
                                                "<input type='checkbox' checked name='ingredientes[]' style='display:none' value='"+data+"'>"+
                                                "<span aria-hidden='true'>&times;"+"</span>"+
                                                "</div>";
                                    $("#items").append(nuevo);
                                    $('#lista').fadeOut();
                                },
                                error: function(){
                                    alert('error al pagar');
                                }
                        });
                    }
                },
                error: function(){
                    alert('error al pagar');
                }
        });
    $("#item").val("");
    }

    $(document).on('click','li', function(){
        $('#lista').fadeOut();
        $("#item").val("");
        var nuevo = "<div class='item'>"+$(this).data('name')+
        "<input type='checkbox' name='ingredientes[]' checked style='display:none' value='"+$(this).data('id')+"'>"+
        "<span aria-hidden='true'>&times;"+"</span>"+"</div>";
        $("#items").append(nuevo);
    });

    //REMOVER EL DIV
    $(document).on('click','.item', function(){
            console.log($(this));
            $(this).remove();
    });

</script>
@endsection