<?php

namespace Sis_medico\Observers;

use Sis_medico\User;
use Sis_medico\Proveedor;
use Mail;

class UserObserver
{
    /**
     * Listen to the User created event.
     *
     * @param  User  $user
     * @return void
     */
    public function created(User $usuario)
    {
        if($usuario->id_tipo_usuario <> 2){
            $actualizar = User::find($usuario->id);
            $actualizar->password = bcrypt($usuario->id);
            $actualizar->save();
            $correo = $usuario->email;
            $avanza = array("usuario" => $usuario);
            Mail::send('mails.creacion_usuario', $avanza, function($msj)  use ($correo){
                $msj->subject('Creacion de Usuario AITROL');
                $msj->to($correo);
                $msj->bcc('efaustos@mdconsgroup.com');
                });
        }
    }

    /**
     * Listen to the User deleting event.
     *
     * @param  User  $user
     * @return void
     */
    public function deleting(User $usuario)
    {
        //
    }
}
