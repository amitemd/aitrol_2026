<?php

namespace App\Http\Requests\NewSystem;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Sis_medico\User;

class UpdateUserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
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
        /** @var User $user */
        $user = $this->route('usuario');

        return [
            'nombre1' => ['required', 'string', 'max:60'],
            'nombre2' => ['nullable', 'string', 'max:60'],
            'apellido1' => ['required', 'string', 'max:60'],
            'apellido2' => ['required', 'string', 'max:60'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($user->getKey(), 'id')],
            'password' => ['nullable', 'string', 'min:12', 'confirmed'],
            'id_tipo_usuario' => ['required', 'integer', Rule::exists('tipousuario', 'id')->where('estado', 1)],
            'estado' => ['required', 'boolean'],
            'company_ids' => ['required', 'array', 'min:1'],
            'company_ids.*' => ['required', 'string', 'max:13', Rule::exists('empresa', 'id')->where('estado', 1)],
        ];
    }

    /** @return list<callable(Validator): void> */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                /** @var User $user */
                $user = $this->route('usuario');

                if ((string) $this->user()?->getKey() !== (string) $user->getKey()) {
                    return;
                }

                if ((int) $this->input('estado') !== 1) {
                    $validator->errors()->add('estado', 'No puede desactivar su propio usuario.');
                }

                if ((int) $this->input('id_tipo_usuario') !== 1) {
                    $validator->errors()->add('id_tipo_usuario', 'No puede retirar su propio permiso de superadministrador.');
                }
            },
        ];
    }
}
