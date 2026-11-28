<?php

namespace Aitrol\Clientes\Infrastructure\Persistence;

use Aitrol\Clientes\Domain\ClienteNoEncontrado;
use Aitrol\Clientes\Domain\Entities\Cliente;
use Aitrol\Clientes\Domain\IdentificacionDuplicada;
use Aitrol\Clientes\Domain\Repositories\ClienteRepository;
use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use App\Models\Persona;
use App\Services\EmpresaActiva;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;

final class EloquentClienteRepository implements ClienteRepository
{
    public function eliminar(int $id): void
    {
        app(EmpresaActiva::class)->transaccion(function () use ($id): void {
            $cliente = ClienteModel::query()->find($id) ?? throw new ClienteNoEncontrado('Cliente no encontrado.');
            $cliente->delete();
        });
    }

    public function buscar(int $id): ?Cliente
    {
        $model = ClienteModel::query()->with('persona')->find($id);

        return $model === null ? null : $this->entidad($model);
    }

    public function listar(?string $busqueda, int $pagina, int $porPagina): array
    {
        $query = ClienteModel::query()->with('persona')
            ->leftJoin('personas', 'personas.id', '=', 'erp_clientes.id_persona');
        if ($busqueda !== null && $busqueda !== '') {
            $patron = '%'.$busqueda.'%';
            $query->where(fn (Builder $query): Builder => $query
                ->whereLike('personas.nombre', $patron)
                ->orWhereLike('personas.apellido', $patron)
                ->orWhereLike('personas.identificacion', $patron)
                ->orWhereLike('personas.email', $patron)
                ->orWhereRaw("LOWER(CONCAT(personas.nombre, ' ', COALESCE(personas.apellido, ''))) LIKE LOWER(?)", [$patron])
                ->orWhere(fn (Builder $legacy): Builder => $legacy->whereNull('erp_clientes.id_persona')
                    ->where(fn (Builder $fields): Builder => $fields->whereLike('erp_clientes.nombre', $patron)
                        ->orWhereLike('erp_clientes.identificacion', $patron)->orWhereLike('erp_clientes.email', $patron))));
        }

        $resultados = $query->orderByRaw('COALESCE(personas.nombre, erp_clientes.nombre)')
            ->orderBy('personas.apellido')->orderBy('erp_clientes.id')
            ->paginate($porPagina, ['erp_clientes.*'], 'page', $pagina);

        return [
            'items' => array_map($this->entidad(...), $resultados->items()),
            'total' => $resultados->total(),
        ];
    }

    public function guardar(Cliente $cliente): Cliente
    {
        try {
            return app(EmpresaActiva::class)->transaccion(function () use ($cliente): Cliente {
                $model = $cliente->id === null
                    ? new ClienteModel
                    : (ClienteModel::query()->lockForUpdate()->find($cliente->id) ?? throw new ClienteNoEncontrado('Cliente no encontrado.'));
                $identificacion = [
                    'tipo_identificacion' => $cliente->identificacion->tipo->value,
                    'identificacion' => $cliente->identificacion->numero,
                ];
                $documento = ['id_tipo_identificacion' => $cliente->identificacion->tipo->idCatalogo(), 'identificacion' => $cliente->identificacion->numero];
                $persona = $model->persona()->lockForUpdate()->first()
                    ?? Persona::buscarOCrearConBloqueo($documento, ['nombre' => $cliente->nombre]);

                $perfil = $cliente->perfil;
                if (isset($perfil['nombres'])) {
                    $persona->nombre = $perfil['nombres'];
                    if (array_key_exists('apellidos', $perfil)) {
                        $persona->apellido = $perfil['apellidos'];
                    }
                } elseif ($cliente->nombre !== $persona->nombreCompleto()) {
                    $persona->nombre = $cliente->nombre;
                    $persona->apellido = null;
                }

                $contacto = ['email' => $cliente->email, 'telefono' => $cliente->telefono, 'direccion' => $cliente->direccion];
                $camposPersona = array_flip(array_keys(Persona::reglasPerfil()));
                $persona->fill($documento + $contacto + array_intersect_key($perfil, $camposPersona))->save();
                $model->persona()->associate($persona);
                $model->fill($identificacion + $contacto + [
                    'nombre' => $persona->nombreCompleto(),
                    'activo' => $cliente->activo,
                    'perfil' => array_replace($model->perfil ?? [], array_diff_key($perfil, $camposPersona + array_flip(['nombres', 'apellidos']))),
                ])->save();

                return $this->entidad($model);
            });
        } catch (UniqueConstraintViolationException $exception) {
            throw new IdentificacionDuplicada('Ya existe un cliente o una persona con esta identificación.', previous: $exception);
        }
    }

    private function entidad(ClienteModel $model): Cliente
    {
        $persona = $model->persona;
        $nombre = $persona?->nombreCompleto() ?? $model->nombre;

        return new Cliente(
            (int) $model->getKey(),
            new IdentificacionFiscal(
                $persona?->id_tipo_identificacion === null ? TipoIdentificacion::from($model->tipo_identificacion) : TipoIdentificacion::desdeCatalogo((int) $persona->id_tipo_identificacion),
                $persona?->identificacion ?? $model->identificacion,
            ),
            $nombre,
            $persona ? $persona->email : $model->email,
            $persona ? $persona->telefono : $model->telefono,
            $persona ? $persona->direccion : $model->direccion,
            $model->activo,
            $model->id_persona,
            ['nombres' => $persona?->nombre ?? $nombre, 'apellidos' => $persona?->apellido] + ($persona?->perfil() ?? []) + ($model->perfil ?? []),
            $model->only(['id_usuario_creacion', 'id_usuario_modificacion', 'created_at', 'updated_at']),
        );
    }
}
