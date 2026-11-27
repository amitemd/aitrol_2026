<?php

namespace Aitrol\Proveedores\Infrastructure\Http;

use Aitrol\Proveedores\Application\UseCases\ProveedoresService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pagination\LengthAwarePaginator;

final class ListarProveedoresRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:160'],
            'page' => ['sometimes', 'integer', 'min:1', 'max:1000000'],
            'per_page' => ['sometimes', 'integer', 'between:1,100'],
        ];
    }

    public function paginar(ProveedoresService $proveedores): LengthAwarePaginator
    {
        $datos = $this->validated();
        $pagina = (int) ($datos['page'] ?? 1);
        $porPagina = (int) ($datos['per_page'] ?? 20);
        $resultado = $proveedores->listar($datos['q'] ?? null, $pagina, $porPagina);

        return (new LengthAwarePaginator($resultado['items'], $resultado['total'], $porPagina, $pagina, ['path' => $this->url()]))
            ->appends(collect($datos)->except('page')->all());
    }
}
