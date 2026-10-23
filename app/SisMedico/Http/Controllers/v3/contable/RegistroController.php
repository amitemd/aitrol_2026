<?php

namespace Sis_medico\Http\Controllers\v3\contable;

use Illuminate\Http\Request;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\v3\contable\Usuarios;
use Sis_medico\v3\contable\LogUsuario;
use Sis_medico\v3\contable\Utilidades;



class RegistroController extends Controller
{
    
    public function index()
    {
        return redirect('v3/configuraciones/planes');
    }

    public function registro_usuario($id )
    {
        return redirect('v3/configuraciones/usuarios?plan=' . urlencode($id));
    }

    public function registro_empresa($id )
    {
        return redirect('v3/configuraciones/empresas?plan=' . urlencode($id));
    }

    public function guardar_usuario(Request $request)
    {
        $request->merge(['submodulo' => 'usuarios']);
        if (!Utilidades::verificarPermisos($request, 'configuraciones')) {
            abort(403, 'No tiene autorizacion para administrar usuarios.');
        }

        $request->validate([
            'id' => 'required|string|max:20',
            'nombre1' => 'required|string|max:60',
            'nombre2' => 'nullable|string|max:60',
            'apellido1' => 'required|string|max:60',
            'apellido2' => 'nullable|string|max:60',
            'email' => 'required|email|max:191',
            'id_pais' => 'required',
            'telefono1' => 'nullable|string|max:30',
            'clave' => 'required|string|min:6',
            'id_plan' => 'required|integer|exists:new_db_planes,id',
        ]);

        $ip = $request->ip();
        $usuarios = Usuarios::find($request->id);
        if(is_null($usuarios)){
            $input = [
                'id'                    => $request->id,
                'nombre1'               => strtoupper($request->nombre1),
                'nombre2'               => strtoupper((string) $request->nombre2),
                'apellido1'             => strtoupper($request->apellido1),
                'apellido2'             => strtoupper((string) $request->apellido2),
                'email'                 => $request->email,
                'id_tipo_usuario'       => '2',
                'id_pais'               => $request->id_pais,
                'telefono1'             => $request->telefono1,
                //'new_system'            => $permiso_v3,
                'password'              => bcrypt($request->clave),
                'estado'                => 1,
                'ip_creacion'           => 'plan_empresarial_'.$request->id_plan,
                'ip_modificacion'       => $ip,
                'id_usuariocrea'        => 'FACELECTRO',
                'id_usuariomod'         => 'FACELECTRO',
            ];
            Usuarios::insertGetId($input);
            $log_usuario = [
                'id_usuario'  => 'FACELECTRO',
                'ip_usuario'  => $ip,
                'descripcion' => "CREA NUEVO USUARIO - CREADO POR COMPRA DE PLAN # ".$request->id_plan,
                'dato_ant1'   => $request->id,
                'dato1'       => strtoupper($request->nombre1) . " " . strtoupper((string) $request->nombre2) . " " . strtoupper($request->apellido1) . " " . strtoupper((string) $request->apellido2),
                'dato_ant2'   => "TIPO USUARIO: 2 CREADO POR COMPRA DE PLAN",
            ];
            LogUsuario::insertGetId($log_usuario);
        }
        return redirect('v3/configuraciones/empresas?plan=' . urlencode($request->id_plan))
            ->with('status', 'Usuario registrado correctamente. Continue con la empresa.');
    }

    public function buscar_usuario(Request $request, $id)
    {
        $request->merge(['submodulo' => 'usuarios']);
        if (!Utilidades::verificarPermisos($request, 'configuraciones')) {
            return response()->json([
                'status' => 'error',
                'message' => 'No tiene autorizacion para consultar usuarios.',
            ], 403);
        }

        $usuario = Usuarios::where('id', $id)->first([
            'id',
            'nombre1',
            'nombre2',
            'apellido1',
            'apellido2',
            'email',
            'id_pais',
            'telefono1',
            'estado',
        ]);
        if (!is_null($usuario)) {
            return $usuario;
        } else {
            return 'no';
        }

    }
}
