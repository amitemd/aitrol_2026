<?php

namespace Sis_medico\Http\Controllers\contable;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Response;
use Sis_medico\Ct_Porcentaje_Renta;
use Sis_medico\Plan_Cuentas;

use Sis_medico\User;
use Sis_medico\Ct_Sucursales;
use Sis_medico\Empresa;

use Sis_medico\Http\Controllers\Controller;

class PorcentajeRentaController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

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
    public function index(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }



        $id_empresa   = $request->session()->get('id_empresa');
        $empresa          =   Empresa::find($id_empresa);

        $porcentaje_r = Ct_Porcentaje_Renta::orderby('id', 'desc')->where('id_empresa', $id_empresa)->paginate(10);
        // dd($porc_reta);

        if (!is_null($request['fecha_desde']) && !is_null($request['fecha_hasta'])) {
            $fecha = $request['fecha_desde'];
        } else {
            $fecha = date('Y-m-d');
        }
        return view('contable.Porcentaje_renta.index', ['porcentaje_r' => $porcentaje_r, 'empresa' => $empresa, 'fecha_desde' => $fecha]);
    }


    public function create(Request $request)
    {

        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa   = $request->session()->get('id_empresa');
        $empresa          =   Empresa::find($id_empresa);

        $porc_reta = Ct_Porcentaje_Renta::orderby('id', 'asc')->where('id_empresa', $id_empresa)->paginate(10);

        return view('contable.Porcentaje_renta.create', ['empresa' => $empresa]);
    }


    public function guardar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }
        $id_empresa   = $request->session()->get('id_empresa');
        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        $input = [
            'id_empresa'            => $id_empresa,
            'porcentaje'            => $request['porcentaje'],
            'anio'                  => $request['anio_porcentaje_r'],
            //'estado'                => '1',
            'id_usuariocrea'        => $idusuario,
            'id_usuariomod'         => $idusuario,
            'ip_creacion'           => $ip_cliente,
            'ip_modificacion'       => $ip_cliente,
            'regimen_especial'      => $request['regimen'],

        ];
        //dd($input);

        Ct_Porcentaje_Renta::create($input);
        return redirect()->route('Porcentaje.index');
    }


    public function edit($id, Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa   = $request->session()->get('id_empresa');
        $empresa          =   Empresa::find($id_empresa);
        $porcentaje_r = Ct_Porcentaje_Renta::findorfail($id);

        return view('contable.Porcentaje_renta.edit', ['porcentaje_r' => $porcentaje_r, 'empresa' => $empresa]);
    }


    public function actualizar(Request $request)
    {


        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa   = $request->session()->get('id_empresa');


        $ip_cliente = $_SERVER["REMOTE_ADDR"];
        $idusuario  = Auth::user()->id;
        date_default_timezone_set('America/Guayaquil');


        $id = $request['id_porcentaje_r'];
        $porcentaje = Ct_Porcentaje_Renta::find($id);

        $porcentaje->porcentaje = $request['porcentaje'];
        $porcentaje->anio = $request['anio_porcentaje_r'];
        $porcentaje->regimen_especial = $request['regimen'];
        $porcentaje->id_usuariomod = $idusuario;
        $porcentaje->save();
        // $input = [
        //     'id_empresa'            => $id_empresa,
        //     'porcentaje'            => $request['porcentaje'],
        //     'anio'                  => $request['anio_porcentaje_r'],
        //     //  'estado'                => $request['estado_porcentaje_ir'],
        //     //    'id_usuariocrea'        => $idusuario,
        //     'id_usuariomod'         => $idusuario,
        //     //   'ip_creacion'           => $ip_cliente,
        //     'ip_modificacion'       => $ip_cliente,
        //     'regimen_especial' => $request['regimen'],
        // ];


        // $porcentaje->update($input);

        return redirect()->route('Porcentaje.index');
    }

    public function buscar(Request $request)
    {
        if ($this->rol()) {
            return response()->view('errors.404');
        }

        $id_empresa   = $request->session()->get('id_empresa');



        $constraints = [
            'id_empresa'            => $id_empresa,

            'anio'         => $request['buscar_anio'],
            'porcentaje'  => $request['buscar_porcentaje'],
            'regimen_especial' => $request['regimen'],
            'id' => $request['codigo'],
        ];
        //dd($constraints);
        $porcentaje_r = $this->doSearchingQuery($constraints);
        return view('contable.Porcentaje_renta.index', ['request' => $request, 'porcentaje_r' => $porcentaje_r, 'searchingVals' => $constraints]);
    }




    private function doSearchingQuery($constraints)
    {

        $query  = Ct_Porcentaje_Renta::query();
        $fields = array_keys($constraints);

        $index = 0;

        foreach ($constraints as $constraint) {
            if ($constraint != null) {
                if ($fields[$index] == 'id') {
                    $query = $query->where($fields[$index], '=', $constraint);
                } else {
                    $query = $query->where($fields[$index], 'like', '%' . $constraint . '%');
                }
            }

            $index++;
        }

        return $query->paginate(5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */




    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function destroy($id)
    {
    User::where('id', $id)->delete();
    return redirect()->intended('/user-management');
    }*/

    /**
     * Search user from database base on some specific constraints
     *
     * @param  \Illuminate\Http\Request  $request
     *  @return \Illuminate\Http\Response
     */
}
