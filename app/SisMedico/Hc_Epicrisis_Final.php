<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;
use Sis_medico\Services\ClinicalSignedDocumentGuard;

class Hc_Epicrisis_Final extends Model
{
    //
    protected $table = 'hc_epicrisis_final';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->encounter_id) && ! empty($model->id_ho_solicitud)
                && \Schema::hasColumn('hc_epicrisis_final', 'encounter_id') && \Schema::hasColumn('ho_solicitud', 'encounter_id')) {
                $model->encounter_id = \DB::table('ho_solicitud')->where('id', $model->id_ho_solicitud)->value('encounter_id');
            }
        });
        static::updating(function ($model) {
            ClinicalSignedDocumentGuard::assertSourceMutation('hc_epicrisis_final', $model->id, $model);
        });
        static::saved(function ($model) {
            if (\Schema::hasTable('clinical_records')) {
                app(Services\ClinicalRecordService::class)->registerEpicrisis($model);
            }
        });
    }

    public function encounter()
    {
        return $this->belongsTo('Sis_medico\ClinicalEncounter', 'encounter_id');
    }
    /*public function sala()
    {
        return $this->belongsTo('Sis_medico\Sala', 'id_sala');
    }*/

}
