<?php

namespace Aitrol\Configuracion\Infrastructure\Persistence;

use Aitrol\Configuracion\Domain\MenuRepository;
use Aitrol\Configuracion\Domain\OpcionMenu;
use Closure;
use DomainException;
use Illuminate\Database\QueryException;

final class EloquentMenuRepository implements MenuRepository
{
    public function modificarJerarquia(Closure $cambio): void
    {
        (new MenuModel)->getConnection()->transaction(function () use ($cambio): void {
            MenuModel::query()->where('protegido', true)->orderBy('id')->lockForUpdate()->first();
            $cambio();
        }, 3);
    }

    public function listar(): array
    {
        return MenuModel::query()->orderBy('orden')->orderBy('id')->get()
            ->mapWithKeys(fn (MenuModel $menu): array => [$menu->id => new OpcionMenu(
                $menu->id,
                $menu->padre_id,
                $menu->nombre,
                $menu->ruta,
                $menu->icono,
                $menu->orden,
                $menu->activo,
                $menu->solo_administrador,
                $menu->protegido,
            )])->all();
    }

    public function guardar(?int $id, array $datos): void
    {
        $menu = $id === null ? new MenuModel : MenuModel::query()->findOrFail($id);

        try {
            $menu->fill($datos)->save();
        } catch (QueryException $exception) {
            if (! $this->esErrorDeClaveForanea($exception)) {
                throw $exception;
            }

            throw new DomainException('El grupo seleccionado ya no existe.', previous: $exception);
        }
    }

    public function eliminar(int $id): void
    {
        try {
            MenuModel::query()->whereKey($id)->delete();
        } catch (QueryException $exception) {
            if (! $this->esErrorDeClaveForanea($exception)) {
                throw $exception;
            }

            throw new DomainException('Mueve o elimina las opciones del grupo antes de borrarlo.', previous: $exception);
        }
    }

    private function esErrorDeClaveForanea(QueryException $exception): bool
    {
        [$estado, $codigo, $mensaje] = array_pad($exception->errorInfo ?? [], 3, null);

        return $estado === '23503'
            || in_array($codigo, [1451, 1452, 787], true)
            || ($codigo === 19 && str_contains((string) $mensaje, 'FOREIGN KEY constraint failed'));
    }
}
