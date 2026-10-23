<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class  LogAuditoriaPublica extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'log_auditoria_publica';


    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];  

    public function usuario()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }

    public static function guardarLog($id_plano, $historia_clinica, $id_seguro,$funcion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $seguro=Seguro::find($id_seguro);
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $id_seguro,
            'funcion'  => $funcion,
            'tipo' => 'CREAR CABECERA', 
            'detalle' => 'LIQUIDACIÓN PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_visualizar($id_plano, $historia_clinica, $id_seguro,$funcion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $seguro=Seguro::find($id_seguro);
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $id_seguro,
            'funcion'  => $funcion,
            'tipo' => 'VISUALIZA LIQUIDACION', 
            'detalle' => 'VISUALIZA LIQUIDACIÓN PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_actualizar($id_plano, $historia_clinica, $id_seguro,$funcion,$nom_procedimiento,$cie10,$estado,$codigo_iess,$fecha_autorizacion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $seguro=Seguro::find($id_seguro);
        //dd($historia_clinica);
        $historia_clinica=Historiaclinica::find($historia_clinica);
        //dd($historia_clinica);
        if($estado == 1){
            $estado = 'ACTIVO';
        }else{
            $estado = 'INACTIVO';
        }
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $id_seguro,
            'funcion'  => $funcion,
            'tipo' => 'ACTUALIZA CABECERA LIQUIDACION', 
            'detalle' => 'ACTUALIZA CABECERA LIQUIDACIÓN PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1.' Procedimiento: '.$nom_procedimiento.' Diagnostico: '.$cie10.' Estado: '.$estado.' Codigo derivacion: '.$codigo_iess.' Fecha Autorizacion: '.$fecha_autorizacion, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }

    public static function guardarLog_actualizar2($id_plano, $funcion, $detalle){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        

        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $archivo_plano->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo' => 'ACTUALIZA CABECERA LIQUIDACION', 
            'detalle' => $detalle, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_agrega_item($id_plano,$funcion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo' => 'AGREGA ITEM LIQUIDACION', 
            'detalle' => 'AGREGA ITEM LIQUIDACIÓN PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_agrega_plantilla($id_plano,$funcion,$plantilla){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo' => 'AGREGA POR PLANTILLA LIQUIDACION', 
            'detalle' => 'AGREGA POR PLANTILLA PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1.' Plantilla: '. $plantilla, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_agrega_laboratorio($id_plano,$funcion,$orden){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo' => 'AGREGA POR B. LABORATORIO LIQUIDACION', 
            'detalle' => 'AGREGA POR B. LABORATORIO PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1.' Orden: '. $orden, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_descarga_excel_pindividual($id_plano,$funcion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo' => 'DESCARGA PLANILLA INDIVIDUAL XLSX LIQUIDACION', 
            'detalle' => 'DESCARGA PLANILLA INDIVIDUAL XLSX PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_descarga_pdf_pindividual($id_plano,$funcion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo'   => 'DESCARGA PLANILLA INDIVIDUAL PDF LIQUIDACION', 
            'detalle' => 'DESCARGA PLANILLA INDIVIDUAL PDF PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }

    public static function guardarLog_descarga_informe_descargo($id_plano, $funcion)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);

        if (is_null($archivo_plano)) {
            return;
        }

        $seguro = Seguro::find($archivo_plano->id_seguro);
        $historia_clinica = Historiaclinica::find($archivo_plano->id_hc);
        $paciente = is_null($historia_clinica) ? null : $historia_clinica->paciente;

        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $archivo_plano->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion' => $funcion,
            'tipo' => 'DESCARGA INFORME DESCARGO PDF LIQUIDACION',
            'detalle' => 'DESCARGA INFORME DESCARGO PDF PÚBLICA SEGURO '.(!is_null($seguro) ? $seguro->nombre : '').' paciente: '.(!is_null($paciente) ? $paciente->apellido1.' '.$paciente->nombre1 : $archivo_plano->id_paciente),
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);
    }
    public static function guardarLog_descarga_nuevo_pdf_pindividual($id_plano,$funcion,$num_paciente){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo'   => 'DESCARGA NUEVA PLANILLA INDIVIDUAL PDF LIQUIDACION', 
            'detalle' => 'DESCARGA NUEVA PLANILLA INDIVIDUAL PDF PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1 . ' Numero Paciente: ' .$num_paciente , 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_elimanar_todo($id_plano,$funcion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo'   => 'ELIMINA TODO P. INDIVIDUAL LIQUIDACION', 
            'detalle' => 'ELIMINA TODO P. INDIVIDUAL LIQUIDACION PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_edita_detalle($id_plano,$funcion, $cantidad,$fecha,$precio){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo'   => 'ACTUALIZA DETALLE P. INDIVIDUAL LIQUIDACION', 
            'detalle' => 'ACTUALIZA DETALLE P. INDIVIDUAL LIQUIDACION PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1.' Fecha: '.$fecha.' Cantidad: '.$cantidad.' Valor unitario: '.$precio, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_elimina_detalle($id_detalle,$funcion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano_det = Archivo_Plano_Detalle::find($id_detalle);
        $archivo_plano = Archivo_Plano_Cabecera::find($archivo_plano_det->id_ap_cabecera);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $archivo_plano->id,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo'   => 'ELIMINA DETALLE P. INDIVIDUAL LIQUIDACION', 
            'detalle' => 'ELIMINA DETALLE P. INDIVIDUAL LIQUIDACION PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_agrega_item_inv($id_plano,$funcion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo' => 'AGREGA ITEM INDIVIDUAL LIQUIDACION', 
            'detalle' => 'AGREGA ITEM INDIVIDUAL LIQUIDACIÓN PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_agrega_boton_insumo($id_plano,$funcion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo' => 'INGRESO DESDE BOTON INSUMOS LIQUIDACION', 
            'detalle' => 'INGRESO DESDE BOTON INSUMOS LIQUIDACIÓN PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }
    public static function guardarLog_agrega_boton_medicina($id_plano,$funcion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        
        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo' => 'INGRESO DESDE BOTON MEDICINA LIQUIDACION', 
            'detalle' => 'INGRESO DESDE BOTON MEDICINA LIQUIDACIÓN PÚBLICA SEGURO '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }

    public static function cerrar_liquidacion($id_plano, $funcion){

        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;

        //dd($historia_clinica);
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);
        $seguro=Seguro::find($archivo_plano->id_seguro);
        $historia_clinica=Historiaclinica::find($archivo_plano->id_hc);
        //dd($historia_clinica);
       
        $paciente=$historia_clinica->paciente; 
        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $historia_clinica->agenda->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion'  => $funcion,
            'tipo'   => 'CERRAR LIQUIDACION', 
            'detalle' => 'CIERRA LIQUIDACION CON '.$seguro->nombre.' paciente: '.$paciente->apellido1 .' '.$paciente->nombre1, 
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);

    }

    public static function guardarLog_asigna_doctor($id_plano, $funcion)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $archivo_plano = Archivo_Plano_Cabecera::find($id_plano);

        if (is_null($archivo_plano)) {
            return;
        }

        $seguro = Seguro::find($archivo_plano->id_seguro);
        $historia_clinica = Historiaclinica::find($archivo_plano->id_hc);
        $paciente = is_null($historia_clinica) ? null : $historia_clinica->paciente;

        $arr_log = [
            'id_cabecera' => $id_plano,
            'id_empresa' => $archivo_plano->id_empresa,
            'id_seguro' => $archivo_plano->id_seguro,
            'funcion' => $funcion,
            'tipo' => 'CARGA DOCTOR ARCHIVO PLANO',
            'detalle' => 'CARGA DOCTOR LIQUIDACIÓN PÚBLICA SEGURO '.(!is_null($seguro) ? $seguro->nombre : '').' paciente: '.(!is_null($paciente) ? $paciente->apellido1.' '.$paciente->nombre1 : $archivo_plano->id_paciente),
            'id_usuariocrea' => $idusuario,
            'id_usuariomod' => $idusuario,
            'ip_creacion' => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
        ];
        LogAuditoriaPublica::create($arr_log);
    }




}
