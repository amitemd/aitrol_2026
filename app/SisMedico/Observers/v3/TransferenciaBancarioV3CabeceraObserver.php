<?php

namespace Sis_medico\Observers\v3;

use Sis_medico\User;
use Illuminate\Support\Facades\Session;
use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\v3\contable\TransferenciasBancarias;

class TransferenciaBancarioV3CabeceraObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function saving(TransferenciasBancarias $transferencia)
    {
        $id_empresa = Session::get('id_empresa');
        if (is_null($id_empresa)) {
            throw new \Exception('No se pudo identificar la empresa en sesión.');
        }

        $validar = Ct_Cierre_Cabecera::verificar_fecha($transferencia->fecha_asiento);
        $validar = json_decode($validar);
        if ($validar->status == 'error') {
            throw new \Exception($validar->message);
        }
    }
}
