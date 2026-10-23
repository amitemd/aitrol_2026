<link rel="stylesheet" href="{{asset('plugins/iCheck/all.css')}}">
<link rel="stylesheet" href="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.css")}}" />
<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>
<style type="text/css">
    .ui-corner-all
    {
        -moz-border-radius: 4px 4px 4px 4px;
    }

    .ui-widget
    {
        font-family: Verdana,Arial,sans-serif;
        font-size: 15px;
    }
    .ui-menu
    {
        display: block;
        float: left;
        list-style: none outside none;
        margin: 0;
        padding: 2px;
    }
    .ui-autocomplete
    {
        overflow-x: hidden;
        max-height: 200px;
        width:1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
        font-size: 10px;
        overflow:hidden;
        white-space:nowrap;
        text-overflow: ellipsis;
    }
    .ui-menu .ui-menu-item
    {
        clear: left;
        float: left;
        margin: 0;
        padding: 0;
        width: 100%;
    }
    .ui-menu .ui-menu-item a
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        cursor: pointer;
        background-color: #ffffff;
    }
    .ui-menu .ui-menu-item a:hover
    {
        display: block;
        padding: 3px 3px 3px 3px;
        text-decoration: none;
        color: White;
        cursor: pointer;
        background-color: #006699;
    }
    .ui-widget-content a
    {
        color: #222222;
    }
    table {
          border-collapse: collapse;
        } 
</style>
@php 

$rd = rand(); 

@endphp


<div class="col-md-12 col-sm-12 col-12" style="padding-left: 10px; padding-right: 5px; margin-bottom: 5px">
    <div class="box" style="border: 2px solid #004AC1; background-color: white; border-radius: 3px; margin-bottom: 0;">
        
        <div class="box-header with-border" style="background-color: #004AC1; color: white; font-family: 'Helvetica general3';border-bottom: #004AC1;padding: 2px;">
            <div class="row">
                <div class="col-8" style="font-size: 15px;"> 
                    <span style="margin-left: 10px;" >Formulario 053</span>
                </div>
            </div>
            <a class="btn btn-success btn-sm" href="{{route('orden_012.pdf_053',['id_orden' => $orden->id])}}" target="_blank"><span class="glyphicon glyphicon-download-alt"></span> Descargar 053</a>
        </div>
        <form id="frm_evol" action="{{route('orden_012.store_053')}}" method="POST" target="_blank">
            {{ csrf_field() }}
            <div class="box-body" style="font-size: 13px;font-family: 'Helvetica general3';">
                <div class="col-md-12" style="padding: 1px;">
                    <div class="form-row">
                            <label class="col-md-9 control-label" style="padding-left: 0px;"><b>1. Datos Institucionales:</b></label>
                             
                        <div class="form-group col-md-4">  
                            <label>Distrito/Area</label>
                            <input class="form-control input-sm" type="text" name="distrito" id="distrito" value="{{$formulario_053->distrito}}" >
                        </div>

                        <div class="form-group col-md-4">  
                            <label>Refiere o deriva a</label>
                            <input class="form-control input-sm" type="text" name="refiere" id="refiere"  value="IESS"  > 
                        </div>


                        <div class="form-group col-md-4">
                            <label> Establecimiento de Salud</label>
                            <input class="form-control input-sm" type="text" name="establecimiento" id="establecimiento" value="{{$formulario_053->establecimiento_salud}}">
                        </div>

                        <div class="form-group col-md-4">
                            <label> Servicio </label>
                            <input class="form-control input-sm" type="text" name="servicio" id="servicio" value="{{$formulario_053->servicio}}">
                        </div>

                        <div class="form-group col-md-4">
                            <label> Especialidad </label>
                            <input class="form-control input-sm" type="text" name="especialidad" id="especialidad" value="{{$formulario_053->especialidad}}">
                        </div>


                        <div class="form-group col-md-4">
                            <label>Fecha Orden</label>
                        
                            <div class="form-group">
                                <div class="input-group date" id="fecha" data-target-input="nearest">
                                    <div class="input-group-append" data-target="#fecha" data-toggle="datetimepicker">
                                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                        <input type="date" name="fecha_orden" id="fecha_orden" class="form-control input-sm datetimepicker-input" data-target="#fecha" value="{{$formulario_053->fecha}}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <label class="col-md-9 control-label" style="padding-left: 0px;"><b>2. Motivo de la Referencia o Derivación:</b></label>
                            <div class="col-md-12" style="padding: 1px;">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="limitada_capacidad" id="limitada_capacidad" @if($formulario_053->limitada_capacidad) checked @endif >Limitada capacidad resolutiva    
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="ausencia_temporal" id="ausencia_temporal" @if($formulario_053->ausencia_temporal) checked @endif > Ausencia temporal del profesional     
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="falta_profesional" id="falta_profesional" @if($formulario_053->falta_profesional) checked @endif >Falta de profesional
                                    </div>
                                    <div class="form-group col-md-3">
                                        <input class="flat-orange" type="checkbox" value="1" name="saturacion_capacidad" id="saturacion_capacidad" @if($formulario_053->saturacion_capacidad) checked @endif >Saturación de capacidad instalada     
                                    </div>
                                    <div class="form-group col-md-9">
                                        <input class="flat-orange" type="checkbox" value="1" name="otros" id="otros" @if($formulario_053->otros) checked @endif >Otros Especifique:
                                        <input class="form-control input-sm" type="text" name="texto_otros" id="texto_otros" value="{{$formulario_053->texto_otros}}"  >    
                                    </div>
                                   
                                </div>
                                
                            </div>
                        
                            <div class="col-md-12" style="padding: 1px;">
                                <div class="form-row">

                                    <div class="form-group col-md-12">
                                        <span style="font-family: 'Helvetica general';font-size: 12px">3. Resumen Clínico:</span>
                                        <div id="thistoria_clinica"  style="width: 100%; border: 2px solid #004AC1;">
                                            <?php echo $id_orden_12->cuadro_clinico ?>
                                        </div>
                                        <input type="hidden" name="cuadro_clinico" id="cuadro_clinico">
                                    </div>
                                    
                                </div>
                            </div>
                                        




                    </div>
                </div>
            </div>   
            <input type="hidden" name="id_orden" value="{{$orden->id}}">
            <input type="hidden" name="id_agenda" value="{{$agenda->id}}">

            <div class="row">
                <div class="col-3">
                    <center>
                    <button style="font-size: 12px; margin-bottom: 12px; height: 60%; width: 70%"  type="submit" class="btn btn-info btn_ordenes"><span class="fa fa-floppy-o"></span>&nbsp;&nbsp;Guardar</button>
                    </center>
                </div>
          </div> 
        </form>
    </div>
</div>

<script src="{{ asset ("/js/jquery-ui.js")}}"></script>
<script src="{{asset('plugins/iCheck/icheck.min.js')}}"></script>


<script type="text/javascript" src="{{ asset ("/librerias/moment.min.js")}}"></script>
<script type="text/javascript" src="{{ asset ("/librerias/tempusdominus-bootstrap-4.min.js")}}"></script>

<script type="text/javascript">    
    $(function () {
        $('#fecha').datetimepicker({
            format: 'DD/MM/YYYY',
        });
    });

    $("#fecha").on("dp.change", function (e) {
       guardar();
    });

    $('input[type="checkbox"].flat-orange').iCheck({
        checkboxClass: 'icheckbox_flat-orange',
        radioClass   : 'iradio_flat-orange'
    }); 

    

</script>