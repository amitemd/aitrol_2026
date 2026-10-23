<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ct_Guia_Remision_Cabecera extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ct_cabecera_remision';
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function updateSinGenerarXML($ruc, $estab, $ptoEmi, $idG)
    {
        $arrayDoc = [
            'estado' => 7,
        ];
        Ct_Guia_Remision_Cabecera::where('establecimiento', $estab)
            ->where('punto_emision', $ptoEmi)
            ->where('id_empresa', $ruc)
            ->where('id', $idG)
            ->update($arrayDoc);
    }
    public static function getBusqueda($req)
    {
        $fechaIni = $req->desde;
        $fechaFin = strtotime('+1 day', strtotime($req->hasta));
        $fechaFin = date('Y-m-d', $fechaFin);
        $transportista = $req->transportista;
        $numGuia = $req->num_guia;
        $numDoc = $req->num_doc;
        $destinario = $req->destinatario;
        $query = Ct_Guia_Remision_Cabecera::leftjoin('transportistas as t', 'ct_cabecera_remision.ci_ruc_trasnportista', '=', 't.ci_ruc')
            ->where('ct_cabecera_remision.id_empresa', session('id_empresa'));
        if ($fechaFin != '' && $fechaIni != '') {
            $query->where('ct_cabecera_remision.created_at', '>=', $fechaIni)
                ->where('ct_cabecera_remision.created_at', '<', $fechaFin);
        } elseif ($transportista != '') {
            $query->where(function ($query) use ($transportista) {
                return $query->orWhere('razon_social', 'like', '%' . $transportista . '%')
                    ->orWhere('nombres', 'like', '%' . $transportista . '%')
                    ->orWhere('ci_ruc', 'like', '%' . $transportista . '%')
                    ->orWhere('apellidos', 'like', '%' . $transportista . '%');
            });
        } elseif ($numGuia != '') {
            $query->where('num_secuencial', 'like', '%' . (int)$numGuia . '%');
        } elseif ($numDoc != '') {
            $query->where('num_doc_destino', 'like', '%' . $numDoc . '%');
        } elseif ($destinario != '') {
            $query->where('razon_social_destinatario', 'like', '%' . $destinario . '%');
        }
        $qu = $query->get([
            'ct_cabecera_remision.id',
            'ct_cabecera_remision.id_empresa',
            'ct_cabecera_remision.created_at',
            'ct_cabecera_remision.establecimiento',
            'ct_cabecera_remision.punto_emision',
            'ct_cabecera_remision.num_secuencial',
            'ct_cabecera_remision.direccion_partida',
            'ct_cabecera_remision.ci_ruc_trasnportista',
            'ct_cabecera_remision.placa',
            'ct_cabecera_remision.fecha_ini',
            'ct_cabecera_remision.fecha_fin',
            'ct_cabecera_remision.ci_destinatario',
            'ct_cabecera_remision.direccion_destinatario',
            'ct_cabecera_remision.motivo_traslado_destinatario',
            'ct_cabecera_remision.ruta',
            'ct_cabecera_remision.tipo_documento_destinatario',
            'ct_cabecera_remision.num_doc_destino',
            'ct_cabecera_remision.fecha_autorizacion_destinatario',
            'ct_cabecera_remision.num_autorizacion_sustento',
            'ct_cabecera_remision.razon_social_destinatario',
            'ct_cabecera_remision.email_traslado_destinatario',
            'ct_cabecera_remision.codigo_est_destino',
            'ct_cabecera_remision.clave_acceso',
            'ct_cabecera_remision.estado',
            't.nombres',
            't.apellidos',
            't.razon_social',
            'ct_cabecera_remision.datos_adicionales'
        ]);
        return $qu;
    }

    public static function updateXmlNoRecepcion($ruc, $estab, $ptoEmi, $idG)
    {
        $arrayDoc = [
            'estado' => 9,
        ];
        Ct_Guia_Remision_Cabecera::where('establecimiento', $estab)
            ->where('punto_emision', $ptoEmi)
            ->where('id_empresa', $ruc)
            ->where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateXmlAutorizacion($ruc, $estab, $ptoEmi, $idG, $clave, $secuencial)
    {
        $arrayDoc = [
            'estado' => 5,
            'clave_acceso' => $clave,
            'num_secuencial' => $secuencial
        ];
        Ct_Guia_Remision_Cabecera::where('establecimiento', $estab)
            ->where('punto_emision', $ptoEmi)
            ->where('id_empresa', $ruc)
            ->where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateRecibidoSri($ruc, $estab, $ptoEmi, $idG)
    {
        $arrayDoc = [
            'estado' => 4,
        ];
        Ct_Guia_Remision_Cabecera::where('establecimiento', $estab)
            ->where('punto_emision', $ptoEmi)
            ->where('id_empresa', $ruc)
            ->where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateXmlFirmado($ruc, $estab, $ptoEmi, $idG)
    {
        $arrayDoc = [
            'estado' => 3,
        ];
        Ct_Guia_Remision_Cabecera::where('establecimiento', $estab)
            ->where('punto_emision', $ptoEmi)
            ->where('id_empresa', $ruc)
            ->where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateValidacionXSD($ruc, $estab, $ptoEmi, $idG)
    {
        $arrayDoc = [
            'estado' => 2,
        ];
        Ct_Guia_Remision_Cabecera::where('establecimiento', $estab)
            ->where('punto_emision', $ptoEmi)
            ->where('id_empresa', $ruc)
            ->where('id', $idG)
            ->update($arrayDoc);
    }
    public static function updateGenerarXML($ruc, $estab, $ptoEmi, $idG, $clave)
    {
        $arrayDoc = [
            'estado' => 1,
            'clave_acceso' => $clave
        ];
        Ct_Guia_Remision_Cabecera::where('establecimiento', $estab)
            ->where('punto_emision', $ptoEmi)
            ->where('id_empresa', $ruc)
            ->where('id', $idG)
            ->update($arrayDoc);
    }
    public static function getGuiaCabecera($id = '')
    {
        $consulta = Ct_Guia_Remision_Cabecera::join('de_empresa as em', 'ct_cabecera_remision.id_empresa', '=', 'em.id_empresa');
        if ($id !== '') {
            $consulta->where('ct_cabecera_remision.id', $id);
        } else {
            $consulta->where('ct_cabecera_remision.estado', 0);
        }
        return $consulta->get([
            'ct_cabecera_remision.id',
            'ct_cabecera_remision.id_empresa',
            'ct_cabecera_remision.created_at',
            'establecimiento',
            'punto_emision',
            'num_secuencial',
            'direccion_partida',
            'ci_ruc_trasnportista',
            'placa',
            'fecha_ini',
            'fecha_fin',
            'ci_destinatario',
            'direccion_destinatario',
            'motivo_traslado_destinatario',
            'ruta',
            'tipo_documento_destinatario',
            'num_doc_destino',
            'fecha_autorizacion_destinatario',
            'num_autorizacion_sustento',
            'razon_social_destinatario',
            'email_traslado_destinatario',
            'fecha_emision_documento',
            'clave_acceso',
            'ct_cabecera_remision.estado'
        ]);
    }
    public function nombreTrasportista()
    {
        return $this->belongsTo('Sis_medico\Trasportista', "ci_ruc_trasnportista");
    }
    public function nombreDestinatario()
    {
        return $this->belongsTo('Sis_medico\Proveedor', "ci_destinatario");
    }
    public function detalle()
    {
        return $this->belongsTo('Sis_medico\Ct_Guia_Remision_Detalle', "id", "id_cabecera_remision");
    }
    public function empresa()
    {
        return $this->belongsTo('Sis_medico\Empresa', "id_empresa");
    }
}
