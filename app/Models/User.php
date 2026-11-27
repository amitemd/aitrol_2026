<?php

namespace App\Models;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $attributes = ['activo' => true];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(EmpresaModel::class, 'id_empresa');
    }

    public function save(array $options = []): bool
    {
        if ($this->exists || $this->id_persona !== null) {
            return parent::save($options);
        }

        try {
            return $this->getConnection()->transaction(function () use ($options): bool {
                $this->persona()->associate(Persona::query()->create([
                    'nombre' => $this->name,
                    'email' => $this->email,
                ]));

                if (parent::save($options)) {
                    return true;
                }

                $this->persona->delete();
                $this->persona()->dissociate();

                return false;
            });
        } catch (\Throwable $exception) {
            $this->persona()->dissociate();

            throw $exception;
        }
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_admin' => 'boolean',
            'is_admin_global' => 'boolean',
            'activo' => 'boolean',
            'password' => 'hashed',
        ];
    }
}
