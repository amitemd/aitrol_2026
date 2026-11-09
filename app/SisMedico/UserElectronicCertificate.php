<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class UserElectronicCertificate extends Model
{
    protected $table = 'user_electronic_certificates';

    protected $guarded = array('id');

    protected $hidden = array('password_encrypted', 'certificate_path');

    protected $dates = array('valid_from', 'valid_to');
}
