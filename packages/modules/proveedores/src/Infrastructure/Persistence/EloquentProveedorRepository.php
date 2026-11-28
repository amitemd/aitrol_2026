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

                $contacto = [
                    'email' => $proveedor->email,
                    'telefono' => $proveedor->telefono,
                    'direccion' => $proveedor->direccion,
                ];
                $contacto = array_intersect_key($proveedor->perfil, $contacto) + array_filter($contacto, fn ($valor): bool => $valor !== null);
                $perfilPersona = array_intersect_key($proveedor->perfil, Persona::reglasPerfil()) + $contacto;
                $persona->fill($perfilPersona + [
                    'nombre' => $proveedor->perfil['nombres'] ?? $proveedor->nombre,
                    'apellido' => $proveedor->perfil['apellidos'] ?? null,
                ])->save();
                $perfil = array_diff_key($perfil, $perfilPersona + ['nombres' => true, 'apellidos' => true]);

                $modelo->persona()->associate($persona);
                $contacto += array_filter($persona->only(['email', 'telefono', 'direccion']), fn ($valor): bool => $valor !== null);
                $modelo->fill($contacto + [
                    'tipo_identificacion' => $proveedor->identificacion->tipo->value,
                    'identificacion' => $proveedor->identificacion->numero,
                    'nombre' => $proveedor->nombre,
                    'activo' => $proveedor->activo,
                    'perfil' => $perfil,
                ])->save();

                return $this->entidad($modelo);
            });
        } catch (UniqueConstraintViolationException $error) {
            throw new IdentificacionDuplicada('Ya existe un proveedor con esta identificación.', previous: $error);
        }
    }

    public function registrarDesdeFactura(string $ruc, string $nombre, ?string $direccion): void
    {
        app(EmpresaActiva::class)->transaccion(function () use ($ruc, $nombre, $direccion): void {
            $identificacion = new IdentificacionFiscal(TipoIdentificacion::Ruc, $ruc);
            if (ProveedorModel::withTrashed()->where('tipo_identificacion', $identificacion->tipo->value)
                ->where('identificacion', $identificacion->numero)->exists()) {
                return;
            }

            $persona = Persona::buscarOCrearConBloqueo([
                'id_tipo_identificacion' => $identificacion->tipo->idCatalogo(),
                'identificacion' => $identificacion->numero,
            ], ['nombre' => $nombre, 'direccion' => $direccion]);

            $proveedor = new ProveedorModel([
                'tipo_identificacion' => $identificacion->tipo->value,
                'identificacion' => $identificacion->numero,
                'nombre' => mb_substr($persona->nombreCompleto() ?: $nombre, 0, 160),
                'email' => mb_strlen($persona->email ?? '') <= 254 ? $persona->email : null,
                'telefono' => mb_strlen($persona->telefono ?? '') <= 30 ? $persona->telefono : null,
                'direccion' => $persona->direccion ?? $direccion,
                'activo' => true,
            ]);
            $proveedor->persona()->associate($persona);
            $proveedor->save();
        });
    }

    private function modelo(int $id, bool $bloquear = false): ProveedorModel
    {
        return ProveedorModel::query()->when($bloquear, fn (Builder $consulta): Builder => $consulta->lockForUpdate())
            ->find($id) ?? throw new ProveedorNoEncontrado('Proveedor no encontrado.');
    }

    private function entidad(ProveedorModel $modelo): Proveedor
    {
        $persona = $modelo->persona;

        return new Proveedor(
            (int) $modelo->getKey(),
            new IdentificacionFiscal(TipoIdentificacion::from($modelo->tipo_identificacion), $modelo->identificacion),
            $persona->nombreCompleto() ?: $modelo->nombre,
            $persona->email ?? $modelo->email,
            $persona->telefono ?? $modelo->telefono,
            $persona->direccion ?? $modelo->direccion,
            $modelo->activo,
            (int) $modelo->id_persona,
            array_filter($persona->perfil(), fn ($valor): bool => $valor !== null)
                + ['nombres' => $persona->nombre, 'apellidos' => $persona->apellido]
                + ($modelo->perfil ?? []),
            $modelo->only(['id_usuario_creacion', 'id_usuario_modificacion', 'created_at', 'updated_at']),
        );
    }
}
