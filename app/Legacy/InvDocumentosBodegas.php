<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\InvTransaccionesBodegas;
use Sis_medico\invTipoMovimiento;
use Illuminate\Support\Facades\Auth;

class InvDocumentosBodegas extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'inv_documentos_bodegas';

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = []; 

    public function tipo_movimiento()
    {
        return $this->belongsTo('Sis_medico\invTipoMovimiento', 'id_inv_tipo_movimiento', 'id');
    }

    public function usuariocrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea', 'id');
    }

    public function usuariomodi()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariomod', 'id');
    }

    public function transaccion_bodega()
    {
        return $this->belongsTo('Sis_medico\InvTransaccionesBodegas', 'id', 'id_documento_bodega');
    }

    public static function getSecuecia($id_documento=null, $id_bodega=null) 
    {
        if ($id_documento!=null) {
            $documento = InvDocumentosBodegas::find($id_documento);
            if(isset($documento->id)) {
                $tras_bodega = InvTransaccionesBodegas::where('id_documento_bodega',$documento->id)->where('id_bodega',$id_bodega)->first();
                if (isset($tras_bodega->id)) {
                    $suecuencia = $tras_bodega->secuencia;
                    $tras_bodega->secuencia =  $tras_bodega->secuencia + 1;
                    $tras_bodega->save();
                } else {
                    $tras_bodega = new InvTransaccionesBodegas;
                    $tras_bodega->id_documento_bodega = $id_documento;
                    $tras_bodega->id_bodega = $id_bodega;
                    $tras_bodega->secuencia = 1;
                    $tras_bodega->save();
                }
                return $tras_bodega->secuencia;
            }
        }
        return 0;
    }
    
    public static function getSecueciaTipo($id_bodega=null,$vtipo=null) 
    {
        $ip_cliente           = $_SERVER["REMOTE_ADDR"];
        $idusuario            = Auth::user()->id;

        $tipo = invTipoMovimiento::where('tipo',$vtipo)->first();
        if ($tipo!=null) {
            $documento = InvDocumentosBodegas::where('id_inv_tipo_movimiento',$tipo->id)
                                                ->first();
            if(isset($documento->id)) {
                $tras_bodega = InvTransaccionesBodegas::where('id_documento_bodega',$documento->id)->where('id_bodega',$id_bodega)->first();

                if (isset($tras_bodega->id)) {
                    $suecuencia = $tras_bodega->secuencia;
                    $tras_bodega->secuencia =  $tras_bodega->secuencia + 1;
                    $tras_bodega->save();
                } else {
                    $tras_bodega = new InvTransaccionesBodegas;
                    $tras_bodega->id_documento_bodega = $documento->id;
                    $tras_bodega->id_bodega = $id_bodega;
                    $tras_bodega->secuencia = 1;
                    $tras_bodega->id_usuariocrea = $idusuario;
                    $tras_bodega->id_usuariomod = $idusuario;
                    $tras_bodega->save();
                    
                }
                return $tras_bodega->secuencia;
            }
        }
        return 0;
    }

    public static function getSecueciaTipoDocum($id_bodega=null,$vtipo=null) 
    {
        $documento = InvDocumentosBodegas::where('abreviatura_documento',$vtipo)->first();
        if ($documento!=null) {
            if(isset($documento->id)) {
                $tras_bodega = InvTransaccionesBodegas::where('id_documento_bodega',$documento->id)->where('id_bodega',$id_bodega)->first();
                if (isset($tras_bodega->id)) {
                    $suecuencia = $tras_bodega->secuencia;
                    $tras_bodega->secuencia =  $tras_bodega->secuencia + 1;
                    $tras_bodega->save();
                } else {
                    $tras_bodega = new InvTransaccionesBodegas;
                    $tras_bodega->id_documento_bodega = $documento->id;
                    $tras_bodega->id_bodega = $id_bodega;
                    $tras_bodega->secuencia = 1;
                    $tras_bodega->save();
                }
                return $tras_bodega->secuencia;
            }
        }
        return 0;
    }
    
}