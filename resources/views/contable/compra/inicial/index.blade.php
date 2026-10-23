@extends('contable.compras_pedidos.base')
@section('action-content')


<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">

<section class="content">
  <!--<div class="box" style=" background-color: white;">-->
    <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';">
      <h3 class="box-title">Productos Iniciales</h3>
      <button type="button" onclick="location.href='{{route('contable.compraspedido.createInicial')}}'" class="btn btn-sm btn-success btn-gray pull-right">
        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Crear Producto Inicial
      </button>

      <button style="margin-right:10px" type="button" onclick="location.href='{{route('contable.excelProdInicial')}}'" class="btn btn-sm btn-success btn-gray pull-right">
            <i class="fa fa-file-excel-o" aria-hidden="true"></i> Subir Masivo
      </button>

    </div>

    <form method="POST"  action="{{route('contable.compraspedido.indexInicial')}}">
      {{ csrf_field() }}
       
        <div class="panel panel-default col-md-12">
          <div class="panel-heading">
              <label> Buscadores</label>
          </div>
          <div class="panel-body">
          <div class="form-row">

            <div class="form-group row col-md-12">
              <label for="buscar_plan_cuenta" class="col-sm-2 col-form-label">Producto: </label>
              <div class="col-md-4 container-6">
              <select id="producto" name="id_producto" class="form-control select2_cuentas" style="width:100%; border:1px solid grayw;">
                        <option> </option>
                        @foreach($productos as $value)
                        @php
                        $select="";
                              if(count($busq)>0){
                                if($value->id == $busq[0]){
                                    $select ="selected";
                                }
                              }
                        @endphp
                              <option {{$select}} value="{{$value->id}}" >{{$value->nombre}}</option>
                        @endforeach
                  </select>
              </div>
            </div>


            </div>
            <div class="form-row">
            <div class="form-group row col-md-6">
              <button style="margin-left: 14px;" type="submit" class="btn btn-primary btn-gray">
                <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
              </button>
            </div>
            </div>
          </div>
         
        </div>
      </form>
      <div class="table table-responsive">
            <table class="table display compact cell-border responsive nowrap">
            <thead>
            <tr>
                  <th scope="col">#</th>
                  <th scope="col">Producto</th>
                  <th scope="col">Bodega</th>
                  <th scope="col">Cantidad</th>
                  <th scope="col">Costo</th>
                  <th scope="col">Costo de Venta</th>
                  <th scope="col">Acciones</th>
            </tr>
            </thead>
            <tbody>
                  @foreach($datos as $value)
                  <tr>
                  <th scope="row">{{$value->id}}</th>
                  <td>@if(isset($value->producto)) @if(!is_null($value->producto->nombre)) {{$value->producto->nombre}} @endif @endif</td>
                  <td>{{$value->b_nombre}}</td>
                  <td>{{intval($value->cantidad)}}</td>
                  <td>{{$value->costo}}</td>
                  <td>{{$value->costo_venta}}</td>
                  <td>
                  @if($value->k_estado==1)
                  <button onclick="eliminar({{$value->id}});" class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                  @endif
                  <a class="btn btn-info"href="{{route('contable.editarProdInicial',['id'=>$value->id])}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                  </td>
                  </tr>
                  @endforeach
            </tbody>
            </table>
            <div>
                  <center>
                        {{$datos->links() }}
                  </center>
            </div>
      </div>

</section>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
   $('#examples3_wrapper').removeClass('dataTables_wrapper');
 });

 $('.select2_cuentas').select2({
          tags: false
      });
</script>

<script>
      function eliminar(id){
            $.ajax({
                type: "get",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                url: "{{route('contable.deleteProdInicial')}}",
                data: {
                      'id': id
                },success: function(data){
                  console.log(data);
                  alertas('Exito...', data.msj, 'success');
                  setTimeout(()=>{
                        window.location.reload();
                  }, 1500)
                },error:  function(){
                  alert('error al cargar');
                }
            }); 
      }

      function alertas(title,text,icon){
            Swal.fire({
                  icon: `${icon}`,
                  title: `${title}`,
                  text: `${text}`,
            })
      }
</script>
@endsection