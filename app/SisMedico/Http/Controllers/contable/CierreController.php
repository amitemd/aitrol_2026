<?php

namespace Sis_medico\Http\Controllers\contable;

use Sis_medico\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Filesystem\FilesystemManager;
use Response;
use Sis_medico\User;
use Sis_medico\Titulo_Profesional;
use Sis_medico\Ct_Cierre_Cabecera;
use Excel;
use Hash;
use PHPExcel_Style_NumberFormat;
use PHPExcel_Worksheet_Drawing;



class CierreController extends Controller 
{

    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private function rol()
    {
        $rolUsuario = Auth::user()->id_tipo_usuario;
        if (in_array($rolUsuario, array(1, 20)) == false) {
            return true;
        }
    }



    public function index(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa = $request->session()->get('id_empresa');
        if(is_null($id_empresa)){
            redirect()->route('dashboard');
        }
        $cierres = Ct_Cierre_Cabecera::where('estado','!=', '0')->where('id_empresa', $id_empresa)->orderBy('id', 'Desc')->paginate(15);
        
        return view('contable/cierre/index',['cierres' => $cierres]);
    }
    
    public function create()
    {
          
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $ultimo = Ct_Cierre_Cabecera::ultima_fecha();

      
        return view('contable/cierre/create', ['ultimo' => $ultimo]);
    }

    public function store(Request $request)
    {
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario = Auth::user()->id;
        $id_empresa = $request->session()->get('id_empresa');
        if(is_null($id_empresa)){
            redirect()->route('dashboard');
        }
        $rules = [
            'motivo'    => 'required|max:60',
            'fecha'     => 'required|date'
        ];
        $messages = [
            'motivo.required' => 'Escriba un Motivo.',
            'motivo.max'      => 'El motivo no puede ser mayor a :max caracteres.',
            'fecha.required'  => 'Agrega la fecha del cierre.',
            'fecha.date'      => 'La fecha del cierre formato incorrecto.'
        ];
        // Ejecuta la validación inicial
        $validatedData = $this->validate($request, $rules, $messages);

        // Verifica si existe un registro con la fecha mayor o igual y un id_empresa diferente
        $fechaIngresada = $request->input('fecha');// Asegúrate de que `id_empresa` esté presente en el request

        $registroExistente = Ct_Cierre_Cabecera::where('fecha_cierre', '>=', $fechaIngresada)
        ->where('id_empresa', '=', $id_empresa)
        ->where('estado', '=', 1)
            ->exists();

        if ($registroExistente) {
            return redirect()->back()
                ->withErrors(['fecha' => 'Ya existe un registro con una fecha mayor o igual y con un ID de empresa diferente.'])
                ->withInput();
        }
        Ct_Cierre_Cabecera::create([

            'observacion'       => $request['motivo'],
            'fecha_cierre'      => $request['fecha'],
            'id_empresa'        => $id_empresa,
            'estado'            => 1,
            'ip_creacion'       => $ip_cliente,
            'ip_modificacion'   => $ip_cliente,
            'id_usuariocrea'    => $idusuario,
            'id_usuariomod'     => $idusuario
        ]);

        return redirect()->route('cierre.mensual.index');
    }

    public function edit($id){

        return view('contable.cierre.modal', ['id' => $id]);
    }
    
    public function update($id, Request $request)
    {
        try {
            $ingresada      = $request['password'];
            $hashedPassword = Auth::user()->password;
            $cabecera       = Ct_Cierre_Cabecera::find($id);
            $ip_cliente = $_SERVER["REMOTE_ADDR"];
            $idusuario = Auth::user()->id;
            //dd($pedido_v);
            if (!is_null($cabecera)) {
                if (Hash::check($ingresada, $hashedPassword) && $cabecera->estado == 1) {

                    DB::beginTransaction();
                    $cabecera->estado = 0;
                    $cabecera->id_usuariomod = $idusuario;
                    $cabecera->ip_modificacion = $ip_cliente;
                    $cabecera->save();
                    DB::commit();
                    return response()->json([
                        'status' => 'success', // o 'error', según el caso
                        'msj'    => 'Operación completada exitosamente',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'error', // o 'error', según el caso
                    'msj'    => "Pedido no existe",
                ]);
            }
            return response()->json([
                'status' => 'error', // o 'error', según el caso
                'msj'    => "Contraseña Incorrecta.",
            ]);
        } catch (\Exception $e) {
            //if there is an error/exception in the above code before commit, it'll rollback
            DB::rollBack();

            return response()->json([
                'status' => 'error', // o 'error', según el caso
                'msj'    => $e->getMessage(),
            ]);
            
        }
    }

}