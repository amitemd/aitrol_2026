
@extends('hc_admision.visita.base')

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
                @foreach($protocolo as $value)
                    @php
                        setlocale(LC_ALL, 'Spanish_Ecuador');
                        $originalDate = $value->fechaini;
                        $newDate = strftime("%A, %d de %B  de %Y %H:%M", strtotime($originalDate));
                    @endphp
                <div class="col-md-12" ondblclick="envio({{$value->protocolo}})" style="border: black 1px solid; margin: 5px 0;padding-left: 0px;padding-right: 0px">
                    <div class="table-responsive">
                        <table class="table table-striped tok" style="margin-bottom: 0px;">
                            <thead>
                                <th>Motivo</th>
                                <th colspan="8">{{$newDate}}</th>    
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="9">{{$value->motivo}}</td>    
                                </tr>
                                <tr>
                                    <td width="10%"><b>Peso:</b></td>
                                    <td width="10%">{{$value->peso}}</td>
                                    <td width="10%"><b>Presion:</b></td>
                                    <td width="10%">{{$value->presion}}</td>
                                    <td width="10%"><b>Pulso:</b></td>
                                    <td width="10%">{{$value->pulso}}</td>
                                    <td width="10%"><b>Temperatura:</b></td>
                                    <td width="10%">{{$value->temperatura}}</td>
                                    <td width="20%">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="5"><b>Hallazgos:</b></td>
                                    <td colspan="4"><b>Exámenes Realizados:</b></td>    
                                </tr> 
                                <tr>
                                    <td colspan="5"><?php echo $value->hallazgos; ?></td>
                                    <td colspan="4" rowspan="3"><b></b></td>    
                                </tr>
                                <tr>
                                    <td colspan="5"><b>Tratamiento:</b></td>    
                                </tr> 
                                <tr>
                                    <td colspan="5">{{$value->rp}}</td>    
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
    function envio(dato){
        location.href ="{{route('visita.crea_actualiza')}}/"+dato;
    }

    $(document).ready(function() {
       
        //crear_select();//subseguro
        $(".breadcrumb").append('<li class="active">Historia Clinica</li>');

    });
    
</script>


@include('sweet::alert')
@endsection

