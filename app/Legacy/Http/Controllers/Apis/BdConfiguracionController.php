<?php

namespace Sis_medico\Http\Controllers\Apis;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Sis_medico\Http\Controllers\Controller;
use Sis_medico\User;
use DateTime;
use DatePeriod;
use DateInterval;
use Sis_medico\Agenda_Permiso;
use Sis_medico\Doctor_Tiempo;
use Sis_medico\Log_usuario;
use Sis_medico\RolesUsuario;
use Sis_medico\User_espe;
use Sis_medico\UsuarioEmpresa;

class BdConfiguracionController extends Controller
{

    public function QuitarPermisos(Request $request)
    {
        try {
            //code...
            $user = User::find($request->id_usuario);
            if (is_null($user)) {
                return ['status' => 'error', 'msj' => 'Usuario no existe'];
            }

            $user->id_tipo_usuario = 2;
            $user->id_usuariomod = 'CREAUSER';
            $user->updated_at = date('Y-m-d H:i:s');
            $user->save();

            $input_log = [
                'id_usuario'  => 'CREAUSER',
                'ip_usuario'  => '::1',
                'descripcion' => "CAMBIO TIPO USUARIO AUTOMATICO",
                'dato_ant1'   => $user->id,
                'dato1'       => strtoupper($user->nombre1 . " " . $user->nombre2 . " " . $user->apellido1 . " " . $user->apellido2),
                'dato2'       => 'CREADO AUTOMATICO EN BD',
            ];
            Log_usuario::create($input_log);

            $agenda_permiso = Agenda_Permiso::where('id_usuario', $request->id_usuario)->get();
            if (!is_null($agenda_permiso)) {
                foreach ($agenda_permiso as  $agp) {
                    $agp->delete();
                }
            }
            $usuario_empresa = UsuarioEmpresa::where('id_usuario', $request->id_usuario)->get();
            if (!is_null($usuario_empresa)) {
                foreach ($usuario_empresa as  $uemp) {
                    $uemp->delete();
                }
            }
            $roles_usuario = RolesUsuario::where('id_usuario', $request->id_usuario)->get();
            if (!is_null($roles_usuario)) {
                foreach ($roles_usuario as  $rol) {
                    $rol->delete();
                }
            }
            /*$user_espe = user_espe::where('usuid', $request->id_usuario)->get();
            if (!is_null($user_espe)) {
                foreach ($user_espe as  $uesp) {
                    $uesp->delete();
                }
            }*/
            $doctor_tiempo = Doctor_Tiempo::where('id_doctor', $request->id_usuario)->get();
            if (!is_null($doctor_tiempo)) {
                foreach ($doctor_tiempo as  $dt) {
                    $dt->delete();
                }
            }
            $data = $user->id.' - '. $user->nombre1.' '. $user->apellido1.' '.$user->apellido2.' - Tipo usuario:'. $user->id_tipo_usuario;
            return ['status' => 'success', 'msj' => 'Permisos eliminados', 'data' => $data];
        } catch (\Exception $e) {
            return ['status' => 'error', 'msj' => $e->getMessage()];
        }
    }
}
