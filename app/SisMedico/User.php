<?php

namespace Sis_medico;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Sis_medico\Notifications\MyResetPassword;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['remember_token'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @param  string  $token
     */
    public function sendPasswordResetNotification(#[\SensitiveParameter] $token)
    {
        $this->notify(new MyResetPassword((string) $token));
    }

    public function usuario()
    {
        return $this->belongsTo('Sis_medico\Ct_Nomina', 'id_user');
    }

    public function permisos()
    {
        return $this->belongsTo('Sis_medico\Ct_Nomina', 'id_user');
    }
}
