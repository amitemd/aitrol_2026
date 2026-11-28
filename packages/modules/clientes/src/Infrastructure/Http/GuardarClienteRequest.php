<?php

namespace Aitrol\Clientes\Infrastructure\Http;

use Aitrol\Clientes\Application\DTOs\DatosCliente;
use Aitrol\Clientes\Domain\ValueObjects\IdentificacionFiscal;
use Aitrol\Clientes\Domain\ValueObjects\TipoIdentificacion;
use App\Models\Persona;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use InvalidArgumentException;

final class GuardarClienteRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        $soloWeb = Rule::excludeIf($this->routeIs('api.*'));

        return [
            'tipo_identificacion' => ['required', Rule::enum(TipoIdentificacion::class)],
            'identificacion' => ['required', 'string', 'max:30'],
            'nombre' => ['required_without_all:nombres,primer_nombre', 'nullable', 'string', 'max:160'],
            'nombres' => ['nullable', 'string', 'max:160'],
            'apellidos' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'string', 'email:filter', 'max:254'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'activo' => [$this->isMethod('PUT') ? 'required' : 'sometimes', 'boolean'],
            'ciudad' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'zona' => ['nullable', 'string', 'max:100'],
            ...Persona::reglasPerfil(),
            'latitud' => ['required_with:longitud', 'nullable', 'numeric', 'between:-90,90'],
            'longitud' => ['required_with:latitud', 'nullable', 'numeric', 'between:-180,180'],
            'contactos' => ['nullable', 'array', 'max:10'],
            'contactos_presentes' => ['sometimes', 'boolean'],
            'contactos.*' => ['array:nombre,cargo,email,telefono'],
            'contactos.*.nombre' => ['nullable', 'string', 'max:120'],
            'contactos.*.cargo' => ['nullable', 'string', 'max:80'],
            'contactos.*.email' => ['nullable', 'email:filter', 'max:254'],
            'contactos.*.telefono' => ['nullable', 'string', 'max:30'],
            'foto' => [$soloWeb, 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'adjuntos' => [$soloWeb, 'nullable', 'array', 'max:5'],
            'adjuntos.*' => [$soloWeb, 'required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:8192'],
        ];
    }

    public function datos(): DatosCliente
    {
        $datos = Persona::normalizarNombre($this->validated());

        try {
            $identificacion = new IdentificacionFiscal(TipoIdentificacion::from($datos['tipo_identificacion']), $datos['identificacion']);
        } catch (InvalidArgumentException $exception) {
            throw ValidationException::withMessages(['identificacion' => $exception->getMessage()]);
        }

        $nombre = isset($datos['nombres'])
            ? trim($datos['nombres'].' '.($datos['apellidos'] ?? ''))
            : $datos['nombre'];
        if (mb_strlen($nombre) > 160) {
            throw ValidationException::withMessages(['nombres' => 'El nombre completo debe tener hasta 160 caracteres.']);
        }

        $perfil = array_intersect_key($datos, array_flip([
            'nombres', 'apellidos', 'email', 'telefono', 'direccion', 'ciudad', 'provincia', 'zona', 'latitud', 'longitud', 'contactos',
            ...array_keys(Persona::reglasPerfil()),
        ]));
        if (isset($perfil['nombres'])) {
            $perfil['apellidos'] ??= null;
        }
        if ($datos['contactos_presentes'] ?? false) {
            $perfil['contactos'] ??= [];
        }
        if (array_key_exists('contactos', $perfil)) {
            $perfil['contactos'] = array_values(array_filter($perfil['contactos'] ?? [], fn (array $contacto): bool => array_filter($contacto) !== []));
        }

        return new DatosCliente(
            $identificacion,
            $nombre,
            $datos['email'] ?? null,
            $datos['telefono'] ?? null,
            $datos['direccion'] ?? null,
            (bool) ($datos['activo'] ?? true),
            $perfil,
        );
    }
}
