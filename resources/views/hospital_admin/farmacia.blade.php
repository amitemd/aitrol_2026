@extends('hospital_admin.base')
@section('action-content')
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<style>
  .color { background-color: #004AC1 !important }
</style>

<div class="modal fade" id="modaleditar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Lista de Medicina</h6>
      </div>
      <div class="card-body">
        <div class="row">

            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                  <a onclick="location.href='{{route('hospital_admin.marcas')}}'" class="btn color" 
                      style="width: 100%; height: 40px; line-height: 30px; font-size: 20px; text-align: center; color: white">
                      Marcas
                  </a>
                </div>
            </div>

            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                  <a onclick="location.href='{{route('hospital_admin.tipoprodu')}}'" class="btn color" 
                      style="width: 100%; height: 40px; line-height: 30px; font-size: 20px; text-align: center; color: white">
                      Tipos de Medicinas
                  </a>
                </div>
            </div>

            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                  <a onclick="location.href='{{route('hospital_admin.bodega')}}'" class="btn color" 
                      style="width: 100%; height: 40px; line-height: 30px; font-size: 20px; text-align: center; color: white">
                      Bodega
                  </a>
                </div>
            </div>

            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                  <a onclick="location.href='{{route('hospital_admin.producto')}}'" class="btn color" 
                      style="width: 100%; height: 40px; line-height: 30px; font-size: 20px; text-align: center; color: white">
                      Medicamentos
                  </a>
                </div>
            </div>

            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                  <a onclick="location.href='{{route('hospital_admin.codigo')}}'" class="btn color" 
                      style="width: 100%; height: 40px; line-height: 30px; font-size: 20px; text-align: center; color: white">
                      Pedidos Realizados
                  </a>
                </div>
            </div>

            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                    <a onclick="location.href='{{route('hospital_admin.transito')}}'" class="btn color" 
                        style="width: 100%; height: 40px; line-height: 30px; font-size: 20px; text-align: center; color: white">
                        En Transito
                    </a>
                </div>
            </div>

            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                  <a href="#" class="btn color" 
                      style="width: 100%; height: 40px; line-height: 30px; font-size: 20px; text-align: center; color: white">
                      Descargar Reporte
                  </a>
                </div>
            </div>

            <div class="col-md-3" style="padding: 5px;">
                <div class="col-md-12 btn-group" style="padding-left: 0px; padding-right: 0px;">
                  <a onclick="location.href='{{route('hospital_admin.proveedores')}}'" class="btn color" 
                      style="width: 100%; height: 40px; line-height: 30px; font-size: 20px; text-align: center; color: white">
                      Proveedores    
                  </a>
                </div>
            </div>

        </div>

      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12">
    <div class="card shadow mb-4">
      <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Búsqueda de medicamentos</h6>
      </div>
      <div class="card-body">
        <form method="POST" id="form_buscador">
          <div class="row my-3">
              <div class="col-md-3 col-sm-3 col-3">
                <input id="codigo" name="codigo" type="text" required maxlength="30" placeholder="Código" style="text-align: center;">
              </div>
              <div class="col-md-3 col-sm-3 col-3">
                <input id="nombre" name="nombre" required maxlength="30" placeholder="Nombre" style="text-align:center;">
              </div>
          </div>
        </form>

        <div id="resultados">
        </div>
        <div id="muestra"> 
          <div class="table-responsive">
            <table id="example2" class="table table-bordered" aria-describedby="example2_info">
              <thead>
                <tr role="row" class="text-dark">
                  <th>Codigo</th>
                  <th>Nombre</th>
                  <th>Descripción</th>
                  <th>Medida</th>
                  <th>Stock Minimo</th>
                  <th>Forma de Despacho</th>
                  <th>Registro Sanitario</th>
                  <th>Marca</th>
                  <th>Tipo de Producto</th>
                  <th>Cantidad de Usos</th>
                  <th>IVA</th>
                  <th>Acción</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($farmacia as $value)
                  <tr role="row" class="odd">
                    <td>{{$value->codigo}}</td>
                    <td>{{$value->nombre}}</td>
                    <!--<td>{{$value->marcas->nombre}}</td>-->
                    <td>{{$value->descripcion}}</td>
                    <td>{{$value->medida}}</td>
                    <td>{{$value->minimo}}</td>
                    <td>@if(($value->despacho)==1) Código de Serie @elseif(($value->despacho)==2) Código de Producto @endif</td>       
                    <td>{{$value->registro_sanitario}}</td>
                    <td>{{$value->marcas->nombre}}</td>
                    <td>{{$value->tipo->nombre}}</td>
                    <td>{{$value->usos}}</td>                  
                    <td>@if(($value->iva)==1) NO @elseif(($value->iva)==0) SI  @endif</td>          
                    <td> <a href="{{ route('hospital_admin.modaleditarp', ['id' => $value->id]) }}" data-toggle="modal" data-target="#modaleditar"  class="btn btn-sm btn-warning"><i class="far fa-edit"></i> Editar</a></td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
          <!--PAGINACION-->
          <div class="row">
            <div class="col-sm-5">
              <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 / {{count($farmacia)}} de {{$farmacia->total()}} registros</div>
            </div>
            <div class="col-sm-7">
              <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                {{ $farmacia->links()}}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>


  <script type="text/javascript">
    $("#fecha").change(function(){
      alert($("#fecha").val());
    });
    function enviar_enter(e){
    //alert('entra1');
      tecla = (document.all) ? e.keyCode : e.which;
        if (tecla==13){
        buscador_paciente_fecha();
        };
    }
    function cambio_fecha(){
      alert('cambio');
    }
  </script>

  <script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
  <script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
  
  <script type="text/javascript">
    $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  </script>


<script>

  window.addEventListener('load',function(){
        document.getElementById("nombre").addEventListener("keyup", () => {
            if((document.getElementById("nombre").value.length)>0)
                fetch(`farmacia/buscadorfa?nombre=${document.getElementById("nombre").value}`,{ method:'get' })
                .then(response  =>  response.text() )
                .then(html      =>  {   document.getElementById("resultados").innerHTML = html  })
            else
                document.getElementById("resultados").innerHTML = document.getElementById("muestra").innerHTML;
                $("#muestra").hide();
        })
        document.getElementById("codigo").addEventListener("keyup", () => {
            if((document.getElementById("codigo").value.length)>0)
                fetch(`farmacia/buscadorfa?codigo=${document.getElementById("codigo").value}`,{ method:'get' })
                .then(response  =>  response.text() )
                .then(html      =>  {   document.getElementById("resultados").innerHTML = html  })
            else
                 document.getElementById("resultados").innerHTML = document.getElementById("muestra").innerHTML;
                  $("#muestra").hide();
        })  
        
    }); 
   
</script>


@endsection
