<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class HospitalEpisodeFormDraft extends Model
{
    protected $table = 'hospital_episode_form_drafts';

    protected $guarded = [];

    protected $casts = ['payload' => 'array'];
}
