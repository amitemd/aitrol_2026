<?php

namespace Aitrol\Configuracion\Infrastructure\Http;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\EmpresaActiva;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

final class EmpresaController
{
    public function __construct(private readonly EmpresaActiva $empresaActiva) {}

    public function index(Request $request): View
    {
        $datos = $request->validate(['q' => ['nullable', 'string', 'max:160']]);
        $consulta = EmpresaModel::query()->whereKey($this->empresaActiva->id());
        $busqueda = $datos['q'] ?? '';
        if ($busqueda !== '') {
            $consulta->where(fn ($query) => $query->whereLike('razon_social', '%'.$busqueda.'%')
                ->orWhereLike('nombre_comercial', '%'.$busqueda.'%')->orWhereLike('ruc', '%'.$busqueda.'%'));
        }

        return view('configuracion::empresas', [
            'empresas' => $consulta->orderBy('razon_social')->orderBy('id')->paginate(20)->appends(['q' => $busqueda]),
        ]);
    }

    public function edit(EmpresaModel $empresa): View
    {
        abort_unless((int) $empresa->id === $this->empresaActiva->id(), 404);

        $identidad = DB::table('erp_identidad_empresas')->where('id_empresa', $empresa->id)->first(['logo_mime', 'firma_vence']);

        return view('configuracion::editar-empresa', ['seleccionada' => $empresa, 'identidad' => $identidad]);
    }

    public function detalle(EmpresaModel $empresa): View
    {
        abort_unless((int) $empresa->id === $this->empresaActiva->id(), 404);

        return view('partials.detalle-ficha-simple', ['titulo' => 'Detalle de empresa', 'volver' => route('configuracion.empresas.index'), 'campos' => [
            'Razón social' => $empresa->razon_social, 'Nombre comercial' => $empresa->nombre_comercial, 'RUC' => $empresa->ruc,
            'Correo electrónico' => $empresa->email, 'Teléfono' => $empresa->telefono, 'Dirección' => $empresa->direccion,
            'Estado' => $empresa->activo ? 'Activa' : 'Inactiva',
        ], 'fotoUrl' => DB::table('erp_identidad_empresas')->where('id_empresa', $empresa->id)->whereNotNull('logo_mime')->exists()
            ? route('configuracion.empresas.logo', $empresa) : null]);
    }

    public function update(Request $request, EmpresaModel $empresa): RedirectResponse|JsonResponse
    {
        abort_unless((int) $empresa->id === $this->empresaActiva->id(), 404);

        $datos = $request->validate([
            'ruc' => ['required', 'string', 'digits:13', Rule::unique('erp_empresas', 'ruc')->ignore($empresa)],
            'razon_social' => ['required', 'string', 'max:160'],
            'nombre_comercial' => ['nullable', 'string', 'max:160'],
            'email' => ['nullable', 'string', 'email:filter', 'max:254'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'activo' => ['sometimes', 'boolean'],
            'logo' => ['nullable', 'file', 'image', 'mimetypes:image/jpeg,image/png,image/webp', 'max:1024', 'dimensions:max_width=2048,max_height=2048'],
            'firma' => ['nullable', 'file', 'extensions:p12,pfx', 'max:512'],
            'clave_firma' => ['exclude_without:firma', 'required_with:firma', 'string', 'max:255'],
        ]);
        $identidad = [];
        if ($logo = $request->file('logo')) {
            $identidad['logo'] = base64_encode($logo->get());
            $identidad['logo_mime'] = $logo->getMimeType();
        }
        if ($firma = $request->file('firma')) {
            $bytes = $firma->get();
            $clave = $datos['clave_firma'];
            if (! openssl_pkcs12_read($bytes, $partes, $clave) || empty($partes['pkey']) || empty($partes['cert'])
                || ! openssl_x509_check_private_key($partes['cert'], $partes['pkey'])) {
                throw ValidationException::withMessages(['firma' => 'No se pudo abrir el certificado PKCS#12 o la clave no corresponde.']);
            }
            $certificado = openssl_x509_parse($partes['cert']);
            $vencimiento = $certificado['validTo_time_t'] ?? 0;
            if ($vencimiento <= now()->timestamp || ($certificado['validFrom_time_t'] ?? PHP_INT_MAX) > now()->timestamp) {
                throw ValidationException::withMessages(['firma' => 'El certificado todavía no está vigente o ya venció.']);
            }
            $identidad['certificado_cifrado'] = Crypt::encryptString(json_encode([
                'pkcs12' => base64_encode($bytes), 'clave' => $clave,
            ], JSON_THROW_ON_ERROR));
            $identidad['firma_vence'] = gmdate('Y-m-d H:i:s', $vencimiento);
        }

        try {
            $this->empresaActiva->transaccion(function () use ($empresa, $datos, $identidad): void {
                $empresa->fill(Arr::except($datos, ['logo', 'firma', 'clave_firma']))->save();
                if ($identidad !== []) {
                    DB::table('erp_identidad_empresas')->updateOrInsert(
                        ['id_empresa' => $empresa->id],
                        $identidad + ['updated_at' => now()],
                    );
                }
            });
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['ruc' => 'Ya existe una empresa con ese RUC.']);
        }

        return $this->resultado($request, 'Empresa actualizada.');
    }

    public function logo(EmpresaModel $empresa): Response
    {
        abort_unless((int) $empresa->id === $this->empresaActiva->id(), 404);
        $identidad = DB::table('erp_identidad_empresas')->where('id_empresa', $empresa->id)->first(['logo', 'logo_mime']);
        abort_unless($identidad?->logo && in_array($identidad->logo_mime, ['image/jpeg', 'image/png', 'image/webp'], true), 404);

        return response(base64_decode($identidad->logo, true), 200, [
            'Content-Type' => $identidad->logo_mime,
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function resultado(Request $request, string $mensaje, int $estado = 200): RedirectResponse|JsonResponse
    {
        return $request->expectsJson()
            ? response()->json(['message' => $mensaje], $estado)
            : to_route('configuracion.empresas.index')->with('estado', $mensaje);
    }
}
