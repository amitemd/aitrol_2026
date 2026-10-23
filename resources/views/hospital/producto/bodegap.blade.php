@extends('layouts.app-template-h')
@section('content')
<style>
h3, h5{
  
}
h3{
  border-bottom: 2px solid #F39C12
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
<div class="card-header" id="info">
  <div class="col-md-12">
    <div class="row">
      <div class="col-md-10 col-sm-9 col-8">
        <h3>BODEGA</h3>
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
      <form action="" method="POST" id="form_buscador">
        {{ csrf_field() }}
        <div class="row">
          <div class="col-md-9 col-sm-6 col-10">
            <h5>LISTA DE BODEGAS</h5>
          </div>
          <div class="col-md-3 col-sm-6 col-10 my-2">
            <input id="nombre" class="form-control" type="text" required maxlength="30"  placeholder="Nombre de la Bodega" style="color:black;text-align: center;">
          </div>
        </div>
      </form>
    </div>
    <div class="card-body" id="resultados">
     
    </div>
    <div id="muestra"  class="card-body table-responsive">
      <table class="table table-hover" aria-describedby="example2_info">
        <thead class="table-primary">
          <tr>
            <th scope="col">Nombre</th>
            <th scope="col">Ubicacion</th>
            <th scope="col">Piso</th>
            <th scope="col">Color</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($bodega as $value)
            <tr role="row" class="odd">
              <td>{{$value->nombre}}</td>
              <td>{{$value->ubicacion }}</td>
              <td>@if(($value->id_piso)==1)piso 1 @elseif (($value->id_piso)==2) piso 2 @elseif (($value->id_piso)==3) piso 3  @endif</td>
              <td bgcolor={{$value->color}}></td>
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
    document.getElementById("nombre").addEventListener("keyup", () => {
      if((document.getElementById("nombre").value.length)>0)
        fetch(`bodegap/buscadorbo?nombre=${document.getElementById("nombre").value}`,{ method:'get' })
        .then(response  =>  response.text() )
        .then(html      =>  {   document.getElementById("resultados").innerHTML = html  })
      else
        document.getElementById("resultados").innerHTML = document.getElementById("muestra").innerHTML;
        $("#muestra").hide();
    })
  });
</script>


@endsection