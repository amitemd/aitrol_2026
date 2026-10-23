<?php

namespace Sis_medico\Http\Controllers\contable;

use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Response;
use Sis_medico\Contable;
use Sis_medico\Ct_Configuraciones;
use Sis_medico\Empresa;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\Inventario;
use Sis_medico\Plan_Cuentas;
use Sis_medico\Plan_Cuentas_Empresa;
use Sis_medico\Ct_Asientos_Cabecera;
use Sis_medico\Log_Check_Kardex;
use Symfony\Component\HttpFoundation\Session\Session;

class Plan_CuentasController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth    = Auth::user()->id;
        if (in_array($rolUsuario, array(1, 4, 5, 7, 20, 22, 26)) == false) {
            return true;
        }
    }

    public function configuracion()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $agenda = Ct_Configuraciones::where('id', 1)
            ->orwhere('id', 2)
            ->orwhere('id', 27)
            ->get();
        $configuracion = Ct_Configuraciones::where('id', '!=', 1)
            ->orwhere('id', '!=', 2)
            ->orwhere('id', '!=', 27)
            ->get();
        return view('contable/configuracion/index', ['configuracion' => $configuracion, 'agenda' => $agenda]);
    }

    public function editar_configuracion($id)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $cuentas       = plan_cuentas::where('estado', '2')->get();
        $configuracion = Ct_Configuraciones::findorfail($id);
        return view('contable/configuracion/edit', ['configuracion' => $configuracion, 'cuentas' => $cuentas]);
    }

    public function guardar_configuracion(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;

        $input = [
            'id_plan'         => $request['id_plan'],
            'iva'             => $request['iva'],
            'ip_modificacion' => $ip_cliente,
            'id_usuariomod'   => $idusuario,
        ];
        Ct_Configuraciones::find($request['id'])->update($input);
        return redirect()->intended('/contable/configuraciones');
    }

    public function index()
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa  = session()->get('id_empresa');
        $principales = Plan_Cuentas_Empresa::where('plan', 'NOT LIKE', '%.%')->where('id_empresa', $id_empresa)->get();
        return view('contable/plan_cuentas/index', ['principales' => $principales]);
    }

    public function hijos($padre)
    {
        $id_padre = $padre;
        //dd($id_padre);

        $id_empresa = session()->get('id_empresa');
        $padre      = Plan_Cuentas_Empresa::where('id_plan', $id_padre)->where('id_empresa', $id_empresa)->first();
        $devolver   = "";

        if (is_null($padre)) {
            $padre = Plan_Cuentas_Empresa::where('plan', $id_padre)->where('id_empresa', $id_empresa)->first();
        }

        if (count($padre->hijos_2()->get()) == 0) {
            $devolver = '<li>' . count($padre->hijos_2()->get()) . $padre->nombre . '</li>';
        } else {
            $hijos = $padre->hijos_2()->get();
            foreach ($hijos as $value) {
                if (count($value->hijos_2()->get()) == 0) {
                    $devolver = $devolver . '<li><a onclick="llamado(this)" id="' . $value->id_plan . '">' . $value->nombre . '</a></li>';
                } else {
                    $devolver  = $devolver . '<li><a onclick="llamado(this)" id="' . $value->id_plan . '" class="treeview"><i class="fa fa-plus elemento" aria-hidden="true"></i> <i class="fa fa-minus oculto elemento2" aria-hidden="true"></i> ' . $value->nombre . '</a><ul class="treeview-menu">';
                    $respuesta = $this->hijos($value->plan);
                    $devolver  = $devolver . $respuesta;
                    $devolver  = $devolver . '</ul></li>';
                }
            }
        }
        return $devolver;
    }

    public function elementos(Request $request)
    {
        $id_empresa = session()->get('id_empresa');
        $id_plan    = $request['id_plan'];
        $cuenta     = Plan_Cuentas_Empresa::where('id_plan', $id_plan)->where('id_empresa', $id_empresa)->first();
        $hijos      = $cuenta->hijos_2()->get();
        return view('contable/plan_cuentas/elementos', ['cuenta' => $hijos, 'general' => $cuenta]);
    }

    public function informacion(Request $request)
    {
        $id_plan = $request['id_plan'];
        $cuenta  = plan_cuentas::find($id_plan);
        return view('contable/plan_cuentas/informacion', ['cuenta' => $cuenta]);
    }

    public function guardar(Request $request)
    {
        $id_empresa = session()->get('id_empresa');
        //  dd($request->all());

        $id           = $request['codigo'];
        $tipo         = $request['tipo'];
        $tipo2        = $request['tipo2'];
        $naturaleza   = $request['naturaleza'];
        $naturaleza_2 = $request['naturaleza_2'];
        $ip_cliente   = $_SERVER["REMOTE_ADDR"];

        if (Auth::user()->id == "0957258056") {
            $validarCuenta = Plan_CuentasController::validateAsientos($request, $id_empresa);
            if ($validarCuenta['status'] == 'error') {
                return $validarCuenta['msj'];
            }
        }

        $idusuario    = Auth::user()->id;
        if ($tipo2 == 3) {
            $input = [
                'nombre'            => $request['nombre'],
                'estado'            => $tipo,
                'estado_cierre_ano' => $request['cierre_ano'],
                'naturaleza'        => $naturaleza,
                'naturaleza_2'      => $naturaleza_2,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariomod'     => $idusuario,
            ];
        } else {
            $input = [
                'nombre'            => $request['nombre'],
                'estado'            => $tipo,
                'estado_cierre_ano' => $request['cierre_ano'],
                'naturaleza'        => $naturaleza,
                'naturaleza_2'      => $naturaleza_2,
                'ip_modificacion'   => $ip_cliente,
                'id_usuariomod'     => $idusuario,
            ];
        }
        //plan_cuentas::find($id)->update($input);

        Plan_Cuentas_Empresa::where('id_plan', $id)->where('id_empresa', $id_empresa)->update($input);
        $cierre_cuenta                    = Plan_Cuentas_Empresa::where('id_plan', $id)->where('id_empresa', $id_empresa)->first();
        $cierre_cuenta->estado_cierre_ano = $request->cierre_ano;
        $cierre_cuenta->save();

        return "ok";
    }

    public static function validateAsientos($request, $id_empresa)
    {
        $plan_empresa = Plan_Cuentas_Empresa::where('id_plan', $request->codigo)->where('id_empresa', $id_empresa)->first();
        if ($plan_empresa->estado != $request->tipo) {
            $asientos = Ct_Asientos_Cabecera::join('ct_asientos_detalle as det', 'ct_asientos_cabecera.id', 'det.id_asiento_cabecera')
                ->where('ct_asientos_cabecera.id_empresa', $id_empresa)
                ->where('det.id_plan_cuenta', $request->codigo)
                ->get();

            if (count($asientos) > 0) {
                return ['status' => 'error', 'msj' => 'No se puede modificar existen asientos creados con esta cuenta'];
            }
        }
        return ['status' => 'success', 'msj' => 'Se puede cambiar'];
    }

    public function nuevo_padre($id)
    {
        $id_empresa = session()->get('id_empresa');
        $padre      = Plan_Cuentas_Empresa::where('plan', $id)->orWhere('id_plan', $id)->where('id_empresa', $id_empresa)->first();
        return view('contable/plan_cuentas/modal_nuevo', ['padre' => $padre]);
    }

    public function guardar_nuevo(Request $request)
    {

        $id_nuevo = $request->id_padre;

        $id            = $request['id_padre'] . '.' . $request['id_cuenta'];
        $nombre        = $request['nombre'];
        $tipo          = $request['tipo'];
        $revision      = plan_cuentas::find($id);
        $revision2     = Plan_Cuentas_Empresa::where("id", $id_nuevo)->orWhere("id_plan", $id_nuevo)->orWhere("plan", $id_nuevo)->first();
        $id_plan_padre = $revision2->plan;
        if (Auth::user()->id == "0951561075") {
            //dd($revision2->plan);
        }

        //$id_plan_nuevo = $re
        if (!is_null($revision)) {
            $id = '';
        }
        if (is_null($nombre)) {
            return "Ingrese el nombre del plan de cuentas";
        }
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');

        $input_ke = [
            'plan'            => $id_plan_padre . '.' . $request['id_cuenta'],
            'id_padre'        => $request['id_padre'],
            'id_empresa'      => session()->get('id_empresa'),
            'nombre'          => $nombre,
            'estado'          => $tipo,
            'naturaleza_2'    => $request['naturaleza_2'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,
        ];
        $ix    = Plan_Cuentas_Empresa::insertGetId($input_ke);
        $input = [
            'id'              => $ix,
            'id_padre'        => $request['id_padre'],
            'nombre'          => $nombre,
            'estado'          => $tipo,
            'naturaleza_2'    => $request['naturaleza_2'],
            'ip_creacion'     => $ip_cliente,
            'ip_modificacion' => $ip_cliente,
            'id_usuariocrea'  => $idusuario,
            'id_usuariomod'   => $idusuario,

        ];
        plan_cuentas::create($input);
        $pl_empresa          = Plan_Cuentas_Empresa::find($ix);
        $pl_empresa->id_plan = $ix;
        $pl_empresa->save();
        return "ok";
    }

    public function seleccion_empresa(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $rolUsuario = Auth::user()->id_tipo_usuario;
        //dd($request->all());
        ini_set('max_execution_time', '300');
        if ($rolUsuario == 1) {
            $empresas = Empresa::all();
            $empresa  = Empresa::findorfail($request->session()->get('id_empresa'));
        } else {
            $id_usuario = Auth::user()->id;
            $empresas   = Empresa::join('usuario_empresa', 'empresa.id', '=', 'usuario_empresa.id_empresa')
                ->where('id_usuario', $id_usuario)->select('empresa.*')->groupBy('usuario_empresa.id')->get();
            $empresa = Empresa::findorfail($request->session()->get('id_empresa'));
            if (count($empresas) < 1) {
                $empresa  = Empresa::where('prioridad', '1')->first();
                $empresas = Empresa::where('id', $empresa->id)->get();
            }
        }
        return view('contable/plan_cuentas/seleccion_empresa', ['empresas' => $empresas, 'request' => $request, 'empresa' => $empresa]);
    }

    public function guardar_empresa(Request $request)
    {
        //dd($request->all());
        $empresa = Empresa::findorfail($request['id_empresa']);
        $request->session()->put('id_empresa', $request['id_empresa']);
        if (isset($request->inventario)) {
            $empresa->inventario = $request->inventario;
        } else {
            $empresa->inventario = 0;
        }
        $empresa->save();


        $texto = "";
        if ($request->inventario == 1) {
            $texto = "Se activo el inventario";
        } else {
            $texto = "Se desactivo el inventario";
        }

        Log_Check_Kardex::create([
            "observacion" => $texto,
            "id_usuariocrea" => Auth::user()->id,
            "ip_creacion" => $_SERVER['REMOTE_ADDR'],
            "id_empresa" => $empresa->id,
        ]);



        return back();
    }

    public function consultar_inventario(Request $request)
    {
        $id_empresa = $request->id_empresa;
        if (!is_null($id_empresa)) {
            $empresa = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
            return ['inventario_estado' => $empresa->inventario];
        } else {
            return ['inventario_estado' => 0];
        }
    }

    public function exportar()
    {
        // dd("Exportar");
        $id_empresa = session()->get('id_empresa');
        $empresa    = Empresa::where('id', $id_empresa)->where('estado', '1')->first();
        $plan       = array();
        $plan       = Plan_Cuentas_Empresa::where('estado', '<>', 0)
            // ->where('id', 'like', "$condicion%")
            ->where('id_empresa', $id_empresa)
            ->select('plan', 'nombre', 'naturaleza', 'naturaleza_2')
            ->orderBy('plan', 'asc')
            ->get();
        //dd($plan);
        Excel::create('PlanCuentas', function ($excel) use ($empresa, $plan) {
            $excel->sheet('EstadoSituacionFinanciera', function ($sheet) use ($empresa, $plan) {
                // dd($participacion);
                $sheet->mergeCells('A1:G1');
                $sheet->cell('A1', function ($cell) use ($empresa) {
                    // manipulate the cel
                    $cell->setValue($empresa->nombrecomercial);
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', '', 'thin');
                });
                $sheet->mergeCells('A2:G2');
                $sheet->cell('A2', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("PLAN DE CUENTAS");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('15');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('A3:G3');
                $sheet->cell('A3', function ($cell) {
                    // manipulate the cel
                    $cell->setValue("");
                    $cell->setFontWeight('bold');
                    $cell->setFontSize('12');
                    $cell->setAlignment('center');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                //$sheet->mergeCells('A4:A5');
                $sheet->cell('A4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('CUENTA');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->mergeCells('B4:G4');
                $sheet->cell('B4', function ($cell) {
                    // manipulate the cel
                    $cell->setValue('NOMBRE');
                    $cell->setBorder('thin', 'thin', 'thin', 'thin');
                });
                // DETALLES

                $i = $this->setDetalles($plan, $sheet, 5);

                //  CONFIGURACION FINAL
                $sheet->cells('A2:G2', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#0070C0');
                    // $cells->setFontSize('10');
                    $cells->setFontWeight('bold');
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                    $cells->setValignment('center');
                });

                $sheet->cells('A4:G4', function ($cells) {
                    // manipulate the range of cells
                    $cells->setBackground('#cdcdcd');
                    $cells->setFontWeight('bold');
                    $cells->setFontSize('12');
                });

                $sheet->setWidth(array(
                    'A' => 12,
                    'B' => 12,
                    'C' => 12,
                    'D' => 12,
                    'E' => 12,
                    'F' => 12,
                    'G' => 12,
                ));
            });
        })->export('xlsx');
    }

    public function setDetalles($data, $sheet, $i, $totpyg = "", $participacion = "")
    {
        foreach ($data as $value) {

            $cont = substr_count($value['plan'], ".");
            // if ($cont == 0) {$valor100 = $value['saldo'];}

            $sheet->cell('A' . $i, function ($cell) use ($value, $cont) {
                // manipulate the cel
                $cell->setValue(" " . $value['plan'] . " ");
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $this->setSangria($cont, $cell);
            });

            $sheet->mergeCells('B' . $i . ':G' . $i);
            $sheet->cell('B' . $i, function ($cell) use ($value, $cont) {
                // manipulate the cel

                $cell->setValue($value['nombre']);
                $cell->setBorder('thin', 'thin', 'thin', 'thin');
                $this->setSangria($cont, $cell, 1);
            });

            $i++;
        }
        $sheet->getStyle('A' . $i)->getAlignment()->setWrapText(true);
        return $i;
    }

    public function setSangria($cont, $cell, $indent = "")
    {
        switch ($cont) {
            case 0:
                $cell->setFontSize('12');
                $cell->setFontWeight('bold');
                break;
            case 1:
                $cell->setFontSize('11');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(1);
                }
                break;
            case 2:
                $cell->setFontSize('10');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(2);
                }
                break;
            case 3:
                $cell->setFontSize('10');
                $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(3);
                }
                break;
            case 4:
                $cell->setFontSize('10');
                // $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(4);
                }
                break;
            default:
                $cell->setFontSize('10');
                // $cell->setFontWeight('bold');
                if ($indent != "") {
                    $cell->setTextIndent(5);
                }
                break;
        }
    }


    public function egresoKardex(Request $request)
    {
        //  dd($request->all());
        $empresa = Empresa::find($request->id_empresa);
        $empresa->egreso_kardex = $request->egreso_kardex;
        $empresa->save();

        $texto = "";
        if ($request->egreso_kardex == 1) {
            $texto = "Se activo el egreso del kardex";
        } else {
            $texto = "Se desactivo el egreso del kardex";
        }

        Log_Check_Kardex::create([
            "observacion" => $texto,
            "id_usuariocrea" => Auth::user()->id,
            "ip_creacion" => $_SERVER['REMOTE_ADDR'],
            "id_empresa" => $empresa->id,
        ]);


        return ["status" => "success", "msj" => "Guardado con exito"];
    }
}
