<?php

namespace Sis_medico\Observers;

use Sis_medico\User;
use Illuminate\Support\Facades\Session;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\Ct_Comprobante_Ingreso;

class ComprobanteIngresoCabeceraObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function saving(Ct_Comprobante_Ingreso $ingreso)
    {
        $id_empresa = Session::get('id_empresa');
        if (is_null($id_empresa)) {
            throw new \Exception('No se pudo identificar la empresa en sesión.');
        }

        $validar = Ct_Cierre_Cabecera::verificar_fecha($ingreso->fecha);
        $validar = json_decode($validar);
        if ($validar->status == 'error') {
            throw new \Exception($validar->message);
        }
    }
}
