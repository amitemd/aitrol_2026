@extends('contable.ventas.base')
@section('action-content')
<section class="content">
    <div class="card">
      <div class="card-header">
            Bienvenido
      </div>
      <div class="card-body">
        <form action="{{route('getAudio.store')}}" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
            {{ csrf_field() }}
          <div class="form-group">
                <label> Entra audio </label>
                <input type="file" name="sonido" id="sonido">

                
            </div>
            <div class="form-group" style="text-align: center;">
                    <button type="submit" class="btn btn-success"> <i class="fa fa-save"></i> Guardar </button>            
            </div>
        
        </form>
           
      </div>
    </div>

</section>

@endsection