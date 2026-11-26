<?php

namespace Sis_medico;

use Illuminate\Database\Eloquent\Model;

class PdfBrandingSetting extends Model
{
    protected $table = 'pdf_branding_settings';

    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'repeat_on_each_page' => 'boolean',
        'width_mm' => 'integer',
        'height_mm' => 'integer',
        'offset_x_mm' => 'integer',
        'offset_y_mm' => 'integer',
        'opacity_percent' => 'integer',
        'rotation_deg' => 'integer',
        'keep_aspect_ratio' => 'boolean',
        'lock_content_position' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo('Sis_medico\Empresa', 'id_empresa');
    }
}
