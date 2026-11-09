<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class V3RegistrationInvitation extends Model
{
    protected $table = 'v3_registration_invitations';

    protected $guarded = array('id');

    protected $dates = array('expires_at', 'used_at', 'revoked_at');

    public function isAvailable()
    {
        return is_null($this->used_at)
            && is_null($this->revoked_at)
            && $this->expires_at
            && $this->expires_at->isFuture();
    }
}
