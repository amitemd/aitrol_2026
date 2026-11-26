<?php

namespace Aitrol\Clientes\Infrastructure\Persistence;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class ClienteModel extends Model
{
    protected $table = 'erp_clientes';

    protected $fillable = [
        'tipo_identificacion', 'identificacion', 'nombre', 'email', 'telefono', 'direccion', 'activo', 'perfil',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['activo' => 'boolean', 'perfil' => 'array', 'documentos' => 'array'];
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
}
