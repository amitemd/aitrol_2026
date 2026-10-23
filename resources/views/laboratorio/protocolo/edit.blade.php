@extends('laboratorio.protocolo.base')

@section('action-content')

<!-- iCheck for checkboxes and radio inputs -->
  <link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<section class="content" >
    
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border"><h3 class="box-title">Editar Protocolo</h3></div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('protocolo.update',['id' => $protocolo->id]) }}">
                        <input type="hidden" name="_method" value="PATCH">
                        {{ csrf_field() }}
                        
                    
                        
                
                        <div class="form-group col-md-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Nombre</label>
                            <div class="col-md-3">
                                <input id="nombre" class="form-control input-sm" type="text" name="nombre" value=@if(old('nombre')!='')"{{old('nombre')}}"@else"{{$protocolo->nombre}}"@endif" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        

                        <!--id_agrupador-->
                        <div class="form-group col-md-12{{ $errors->has('est_amb_hos') ? ' has-error' : '' }}">
                            <label for="est_amb_hos" class="col-md-4 control-label">Ambulatorio / Hospitalizado</label>
                            <div class="col-md-3">
                                <select id="est_amb_hos" name="est_amb_hos" class="form-control" required>
                                        <option @if(old('est_amb_hos')== '0') selected @endif @if($protocolo->est_amb_hos=='0') selected @endif value="0">Ambulatorio</option>
                                        <option @if(old('est_amb_hos')== '1') selected @endif @if($protocolo->est_amb_hos=='1') selected @endif value="1">Hospitalizado</option>
                                </select>  
                                
                                @if ($errors->has('est_amb_hos'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('est_amb_hos') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                   

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <span class="glyphicon glyphicon-floppy-disk"></span> Editar
                                </button>
                            </div>
                        </div>
                    </form>
@foreach($agrupadores as $agrupador)
<!--div class="col-md-4">    
    <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
        <div class="table-responsive">
          <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
            <thead>
              <tr role="row" style="background-color: #00bfff;">
                <th width="90%" ><b>{{$agrupador->nombre}}</b></th>
                <th >Sel.</th>
              </tr>
            </thead>
            <tbody >
            @foreach ($examenes as $value)
            @php $ch_det=false; @endphp
            @foreach($detalles as $detalle)
                @if($detalle->id_examen==$value->id)
                    @php $ch_det=true; @endphp
                @endif
            @endforeach
            @if($value->id_agrupador==$agrupador->id)
              <tr role="row">
                <td>{{$value->nombre}}</td>
                <td><input id="ch{{$value->id}}" @if($ch_det) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"</td>         
              </tr>
            @endif  
            @endforeach
            </tbody>
          </table>
        </div>
      
    </div>
</div-->    
@endforeach 



            <div class="col-md-12">                        
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='1')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                            @php $contador = $contador + 1; @endphp        
                        @if($contador=='2')
                        @php $contador=0; @endphp
                        </tr>
                        @endif
                        
                        @endif  
                        @endforeach
                        @if($contador=='1')
                        <td></td>
                        <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach 
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='3')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th width="90%"><b>{{$agrupador->nombre}}</b></th>
                            <th >Sel.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                        <tr role="row">
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
                        </tr>
                        @endif  
                        @endforeach
                    </tbody>
                </table>
            </div>
      
        </div>

        @endif    
        @endforeach   
        @foreach($agrupadores as $agrupador)
        @if($agrupador->id=='4')
    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th width="90%"><b>{{$agrupador->nombre}}</b></th>
                            <th >Sel.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                        <tr role="row">
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
                        </tr>
                        @endif  
                        @endforeach
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='2')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                            @php $contador = $contador + 1; @endphp        
                        @if($contador=='2')
                        @php $contador=0; @endphp
                        </tr>
                        @endif
                        
                        @endif  
                        @endforeach
                        @if($contador=='1')
                        <td></td>
                        <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach      
</div>

<div class="col-md-12">                        
    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='5')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                            @php $contador = $contador + 1; @endphp        
                        @if($contador=='2')
                        @php $contador=0; @endphp
                        </tr>
                        @endif
                        
                        @endif  
                        @endforeach
                        @if($contador=='1')
                        <td></td>
                        <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach

    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='6')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th colspan="4" width="90%"><b>{{$agrupador->nombre}}</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr role="row">
                        @php $contador=0; @endphp
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td> 
                            @php $contador = $contador + 1; @endphp        
                        @if($contador=='2')
                        @php $contador=0; @endphp
                        </tr>
                        @endif
                        
                        @endif  
                        @endforeach
                        @if($contador=='1')
                        <td></td>
                        <td></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
      
        </div>
    </div>
    @endif    
    @endforeach 

    @foreach($agrupadores as $agrupador)
    @if($agrupador->id=='7')
    <div class="col-md-4">    
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
      
            <div class="table-responsive">
                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                    <thead>
                        <tr role="row" style="background-color: #00bfff;">
                            <th width="90%"><b>{{$agrupador->nombre}}</b></th>
                            <th >Sel.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examenes as $value)
                        @if($value->id_agrupador==$agrupador->id)
                        <tr role="row">
                            <td>{{$value->nombre}}</td>
                            <td><input id="ch{{$value->id}}" @if(!is_null($detalles->where('id_examen',$value->id)->first())) checked @endif name="ch{{$value->id}}" type="checkbox" class="flat-green"></td>         
                        </tr>
                        @endif  
                        @endforeach
                    </tbody>
                </table>
            </div>
      
        </div>

        @endif    
        @endforeach   
        
         
</div>


   
                </div>
            
            </div>
        </div>
    </div>
    
</section>

<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>

<script type="text/javascript">
    //Flat red color scheme for iCheck
    $('input[type="checkbox"].flat-green').iCheck({
      checkboxClass: 'icheckbox_flat-green',
      radioClass   : 'iradio_flat-green'
    })

    $('input[type="checkbox"].flat-green').on('ifChecked', function(event){
            
        $.ajax({
            type: 'get',
            url: "{{ url('protocolo/examen')}}/"+this.id+"/{{$protocolo->id}}", //protocolo.examen
                       
            success: function(data){
                
            }    
        });  


    });

    $('input[type="checkbox"].flat-green').on('ifUnchecked', function(event){
        
        $.ajax({
            type: 'get',
            url: "{{ url('protocolo/examen/eliminar')}}/"+this.id+"/{{$protocolo->id}}", //protocolo.eliminar
                       
            success: function(data){
                
            }    
        });

    });     

    $(document).ready(function() {
       

        
        $(".breadcrumb").append('<li><a href="{{asset('/protocolo')}}"></i> Protocolo</a></li>');
        $(".breadcrumb").append('<li class="active">Editar</li>');
           

    });

    

</script>
@endsection
