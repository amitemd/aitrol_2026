<?php

namespace Aitrol\Clientes\Infrastructure\Http;

use Aitrol\Clientes\Application\DTOs\DatosCliente;
use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final class GuardarClienteRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'tipo_identificacion' => ['required', Rule::enum(TipoIdentificacion::class)],
            'identificacion' => ['required', 'string', 'max:30'],
            'nombre' => ['required', 'string', 'max:160'],
            'email' => ['nullable', 'string', 'email:filter', 'max:254'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'activo' => [$this->isMethod('PUT') ? 'required' : 'sometimes', 'boolean'],
        ];
    }

    public function datos(): DatosCliente
    {
        $datos = $this->validated();

        try {
            $identificacion = new IdentificacionFiscal(TipoIdentificacion::from($datos['tipo_identificacion']), $datos['identificacion']);
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['identificacion' => $exception->getMessage()]);
        }

        return new DatosCliente(
            $identificacion,
            $datos['nombre'],
            $datos['email'] ?? null,
            $datos['telefono'] ?? null,
            $datos['direccion'] ?? null,
            (bool) ($datos['activo'] ?? true),
        );
    }
}
