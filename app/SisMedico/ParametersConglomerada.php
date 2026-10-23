<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Ct_Kardex;
use Illuminate\Support\Facades\DB;
use Session;

class ParametersConglomerada
{
    //case only humana
    /*HUMANA 
    date 22 January 2021 
    PH-METRIA
    MANO METRIA
    IMPEDANCIOMETRIA
    MANO RECTAL
    CAPSULA ENDOSCOPICA
    ENDOSCOPIA 
    COLONOSCOPIA
    CEPRE 
    ECOENDOSCOPIA 
    POEM
    ENTEROSCOPIA 
    FIBROSCAN
    CODIGO: PROCEDIMIENTOS AMBULATORIOS DE
    GATROENTEROLOGIA
    */
    protected $f = true;
    public static function getHumana($codigo = "")
    {
        //CAMBIAR AQUI SI SSE CAMBIAN LOS CODIGOS o AÑADIR
        $parameters_default = array('COLO-PENTAX', 'EDA-PENTAX', 'CPRE-PENTAX', 'PENTAX-ECO', 'manom-esofag.', 'RIECED-0165', 'MANO RECTAL', 'CAPSULA', 'POEM', 'FIBROSCAN', 'PH-PENTAX', 'RIECED-0039');
        $otherParameter = array('ECOGRAFIA');
        if ($codigo != "") {
            $codeFle = "";
            if (in_array($codigo, $parameters_default)) {
                $codeFle = "41253"; //PROCEDIMIENTOS AMBULATORIOS DE GASTROENTEROLOGIA
            }
            if (in_array($codigo, $otherParameter)) {
                $codeFle = "46877"; //ULTRASONOGRAFIA DIAGNOSTICA 
            }

            return $codeFle;
        } else {

            return false;
        }
    }
    public static function getSpot($s = "")
    {
        $parameters_default = array('COLO-PENTAX', 'EDA-PENTAX', 'CPRE-PENTAX', 'PENTAX-ECO', 'manom-esofag.', 'RIECED-0165', 'MANO RECTAL', 'CAPSULA', 'POEM', 'FIBROSCAN', 'PH-PENTAX', 'RIECED-0039');
        if ($s != "") {
            if (is_array($s, $parameters_default)) {
                return true;
            }
        } else {
            return false;
        }
    }
    public static function getParametersDefault()
    {
        $parameters_default = array('COLO-PENTAX', 'EDA-PENTAX', 'CPRE-PENTAX', 'PENTAX-ECO', 'manom-esofag.', 'RIECED-0165', 'MANO RECTAL', 'CAPSULA', 'POEM', 'FIBROSCAN', 'PH-PENTAX', 'RIECED-0039');
        return $parameters_default;
    }
    //only case u can add other case inside to view
    public function doParameters($values = "")
    {
        $parameters_default = array('COLO-PENTAX', 'EDA-PENTAX', 'CPRE-PENTAX', 'PENTAX-ECO', 'manom-esofag.', 'RIECED-0165', 'MANO RECTAL', 'CAPSULA', 'POEM', 'FIBROSCAN', 'PH-PENTAX', 'RIECED-0039');
        if ($values != "") {
            array_push($parameters_default, $values);
        }

        return $parameters_default;
    }
    //for Omni
    public static function getUses($name, $types = "")
    {
        //define type 
        //1: PARTICULARES 2:PRIVADOS 3: PUBLICOS
        if ($types != "") {
            $dataCode = "";
            if ($types == "particulares") {
                if (stristr($name, 'ENDOSCOPIA')) {
                    $dataCode = "PART-1";
                } elseif (stristr($name, 'COLONOSCOPIA')) {
                    $dataCode = "PART-2";
                } elseif (stristr($name, 'CPRE')) {
                    $dataCode = "PART-3";
                } elseif (stristr($name, 'ECOENDOSCOPIA')) {
                    $dataCode = "PART-4";
                } elseif (stristr($name, 'EDA')) {
                    $dataCode = "PART-1";
                }
            } elseif ($types == "privados") {
                if (stristr($name, 'ENDOSCOPIA')) {
                    $dataCode = "U3B-1";
                } elseif (stristr($name, 'COLONOSCOPIA')) {
                    $dataCode = "U3B-2";
                } elseif (stristr($name, 'CPRE')) {
                    $dataCode = "U3B-3";
                } elseif (stristr($name, 'ECOENDOSCOPIA')) {
                    $dataCode = "U3B-4";
                } elseif (stristr($name, 'EDA')) {
                    $dataCode = "U3B-1";
                }
            } elseif ($types == "publicos") {
                if (stristr($name, 'ENDOSCOPIA')) {
                    $dataCode = "S.PUB-1";
                } elseif (stristr($name, 'COLONOSCOPIA')) {
                    $dataCode = "S.PUB-2";
                } elseif (stristr($name, 'CPRE')) {
                    $dataCode = "S.PUB-3";
                } elseif (stristr($name, 'ECOENDOSCOPIA')) {
                    $dataCode = "S.PUB-4";
                } elseif (stristr($name, 'EDA')) {
                    $dataCode = "S.PUB-1";
                } elseif (stristr($name, 'GASTRO')) {
                    $dataCode = "S.PUB-1";
                } elseif (stristr($name, 'INTEROSCOPIO')) {
                    $dataCode = "S.PUB-1";
                } elseif (stristr($name, 'BRONCOSCOPIA')) {
                    $dataCode = "RIECED-0179";
                }
            }
            return $dataCode;
        } else {
            return false;
        }
    }
    //for omni 
    public static function particulares($seguros = [])
    {
        $sec_part = array();
        foreach ($seguros as $x) {
            // SEGUROS PRIVADOS EXCEPTO( BMI/BUPA/MEDEC/BEST DOCTORS/VUMI) CON PARTICULAR
            if ($x->id != 8 && $x->id != 9 && $x->id != 14 && $x->id != 15 && $x->id != 19) {
                if ($x->tipo == 1) {
                    array_push($sec_part, $x->nombre);
                }
                if ($x->id == 1) {
                    array_push($sec_part, $x->nombre);
                }
            }
        }
        return $sec_part;
    }
    public static function privados($seguros = [])
    {
        $sec_private = array();
        foreach ($seguros as $x) {

            // SEGUROS PRIVADOS SOLO( BMI/BUPA/MEDEC/BEST DOCTORS/VUMI)
            if ($x->id == 8 || $x->id == 9 || $x->id == 14 || $x->id == 15 || $x->id == 19) {
                array_push($sec_private, $x->nombre);
            }
        }
        return $sec_private;
    }
    public static function publicos($seguros = [])
    {
        $last_public = array();
        foreach ($seguros as $x) {
            // SEGUROS PRIVADOS SOLO( IEES/MSP/ISSFA/POLICIA)
            if ($x->id == 2 || $x->id == 6 || $x->id == 5 || $x->id == 3) {
                array_push($last_public, $x->nombre);
            }
        }
        return $last_public;
    }
    //function conglomerada
    public static function dontSee($codigo = "")
    {
        if ($codigo != "") {
            $permision = array('COMISION', 'FEE-', 'DEDUC.');
            if (in_array($codigo, $permision)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //recibo de cobro nombres que no salen por default

    public static function recibo($codigo)
    {
        $dataCode = "";
        //$permision = array('COMISION', 'FEE-', 'DEDUC.');
        if (stristr($codigo, 'FEE Administrativo')) {
            $dataCode = "COMISION";
        } elseif (stristr($codigo, 'COLONOSCOPIA')) {
            $dataCode = "COLO-PENTAX";
        } elseif (stristr($codigo, 'ENDOSCOPIA')) {
            $dataCode = "ECO-PENTAX";
        } elseif (stristr($codigo, 'ECOGRAFIA')) {
            $dataCode = "ECOGRAFIA";
        } elseif (stristr($codigo, 'EDA')) {
            $dataCode = "EDA-PENTAX";
        } elseif (stristr($codigo, 'DEDUCIBLE')) {
            $dataCode = "DEDUC.";
        } elseif (stristr($codigo, 'MANOMETRIA ESOFAGICA')) {
            $dataCode = "manom-esofag.";
        } elseif (stristr($codigo, 'DERECHO DE SALA')) {
            $dataCode = "DE-SA";
        } elseif (stristr($codigo, 'CPRE')) {
            $dataCode = "CPRE-PENTAX";
        } elseif (stristr($codigo, 'MANOMETRAI ESOFAGICA')) {
            $dataCode = "manom-esofag.";
        } elseif (stristr($codigo, 'BIOPSIA')) {
            $dataCode = "BIOP";
        } elseif (stristr($codigo, 'PH-METRIA')) {
            $dataCode = "PH-PENTAX";
        } elseif (stristr($codigo, 'PH METRIA')) {
            $dataCode = "PH-PENTAX";
        } elseif (stristr($codigo, 'COLONO')) {
            $dataCode = "COLO-PENTAX";
        } elseif (stristr($codigo, 'KIT MUCOSCTOMIA CAPTIVATOR')) {
            $dataCode = "RIECED-0186";
        } elseif (stristr($codigo, 'PHMETRIA')) {
            $dataCode = "PH-PENTAX";
        }
        return $dataCode;
    }
    public function getAnteriores($id_movimiento, $hasta, $id_empresa, $producto)
    {
        if (!is_null($id_movimiento) && !is_null($producto)) {
            //estoy drogado funcion para calcular el ultimo precio por producto ahora solo me falta calcular el ultimo paso donde retorno el producto
            $fechadesde = '2020-12-31';
            $getAnterior = "";
            $tots=0;
            $getAnterior = Ct_Kardex::where('id_empresa', $id_empresa)
                ->where('tipo', 'INVENTARIO')
                ->where('producto_id', $producto)
                ->whereBetween('fecha', [$fechadesde . " 00:00:00", $fechadesde . " 23:59:59"])
                ->orderBy('fecha', 'ASC')
                ->orderBy('id', 'ASC')
                ->select(DB::raw('SUM(cantidad) as cantidad'), DB::raw('SUM(valor_unitario) as valor_unitario'), DB::raw('SUM(total) as total'), 'fecha')->first();
            $kardex = Ct_Kardex::whereRaw('movimiento', '2')->where('producto_id', $producto)->whereBetween('fecha', [$fechadesde . ' 00:00:00', $hasta . ' 23:59:59'])->get();
            $getPrice = 0;
            $getPriceant = 0;
            $getCount = 0;
            $getTotal = 0;
            $cantidadant = 0;
            $anterior = $getAnterior->cantidad;
            if (is_null($anterior)) {
                $anterior = 0;
            }
            $cantidad = $anterior;
            $anteriorprecio = $getAnterior->valor_unitario;
            if (is_null($anteriorprecio)) {
                $anteriorprecio = 0;
            }
            $anteriortotal = $getAnterior->total;
            if (is_null($anteriortotal)) {
                $anteriortotal = 0;
            }
            //dd($anteriorprecio);
            $totalCosto = $anteriortotal;
            $precioCosto = $anteriorprecio;
            $contador = 0;
            foreach ($kardex as $value) {
                if ($value->movimiento == 1) {
                    $cantidad += $value->cantidad;
                } else {

                    $cantidad = $cantidad - $value->cantidad;
                }
                $getPrice += $value->valor_unitario;
                $getTotal += $value->total;

                if ($value->movimiento == 1) {
                    $totalCosto += $value->total;
                    if ($cantidad > 0) {
                        $precioCosto = $totalCosto / $cantidad;
                    } else {
                        $precioCosto = 0;
                    }
                } else {
                    $totalCosto = $precioCosto * $cantidad;
                    $tots = $precioCosto * $value->cantidad;
                }
            }
            $parameters = array();
            $parameters['totalCosto'] = $totalCosto;
            $parameters['precioCosto'] = $precioCosto;
            $parameters['cantidad'] = $cantidad;
            $parameters['getPrice'] =$getPrice;
            $parameters['getPriceAnterior']= $getPriceant;
            $parameters['totalVenta']= $tots;
            return $parameters;
        } else {
            return 'error';
        }
    }
    public static function regresar(){
        $parameters_default = array('1032', '1144', '1152', '1103', '1118', '1113', '1101', '1104', '1102', '1131', '1129', '1128','1324','1316','1385','1346','1329','1331','1336','1335','1334','1358','1360','1339','1375','1366','1380','1383','1381','1509','1519','1433','1384','1365','1415','1542','1416','1403','1406','1420','1422','1412','1409','1540','1400','1397','1395');
        $get= Ct_Ven_Orden::whereIn('id',$parameters_default)->where('estado_pago','1')->where('id_empresa','0992704152001')->get();
        foreach($get as $x){
            $s= Ct_Ven_Orden::find($x->id);
            $s->estado_pago=0;
            $s->save();
        }
        return response()->json(['state'=>'1','stateText'=>$get]);
    }

    public static function getProveedores($id=""){
        $id_empresa    = Session::get('id_empresa');
        $proveedores = Ct_Acreedores::where('id_empresa', $id_empresa)
        ->select('id_proveedor  as id', 
                    'razonsocial as razonsocial',
                    'nombrecomercial as nombrecomercial',
                    'ciudad as ciudad',
                    'direccion as direccion',
                    'email as email',
                    'email2 as email2',
                    'telefono1 as telefono1',
                    'banco as banco',
                    'cuenta as cuenta',
                    'telefono2 as telefono2',
                    'tipo as tipo',
                    'logo as logo',
                    'id_tipoproveedor as id_tipoproveedor',
                    'autorizacion as autorizacion',
                    'serie as serie',
                    'id_usuariocrea as id_usuariocrea',
                    'id_usuariomod as id_usuariomod',
                    'ip_creacion as ip_creacion',
                    'ip_modificacion as ip_modificacion',
                    'created_at as created_at',
                    'updated_at as updated_at',
                    'deleted_at as deleted_at',
                    'id_tipo as id_tipo',
                    'id_cuentas as id_cuentas',
                    'id_porcentaje_iva as id_porcentaje_iva',
                    'id_porcentaje_ft as id_porcentaje_ft',
                    'id_configuracion as id_configuracion',
                    'tipo_cuenta as tipo_cuenta',
                    'identificacion as identificacion',
                    'beneficiario as beneficiario');
        return $proveedores;
    }
    public static function pdf_permitidos($id){
        $parameters_default = array('0992704152001','1307189140001','1314490929001','0993170887001','0916293723001');
        if (in_array($id, $parameters_default)) {
            return true;
        } else {
            return false;
        }
    }
}
