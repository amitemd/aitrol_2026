<?php

namespace Sis_medico\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;
use Sis_medico\UserElectronicCertificate;

class UserElectronicCertificateService
{
    const DISK = 'user_electronic_certificates';
    const MAX_BYTES = 5242880;

    private $files;

    public function __construct(V3FileSecurityService $files)
    {
        $this->files = $files;
    }

    public function store($userId, $companyId, UploadedFile $upload, $password, $actorId, $ip)
    {
        $password = (string) $password;
        if ($password === '') throw new InvalidArgumentException('Ingrese la contraseña del certificado electrónico.');

        $contents = @file_get_contents($upload->getRealPath());
        if ($contents === false || $contents === '') {
            throw new InvalidArgumentException('No se pudo leer el archivo del certificado electrónico.');
        }
        $metadata = $this->inspect($contents, $password);
        $directory = $this->safeSegment($companyId) . '/' . hash('sha256', (string) $userId);
        $filename = bin2hex(random_bytes(24)) . '.' . strtolower($upload->getClientOriginalExtension());
        $relativePath = $directory . '/' . $filename;
        $disk = Storage::disk(self::DISK);

        if (!$disk->put($relativePath, $contents, array('visibility' => 'private'))) {
            throw new RuntimeException('No se pudo guardar el certificado en el almacenamiento privado.');
        }

        $oldPath = null;
        try {
            DB::transaction(function () use ($userId, $companyId, $upload, $password, $actorId, $ip, $metadata, $relativePath, &$oldPath) {
                $record = UserElectronicCertificate::where('id_empresa', (string) $companyId)
                    ->where('user_id', (string) $userId)->lockForUpdate()->first();
                if (!$record) $record = new UserElectronicCertificate;
                else $oldPath = $record->certificate_path;

                $record->id_empresa = (string) $companyId;
                $record->user_id = (string) $userId;
                $record->certificate_path = $relativePath;
                $record->original_name = $this->safeOriginalName($upload->getClientOriginalName());
                $record->password_encrypted = Crypt::encryptString($password);
                $record->subject_name = $metadata['subject_name'];
                $record->issuer_name = $metadata['issuer_name'];
                $record->serial_number = $metadata['serial_number'];
                $record->fingerprint_sha256 = $metadata['fingerprint_sha256'];
                $record->valid_from = $metadata['valid_from'];
                $record->valid_to = $metadata['valid_to'];
                $record->estado = 1;
                $record->uploaded_by = (string) $actorId;
                $record->upload_ip = substr((string) $ip, 0, 64);
                $record->save();
            }, 3);
        } catch (Exception $exception) {
            $disk->delete($relativePath);
            throw $exception;
        }

        if ($oldPath && $oldPath !== $relativePath) $disk->delete($oldPath);
        return $this->summary($userId, $companyId);
    }

    public function inspect($contents, $password)
    {
        if (!function_exists('openssl_pkcs12_read')) {
            throw new RuntimeException('El servidor no tiene disponible la extensión OpenSSL para validar certificados.');
        }

        $certificates = array();
        if (!@openssl_pkcs12_read($contents, $certificates, (string) $password)) {
            throw new InvalidArgumentException('No se pudo abrir el certificado. Verifique el archivo y su contraseña.');
        }
        if (empty($certificates['cert']) || empty($certificates['pkey'])) {
            throw new InvalidArgumentException('El archivo no contiene un certificado y una clave privada utilizables.');
        }
        if (!@openssl_x509_check_private_key($certificates['cert'], $certificates['pkey'])) {
            throw new InvalidArgumentException('La clave privada no corresponde al certificado cargado.');
        }

        $parsed = @openssl_x509_parse($certificates['cert']);
        if (!is_array($parsed)) throw new InvalidArgumentException('El certificado electrónico no es válido.');

        $from = isset($parsed['validFrom_time_t']) ? (int) $parsed['validFrom_time_t'] : 0;
        $to = isset($parsed['validTo_time_t']) ? (int) $parsed['validTo_time_t'] : 0;
        if (!$from || !$to) throw new InvalidArgumentException('No se pudo determinar la vigencia del certificado.');
        if ($from > time()) throw new InvalidArgumentException('El certificado electrónico todavía no está vigente.');
        if ($to < time()) throw new InvalidArgumentException('El certificado electrónico está vencido.');

        $fingerprint = @openssl_x509_fingerprint($certificates['cert'], 'sha256');
        if (!$fingerprint) throw new InvalidArgumentException('No se pudo obtener la huella del certificado.');

        return array(
            'subject_name' => $this->distinguishedName(isset($parsed['subject']) ? $parsed['subject'] : array()),
            'issuer_name' => $this->distinguishedName(isset($parsed['issuer']) ? $parsed['issuer'] : array()),
            'serial_number' => substr((string) (isset($parsed['serialNumberHex']) ? $parsed['serialNumberHex'] : (isset($parsed['serialNumber']) ? $parsed['serialNumber'] : '')), 0, 191),
            'fingerprint_sha256' => strtolower(str_replace(':', '', $fingerprint)),
            'valid_from' => date('Y-m-d H:i:s', $from),
            'valid_to' => date('Y-m-d H:i:s', $to),
        );
    }

    public function summary($userId, $companyId)
    {
        $record = UserElectronicCertificate::where('id_empresa', (string) $companyId)
            ->where('user_id', (string) $userId)->first();
        if (!$record) return array('configured' => false);

        $validTo = $record->valid_to ? strtotime((string) $record->valid_to) : 0;
        return array(
            'configured' => true,
            'active' => (bool) $record->estado,
            'subject_name' => $record->subject_name,
            'issuer_name' => $record->issuer_name,
            'serial_number' => $record->serial_number,
            'fingerprint' => strtoupper(implode(':', str_split(substr($record->fingerprint_sha256, 0, 16), 2))),
            'valid_from' => $record->valid_from ? date('d/m/Y', strtotime((string) $record->valid_from)) : null,
            'valid_to' => $validTo ? date('d/m/Y', $validTo) : null,
            'expired' => $validTo > 0 && $validTo < time(),
            'original_name' => $record->original_name,
            'updated_at' => $record->updated_at ? date('d/m/Y H:i', strtotime((string) $record->updated_at)) : null,
        );
    }

    public function delete($userId, $companyId)
    {
        $record = UserElectronicCertificate::where('id_empresa', (string) $companyId)
            ->where('user_id', (string) $userId)->first();
        if (!$record) return false;
        $path = $record->certificate_path;
        $record->delete();
        if ($path) Storage::disk(self::DISK)->delete($path);
        return true;
    }

    /** Returns private signing material for server-side document signers only. */
    public function credentialsForSigning($userId, $companyId)
    {
        $record = UserElectronicCertificate::where('id_empresa', (string) $companyId)
            ->where('user_id', (string) $userId)->where('estado', 1)->first();
        if (!$record) throw new RuntimeException('El usuario no tiene un certificado electrónico configurado.');
        if ($record->valid_from && strtotime((string) $record->valid_from) > time()) throw new RuntimeException('El certificado electrónico todavía no está vigente.');
        if ($record->valid_to && strtotime((string) $record->valid_to) < time()) throw new RuntimeException('El certificado electrónico está vencido.');
        if (!Storage::disk(self::DISK)->exists($record->certificate_path)) throw new RuntimeException('No se encuentra el archivo privado del certificado electrónico.');

        $root = realpath(storage_path('app/private/user-electronic-certificates'));
        $path = $root ? realpath($root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $record->certificate_path)) : false;
        if (!$root || !$path || strpos(strtolower($path), strtolower($root . DIRECTORY_SEPARATOR)) !== 0 || !is_file($path)) {
            throw new RuntimeException('La ruta privada del certificado electrónico no es válida.');
        }

        return array(
            'path' => $path,
            'password' => Crypt::decryptString($record->password_encrypted),
            'fingerprint_sha256' => $record->fingerprint_sha256,
            'certificate_id' => $record->id,
        );
    }

    private function distinguishedName($parts)
    {
        if (!is_array($parts)) return null;
        $values = array();
        foreach (array('CN', 'O', 'OU') as $key) {
            if (!isset($parts[$key])) continue;
            foreach ((array) $parts[$key] as $value) {
                $value = trim((string) $value);
                if ($value !== '' && !in_array($value, $values, true)) $values[] = $value;
            }
        }
        return $values ? substr(implode(' · ', $values), 0, 255) : null;
    }

    private function safeSegment($value)
    {
        $segment = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $value);
        return $segment === '' ? 'company' : $segment;
    }

    private function safeOriginalName($name)
    {
        $name = trim(str_replace(array("\0", '/', '\\'), '_', (string) $name));
        return substr($name === '' ? 'certificado.p12' : $name, 0, 255);
    }
}
