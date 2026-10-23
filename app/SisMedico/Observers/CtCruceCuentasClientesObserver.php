<?php

namespace Sis_medico\Observers;

use Sis_medico\User;
use Sis_medico\Ct_Cruce_Cuentas_Clientes;
use Illuminate\Support\Facades\Session;
use Sis_medico\Ct_Cierre_Cabecera;
use Mail;

class CtCruceCuentasClientesObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function saving(Ct_Cruce_Cuentas_Clientes $cruceCuentas)
    {
        $id_empresa = Session::get('id_empresa');
        if(is_null($id_empresa)){
            throw new \Exception('No se pudo identificar la empresa en sesión.');
        }

        $validar = Ct_Cierre_Cabecera::verificar_fecha($cruceCuentas->fecha);
        $validar = json_decode($validar);
        if ($validar->status == 'error') {
            throw new \Exception($validar->message);
        }
        
    }

}
