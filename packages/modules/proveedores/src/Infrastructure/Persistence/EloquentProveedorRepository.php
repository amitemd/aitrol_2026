<?php

namespace Aitrol\Proveedores\Infrastructure\Persistence;

use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use Aitrol\Proveedores\Domain\Entities\Proveedor;
use Aitrol\Proveedores\Domain\IdentificacionDuplicada;
use Aitrol\Proveedores\Domain\ProveedorNoEncontrado;
use Aitrol\Proveedores\Domain\Repositories\ProveedorRepository;
use App\Models\Persona;
use App\Services\EmpresaActiva;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;

final class EloquentProveedorRepository implements ProveedorRepository
{
    public function eliminar(int $id): void
    {
        app(EmpresaActiva::class)->transaccion(fn () => $this->modelo($id)->delete());
    }

    public function buscar(int $id): ?Proveedor
    {
        $modelo = ProveedorModel::query()->with('persona:id,id_ciudad')->find($id);

        return $modelo === null ? null : $this->entidad($modelo);
    }

    public function listar(?string $busqueda, int $pagina, int $porPagina): array
    {
        $consulta = ProveedorModel::query()->with('persona:id,id_ciudad');
        if ($busqueda !== null && $busqueda !== '') {
            $patron = '%'.$busqueda.'%';
            $consulta->where(fn (Builder $filtro): Builder => $filtro
                ->whereLike('nombre', $patron)
                ->orWhereLike('identificacion', $patron)
                ->orWhereLike('email', $patron));
        }

        $resultado = $consulta->orderBy('nombre')->orderBy('id')->paginate($porPagina, ['*'], 'page', $pagina);

        return [
            'items' => array_map($this->entidad(...), $resultado->items()),
            'total' => $resultado->total(),
        ];
    }

    public function guardar(Proveedor $proveedor): Proveedor
    {
        try {
            return app(EmpresaActiva::class)->transaccion(function () use ($proveedor): Proveedor {
                $modelo = $proveedor->id === null ? new ProveedorModel : $this->modelo($proveedor->id, bloquear: true);
                $perfil = array_replace($modelo->perfil ?? [], $proveedor->perfil);

                $persona = Persona::buscarOCrearConBloqueo([
                    'id_tipo_identificacion' => $proveedor->identificacion->tipo->idCatalogo(),
                    'identificacion' => $proveedor->identificacion->numero,
                ], [
                    'nombre' => $perfil['nombres'] ?? $proveedor->nombre,
                    'apellido' => $perfil['apellidos'] ?? null,
                ]);

                if (array_key_exists('id_ciudad', $perfil)) {
                    $persona->id_ciudad = $perfil['id_ciudad'];
                    if ($persona->isDirty('id_ciudad')) {
                        $persona->save();
                    }
                    unset($perfil['id_ciudad']);
                }

                $modelo->persona()->associate($persona);
                $modelo->fill([
                    'tipo_identificacion' => $proveedor->identificacion->tipo->value,
                    'identificacion' => $proveedor->identificacion->numero,
                    'nombre' => $proveedor->nombre,
                    'email' => $proveedor->email,
                    'telefono' => $proveedor->telefono,
                    'direccion' => $proveedor->direccion,
                    'activo' => $proveedor->activo,
                    'perfil' => $perfil,
                ])->save();

                return $this->entidad($modelo);
            });
        } catch (UniqueConstraintViolationException $error) {
            throw new IdentificacionDuplicada('Ya existe un proveedor con esta identificación.', previous: $error);
        }
    }

    private function modelo(int $id, bool $bloquear = false): ProveedorModel
    {
        return ProveedorModel::query()->when($bloquear, fn (Builder $consulta): Builder => $consulta->lockForUpdate())
            ->find($id) ?? throw new ProveedorNoEncontrado('Proveedor no encontrado.');
    }

    private function entidad(ProveedorModel $modelo): Proveedor
    {
        return new Proveedor(
            (int) $modelo->getKey(),
            new IdentificacionFiscal(TipoIdentificacion::from($modelo->tipo_identificacion), $modelo->identificacion),
            $modelo->nombre,
            $modelo->email,
            $modelo->telefono,
            $modelo->direccion,
            $modelo->activo,
            (int) $modelo->id_persona,
            ['id_ciudad' => $modelo->persona->id_ciudad === null ? null : (int) $modelo->persona->id_ciudad] + ($modelo->perfil ?? []),
            $modelo->only(['id_usuario_creacion', 'id_usuario_modificacion', 'created_at', 'updated_at']),
        );
    }
}
