@extends('contable.estructura_flujo_efectivo.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset('hc4/awesome/css/font-awesome.css')}}">
    <!-- Main content -->
    <section class="content">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Contable</a></li>
        <li class="breadcrumb-item"><a href="#">Contabilidad</a></li>
        <li class="breadcrumb-item active">Estructura flujo de efectivo</li>
        
      </ol>
    </nav>
    <div class="box">
      <div class="box-header header_new"> 
          <div class="col-md-9">
              <h3 class="box-title">Listado de cuentas</h3>
          </div>
          <div class="col-md-1 text-right"> 
            <!-- <a class="btn btn-primary" href="{{route('estructuraflujoefectivo.create')}}">Agregar nueva cuenta</a> -->
            <button onclick="location.href='{{route('estructuraflujoefectivo.create')}}'" class="btn btn-success btn-gray" >
              <i aria-hidden="true"></i>Agregar nueva cuenta
            </button>
          </div> 
      </div>
  <!-- /.box-header -->
  <div class="row head-title">
    <div class="col-md-12 cabecera">
        <label class="color_texto" for="title">BUSCAR CUENTA</label>
    </div>
  </div>
  <div class="box-body dobra"> 
  <form method="POST" id="reporte_master" action="{{ route('estructuraflujoefectivo.buscar') }}" > 
    {{ csrf_field() }}
    <div class="form-group col-md-1 col-xs-2">
      <label class="texto" for="buscar_asiento">Cuenta: </label>
    </div>
    <div class="form-group col-md-3 col-xs-10 container-4">
      <input class="form-control" type="text" id="buscar_cuenta" name="buscar_cuenta" value="@if(isset($searchingVals)){{$searchingVals['id_plan']}}@endif" placeholder="Ingrese la cuenta..." />
    </div>
    <div class="col-xs-2">
      <button type="submit" id="buscarAsiento" class="btn btn-primary">
          <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
      </button>
    </div>
  </form>
</div>

<div class="row head-title">
  <div class="col-md-12 cabecera">
      <label class="color_texto" >DETALLE DE LAS CUENTAS</label>
  </div>
</div>  

<div id="example2_wrapper" class="box-body">
        <div class="table-responsive col-md-12">
        <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="example2_info">
            <thead>
              <tr class='well-dark'>
                <th >Cuenta</th>
                <th >Descripción</th>
                <th >Grupo</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" >Signo</th>
                <th >Fecha de Creación</th>
                <th >Acción</th>
              </tr>
            </thead>
            <tbody>
            @foreach ($estructura as $value)
                <tr role="row" class="odd">
                  <td class="sorting_1">{{ $value->id_plan}}</td>
                  <td >{{ @$value->plan->nombre}}</td>
                  <td >{{ @$value->grupo->nombre}}</td>
                  <td >@if($value->signo=='1') POSITIVO @else NEGATIVO @endif</td>
                  <td >{{ $value->created_at }}</td>
                  <td>  
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <a href="{{ route('estructuraflujoefectivo.edit', ['id' => $value->id]) }}" class="btn btn-success btn-gray">
                      <i class="glyphicon glyphicon-edit" aria-hidden="true"></i><!-- Actualizar -->
                    </a> 
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    {{-- <a href="{{ route('estructuraflujoefectivo.destroy', ['id' => $value->id]) }}" class="btn btn-danger col-md-8 col-sm-8 col-xs-8 btn-margin">
                        Borrar
                    </a> --}}
                    <a href="{{ route('estructuraflujoefectivo.destroy', ['id' => $value->id]) }}" onclick="return confirmar()" class="btn btn-danger btn-gray">
                      <i class="glyphicon glyphicon-trash" aria-hidden="true"></i><!-- Eliminar -->
                    </a> 
                  </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      
      <div class="row">
        <div class="col-sm-5">
          <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando 1 al {{count($estructura)}} de {{$estructura->total()}} Registros</div>
        </div>
        <div class="col-sm-7">
          <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
            {{ $estructura->links() }}
          </div>
        </div>
      </div>
    
    </div>
    </section>
    <!-- /.content -->
  </div>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
  <script type="text/javascript">



  $('#example2').DataTable({
      'paging'      : false,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : false,
      'autoWidth'   : false
    })
  
    function confirmar(){
      // confirm('Are you sure?');
      Swal.fire({
        title: 'Alerta!',
        text: "Esta seguro que desea eliminar el registro?",
        // icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si, borrar!'
      }).then((result) => {
        if (result.value) {
          Swal.fire(
            'Eliminado!',
            'El registro ha sido eliminado.',
            'success'
          )
          return true;
        }
      });
      return false;
    }


 </script> 








@endsection