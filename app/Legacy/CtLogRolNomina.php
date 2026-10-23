<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CtLogRolNomina extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_log_rol_nomina';

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

    public static function guarda_log_masivo_genera_roles($arr_det_nom, $id_sucursal, $id_empresa,$nom_funcion){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_det_nom as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $arr_det_nom['id_rol'],
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'GENERA ROLES MASIVO', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_masivo_horas_extras($arr_det, $id_sucursal, $id_empresa,$nom_funcion,$rol){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_det as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $rol->id,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'MASIVO HORAS EXTRAS', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_masivo_prestamos_quiro($arr_qui, $id_sucursal, $id_empresa,$nom_funcion,$tipo_pres,$rol){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_qui as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $rol->id,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion.'_'.$tipo_pres,
            'tipo' => 'MASIVO PRESTAMOS QUIROGRAFARIOS', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_masivo_prestamos_hipo($arr_hip, $id_sucursal, $id_empresa,$nom_funcion,$tipo_pres,$rol){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_hip as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $rol->id,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion.'_'.$tipo_pres,
            'tipo' => 'MASIVO PRESTAMOS HIPOTECARIOS', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guardarLog_descarga_pdf_rol_pago($rol_pago,$nom_funcion,$id_empresa,$id_sucursal,$detalle){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_log = [
            'id_rol' => $rol_pago,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'DESCARGA ROL PAGO PDF', 
            'detalle' => $detalle,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guardarLog_elimina_rol_pago($rol_pago,$nom_funcion,$id_empresa,$id_sucursal,$detalle){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_log = [
            'id_rol' => $rol_pago,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'ELIMINA ROL DE PAGO', 
            'detalle' => $detalle,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guardarLog_envia_mail_rol_pago($rol_pago,$nom_funcion,$id_empresa,$id_sucursal,$detalle){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_log = [
            'id_rol' => $rol_pago,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'ENVIA MAIL ROL DE PAGO', 
            'detalle' => $detalle,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guardarLog_visualiza_rol_pago($rol_pago,$nom_funcion,$id_empresa,$id_sucursal,$detalle){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_log = [
            'id_rol' => $rol_pago,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'VISUALIZA ROL DE PAGO', 
            'detalle' => $detalle,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_update_rol($arr_update_det, $id_sucursal, $id_empresa,$nom_funcion,$id_rol){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_update_det as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $id_rol,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'ACTUALIZA ROL', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_update_observacion_rol($arr_obs, $id_sucursal, $id_empresa,$nom_funcion,$id_rol){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_obs as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $id_rol,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'ACTUALIZA OBSERVACION ROL', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_recalcular_db_rol($arr_new_db, $id_sucursal, $id_empresa,$nom_funcion,$id_rol){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_new_db as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $id_rol,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'RECALCULAR ROL', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_agrega_prestamo_rol($arr_det_prest, $id_sucursal, $id_empresa,$nom_funcion,$id_rol){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_det_prest as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $id_rol,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'AGREGA PRESTAMO EN ROL', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_elimina_prestamo_rol($detalle, $id_sucursal, $id_empresa,$nom_funcion,$rol_pago){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_log = [
            'id_rol' => $rol_pago,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'ELIMINA PRESTAMO DE ROL PAGO', 
            'detalle' => $detalle,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_agrega_saldo_inicial_rol($arr_saldo_ini, $id_sucursal, $id_empresa,$nom_funcion,$id_rol){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_saldo_ini as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $id_rol,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'AGREGA SALDO INICIAL EN ROL', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_elimina_saldo_inicial_rol($detalle, $id_sucursal, $id_empresa,$nom_funcion,$rol_pago){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_log = [
            'id_rol' => $rol_pago,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'ELIMINA SALDO INICIAL DE ROL PAGO', 
            'detalle' => $detalle,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_agrega_otro_anticipo_rol($detalle, $id_sucursal, $id_empresa,$nom_funcion,$rol_pago){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_log = [
            'id_rol' => $rol_pago,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'AGREGA ANTICIPO EN ROL PAGO', 
            'detalle' => $detalle,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_elimina_otro_anticipo_rol($detalle, $id_sucursal, $id_empresa,$nom_funcion,$rol_pago){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_log = [
            'id_rol' => $rol_pago,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'ELIMINA ANTICIPO EN ROL PAGO', 
            'detalle' => $detalle,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_agrega_otro_anticipo_listado_rol($detalle, $id_sucursal, $id_empresa,$nom_funcion,$rol_pago){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_log = [
            'id_rol' => $rol_pago,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'AGREGA ANTICIPO EN ROL PAGO DESDE EL LISTADO', 
            'detalle' => $detalle,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_agrega_prestamo_quirografario_rol($arr_quiro, $id_sucursal, $id_empresa,$nom_funcion,$id_rol){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_quiro as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $id_rol,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'AGREGA PRESTAMO QUIROGRAFARIO EN ROL', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_elimina_prestamo_quirografario_rol($detalle, $id_sucursal, $id_empresa,$nom_funcion,$rol_pago){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_log = [
            'id_rol' => $rol_pago,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'ELIMINA PRESTAMO QUIROGRAFARIO EN ROL PAGO', 
            'detalle' => $detalle,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_agrega_prestamo_hipotecario_rol($arr_quiro, $id_sucursal, $id_empresa,$nom_funcion,$id_rol){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_quiro as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $id_rol,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'AGREGA PRESTAMO HIPOTECARIO EN ROL', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_elimina_prestamo_hipotecario_rol($detalle, $id_sucursal, $id_empresa,$nom_funcion,$rol_pago){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $arr_log = [
            'id_rol' => $rol_pago,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'ELIMINA PRESTAMO HIPOTECARIO EN ROL PAGO', 
            'detalle' => $detalle,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
    public static function guarda_log_agrega_forma_pago_rol($arr_form_pago, $id_sucursal, $id_empresa,$nom_funcion,$id_rol){
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $text = "";
        foreach($arr_form_pago as $key=>$det){
            //dd($key,$det);
            $text .= $key . ":" . $det."-";
        }
        //dd($text);
        $arr_log = [
            'id_rol' => $id_rol,
            'id_empresa' => $id_empresa,
            'id_sucursal' => $id_sucursal,
            'funcion'  => $nom_funcion,
            'tipo' => 'AGREGA FORMA DE PAGO EN ROL', 
            'detalle' => $text,
            'id_usuariocrea' => $idusuario,
            'id_usuariomod'=> $idusuario,
            'ip_creacion'=> $ip_cliente,
            'ip_modificacion'=> $ip_cliente,
        ];
        CtLogRolNomina::create($arr_log);
    }
}
