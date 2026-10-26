<?php

namespace App\Http\Controllers\NewSystem;

use App\Http\Controllers\Controller;
use App\Http\Requests\NewSystem\StoreUserRequest;
use App\Http\Requests\NewSystem\UpdateUserRequest;
use App\Services\NewSystem\UserAdministrationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Sis_medico\User;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('buscar')->trim()->toString();

        $users = User::query()
            ->select([
                'users.id',
                'users.nombre1',
                'users.nombre2',
                'users.apellido1',
                'users.apellido2',
                'users.email',
                'users.estado',
                'users.id_tipo_usuario',
                'users.updated_at',
            ])
            ->selectSub(
                DB::table('usuario_empresa')
                    ->selectRaw('COUNT(DISTINCT id_empresa)')
                    ->whereColumn('id_usuario', 'users.id'),
                'companies_count',
            )
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('users.id', 'like', "%{$search}%")
                        ->orWhere('users.email', 'like', "%{$search}%")
                        ->orWhere('users.nombre1', 'like', "%{$search}%")
                        ->orWhere('users.nombre2', 'like', "%{$search}%")
                        ->orWhere('users.apellido1', 'like', "%{$search}%")
                        ->orWhere('users.apellido2', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('users.estado')
            ->orderBy('users.apellido1')
            ->orderBy('users.nombre1')
            ->orderBy('users.id')
            ->paginate(20)
            ->withQueryString();

        return view('new-system.users.index', [
            'title' => 'Usuarios',
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('new-system.users.create', [
            'title' => 'Nuevo usuario',
            'roles' => $this->roles(),
            'selectedCompanies' => [],
        ]);
    }

    public function store(StoreUserRequest $request, UserAdministrationService $service): RedirectResponse
    {
        $user = $service->create(
            $request->validated(),
            (string) $request->user()->getKey(),
            (string) $request->ip(),
        );

        return redirect()
            ->route('new-system.users.edit', $user)
            ->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): View
    {
        return view('new-system.users.edit', [
            'title' => 'Editar usuario',
            'usuario' => $usuario,
            'roles' => $this->roles(),
            'selectedCompanies' => DB::table('usuario_empresa')
                ->where('id_usuario', $usuario->getKey())
                ->pluck('id_empresa')
                ->all(),
        ]);
    }

    public function update(
        UpdateUserRequest $request,
        User $usuario,
        UserAdministrationService $service,
    ): RedirectResponse {
        $service->update(
            $usuario,
            $request->validated(),
            (string) $request->user()->getKey(),
            (string) $request->ip(),
        );

        return redirect()
            ->route('new-system.users.edit', $usuario)
            ->with('status', 'Usuario actualizado correctamente.');
    }

    /** @return Collection<int, object> */
    private function roles(): Collection
    {
        return DB::table('tipousuario')
            ->select(['id', 'nombre', 'descripcion'])
            ->where('estado', 1)
            ->orderBy('nombre')
            ->orderBy('id')
            ->get();
    }
}
