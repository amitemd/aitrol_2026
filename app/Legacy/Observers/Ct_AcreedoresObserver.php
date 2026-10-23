<?php

namespace Sis_medico\Observers;

use Sis_medico\Ct_acreedores;
use Sis_medico\Proveedor;

class Ct_AcreedoresObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function created(Ct_acreedores $proveedor)
    {
        $proveedor_2 = Proveedor::find($proveedor->id_proveedor);
        if (is_null($proveedor_2)) {
            Proveedor::create([
                'id'                => $proveedor->id_proveedor,
                'razonsocial'       => $proveedor->razonsocial,
                'nombrecomercial'   => $proveedor->nombrecomercial,
                'ciudad'            => $proveedor->ciudad,
                'direccion'         => $proveedor->direccion,
                'email'             => $proveedor->email,
                'telefono1'         => $proveedor->telefono1,
                'telefono2'         => $proveedor->telefono2,
                'id_tipoproveedor'  => $proveedor->id_tipoproveedor,
                'id_tipo'           => $proveedor->id_tipo,
                'estado'            => $proveedor->estado,
                'visualizar'        => $proveedor->visualizar,
                'id_configuracion'  => $proveedor->id_configuracion,
                'id_porcentaje_iva' => $proveedor->id_porcentaje_iva,
                'id_porcentaje_ft'  => $proveedor->id_porcentaje_ft,
                'id_usuariocrea'    => $proveedor->id_usuariocrea,
                'id_usuariomod'     => $proveedor->id_usuariomod,
                'ip_creacion'       => $proveedor->ip_creacion,
                'ip_modificacion'   => $proveedor->ip_modificacion,
            ]);
        }
    }

    /**
     * Listen to the User deleting event.
     *
     * @param  User  $user
     * @return void
     */
    public function deleting(Ct_acreedores $proveedor)
    {
        //
    }
}
