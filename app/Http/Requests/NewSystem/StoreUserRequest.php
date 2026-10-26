<?php

namespace App\Http\Requests\NewSystem;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => Str::upper(trim((string) $this->input('id'))),
            'email' => Str::lower(trim((string) $this->input('email'))),
            'nombre1' => Str::upper(trim((string) $this->input('nombre1'))),
            'nombre2' => Str::upper(trim((string) $this->input('nombre2'))),
            'apellido1' => Str::upper(trim((string) $this->input('apellido1'))),
            'apellido2' => Str::upper(trim((string) $this->input('apellido2'))),
        ]);
    }

    public function authorize(): bool
    {
        return $this->user()?->can('manage-new-system-users') ?? false;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id' => ['required', 'string', 'max:13', 'alpha_dash:ascii', Rule::unique('users', 'id'), Rule::unique('users', 'cedula')],
            'nombre1' => ['required', 'string', 'max:60'],
            'nombre2' => ['nullable', 'string', 'max:60'],
            'apellido1' => ['required', 'string', 'max:60'],
            'apellido2' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'id_tipo_usuario' => ['required', 'integer', Rule::exists('tipousuario', 'id')->where('estado', 1)],
            'estado' => ['required', 'boolean'],
            'company_ids' => ['required', 'array', 'min:1'],
            'company_ids.*' => ['required', 'string', 'max:13', Rule::exists('empresa', 'id')->where('estado', 1)],
        ];
    }
}
