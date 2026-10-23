
@extends('horario.base')
@section('action-content')
  <div class="container-fluid" >
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary ">
                <div class="box-header with-border ">
                  <h4 class="modal-title" id="myModalLabel">Agregar Horario Laborable :</h4>
                
                  <form method="post" action="{{route('user-management.creahorario', ['id' => $usuario->id])}}" >
                    {{csrf_field()}}
                    <div class="form-group col-md-4 {{ $errors->has('dia') ? ' has-error' : '' }}">
                      <label for="dia" class="col-md-12 control-label">Día</label>
                      <select id="dia" name="dia" class="form-control">
                        <option @if(old('dia')=="TD") selected @endif value="TD">DIAS LABORABLES</option>
                        <option @if(old('dia')=="LU") selected @endif value="LU">LUNES</option>
                        <option @if(old('dia')=="MA") selected @endif value="MA">MARTES</option>
                        <option @if(old('dia')=="MI") selected @endif value="MI">MIÉRCOLES</option>
                        <option @if(old('dia')=="JU") selected @endif value="JU">JUEVES</option>
                        <option @if(old('dia')=="VI") selected @endif value="VI">VIERNES</option>
                        <option @if(old('dia')=="SA") selected @endif value="SA">SÁBADO</option>
                        <option @if(old('dia')=="DO") selected @endif value="DO">DOMINGO</option>
                      </select>
                      @if ($errors->has('dia'))
                      <span class="help-block">
                        <strong>{{ $errors->first('dia') }}</strong>
                      </span>
                      @endif   
                    </div>

                    <div class="form-group col-md-4 bootstrap-timepicker {{ $errors->has('hora_ini') ? ' has-error' : '' }}" >
                      <label for="hora_ini" class="col-md-12 control-label">Inicio</label>       
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-clock-o"></i>
                        </div>
                        <input type="text" value="{{ old('hora_ini') }}" name="hora_ini" class="form-control timepicker" id="hora_ini" required>
                      </div>
                      @if ($errors->has('hora_ini'))
                      <span class="help-block">
                        <strong>{{ $errors->first('hora_ini') }}</strong>
                      </span>
                      @endif        
                    </div>
                    

                    <div class="form-group col-md-4 bootstrap-timepicker {{ $errors->has('hora_fin') ? ' has-error' : '' }}" >
                      <label for="hora_fin" class="col-md-12 control-label">Fin</label>       
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-clock-o"></i>
                        </div>
                        <input type="text" value="{{ old('hora_fin') }}" name="hora_fin" class="form-control timepicker" id="hora_fin" required>
                      </div>
                      @if ($errors->has('hora_fin'))
                      <span class="help-block">
                        <strong>{{ $errors->first('hora_fin') }}</strong>
                      </span>
                      @endif        
                    </div>

                    <div class="form-group">
                      <div class="col-md-6 col-md-offset-4">
                          <button type="submit" class="btn btn-primary">
                              Agregar
                          </button>
                      </div>    
                    </div>  
                  </form>
                </div>
                <div class="box-body with-border ">
                  <h4 class="modal-title" id="myModalLabel">Editar Horario Laborable :</h4>
                  <form method="post" action="{{route('user-management.editahorario', ['id' => $usuario->id])}}" >
                    {{csrf_field()}}
                    <div class="table-responsive col-md-12  col-xs-6">
                      <table id="example2" class="table table-bordered table-hover">
                        <thead>
                        <tr>
                          <th width="5%">Día</th>
                          <th width="50%">Inicio</th> 
                          <th width="50%">Fin</th>       
                          <th width="5%">Activo</th>                       
                        </tr>
                        </thead>
                        <tbody>
                        @if(!is_null($horarios))
                        @foreach ($horarios as $horario)
                        <input type="hidden" value="{{$horario->id}}" name="hid{{$horario->id}}" id="hid{{$horario->id}}">  
                        <tr>  
                          <td class="col-sm-1">@if($horario->dia=="LU"){{"LUNES"}}
                              @elseif($horario->dia=="MA"){{"MARTES"}}
                              @elseif($horario->dia=="MI"){{"MIERCOLES"}}
                              @elseif($horario->dia=="JU"){{"JUEVES"}}
                              @elseif($horario->dia=="VI"){{"VIERNES"}}
                              @elseif($horario->dia=="SA"){{"SABADO"}}
                              @elseif($horario->dia=="DO"){{"DOMINGO"}}
                              @endif
                          </td>

                          <td  class="col-sm-5">
                            <div class="form-group bootstrap-timepicker {{ $errors->has('hora_ini'.$horario->id) ? ' has-error' : '' }}" >
                              <div class="input-group  ">
                                <div class="input-group-addon  ">
                                  <i class="fa fa-clock-o "></i>
                                </div>
                                <input type="text" value="@if(old('hora_ini'.$horario->id)!='') {{old('hora_ini'.$horario->id)}} @else {{$horario->hora_ini}} @endif" name="hora_ini{{$horario->id}}" class="form-control timepicker  " id="hora_ini{{$horario->id}}" required> 
                              </div>
                              @if ($errors->has('hora_ini'.$horario->id))
                            <span class="help-block">
                              <strong>{{ $errors->first('hora_ini'.$horario->id) }}</strong>
                            </span>
                            @endif
                            </div>   
                          </td>
                          <td  class="col-sm-5">     
                            <div class="form-group bootstrap-timepicker {{ $errors->has('hora_fin'.$horario->id) ? ' has-error' : '' }}" >
                              <div class="input-group">
                                <div class="input-group-addon">
                                  <i class="fa fa-clock-o"></i>
                                </div>
                                <input type="text" value="@if(old('hora_fin'.$horario->id)!='') {{old('hora_fin'.$horario->id)}} @else {{$horario->hora_fin}} @endif" name="hora_fin{{$horario->id}}" class="form-control timepicker" id="hora_fin{{$horario->id}}" required> 
                              </div>
                              @if ($errors->has('hora_fin'.$horario->id))
                            <span class="help-block">
                              <strong>{{ $errors->first('hora_fin'.$horario->id) }}</strong>
                            </span>
                            @endif
                            </div>
                            
                          </td>    
                          <td class="col-sm-1">    
                            <input type="checkbox" id="estado{{$horario->id}}" name="estado{{$horario->id}}" value="1"  @if(old('estado{{$horario->id}}')=="1") checked @elseif($horario->estado=='1') checked @endif> 
                          </td>
                        </tr>
                        @endforeach
                        @endif
                        </tbody>
                      </table>
                    </div>
                    <div class="form-group">
                      <div class="col-md-6 col-md-offset-4">
                          <button type="submit" class="btn btn-primary">
                              Editar
                          </button>
                      </div>    
                    </div>     
                  </form>     
                </div>  
            </div>
        </div>
        <!--
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                  <h4 class="modal-title" id="myModalLabel">Actualizar Máximo de Pacientes a Atender por Día:</h4>
                  
                </div>
                <div class="box-header with-border">
                  <form method="post" action="{{route('user-management.max', ['id' => $usuario->id])}}" >
                    {{csrf_field()}}
                    <div class="form-group col-md-6">
                      <label for="max_consulta" class="col-md-12 control-label">Máximo Consultas</label>
                      <select id="max_consulta" name="max_consulta" class="form-control">
                      @for ($i = 1; $i <= 15; $i++)
                        <option {{$usuario->max_consulta == $i ? 'selected' : ''}} value={{$i}}>{{$i}}</option>
                      @endfor
                      </select>  
                    </div>

                    <div class="form-group col-md-6">
                      <label for="max_procedimiento" class="col-md-12 control-label">Máximo Procedimientos</label>
                      <select id="max_procedimiento" name="max_procedimiento" class="form-control">
                      @for ($i = 1; $i <= 15; $i++)
                        <option {{$usuario->max_procedimiento == $i ? 'selected' : ''}} value={{$i}}>{{$i}}</option>
                      @endfor
                      </select>  
                    </div>
                    <div class="form-group">
                      <div class="col-md-6 col-md-offset-4">
                          <button type="submit" class="btn btn-primary">
                              Editar
                          </button>
                      </div>    
                    </div>  
                  </form>
                </div>  
            </div>
        </div>-->
    </div>
  </div>
@endsection        