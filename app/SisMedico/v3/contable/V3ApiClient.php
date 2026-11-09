<?php

namespace Sis_medico\v3\contable;

use Illuminate\Database\Eloquent\Model;

class V3ApiClient extends Model
{
    protected $table = 'v3_api_clients';
    protected $guarded = array('token_hash');
    protected $dates = array('expires_at', 'last_used_at');

    public function allows($scope)
    {
        $scopes = json_decode((string) $this->scopes, true);
        if (!is_array($scopes)) return false;
        return in_array('*', $scopes, true) || in_array($scope, $scopes, true);
    }
}
