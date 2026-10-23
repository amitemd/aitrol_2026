<?php

namespace Sis_medico\Http\Controllers\contable;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Response;
use Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Sis_medico\Ct_Acreedores;
use Sis_medico\Ct_Tipo_Pago;
use Sis_medico\Ct_productos;
use Sis_medico\Ct_Tipo_Tarjeta;
use Sis_medico\Ct_Caja_Banco;
use Sis_medico\Ct_Bancos;
use Sis_medico\Ct_compras;
use Sis_medico\Ct_Divisas;
use Sis_medico\Ct_detalle_compra;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Ct_Asientos_Detalle;
use Sis_medico\Ct_Anticipo_Proveedores;
use Sis_medico\Ct_Factura_Contable;
use Sis_medico\Ct_master_tipos;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Ct_rubros;
use Sis_medico\Proveedor;
use Sis_medico\Bodega;
use Sis_medico\Pais;
use Sis_medico\User;
use Sis_medico\Empresa;
use Sis_medico\Marca;
use Sis_medico\Ct_Detalle_Anticipo_Proveedores;
use Sis_medico\Validate_Decimals;
use laravel\laravel;
use Carbon\Carbon;


class CuentasPagarController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20,22)) == false) {
            return true;
        }
    }
    public function index(Request $request,$id,$tipo){
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        $empresa= Empresa::where('id',$id_empresa)->first();
        $tipo_pago = Ct_Tipo_Pago::where('estado', '1')->get();
        $compras=null;
        $contable=null;
        $proveedor=null;
        $facturas=null;
        $anticipos=null;
        if($tipo!='1'){
            $compras=Ct_Compras::find($id);
            //$proveedor= Proveedor::where('id',$compras->proveedor)->first();
            $proveedor= Ct_Acreedores::where('id_proveedor',$compras->proveedor)->where('id_empresa', $id_empresa)->first();
            $facturas= Ct_Compras::where('proveedor',$compras->proveedor)->where('estado','>','1')->get();
            $anticipos= Ct_Anticipo_Proveedores::where('id_proveedor',$compras->proveedor)->get();
        }else{
            $contable=Ct_Factura_Contable::find($id);
            //$proveedor= Proveedor::where('id',$contable->proveedor)->first();
            $proveedor= Ct_Acreedores::where('id_proveedor',$contable->proveedor)->where('id_empresa', $id_empresa)->first();
            $facturas= Ct_Factura_Contable::where('proveedor',$contable->proveedor)->where('estado','>','1')->get();
            $anticipos= Ct_Anticipo_Proveedores::where('id_proveedor',$contable->proveedor)->get();
        }
        $tipo_tarjeta = Ct_Tipo_Tarjeta::all();
        $lista_banco  = Ct_Caja_Banco::where('clase','1')->get();
        $caja= Ct_Caja_Banco::where('clase','2')->get();
        $cuentas = Plan_Cuentas::where('estado', '2')->get();
        //aún no se si va estar bien no seccionar una cuenta
        
        //dd($proveedor);
        return view('contable/comp_egreso/pagar',['empresa'=>$empresa,'tipo_pago'=>$tipo_pago,'compras'=>$compras,'contable'=>$contable,'lista_banco'=>$lista_banco,'tipo_tarjeta'=>$tipo_tarjeta,'proveedor'=>$proveedor,'facturas'=>$facturas,'anticipos'=>$anticipos,'cuentas'=>$cuentas,'caja'=>$caja]);
    }
    
    

}
