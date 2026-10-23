<?php

namespace Sis_medico\Http\Controllers\contable;

use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Sis_medico\Ct_Orden_Venta;
use Sis_medico\Ct_Orden_Venta_Detalle;
use Sis_medico\Comisiones_Gastro;
use Sis_medico\Ct_productos;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Producto_Comision;

class ComisionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 20, 22)) == false) {
            return true;
        }
    }

    public function index_comisiones()
    {

        $doctor = Comisiones_Gastro::where("estado",1)->get();
        $resultado = db::table("ct_orden_venta")
            ->join('comisiones_gastro','ct_orden_venta.doctor_referido','comisiones_gastro.id')
            ->join('agenda as ag','ag.id', 'ct_orden_venta.id_agenda')
            ->where('ct_orden_venta.doctor_referido','<>',null)
            ->where("ct_orden_venta.estado",1)
            ->whereBetween('ct_orden_venta.fecha_emision', [date('Y-m-d') . ' 00:00:00', date('Y-m-d') . ' 23:59:59'])
            ->select('ag.id_paciente as cedula_paciente','comisiones_gastro.nombre as nombreDoctor', 'ct_orden_venta.total', 'ct_orden_venta.comision_total', 'ct_orden_venta.id')
            ->paginate(10);
        return view("contable/comisiones/index",['resultado'=>$resultado,'doctor'=>$doctor]);
    }

    public function buscar_comisiones(Request $request)
    {

        $doctor = Comisiones_Gastro::where("estado",1)->get();
        $resultado = Ct_Orden_Venta::join('comisiones_gastro','ct_orden_venta.doctor_referido','comisiones_gastro.id')
            ->join('agenda as ag','ag.id', 'ct_orden_venta.id_agenda')
            ->where('ct_orden_venta.doctor_referido', '<>', null)->where("ct_orden_venta.estado",1);
        if(!is_null($request->fecha_desde) and !is_null($request->fecha_hasta)){
            $resultado = $resultado->whereBetween('ct_orden_venta.fecha_emision',[$request->fecha_desde,$request->fecha_hasta]);
        }
        $resultado = $resultado->select('ag.id_paciente as cedula_paciente','comisiones_gastro.nombre as nombreDoctor', 'ct_orden_venta.total', 'ct_orden_venta.comision_total', 'ct_orden_venta.id')->paginate(10);
        return view("contable/comisiones/index",['resultado'=>$resultado,'fecha_desde_buscador'=>$request->fecha_desde,'fecha_hasta_buscador'=>$request->fecha_hasta,'doctor'=>$doctor,'doctor_nombre'=>$request->doctor]);
    } 

    public function detalle_examenes(Request $request)
    {

       $detalle = Ct_Orden_Venta::where('id',$request->id)->first();
       return view("contable/comisiones/detalle",['detalle'=>$detalle->detalles,'cabecera'=>$detalle]);
    }
    public function subir_excel(Request $request)
    {
        $id_empresa       = $request->session()->get('id_empresa');
        $id_usuario = Auth::user()->id;
        $path = $request->file('excel_comisiones')->getRealPath();
        $doctor_comision = Comisiones_Gastro::find($request->doctor_excel);
        $sheets = Excel::selectSheetsByIndex(0)->load($path, null, null, false)->get();
        try {
            if (count($sheets) > 0) {
                foreach ($sheets as $val) {
                    $id_producto = Ct_productos::where("codigo", $val->codigo)->where("id_empresa",$id_empresa)->first();
                    $porcentaje_p = floatval($val->de_comision_particular) * 100;
                    $porcentaje_priv = floatval($val->de_comision_seguros_privados) * 100;
                    Producto_Comision::create([
                        'estado'    => 1,
                        'id_ct_producto' => $id_producto->id,
                        'id_comision_gatro' => $doctor_comision->id,
                        //particular
                        'valor' => $val->pvp_particular,
                        'porcentaje' => $porcentaje_p,
                        //privado
                        'valor_privado' => $val->pvp_seguros_privados,
                        'porcentaje_privado' => $porcentaje_priv,
                        'id_usuario_crea' => $id_usuario,
                        'id_usuario_mod' => $id_usuario,
                    ]);
                }
                return redirect()->back()->with('carga_exitosa', 'Se cargaron las comisiones');   
            }
            return redirect()->back()->with('vacio_excel', 'El excel no tiene el formato requerido');   
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Hubo un error en la lectura del excel');   
        }
    }
}
