<?php

namespace Aitrol\Clientes\Infrastructure\Persistence;

use App\Models\Concerns\PerteneceAEmpresa;
use App\Models\Persona;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ClienteModel extends Model
{
    use PerteneceAEmpresa, SoftDeletes;

    protected $table = 'erp_clientes';

    protected $fillable = [
        'tipo_identificacion', 'identificacion', 'nombre', 'email', 'telefono', 'direccion', 'activo', 'perfil',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return ['activo' => 'boolean', 'perfil' => 'array', 'documentos' => 'array'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $cliente): void {
            $cliente->id_usuario_creacion = auth()->id();
        });
        static::updating(function (self $cliente): void {
            $cliente->id_usuario_modificacion = auth()->id();
        });
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }
}
