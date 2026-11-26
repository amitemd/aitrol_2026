<?php

namespace Aitrol\Clientes\Infrastructure\Persistence;

use Aitrol\Clientes\Domain\ClienteNoEncontrado;
use Aitrol\Clientes\Domain\Entities\Cliente;
use Aitrol\Clientes\Domain\IdentificacionDuplicada;
use Aitrol\Clientes\Domain\Repositories\ClienteRepository;
use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;

final class EloquentClienteRepository implements ClienteRepository
{
    public function buscar(int $id): ?Cliente
    {
        $model = ClienteModel::query()->find($id);

        return $model === null ? null : $this->entidad($model);
    }

    public function listar(?string $busqueda, int $pagina, int $porPagina): array
    {
        $query = ClienteModel::query();

        if ($busqueda !== null && $busqueda !== '') {
            $patron = '%'.$busqueda.'%';
            $query->where(fn (Builder $query): Builder => $query
                ->whereLike('nombre', $patron)
                ->orWhereLike('identificacion', $patron)
                ->orWhereLike('email', $patron));
        }

        $resultados = $query->orderBy('nombre')->orderBy('id')->paginate($porPagina, ['*'], 'page', $pagina);

        return [
            'items' => array_map($this->entidad(...), $resultados->items()),
            'total' => $resultados->total(),
        ];
    }

    public function guardar(Cliente $cliente): Cliente
    {
        $model = $cliente->id === null
            ? new ClienteModel
            : (ClienteModel::query()->find($cliente->id) ?? throw new ClienteNoEncontrado('Cliente no encontrado.'));

        try {
            $model->fill([
                'tipo_identificacion' => $cliente->identificacion->tipo->value,
                'identificacion' => $cliente->identificacion->numero,
                'nombre' => $cliente->nombre,
                'email' => $cliente->email,
                'telefono' => $cliente->telefono,
                'direccion' => $cliente->direccion,
                'activo' => $cliente->activo,
            ])->save();
        } catch (UniqueConstraintViolationException $exception) {
            throw new IdentificacionDuplicada('Ya existe un cliente con esta identificación.', previous: $exception);
        }

        return $this->entidad($model);
    }

    private function entidad(ClienteModel $model): Cliente
    {
        return new Cliente(
            (int) $model->getKey(),
            new IdentificacionFiscal(TipoIdentificacion::from($model->tipo_identificacion), $model->identificacion),
            $model->nombre,
            $model->email,
            $model->telefono,
            $model->direccion,
            $model->activo,
        );
    }
}
