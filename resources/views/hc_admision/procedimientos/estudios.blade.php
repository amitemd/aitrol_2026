
@extends('hc_admision.procedimientos.base')

@section('action-content')
 
<style type="text/css">
.table>tbody>tr>td, .table>tbody>tr>th { 
    padding: 0.4% ;
}  
</style>
<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <style type="text/css">
  .icheckbox_flat-green.checked.disabled {
        background-position: -22px 0 !important;
        cursor: default;
    }
    #mceu_61{
        display: none;
    }
    .tok td, .tok  th {
    border: 1px solid #dddddd;
    text-align: left;
    padding: 8px;
}
</style>
 
 <div class="modal fade" id="favoritesModal2" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:1350px; " >
    <div class="modal-content"  id="imprimir3">
       <p>Hola Mundo</p>
    </div>
  </div>
</div>
<div class="modal fade" id="favoritesModal" tabindex="-1" role="dialog" aria-labelledby="favoritesModalLabel">
  <div class="modal-dialog" role="document" style="width:70%;">
    <div class="modal-content" >
    </div>
  </div>
</div>
 
<div class="container-fluid" >
    <div class="row ">
        <div class="col-md-9">
            <div class="box box-primary" style="margin-bottom: 5px;">
                <div class="box-header with-border" style="padding: 1px;">
                    <div class="table-responsive col-md-12">
                        <table class="table table-striped" style="margin-bottom: 0px;">
                            <tbody>
                                <tr>
                                    <td><b>Nombres</b></td>
                                    <td><b>Apellidos</b></td>
                                    <td><b>Identificación</b></td>
                                    <td style="text-align:right;"><b>Cortesias en el día</b></td>
                                </tr>
                                <tr>
                                    <td>{{ $agenda->pnombre1}} @if($agenda->pnombre2 != "(N/A)"){{ $agenda->pnombre2}}@endif</td>
                                    <td>{{ $agenda->papellido1}} @if($agenda->papellido2 != "(N/A)"){{ $agenda->papellido2}}@endif</td>
                                    <td>{{$agenda->id_paciente}}</td>
                                    <td style="text-align:right; @if($cant_cortesias>1) color:red; @endif">{{$cant_cortesias}}</td>
                                </tr>
                            </tbody>
                        </table>    
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <a type="button" href="{{route('agenda.detalle', ['id' => $agenda->id ])}}" class="btn btn-success btn-sm">
                <span class="glyphicon glyphicon-user"> Filiación</span>
            </a>
        </div>
        <div class="col-md-12">
            <div class="box box-primary" style="overflow:scroll; height:550px;">
                @foreach($protocolos as $value)
                <div class="col-md-12" ondblclick="editar({{$value->protocolo}})" style="border: black 1px solid; margin: 5px 0;padding-left: 0px;padding-right: 0px">
                    <div class="table-responsive">
                        <table class="table table-striped tok" style="margin-bottom: 0px;">
                            <thead>
                                <th>Procedimiento</th>
                                <th>Hallazgos</th>
                                <th><div class="col-md-9">Conclusiones</div><div class="col-md-3"><span style="color: Cornsilk;">{{$value->protocolo}}</span></div></th>    
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="20%"><span style="color: blue;"><b>{{$value->nombre_general}}</b></span></td>
                                    <td rowspan="5" width="50%"><?php echo $value->hallazgos; ?></td>
                                    <td rowspan="5" width="30%">conslusiones</td>    
                                </tr>
                                <tr>
                                    <td>Fecha : <b>{{substr($value->fechaini,0,10)}}</b></td>    
                                </tr>
                                <tr>
                                    <td><b>Dr. {{$value->d1apellido1}}</b></td>    
                                </tr>
                                <tr>
                                    <td>Asistente: {{$value->d2apellido1}}</td>    
                                </tr>
                                <tr>
                                    <td>Asistente: {{$value->d3apellido1}}</td>    
                                </tr>
                            </tbody>
                        </table>
                    </div>        
                </div>
                @endforeach
            </div>
        </div>  

        
        

    </div>
</div>

<script type="text/javascript">

function editar(id){
    //alert("{{url('estudio/editar')}}/"+id);

    location.href ="{{url('estudio/editar')}}/"+id;

}    

</script>



@include('sweet::alert')
@endsection

