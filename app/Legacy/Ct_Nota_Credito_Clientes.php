<?php

namespace Sis_medico;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ct_Nota_Credito_Clientes extends Model
{

    protected $table = 'ct_nota_credito_clientes';
    protected $guarded = [];

    public function cliente()
    {

        // /$id_empresa   = session()->get('id_empresa');
        //return $this->belongsTo('Sis_medico\Ct_Clientes_Empresa','id_cliente', 'identificacion')->where('id_empresa',$id_empresa);
        return $this->belongsTo('Sis_medico\Ct_Clientes', 'id_cliente', 'identificacion');
    }

    public function usercrea()
    {
        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function usuario()
    {

        return $this->belongsTo('Sis_medico\User', 'id_usuariocrea');
    }
    public function valorf()
    {
        return $this->belongsTo('Sis_medico\Ct_ventas', 'id_factura');
    }

    public function asientos()
    {
        return $this->belongsTo('Sis_medico\Ct_Asientos_Cabeceras', 'id_asiento_cabecera');
    }
    public function detallesNC()
    {
        return $this->hasMany('Sis_medico\Ct_Detalle_Credito_Clientes', 'id_not_cred');
    }

    public function detallesDP()
    {
        return $this->hasMany('Sis_medico\Ct_Devolucion_Productos', 'id_nota_credito');
    }

    public static function setLogDocElectronico($dato)
    {
        try {
            DB::table('de_log_facturacion')->insert($dato);
        } catch (Exception $ex) {
            echo $ex->getMessage();
            exit;
        }
    }
    public static function getNotaCredito($id = '')
    {
        $consulta = Ct_Nota_Credito_Clientes::join('de_empresa as em', 'ct_nota_credito_clientes.id_empresa', '=', 'em.id_empresa')
            ->where('ct_nota_credito_clientes.electronica', 1);
        if ($id !== '') {
            $consulta->where('ct_nota_credito_clientes.id', $id);
        } else {
            $consulta->where('ct_nota_credito_clientes.doc_electronico', 0)
                ->where(function ($q) {
                    $q->where('ct_nota_credito_clientes.punto_emision', 100)
                        ->orWhereIn('ct_nota_credito_clientes.id', function ($manual) {
                            $manual->select('dlf_manual.id_documento')
                                ->from('de_log_facturacion as dlf_manual')
                                ->where('dlf_manual.tipo_documento', '04')
                                ->where('dlf_manual.comentario', 'REPROCESO_MANUAL');
                        });
                });
        }
        return $consulta->get([
                'ct_nota_credito_clientes.id',
                'ct_nota_credito_clientes.id_empresa',
                'ct_nota_credito_clientes.created_at',
                'sucursal',
                'punto_emision',
                'secuencia',
                'subtotal',
                'impuesto',
                'sub_sin_imp',
                'total_credito',
                'total_deudas',
                'total_abonos',
                'total_nuevo_saldo',
                'subtotal0',
                'subtotal12',
                'nro_autorizacion',
                'nro_comprobante',
                'numero_factura',
                'fecha',
                'descuento',
                'id_factura',
                'id_cliente',
                'observacion',
                'concepto',
                'ct_nota_credito_clientes.id_usuariocrea',
            ]);
    }
    public static function updateSinGenerarXML($id)
    {
        $arrayDoc = [
            'doc_electronico' => 7,
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateGenerarXML($id, $clave)
    {
        $arrayDoc = [
            'doc_electronico' => 1,
            'nro_autorizacion' => $clave
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateValidacionXSD($id)
    {
        $arrayDoc = [
            'doc_electronico' => 2,
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlFirmado($id)
    {
        $arrayDoc = [
            'doc_electronico' => 3,
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateRecibidoSri($id)
    {
        $arrayDoc = [
            'doc_electronico' => 4,
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlAutorizacion($id, $clave, $secuencial)
    {
        $arrayDoc = [
            'doc_electronico' => 5,
            'nro_autorizacion' => $clave,
            'secuencia' => $secuencial
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlNoRecepcion($idG)
    {
        $arrayDoc = [
            'doc_electronico' => 9
        ];
        Ct_Nota_Credito_Clientes::where('id', $idG)->first()
            ->update($arrayDoc);
    }
    public static function updateXmlNoAutorizacion($id)
    {
        $arrayDoc = [
            'doc_electronico' => 10,
        ];
        Ct_Nota_Credito_Clientes::where('id', $id)
            ->update($arrayDoc);
    }
}
