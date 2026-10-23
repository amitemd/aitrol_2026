<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class De_Info_Tributaria extends Model
{
    //
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'de_info_tributaria';


    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function updateNumDocumento($idEm, $numDocumento, $secuancialDocumento, $id_sucursal, $id_caja, $id_maestro)
    {
        $secuancialDocumento = (int) $secuancialDocumento;
        $array = [
            'numero_factura' => $numDocumento,
            'secuencial_nro' => $secuancialDocumento,
        ];
        De_Info_Tributaria::where('id_maestro_documentos', $id_maestro)
            ->where('id_sucursal', $id_sucursal)
            ->where('id_caja', $id_caja)
            ->where('id_empresa', $idEm)
            ->where('secuencial_nro', '<', $secuancialDocumento)
            ->update($array);
    }

    public static function getDatos($id, $esta, $emis, $idDoc)
    {
        return De_Info_Tributaria::where('id_empresa', $id)
            ->where('cod_sucursal', $esta)
            ->where('cod_caja', $emis)
            ->where('id_maestro_documentos', $idDoc)
            ->where('estado', 1)
            ->first([
                'secuencial_nro',
                'id_sucursal',
                'id_caja',
                'id_maestro_documentos'
            ]);
    }

    public static function reservarSecuencial($id, $esta, $emis, $idDoc)
    {
        return DB::transaction(function () use ($id, $esta, $emis, $idDoc) {
            $datos = De_Info_Tributaria::where('id_empresa', $id)
                ->where('cod_sucursal', $esta)
                ->where('cod_caja', $emis)
                ->where('id_maestro_documentos', $idDoc)
                ->where('estado', 1)
                ->lockForUpdate()
                ->first();

            if ($datos === null) {
                return null;
            }

            $ultimoGenerado = DB::table('de_documentos_electronicos')
                ->where('id_maestro_documento', $idDoc)
                ->where('establecimiento', $esta)
                ->where('emision', $emis)
                ->where('ruc_emisor', $id)
                ->whereNotNull('secuencial')
                ->orderByRaw('CAST(secuencial AS UNSIGNED) DESC')
                ->first(['secuencial']);

            $ultimoSecuencial = (int) $datos->secuencial_nro;
            if ($ultimoGenerado !== null) {
                $ultimoSecuencial = max($ultimoSecuencial, (int) $ultimoGenerado->secuencial);
            }
            if ($ultimoSecuencial >= 999999999) {
                throw new \RuntimeException('El secuencial tributario alcanzó el máximo permitido de nueve dígitos.');
            }

            $reservado = $ultimoSecuencial + 1;
            $datos->secuencial_nro = $reservado;
            $datos->numero_factura = str_pad($esta, 3, '0', STR_PAD_LEFT)
                . '-' . str_pad($emis, 3, '0', STR_PAD_LEFT)
                . '-' . str_pad($reservado, 9, '0', STR_PAD_LEFT);
            $datos->save();

            return $datos;
        });
    }

    public static function sincronizarSecuencialAsignado($id, $esta, $emis, $idDoc, $secuencial)
    {
        return DB::transaction(function () use ($id, $esta, $emis, $idDoc, $secuencial) {
            $datos = De_Info_Tributaria::where('id_empresa', $id)
                ->where('cod_sucursal', $esta)
                ->where('cod_caja', $emis)
                ->where('id_maestro_documentos', $idDoc)
                ->where('estado', 1)
                ->lockForUpdate()
                ->first();

            if ($datos === null) {
                return null;
            }

            $secuencial = (int) $secuencial;
            if ($secuencial < 1 || $secuencial > 999999999) {
                throw new \InvalidArgumentException('El secuencial asignado no es válido.');
            }

            if ((int) $datos->secuencial_nro < $secuencial) {
                $datos->secuencial_nro = $secuencial;
                $datos->numero_factura = str_pad($esta, 3, '0', STR_PAD_LEFT)
                    . '-' . str_pad($emis, 3, '0', STR_PAD_LEFT)
                    . '-' . str_pad($secuencial, 9, '0', STR_PAD_LEFT);
                $datos->save();
            }

            return $datos;
        });
    }
}
