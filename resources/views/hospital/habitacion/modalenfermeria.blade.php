<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title" id="modalenfermeria">{{trans('hospitalizacion.Evoluci&oacute;nEnfermeria')}}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <form action="{{ route('hospital.medicamento_enfermeria')}}" method="POST">
        <div class="modal-body">
            {{ csrf_field() }}
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">{{trans('hospitalizacion.Paciente')}}</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="paciente" id="paciente" disabled value="{{$pacienteid->apellido1}} {{$pacienteid->apellido2}} {{$pacienteid->nombre1}} {{$pacienteid->nombre2}}">
                     <input type="hidden" name="id_paciente" id="id_paciente" value="{{$pacienteid->id}}">
                </div>
            </div>
            
            <div class="form-group">
                @foreach($prescri as $value)
                <input type="hidden" name="id_evu" value="{{$value->id_evolucion}}">
                @endforeach       
                <label style="padding-left:2px;" class="col-sm-3 col-form-label">{{trans('hospitalizacion.Evoluci&oacute;n:')}}</label>
                <textarea class="form-control" name="evolucion_enf" id="evolucion_enf" rows="3" placeholder="Escriba el mejoramiento del paciente...!"></textarea>
            </div>
             <div class="form-group">     
                <label style="padding-left:2px;" class="col-sm-3 col-form-label">{{trans('hospitalizacion.Cantidad:')}}</label>
                <input type="number" class="form-control" name="cantidad_suministrada" id="cantidad_suministrada" rows="3" placeholder="cantidad suministrada enfermero...!"></input>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Rp</label>
                    @foreach($prescri as $value)
                    <input type="hidden" name="id_evu" value="{{$value->id_evolucion}}">
                    @endforeach
                    <b  class="form-control" style="padding-bottom: 100px;" rows="10">
                        @foreach($prescri as $value)
                            {{$value->medicamento}}<br>
                        @endforeach
                    </b>
                    <!-- <div id=""  style="border: solid 1px;min-height: 120px;border-radius:3px;border: 1px solid #004AC1;"></div>
                    <input type="hidden" value="" name="rp" id=""> -->
                     
                </div>
                <div class="form-group col-md-6">
                    <label>{{trans('hospitalizacion.PrescripciondeDoctor')}}</label>
                    @foreach($prescri as $value)
                    <input type="hidden" name="id_evu" value="{{$value->id_evolucion}}">
                    @endforeach
                    <b name="" class="form-control" style="padding-bottom: 100px;" id="" rows="4">
                        @foreach($prescri as $value)
                            {{$value->prescripcion_dr}}<br>
                        @endforeach
                    </b>
                    
                </div>
            </div>
            <!-- <div class="form-row">
                <div class="form-group col-md-6">
                    <label class="col-form-label">Medicamento</label>
                   @foreach($prescri as $value)
                   <input type="text" class="form-control" disabled value="{{$value->id_nombre}}" style="color:black"></input>
                    @endforeach
                </div>
                <div class="form-group col-md-6">
                    <label class="col-form-label">Cantidad</label>
                    <input type="number" class="form-control" name="cantidad_suministrada" id="cantidad_suministrada">
                </div>
            </div> -->

        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-secondary" data-dismiss="modal">{{trans('hospitalizacion.Cerrar')}}</button>
            <button type="submit" class="btn btn-primary">{{trans('hospitalizacion.Guardar')}}</button>
        </div>
    </form>
</div>
