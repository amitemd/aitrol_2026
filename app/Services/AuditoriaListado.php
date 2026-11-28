<?php

namespace App\Services;

use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class AuditoriaListado
{
    public function compose(View $view): void
    {
        $datos = $view->getData();
        if (($datos['seccion'] ?? null) === 'cuentas' && $view->name() === 'contabilidad::index') {
            return;
        }

        $registros = $datos['registros'] ?? $datos['clientes'] ?? $datos['proveedores']
            ?? $datos['roles'] ?? $datos['empresas'] ?? $datos['usuarios'] ?? $datos['sucursales']
            ?? (isset($datos['registro']) ? [$datos['registro']] : []);
        $filas = $registros instanceof AbstractPaginator ? $registros->getCollection() : collect($registros);
        $ids = $filas->flatMap(function ($fila): array {
            $auditoria = data_get($fila, 'auditoria', $fila);

            return [
                data_get($auditoria, 'id_usuario_creacion') ?? data_get($auditoria, 'id_usuario'),
                data_get($auditoria, 'id_usuario_modificacion') ?? data_get($auditoria, 'id_usuario_anulacion'),
            ];
        })->filter()->unique()->values();
        $autores = $ids->isEmpty() ? new Collection : DB::table('users')
            ->leftJoin('personas', 'personas.id', '=', 'users.id_persona')
            ->whereIn('users.id', $ids)
            ->get(['users.id', 'users.name', 'personas.nombre', 'personas.apellido'])
            ->mapWithKeys(fn ($usuario): array => [
                $usuario->id => $this->nombreCorto($usuario->nombre ?: $usuario->name, $usuario->apellido),
            ]);

        $view->with('autoresAuditoria', $autores);
    }

    private function nombreCorto(string $nombre, ?string $apellido): string
    {
        $partes = explode(' ', Str::squish($nombre));
        $inicial = Str::upper(Str::substr($partes[0], 0, 1));

        return $apellido || count($partes) > 1 ? $inicial.'. '.($apellido ?: $partes[1]) : $inicial.'.';
    }
}
