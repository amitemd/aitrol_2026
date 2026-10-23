@extends('hospital_admin.base')
@section('action-content')
<div class="modal fade" id="modals"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">

    </div>
  </div>
</div>


<a type="button" href="{{route('hospital_admin.producto') }}" class="btn btn-sm my-2 btn-primary"><i class="far fa-arrow-alt-circle-left"></i> Regresar</a>


<div class="row">
  <div class="col-md-12">

  <div class="card shadow mb-4">
    <div class="card-header py-3">
      <h6 class="m-0 font-weight-bold text-primary">Busqueda de producto por codigo</h6>
    </div>
    <div class="card-body">
      <form action="" method="POST" id="form_buscador">
        <div class="form-group">
          <label>Escriba es codigo del producto</label>
          <input type="number" class="form-control" name="codigo" id="codigo" placeholder="CÓDIGO">
        </div>
      </form>
    </div>
  </div>

  </div>
</div>

<div class="body col-md-12" id="resultados">
  No data avaliable
</div>

<script>
  window.addEventListener('load',function(){
    document.getElementById("codigo").addEventListener("keyup", () => {
      if((document.getElementById("codigo").value.length)>0)
        fetch(`darbaja/tablap?codigo=${document.getElementById("codigo").value}`,{ method:'get' })
        .then(response  =>  response.text() )
        .then(html      =>  {   document.getElementById("resultados").innerHTML = html  })
      else
        document.getElementById("resultados").innerHTML = "No data avaliable"
    })
  }); 
</script>
@endsection