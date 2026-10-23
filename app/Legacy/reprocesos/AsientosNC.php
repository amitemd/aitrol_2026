<?php

namespace Sis_medico\reprocesos;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Nota_Credito_Clientes;
use Sis_medico\LogConfig;
use Sis_medico\Plan_Cuentas;

class AsientosNC extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public static function reprocesarAsientoNC($r)
    {
        try {
            //SELECT * FROM ct_nota_credito_clientes WHERE id_empresa=0993069299001 AND YEAR(fecha)=2022 AND MONTH(fecha)<12
            $NC = Ct_Nota_Credito_Clientes::where('id_empresa', session('id_empresa'))
                ->whereYear('fecha', $r->a)
                ->whereMonth('fecha', $r->m)
                ->get();
            if (count($NC) > 0) {
                foreach ($NC as $row) {
                    $idusuario = $row->id_usuariocrea;
                    $ip_creacion = $row->ip_creacion;
                    if (count($row->detallesNC) > 0) {
                        /*foreach ($row->detallesNC as $dnc) {
                            $arrayAs1 =[];
                            $arrayAs2 =[];
                            $arrayAs3 =[];
                            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                            $plan_nota_credito = Plan_Cuentas::find($id_plan_confg);
                            $arrayAs1 = [
                                'id_asiento_cabecera' => $row->id_asiento_cabecera,
                                'id_plan_cuenta'      => $plan_nota_credito->id,
                                'descripcion'         => $plan_nota_credito->nombre,
                                'haber'                => $row->total_credito,
                                'debe'                => '0',
                                'estado'              => '1',
                                'id_usuariocrea'      => $idusuario,
                                'id_usuariomod'       => $idusuario,
                                'ip_creacion'         => $ip_creacion,
                                'ip_modificacion'     => $ip_creacion,
                            ];
                            Ct_Asientos_Detalle::where('id_asiento_cabecera', $row->id_asiento_cabecera)
                                ->update($arrayAs1);
                            //$plan_nota_credito2 = Plan_Cuentas::where('id', '4.1.07.01')->first();.
                            $id_plan_confg = LogConfig::busqueda('4.1.07.01');
                            $plan_nota_credito2 = Plan_Cuentas::find($id_plan_confg);

                            $arrayAs2 = [
                                'id_asiento_cabecera' => $row->id_asiento_cabecera,
                                'id_plan_cuenta'      => $plan_nota_credito2->id,
                                'descripcion'         => $plan_nota_credito2->nombre,
                                'debe'                => $row->sub_sin_imp,
                                'haber'               => '0',
                                'estado'              => '1',
                                'id_usuariocrea'      => $idusuario,
                                'id_usuariomod'       => $idusuario,
                                'ip_creacion'         => $ip_creacion,
                                'ip_modificacion'     => $ip_creacion,
                            ];
                            Ct_Asientos_Detalle::where('id_asiento_cabecera', $row->id_asiento_cabecera)
                                ->update($arrayAs2);

                            $arrayAsIva = [];

                            if ($row->total_credito != $row->sub_sin_imp) {
                                $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
                                $plan_nota_credito3 = Plan_Cuentas::find($id_plan_confg);

                                $impuesto = Ct_Asientos_Detalle::where('id_asiento_cabecera', $row->id_asiento_cabecera)
                                    ->where('id_plan_cuenta', $plan_nota_credito3->id)
                                    ->first();
                                if ($impuesto == '') {
                                    $arrayAs3 = [
                                        'id_asiento_cabecera' => $row->id_asiento_cabecera,
                                        'id_plan_cuenta'      => $plan_nota_credito3->id,
                                        'descripcion'         => $plan_nota_credito3->nombre,
                                        'debe'                => $row->impuesto,
                                        'haber'               => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $ip_creacion,
                                        'ip_modificacion'     => $ip_creacion,
                                    ];
                                    Ct_Asientos_Detalle::insert($arrayAs3);
                                } else {
                                    $arrayAs3 = [
                                        'id_asiento_cabecera' => $row->id_asiento_cabecera,
                                        'id_plan_cuenta'      => $plan_nota_credito3->id,
                                        'descripcion'         => $plan_nota_credito3->nombre,
                                        'debe'                => $row->impuesto,
                                        'haber'               => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $ip_creacion,
                                        'ip_modificacion'     => $ip_creacion,
                                    ];
                                    Ct_Asientos_Detalle::where('id_asiento_cabecera', $row->id_asiento_cabecera)
                                        ->update($arrayAs3);
                                }
                            }
                        }*/
                    } elseif (count($row->detallesDP) > 0) {
                        foreach ($row->detallesDP as $ddp) {
                            $arrayAs1 =[];
                            $arrayAs2 =[];
                            $arrayAs3 =[];
                            $id_plan_confg = LogConfig::busqueda('1.01.02.05.01');
                            $plan_nota_credito = Plan_Cuentas::find($id_plan_confg);
                            $arrayAs1 = [
                                'id_asiento_cabecera' => $row->id_asiento_cabecera,
                                'id_plan_cuenta'      => $plan_nota_credito->id,
                                'descripcion'         => $plan_nota_credito->nombre,
                                'haber'                => $row->total_credito,
                                'debe'                => '0',
                                'estado'              => '1',
                                'id_usuariocrea'      => $idusuario,
                                'id_usuariomod'       => $idusuario,
                                'ip_creacion'         => $ip_creacion,
                                'ip_modificacion'     => $ip_creacion,
                            ];
                            Ct_Asientos_Detalle::where('id_asiento_cabecera', $row->id_asiento_cabecera)
                                ->update($arrayAs1);
                            //$plan_nota_credito2 = Plan_Cuentas::where('id', '4.1.07.01')->first();.
                            $id_plan_confg = LogConfig::busqueda('4.1.07.01');
                            $plan_nota_credito2 = Plan_Cuentas::find($id_plan_confg);

                            $arrayAs2 = [
                                'id_asiento_cabecera' => $row->id_asiento_cabecera,
                                'id_plan_cuenta'      => $plan_nota_credito2->id,
                                'descripcion'         => $plan_nota_credito2->nombre,
                                'debe'                => $row->sub_sin_imp,
                                'haber'               => '0',
                                'estado'              => '1',
                                'id_usuariocrea'      => $idusuario,
                                'id_usuariomod'       => $idusuario,
                                'ip_creacion'         => $ip_creacion,
                                'ip_modificacion'     => $ip_creacion,
                            ];
                            Ct_Asientos_Detalle::where('id_asiento_cabecera', $row->id_asiento_cabecera)
                                ->update($arrayAs2);

                            if ($row->total_credito != $row->sub_sin_imp) {
                                $id_plan_confg = LogConfig::busqueda('2.01.07.01.01');
                                $plan_nota_credito3 = Plan_Cuentas::find($id_plan_confg);

                                $impuesto = Ct_Asientos_Detalle::where('id_asiento_cabecera', $row->id_asiento_cabecera)
                                    ->where('id_plan_cuenta', $plan_nota_credito3->id)
                                    ->first();
                                if ($impuesto == '') {
                                    $arrayAs3 = [
                                        'id_asiento_cabecera' => $row->id_asiento_cabecera,
                                        'id_plan_cuenta'      => $plan_nota_credito3->id,
                                        'descripcion'         => $plan_nota_credito3->nombre,
                                        'debe'                => $row->impuesto,
                                        'haber'               => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $ip_creacion,
                                        'ip_modificacion'     => $ip_creacion,
                                    ];
                                    Ct_Asientos_Detalle::insert($arrayAs3);
                                } else {
                                    $arrayAs3 = [
                                        'id_asiento_cabecera' => $row->id_asiento_cabecera,
                                        'id_plan_cuenta'      => $plan_nota_credito3->id,
                                        'descripcion'         => $plan_nota_credito3->nombre,
                                        'debe'                => $row->impuesto,
                                        'haber'               => '0',
                                        'estado'              => '1',
                                        'id_usuariocrea'      => $idusuario,
                                        'id_usuariomod'       => $idusuario,
                                        'ip_creacion'         => $ip_creacion,
                                        'ip_modificacion'     => $ip_creacion,
                                    ];
                                    Ct_Asientos_Detalle::where('id_asiento_cabecera', $row->id_asiento_cabecera)
                                        ->update($arrayAs3);
                                }
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            echo json_encode(['Error' => $e->getMessage(), 'Linea' => $e->getLine()]);
            exit;
        }
    }
}
