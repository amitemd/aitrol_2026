<style type="text/css">
  .parent{
    overflow-y:scroll;
    height: 462px;
  }
  .parent::-webkit-scrollbar{
    width: 8px;
  } /* this targets the default scrollbar (compulsory) */
  .parent::-webkit-scrollbar-thumb{
      background: #FFFFFF;
      border-radius: 10px;
  }
  .parent::-webkit-scrollbar-track{
    width: 10px;
    background-color: #004AC1; 
    box-shadow: inset 0px 0px 0px 3px #FFFFFF;
  }
  .boton-lab{
    font-size: 14px ;
    width: 90%;
    background-color: #004AC1; 
    color: white; 
    text-align: center;
    margin-bottom: 10px;
  } 
</style>

<div class="card">
    <div class="card-header bg bg-primary">
        <div class="row">
            <div class="col-md-6">
                <label class="colorbasic sradio" > 5 </label> 
            </div>
            <div class="col-md-6">
                <label class="colorbasic" > {{trans('hospitalizacion.SolicituddeExámenes')}} </label>
            </div>
        </div>
    </div>
    <div style="border-left-width: 20px; padding-left: 15px; padding-right: 10px; padding-top: 20px;padding-bottom: 0px; background-color: #FFFFFF; margin-left: 0px">
            <!--Hacemos el llamado al estilo parent-->
            <div class="parent" style="background-color: #FFFFFF">
              <div style=" margin-right: 30px;">
                 <!-- FOREACH DE LAS ORDENES DE LABORATORIO-->
                  <div class="card" style="border: 2px solid #004AC1; border-radius: 10px; background-color: white; font-size: 13px;  margin-bottom: 10px;margin-top: 0px;">
                    <!--Cabecera-->
                    <div class="card-header with-border" style=" text-align: center; border-bottom: #004AC1;">
                        <span> <b class="card-title">{{trans('hospitalizacion.FECHAORDEN')}}</b></span>
                      <div class="pull-right card-tools ">
                        <button  type="button" class="btn btn-info btn-sm" data-widget="collapse" title="" data-original-title="Collapse" id="fili">
                        <i class="fa fa-minus"></i>
                        </button>
                      </div>
                    </div>
                    <!--Cuerpo-->
                    <div class="card-body">
                      <div style="margin-left: 0px;margin-bottom: 10px; margin-right: 10px">
                        <div class="col-12">
                          <div class="row ">
                            <div class="col-md-8 col-12" style="border: 2px solid #004AC1;margin-left: 10px;margin-right: 10px;padding-right: 0px;padding-left: 0px;border-radius: 10px;background-color: white; height: 100%; margin-bottom: 10px">
                              <div class="col-12"  style="background-color: #004AC1; color: white;border-bottom: #004ac1; text-align: center ">
                                  <label class="card-title" style="background-color: #004AC1;  font-size: 16px; color: white;">
                                  {{trans('hospitalizacion.DetalledelaOrdendeLaboratorio')}}
                                  </label>
                              </div>
                              <br>
                              <div class="row">
                                <!--Obtenemos el nombre del Doctor de cada Orden de Laboratorio-->
                                <div class="form-group col-12" style="padding-right: 0px">
                                  <label for="id_doctor_ieced" class="col-12" >{{trans('hospitalizacion.Médico')}}</label>
                                  <div class="col-12">
                                    
                                  </div>
                                </div>
                                <div class="form-group col-12" style="padding-right: 0px">
                                  <label for="id_doctor_ieced" class="col-12" >{{trans('hospitalizacion.Seguro')}}</label>
                                  <div class="col-12">
                                    
                                  </div>
                                </div>
                              </div>
                            </div>
                            
                            <div class="col-md-3 col-12">
                              <button class="btn btn-primary btn-sm" > <i class="fa fa-download"></i> &nbsp;Cotizacion</button>
                              <br>
                              <br>
                              <button class="btn btn-primary btn-sm"> <i class="fa fa-download"></i>{{trans('hospitalizacion.Resultados')}}</button>
                            </div>
                            
                          </div>  
                        </div>
                      </div>                   
                    </div>
                  </div>
          
              </div>
            </div>
          </div>



  

</div>