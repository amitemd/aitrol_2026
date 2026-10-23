<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class GatewayTicket extends Model
{
    protected $table = 'gateway_tickets';
    protected $guarded = ['id'];
    protected $casts = ['payload' => 'array', 'snapshot' => 'array'];
}
