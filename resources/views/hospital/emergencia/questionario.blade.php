@extends('hospital.base')
@section('action-content')
<div class="col-md-12">
	    <div class="box" style="border-color: #FDFEFE; border-radius: 30px;">
          <div class="box-header" style="color: white; padding: 5px; border-radius: 30px; background-image: linear-gradient(to right, #004AC1,#004AC1,#004AC1); margin-bottom: 5px">
            <h6  style="text-align:center; font-family: Montserrat Bold;">MÉDICO (SOLICITANTE)</h6>
            <div class="box-tools pull-right">
              <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                <i class="fa fa-minus"></i>
              </button>
            </div>
          </div>
          <div class="box-body collaspe in">
            <div class="col-md-12">
               <div class="row">
               		<div class="col-md-4">
               			<label style="font-family: Montserrat Bold;">Servicio</label>
               		</div>
               		<div class="col-md-4">
               			<label style="font-family: Montserrat Bold;">Médico Especialista</label>
               		</div>
               		<div class="col-md-4">
               			<label style="font-family: Montserrat Bold;"   >TIPO</label>
               		</div>

               </div>
            </div>  

            <div class="col-md-12 mb-3">
              <div class="row">
               	<div class="col-md-4">
               	 	<input class="col-md-12" type="servicio" name="" style="border-radius: 5px; border: 1px solid #BFC9CA;">

               	</div>
               	<div class="col-md-4">
               		<input class="col-md-12" type="servicio" name="" style="border-radius: 5px; border: 1px solid #BFC9CA;">
               	</div> 
               	<div class="col-md-4">
               		<input class="col-md-12" type="servicio" name="gruposanguineo" value="{{$formnularioid->gruposanguineo}}" style="border-radius: 5px; border: 1px solid #BFC9CA;">
               	</div>            
              </div>
            </div>
 
            <div class="col-md-12">
             	 <div class="row">
               		<div class="col-md-2">
               			<label style="font-family: Montserrat Bold;">Datos Clínicos</label>
               		</div>
               		<div class="col-md-10">
               			<div class="form-group" style="border-radius: 5px; border: 1px solid #BFC9CA;">
                      <input class="form-control" rows="5" id="comment" name="observacion" value="{{$formnularioid->observacion}}">
                        
                      </input>
                    </div>
               		</div>
               		<div class="col-md-2">
               				<label style="font-family: Montserrat Bold;"> Diagnóstico o Síndrome: </label>
               		</div>
               		<div class="col-md-6" >
               			<div>
                      <textarea class="form-control" >
                      {{$formnularioid->historia_clinica}}"
                      </textarea >
                    </div>
               		</div>
               		<div class="col-md-4" style="">
               			<div class="row">
                      <div class="col-md-3" style="margin-top: 75px;">
                        <h6 style="font-family: Montserrat Bold;">CIE-10</h6>
                      </div>
                      <div class="col-md-9">
                        <div class="row">
                          <div class="col-md-6">
                            <h6 style="font-family: Montserrat Bold;">Presuntivo</h6>
                            <textarea name="" class="form-control" rows="5" id="#"></textarea>
                          </div>
                          <div class="col-md-6">
                            <h6 style="font-family: Montserrat Bold;">Definitivo</h6>
                            <textarea name="" class="form-control" rows="5" id="#"></textarea>
                          </div>
                        </div>
                      </div>
                    </div>
               		</div>
                 </div>
              </div>
              <br>
              <div class="col-md-12">
             	 <div class="row">
               		<div class="col-md-2">
               			<label style="font-family: Montserrat Bold;">Exámen Solicitado: </label>
               		</div>
               		<div class="col-md-10">
               			<div class="form-group">
                      <textarea class="form-control" rows="5" id="comment">
                        
                      </textarea>
                    </div>
               		</div>
               		<div class="col-md-2">
               				<label style="font-family: Montserrat Bold;"> Transferido a: </label>
               		</div>
               		<div class="col-md-10">
               			<div class="form-group">
                      <textarea class="form-control" rows="5" id="comment">
                        
                      </textarea>
                    </div>
               		</div>

                 </div>
              </div>
             <div class="col-md-12">
	             	 <div class="row">
	               		<div class="col-md-2">
	               			<label style="font-family: Montserrat Bold;">Otras Anotaciones: </label>
	               		</div>
	               		<div class="col-md-10">
	               			<div class="form-group">
                        <textarea class="form-control" rows="5" id="comment">
                          
                        </textarea>
                      </div>
	               		</div>
                  </div>
                </div>
	         <div class="col-md-12">
	         	<div class="row">
	         		<div class="col-md-6">
	         			 <section  style=" font-family: Montserrat Bold; margin: 2px; color: white; text-align:center; padding: 10px; border-radius: 30px; background-image: linear-gradient(to right,#004AC1,#0C8BEC,#004AC1);  margin-bottom: 15px;">
        					CALIFICACIÓN DEL DERECHO
       					 </section>
       					 <img src="{{asset('/')}}hc4/img/firma_prueba.png" style="width: 200px; margin-left: 300px;">

	         		</div>
	         		<div class="col-md-6">
	         			 <section  style=" font-family: Montserrat Bold; margin: 2px; color: white; text-align:center; padding: 10px; border-radius: 30px; background-image: linear-gradient(to right,#004AC1,#0C8BEC,#004AC1);  margin-bottom: 15px;">
        					MÉDICO
       					 	</section>
       					 	<img src="{{asset('/')}}hc4/img/firma_prueba.png" style="width: 200px; margin-left: 300px;">
	         		</div>
	         	</div>
	         </div>
            
         </div>
       </div>
	</div>


</div>





@endsection