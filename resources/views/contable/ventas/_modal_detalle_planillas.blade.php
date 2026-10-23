      <style type="text/css">
    
    .h3{
      font-family: 'BrixSansBlack';
      font-size: 12pt;
      display: block;
      background: #3d7ba8;
      color: #FFF;
      text-align: center;
      padding: 3px;
      margin-bottom: 5px;
    }

</style>
<div class="modal-content">
      <input  name="id_vent" id="id_venta" type="text" class="hidden" value="@if(!is_null($id)){{$id}}@endif">
      <div class="box-header">
            <br>
            <div class="col-md-9">
              <h3 class="box-title"><b>DETALLE PLANILLA</b></h3>
            </div>
            <div class="col-md-1 text-right">
            </div>
            <div class="col-md-2">
              <button type="button" id="cerrar" onclick="cerrar()" class="close" data-dismiss="modal">&times;</button>
            </div>
      </div>
      <div style="text-align: left" class="modal-header">
      
        <div class="box-header">
          <h3 class="box-title"><b>Agenda # {{$agenda->id}}</b></h3>
        </div>
      </div>

      <div class="box-body">
        <div class="table-responsive col-md-12">
            <table id="example2" class="table table-bordered table-hover dataTable table-striped" role="blue" aria-describedby="example2_info">
              {{-- <caption><b></b></caption> --}}
              <thead>
              <tr class="well-dark">
                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">#</th>
                <th width="50%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Procedimiento</th>
                <th width="50%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Convenio</th>
                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1"  aria-label="Codigo: activate to sort column ascending">ACCIÓN</th>
              </tr>
              </thead>
              <tbody>   
                        @if(isset($hc->procedimiento))
                        @php $i=1; @endphp
                            @foreach($hc->procedimiento as $ps)

                              @php

                                //HONORARIOS MEDICOS Y ANASTESIOLOGICOS 18/FEB/2022 VICTOR
                                $seguro = $hc->seguro;
                                $convenio = null;
                                if( $ps->id_seguro != null ){
                                    $seguro = $ps->seguro;
                                } 



                                $id_empresa = $agenda->id_empresa;
                                if($ps->id_empresa != null){
                                    $id_empresa = $ps->id_empresa;
                                }



                                $incluir_convenio = false; $convenio = null;

                                
                                
                                if($seguro->tipo == 0){ //PUBLICO
                                    $convenio = Sis_medico\Convenio::where('id_empresa',$id_empresa)->where('id_seguro',$seguro->id)->first();
                                }

                                
                                if($seguro->tipo == 1){ //PRIVADO
                                   
                                  $convenios = Sis_medico\Convenio::where('id_seguro',$seguro->id)->get(); 
                                  if($convenios->count() > 1){
                                      $incluir_convenio = true;
                                  }

                                  if($convenios->count() == 1){
                                      $incluir_convenio = false;
                                      $convenio = $convenios->first();
                                       
                                  }
                                }
                       
                              @endphp  
                             
                            <tr>
                                <td>{{$i}} - {{$ps->id}}</td>
                                <td>
                                  
                                  @foreach($ps->hc_procedimiento_f as $px)
                                    {{$px->procedimiento->nombre}}+
                                  @endforeach
                                  
                                </td> 
                                <td>Seguro: {{ $seguro->nombre}} @if(!is_null($convenio)) Nivel: {{$convenio->nivel->nombre}} @endif</td>        
                                <td>
                                  <a target="_blank" href="{{ route('planilla.costo.detalle.pdf', ['id_venta' => $id, 'id_hc_procedimiento' => $ps->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Costo</a>
                                  @if($agenda->id_seguro==2||$agenda->id_seguro==5)
                                    <a target="_blank" href="{{ route('ap_planilladetalle.planilla_detalle_contab_pdf',['id_procedimiento' => $ps->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Publico</a>
                                    <a target="_blank" href="{{ route('ap_planilladetalle.planilla_detalle_contab_pdf_vs',['id_venta' => $id,'id_procedimiento' => $ps->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">VS</a>
                                  @else
                                    <a target="_blank" href="{{ route('planilla.venta.detalle.pdf',['id_venta' => $id, 'id_hc_procedimiento' => $ps->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">Venta</a>
                                    <a target="_blank" href="{{ route('planilla.venta.vs.compra.detalle.pdf',['id_venta' => $id, 'id_hc_procedimiento' => $ps->id]) }}" class="btn btn-success col-md-3 col-xs-3 btn-margin btn-gray" style="font-size: 10px;padding-left: 2px;padding-right: 20px;">VS</a>
                                  @endif

                                </td>
                            </tr>
                            @php $i++; @endphp
                            @endforeach
                        @endif
                  
              </tbody>  
            </table> 
        </div> 
      </div> 
    

      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">CERRAR</button>
      </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">




  
  $('#example2').DataTable({
    'paging'      : false,
    'lengthChange': false,
    'searching'   : false,
    'ordering'    : false,
    'info'        : false,
    'autoWidth'   : false
  })

</script>


