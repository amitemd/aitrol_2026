@extends('examen_preanestesico.resultados')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{trans('Crear')}}</h3>
        </div>
        <div class="box-body">
            <form id="crear_examen_fisico" method="post" action="{{route('formato.rc_preanestesico_store')}}">
                {{ csrf_field() }}

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('anestesico') ? ' has-error' : '' }}">
                        <label for="anestesico" class="col-md-20 control-label">ANESTÉSICO </label>
                        <input type="text" name="anestesico" id="anestesico" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('quirurgico') ? ' has-error' : '' }}">
                        <label for="quirurgico" class="col-md-20 control-label">QUIRÚRGICOS </label>
                        <input type="text" name="quirurgico" id="quirurgico" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('alergicos') ? ' has-error' : '' }}">
                        <label for="alergicos" class="col-md-20 control-label">ALÉRGICOS </label>
                        <input type="text" name="alergicos" id="alergicos" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('transfusiones') ? ' has-error' : '' }}">
                        <label for="transfusiones" class="col-md-20 control-label">TRANSFUSIONES </label>
                        <input type="text" name="transfusiones" id="transfusiones" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('habitos') ? ' has-error' : '' }}">
                        <label for="habitos" class="col-md-20 control-label">HÁBITOS </label>
                        <input type="text" name="habitos" id="habitos" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('antecedentes') ? ' has-error' : '' }}">
                        <label for="antecedentes" class="col-md-20 control-label">Antecendentes Patológicos Familiares</label>
                        <input type="text" name="antecedentes" id="antecedentes" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div style="width: 50%;" class="box-body">
                    <label>Tipo</label>
                    <select class="form-control input-sm" style="width: 100%;" name="tipo" id="tipo">
                        <option value="">Seleccione un tipo</option>
                        <option value="ELECTIVA">ELECTIVA</option>
                        <option value="EMERGENCIA">EMERGENCIA</option>
                        <option value="URGENCIA">URGENCIA</option>
                    </select>
                </div>
                <br>
                <div style="width: 50%;" class="box-body">
                    <label>Riesgo Quirurgico</label>
                    <select class="form-control input-sm" style="width: 100%;" name="riesgo_quirurgico" id="riesgo_quirurgico">
                        <option value="">Seleccione</option>
                        <option value="BAJO">BAJO</option>
                        <option value="MODERADO">MODERADO</option>
                        <option value="ALTO">ALTO</option>
                    </select>
                </div>
                <label> VÍA AÉREA </label>
                <div style="width: 50%;" class="box-body">
                    <label>APERTURA BUCAL(cm) </label>
                    <select class="form-control input-sm" style="width: 100%;" name="apertura" id="apertura">
                        <option value="">Seleccione</option>
                        <option value="&lt;2"> &lt;2 </option>
                        <option value="2-2,5">2-2,5</option>
                        <option value="2.6-3">2.6-3 </option>
                        <option value="&gt;3">&gt;3 </option>
                    </select>
                </div>
                <br>
               
                <div style="width: 50%;" class="box-body">
                    <label>DISTANCIA TIROMENTONEANA(cm) </label>
                    <select class="form-control input-sm" style="width: 100%;" name="distancia" id="distancia">
                        <option value="">Seleccione</option>
                        <option value="&lt;3"> &lt;3 </option>
                        <option value="&gt;6">&gt;6 </option>
                        <option value="6 - 6.5">6 - 6.5</option>
                        <option value="&gt;6.5">&gt;6.5 </option>
                    </select>
                </div>
                <br>
                <div style="width: 50%;" class="box-body">
                    <label> MALLAMPATI </label>
                    <select class="form-control input-sm" style="width: 100%;" name="mallampati" id="mallampati">
                        <option value="">Seleccione</option>
                        <option value="I">I</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                    </select>
                </div>
                <br>
                <div style="width: 50%;" class="box-body">
                    <label>PROTRUSIÓN MANDIBULAR </label>
                    <select class="form-control input-sm" style="width: 100%;" name="protrusion" id="protrusion">
                        <option value="">Seleccione</option>
                        <option value="&lt;0"> &lt;0 </option>
                        <option value="0">0</option>
                        <option value="&gt;0">&gt;0</option>
                    </select>
                </div>
                <br>
                <div style="width: 50%;" class="box-body">
                    <label>PERIMETRO CERVICAL(cm)  </label>
                    <select class="form-control input-sm" style="width: 100%;" name="perimetro" id="peimetro">
                        <option value="">Seleccione</option>
                        <option value="&lt;40"> &lt;40 </option>
                        <option value="&gt;40">&gt;40</option>
                    </select>
                </div>
                <br>
                <div style="width: 50%;" class="box-body">
                    <label>MOVILIDAD CERVICAL(°)  </label>
                    <select class="form-control input-sm" style="width: 100%;" name="movilidad" id="movilidad">
                        <option value="">Seleccione </option>
                        <option value="&lt;35"> &lt;35 </option>
                        <option value="&gt;35">&gt;35</option>
                    </select>
                </div>
                <br>
                <div style="width: 50%;" class="box-body">
                    <label>HISTORIA DEINTUBACIÓN DIFICIL  </label>
                    <select class="form-control input-sm" style="width: 100%;" name="historia" id="historia">
                        <option value="">Seleccione</option>
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                    </select>
                </div>
                <br>
                <div style="width: 50%;" class="box-body">
                    <label>PATOLOGÍA ASOCIADA A INTUBACIÓN DIFICIL  </label>
                    <select class="form-control input-sm" style="width: 100%;" name="patologia" id="patologia">
                        <option value="">Seleccione</option>
                        <option value="SI">SI</option>
                        <option value="NO">NO</option>
                    </select>
                </div>


                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('torax') ? ' has-error' : '' }}">
                        <label for="torax" class="col-md-20 control-label">TÓRAX</label>
                        <input type="text" name="torax" id="torax" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('corazon') ? ' has-error' : '' }}">
                        <label for="corazon" class="col-md-20 control-label">CORAZÓN </label>
                        <input type="text" name="corazon" id="corazon" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('pulmones') ? ' has-error' : '' }}">
                        <label for="pulmones" class="col-md-20 control-label">PULMONES</label>
                        <input type="text" name="pulmones" id="pulmones" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('abdomen') ? ' has-error' : '' }}">
                        <label for="abdomen" class="col-md-20 control-label"> ABDÓMEN</label>
                        <input type="text" name="abdomen" id="abdomen" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('extremidades') ? ' has-error' : '' }}">
                        <label for="extremidades" class="col-md-20 control-label"> EXTREMIDADES</label>
                        <input type="text" name="extremidades" id="extremidades" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('sistema') ? ' has-error' : '' }}">
                        <label for="sistema" class="col-md-20 control-label"> SISTEMA NERVIOSO CENTRAL</label>
                        <input type="text" name="sistema" id="sistema" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('equivalente') ? ' has-error' : '' }}">
                        <label for="equivalente" class="col-md-20 control-label">EQUIVALENTE METABÓLICO (METS)</label>
                        <input type="text" name="equivalente" id="equivalente" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div style="width: 50%;" class="box-body">
                    <label> ESTADO FISICO ASA </label>
                    <select class="form-control input-sm" style="width: 100%;" name="estado_fisico" id="estado_fisico">
                        <option value="">Seleccione</option>
                        <option value="I">I</option>
                        <option value="II">II</option>
                        <option value="III">III</option>
                        <option value="IV">IV</option>
                        <option value="V">V</option>
                        <option value="VI">VI</option>
                    </select>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('riesgo') ? ' has-error' : '' }}">
                        <label for="riesgo_cardiaco" class="col-md-20 control-label">RIESGO CARDÍACO</label>
                        <input type="text" name="riesgo_cardiaco" id="riesgo_cardiaco" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('riesgo_pulmonar') ? ' has-error' : '' }}">
                        <label for="riesgo_pulmonar" class="col-md-20 control-label">RIESGO PULMONAR</label>
                        <input type="text" name="riesgo_pulmonar" id="riesgo_pulmonar" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('riesgo_tromboembolico') ? ' has-error' : '' }}">
                        <label for="riesgo_tromboembolico" class="col-md-20 control-label">RIESGO TROMBOEMBÓLICO</label>
                        <input type="text" name="riesgo_tromboembolico" id="riesgo_tromboembolico" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('liquidos_claros') ? ' has-error' : '' }}">
                        <label for="liquidos_claros" class="col-md-20 control-label">LIQUIDOS CLAROS</label>
                        <input type="text" name="liquidos_claros" id="liquidos_claros" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('leche_materna') ? ' has-error' : '' }}">
                        <label for="leche_materna" class="col-md-20 control-label">LECHE MATERNA</label>
                        <input type="text" name="leche_materna" id="leche_materna" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('solidos') ? ' has-error' : '' }}">
                        <label for="solidos" class="col-md-20 control-label">SOLIDOS</label>
                        <input type="text" name="solidos" id="solidos" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('plan_anestesico') ? ' has-error' : '' }}">
                        <label for="plan_anestesico" class="col-md-20 control-label">PLAN ANESTÉSICO</label>
                        <input type="text" name="plan_anestesico" id="plan_anestesico" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('observaciones') ? ' has-error' : '' }}">
                        <label for="observaciones" class="col-md-20 control-label">OBSERVACIONES</label>
                        <input type="text" name="observaciones" id="observaciones" size="20" class="form-control" required maxlength="255">
                    </div>
                </div>

                
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{trans('Agregar')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection