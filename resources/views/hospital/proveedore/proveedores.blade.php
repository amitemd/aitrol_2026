@extends('layouts.app-template-h')
@section('content')
<style>
h3, h5{

}
h3{
  border-bottom: 3px solid #F39C12;
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
        <div class="col-md-9">
          <h3>PROVEEDORES</h3>
        </div>
        <div class="col-md-3  my-2">
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
          <div class="form-group row">
            <div class="col-md-6">
              <h5 class="card-title">LISTA DE PROVEEDORES</h5>
            </div>
            <div class="col-md-6 row">
              <div class="col-6 my-2">
                <input id="nombrecomercial" class="form-control" type="text" required maxlength="30"  placeholder="Nombre Comercial" style="color:black;text-align: center;">
              </div>
              <div class="col-6 my-2">
                <input id="ruc" class="form-control" type="text" required maxlength="30" placeholder="RUC" style="color:black;text-align:center;">
              </div>
            </div>
          </div>
        </form>
      </div>
      <div id="resultados">
      </div>
      <div id="muestra" class="card-body table-responsive">
          <table class="table table-hover" aria-describedby="example2_info">
            <thead  class="table-primary">
              <tr >
                <th scope="col">Logo</th>
                <th scope="col">Nombre Comercial</th>
                <th scope="col">Raz&oacute;n social</th>
                <th scope="col">Ruc</th>
                <th scope="col">Email</th>
                <th scope="col">Tipo Proveedor</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($proovedor as $value)
                <tr role="row" class="odd">
                  <td> <img src="{{asset('/logo').'/'.$value  ->logo}}" style="width:80px;height:80px;"  alt="Logo Image" > </td>
                  <td> {{ $value->nombrecomercial}}</td>
                  <td> {{ $value->razonsocial}}</td>
                  <td> {{ $value->ruc}}</td>
                  <td> {{ $value->email}}</td>
                  <td> @if(($value->id_tipoproveedor)==1) Takeda Mexico @elseif (($value->id_tipoproveedor)==2) Roche @elseif (($value->id_tipoproveedor)==3) ICN Farmacéutica @elseif (($value->id_tipoproveedor)==4) farmacia  @endif </td>  
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
    document.getElementById("nombrecomercial").addEventListener("keyup", () => {
      if((document.getElementById("nombrecomercial").value.length)>0)
        fetch(`proveedores/buscadort?nombrecomercial=${document.getElementById("nombrecomercial").value}`,{ method:'get' })
        .then(response  =>  response.text() )
        .then(html      =>  {   document.getElementById("resultados").innerHTML = html  })
      else
        document.getElementById("resultados").innerHTML = document.getElementById("muestra").innerHTML;
        $("#muestra").hide();
    })
    document.getElementById("ruc").addEventListener("keyup", () => {
      if((document.getElementById("ruc").value.length)>0)
        fetch(`proveedores/buscadort?ruc=${document.getElementById("ruc").value}`,{ method:'get' })
        .then(response  =>  response.text() )
        .then(html      =>  {   document.getElementById("resultados").innerHTML = html  })
      else
        document.getElementById("resultados").innerHTML = document.getElementById("muestra").innerHTML;
        $("#muestra").hide();
    })     
  });
</script>
@endsection