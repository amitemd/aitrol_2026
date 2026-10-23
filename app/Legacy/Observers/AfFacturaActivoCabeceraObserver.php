<?php

namespace Sis_medico\Observers;

use Sis_medico\Ct_Cierre_Cabecera;
use Sis_medico\AfFacturaActivoCabecera;
use Illuminate\Support\Facades\Session;

class AfFacturaActivoCabeceraObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function saving(AfFacturaActivoCabecera $depreciacion)
    {
        $id_empresa = Session::get('id_empresa');
        if(is_null($id_empresa)){
            throw new \Exception('No se pudo identificar la empresa en sesión.');
        }

        $validar = Ct_Cierre_Cabecera::verificar_fecha($depreciacion->fecha_asiento);
        $validar = json_decode($validar);
        if ($validar->status == 'error') {
            throw new \Exception($validar->message);
        }
        
    }

}
