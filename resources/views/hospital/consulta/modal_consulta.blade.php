<div class="modal-content" style="width:auto">
    <div class="modal-header">
        <input type="hidden">
        <button style="line-height: 30px;" id="button" type="button" class="close" data-dismiss="modal">&times;</button>
        <h3 style="text-align: center;color:black;font-size:30pxt;font-weight:bold" class="modal-title">  </h3>
    </div>
    <div class="modal-body">
        <div class="box-body">
        <h4> {{trans('hospitalizacion.cuadroclinico')}}: </h4>
        <p>   <?php echo $evolucion->cuadro_clinico ?> </p>
        
        @if(count($hc_cie10)>0)
        <h4> {{trans('hospitalizacion.diagnostico')}}: </h4>
        <div class="table-responsive col-md-12">
            <table class="table table-striped" >
                <thead>
                    <th width="5%">@lang('laboratorio.codigo')</th>
                    <th width="30%">@lang('laboratorio.descripcion')</th>
                    <th width="10%">@lang('laboratorio.ingreso')/@lang('laboratorio.egreso')</th> 
                    <th width="10%">@lang('laboratorio.presuntivo')/@lang('laboratorio.definitivo')</th>      
                </thead>
                <tbody>
                
                    @foreach($hc_cie10 as $value)
                    <tr>
                        <td>{{$value->cie10}}</td>
                        <td>{{$c10_arr[$value->id]}}</td>
                        <td>{{$value->ingreso_egreso}}</td>
                        <td>{{$value->presuntivo_definitivo}}</td>
                    </tr>
                    @endforeach
                 
                </tbody>
            </table>
        </div>
        @endif 


        </div>
    </div>
</div>