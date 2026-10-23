@extends('layouts.app-template-h')
@section('content')

<style>
h3, h5{
 
}
h3{
  border-bottom: 2px solid #F39C12;
}
h5{
  color: black;
}
#example2 td{
    color: black;
   font-weight: bold;
   text-align: center;
}
#example2 td, #example2 th {
border: 1px solid #e0e0e0;
padding: 5px;

}
#example2 th {
padding-top: 6px;
padding-bottom: 8px;
text-align: center;
background: linear-gradient(135deg, #3352ff 0%, #051eff 100%);
color: white;
}
#exaple2 tr:nth-child(even){background-color: white;}
#example2 tr:hover {background-color: white  ;}
</style>

<div class="content">
  <div class="card-header">
    <div class="col-md-12">
      <div class="row">
        <div class="col-md-10 col-sm-9 col-8">
          <h3>MEDICAMENTOS</h3>
        </div>
        <div class="col-md-2 col-sm-3 col-4 my-2">
          <a type="button" href="{{ route('hospital.farmacia') }}" class="form-control btn btn-primary btn-sm">
            <span style="width: 100%">Regresar</span>
          </a>
        </div>
      </div>
    </div>
  </div>
  <section class="content">
    <div class="card">
      <div class="col-12" >
        <form method="POST" id="form_buscador">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-md-6">
              <div class="col-12">
                <h5>B&Uacute;SQUEDA DE MEDICAMENTOS</h5>
              </div>
            </div>
            <div class="col-md-6">
              <div class="input-group">
                <div class="col-6 my-2">
                  <input class="form-control" required maxlength="30" placeholder="CÓDIGO" name="codigo" id="codigo" style="text-align:center;">
                </div>
                <div class="col-6 my-2">
                  <input class="form-control" required maxlength="30" placeholder="NOMBRE" name="nombre" id="nombre" style="text-align:center;">
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="body" id="resultados">
    </div>
    <div id="muestra" class="card-body table-responsive">
      <table class="table table-hover" aria-describedby="example2_info">
        <thead class="table-primary">
          <tr >
            <th scope="col">Código</th>
            <th scope="col">Nombre</th>
            <th scope="col">Descripción</th>
            <th scope="col">Estado</th>
            <th scope="col">Medida</th>
            <th scope="col">Stock Minimo</th>
            <th scope="col">Forma de Despacho</th>
            <th scope="col">Registro Sanitario</th>
            <th scope="col">Marca</th>
            <th scope="col">Tipo de Medicamento</th>
            <th scope="col">Cantidad de Usos</th>
            <th scope="col">IVA</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($codigo as $value)
            <tr role="row" class="odd">
              <td>{{$value->codigo}}</td>
              <td> {{$value->nombre}}</td>
              <td>{{$value->descripcion}}</td>
              <td @if($value->estado==1) bgcolor='#69f0ae'  @elseif($value->estado==2) bgcolor='#d32f2f' @endif> @if($value->estado==1)  ACTIVO   @elseif($value->estado==2)  INACTIVO @endif</td> 
              <td>{{$value->medida}}</td>
              <td>{{$value->minimo}}</td>
              <td>@if(($value->despacho)==1) Código de Serie @elseif(($value->despacho)==2) Código de Producto @endif</td>
              <td>{{$value->registro_sanitario}}</td>
              <td>{{$value->marcas->nombre}}</td>
              <td>{{$value->tipo->nombre}}</td>
              <td>{{$value->usos}}</td>                  
              <td>@if(($value->iva)==1) NO @elseif(($value->iva)==0) SI  @endif</td>                 
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    </div>
  </section>
</div>

<script>
  window.addEventListener('load',function(){
    document.getElementById("codigo").addEventListener("keyup", () => {
      if((document.getElementById("codigo").value.length)>0)
        fetch(`producto/buscador?codigo=${document.getElementById("codigo").value}`,{ method:'get' })
        .then(response  =>  response.text() )
        .then(html      =>  {   document.getElementById("resultados").innerHTML = html  })
      else
      document.getElementById("resultados").innerHTML= document.getElementById("muestra").innerHTML;
      $("#muestra").hide();
    })
    document.getElementById("nombre").addEventListener("keyup", () => {
      if((document.getElementById("nombre").value.length)>0)
        fetch(`producto/buscador?nombre=${document.getElementById("nombre").value}`,{ method:'get' })
        .then(response  =>  response.text() )
        .then(html      =>  {   document.getElementById("resultados").innerHTML = html  })
      else
      document.getElementById("resultados").innerHTML= document.getElementById("muestra").innerHTML;
      $("#muestra").hide();
    }) 
  });
</script>
@endsection