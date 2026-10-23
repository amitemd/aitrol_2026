@extends('layouts.app-template-h')
@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6 col-xs-6" id="content">

                <div class="card col-md-12">
                    <div class="card">
                        <div class="card-header bg bg-primary">
                            <div class="row">
                                <div class="d-flex align-items-center col-md-12">
                                   <span class="sradio">!</span>
                                    <h4 class="card-title ml-25 colorbasic">
                                        Datos Principales del Paciente
                                    </h4>

                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <br>
                            <div class="row">
                                <div class="col-md-5">
                                   <label> <b> Paciente</b> </label> 
                                   <div class="col-md-12" style= "padding-left: 0px; padding-right: 0px;">
                                        <span style="font-size: 18px;" class="text-danger">@if(!empty($solicitud))  {{ $solicitud->paciente->apellido1}} {{ $solicitud->paciente->apellido2}} {{ $solicitud->paciente->nombre1}} {{ $solicitud->paciente->nombre2}} @endif</span>
                                   </div>
                                </div>
                                <div class="col-md-2">
                                   <label> <b> Identificación</b> </label> 
                                   <div class="col-md-12" style= "padding-left: 0px; padding-right: 0px;">
                                        <span>@if(!empty($solicitud)) {{$solicitud->id_paciente}} @endif</span>
                                   </div>
                                </div>
                                <div class="col-md-2">
                                   <label> <b> Edad</b> </label> 
                                   <div class="col-md-12" style= "padding-left: 0px; padding-right: 0px;">
                                        <span>{{$edad}}</span>
                                   </div>
                                </div>
                                <div class="col-md-2">
                                   <label> <b> Seguro</b> </label> 
                                   <div class="col-md-12" style= "padding-left: 0px; padding-right: 0px;">
                                        <span>@if(!empty($solicitud)) {{ $solicitud->agenda->seguro->nombre}} @endif</span>
                                   </div>
                                </div>
                            </div>
                            <br>
                            <form  id="frm">
                                <div class="row">
                                
                                    <input type="hidden" name="id_paciente" value="@if(!empty($solicitud)) {{$solicitud->id_paciente}} @endif">  
                                    <div class="col-md-8">
                                        <label><b>Alergias</b></label>
                                        <div class="col-md-12">
                                            <select id="ale_list" name="ale_list[]" class="form-control" multiple >
                                                @foreach($alergias as $ale_pac)
                                                <option selected value="{{$ale_pac->id_principio_activo}}" >{{$ale_pac->principio_activo->nombre}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div> 
                                </div>
                            </form>
                             
                        </div>
                        
                    </div>
                </div> 
				<div class="card col-md-12" id="pasos">
            	
            	</div> 
				<!-- <div class="card col-md-12" id="quirofano">
            	
            	</div>  -->
            </div>

            <div class="col-md-6 col-xs-6">
	            @include('layouts.box_quirofano')
	        </div> 


            
        </div>
    </div>

    <script src="{{ asset ("/js/jquery-ui.js")}}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset ("/js/icheck.js") }}"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script type="text/javascript">

        $('#ale_list').select2({
            placeholder: "Seleccione Medicamento...",
            minimumInputLength: 2,
            ajax: {
                url: "{{route('generico.find')}}",
                dataType: 'json',
                data: function (params) {
                    //console.log(params);
                    return {
                        q: $.trim(params.term)
                    };
                },
                processResults: function (data) {
                    //console.log(data);
                    return {
                        results: data
                    };
                },
                cache: true
            },
            tags: true,
            createTag: function (params) {
                var term = $.trim(params.term);
                return {
                    id: term.toUpperCase()+'xnose',
                    text: term.toUpperCase(),
                    newTag: true, // add additional parameters
                }
            }
        });

        $('#ale_list').on('change', function (e) {
	      guardar_alergia();
	    });

	    function guardar_alergia(){
	        $.ajax({
	          type: 'post',
	          url:"{{route('quirofano.guardar_alergia')}}", 
	          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
	          datatype: 'json',
	          data: $("#frm").serialize(),
	          success: function(data){
	            console.log(data);
	          },
	          error: function(data){

	            console.log(data.responseJSON);

	          }
	        });
	    }


    </script>
@endsection