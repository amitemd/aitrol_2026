<?php

namespace Sis_medico;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Sis_medico\Notifications\MyResetPassword;
use Sis_medico\Services\PersonIdentityService;
use Sis_medico\Services\PersonRoleTenantService;
use Illuminate\Support\Facades\DB;
use Sis_medico\Support\RequestSchema as Schema;
use Sis_medico\Support\RequestQueryCache;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Rol institucional reservado para la cuenta que administra el sistema.
     * Los demas perfiles siempre se resuelven mediante permisos configurados.
     */
    const SYSTEM_ROLE_ID = 1;

    /**
     * Identifier used by the existing user catalog for the Doctor profile.
     */
    const DOCTOR_ROLE_ID = 3;
    
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['remember_token'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $keyType = 'string';

    public $incrementing = false;

    protected $hidden = [
        'password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes',
    ];

    protected $dates = ['two_factor_confirmed_at', 'two_factor_exempted_at', 'password_changed_at'];

    protected static function boot()
    {
        parent::boot();
        static::saving(function ($user) {
            if (!$user->isDirty('password') || empty($user->password)) return;

            try {
                if (!Schema::hasColumn($user->getTable(), 'password_changed_at')) return;
            } catch (\Exception $exception) {
                return;
            }

            $user->password_changed_at = date('Y-m-d H:i:s');
            // También invalida cookies persistentes de "recordarme".
            $user->setRememberToken(Str::random(60));
        });
        static::addGlobalScope('active_company', function ($query) {
            app(PersonRoleTenantService::class)->constrainEloquent($query, 'USUARIO');
        });
        static::saved(function ($user) {
            app(PersonIdentityService::class)->sync($user, 'USUARIO', 'id');
        });
    }

    public function persona()
    {
        return $this->belongsTo('Sis_medico\Persona', 'persona_id');
    }

    public function hasTwoFactorEnabled()
    {
        return !empty($this->two_factor_secret) && !empty($this->two_factor_confirmed_at);
    }

    public function isTwoFactorExempt()
    {
        return !empty($this->two_factor_exempted_at);
    }

    public function requiresTwoFactorEnrollment()
    {
        return !$this->hasTwoFactorEnabled() && !$this->isTwoFactorExempt();
    }

    public function passwordHasExpired($now = null)
    {
        if (empty($this->password_changed_at)) return true;

        $maximumAgeDays = max(1, (int) config('password_policy.max_age_days', 72));
        $reference = $now instanceof \DateTimeInterface
            ? \DateTimeImmutable::createFromInterface($now)
            : new \DateTimeImmutable('now', new \DateTimeZone(config('app.timezone')));
        $changedAt = new \DateTimeImmutable(
            $this->password_changed_at instanceof \DateTimeInterface
                ? $this->password_changed_at->format('Y-m-d H:i:s')
                : (string) $this->password_changed_at,
            new \DateTimeZone(config('app.timezone'))
        );

        return $changedAt <= $reference->modify('-'.$maximumAgeDays.' days');
    }

    public function sendPasswordResetNotification(#[\SensitiveParameter] $token)
    {
        $this->notify(new MyResetPassword((string) $token));
    }
    
    public function usuario()
    {
        return $this->belongsTo('Sis_medico\Ct_Nomina', 'id_user');
    }

    public function empresas()
    {
        return $this->belongsToMany('Sis_medico\Empresa', 'usuario_empresa', 'id_usuario', 'id_empresa');
    }

    /**
     * Determine whether the authenticated account belongs to a doctor.
     *
     * @return bool
     */
    public function isDoctor()
    {
        return $this->hasUserTypeName(array('DOCTORES', 'DOCTOR'));
    }

    /**
     * Determine whether this account is the unrestricted Systems operator.
     *
     * @return bool
     */
    public function isSystemUser()
    {
        return $this->hasUserTypeName(array('SISTEMAS', 'SISTEMA'));
    }

    /**
     * Determine whether this account belongs to the nursing profile.
     * The catalog name is authoritative; the numeric ID may differ by database.
     */
    public function isNursingUser()
    {
        return $this->hasUserTypeName(array('ENFERMERIA', 'ENFERMERÍA'));
    }

    public function tipoUsuario()
    {
        return $this->belongsTo('Sis_medico\TipoUsuario', 'id_tipo_usuario');
    }

    /**
     * Resolve a role foreign key from its catalog name when a query needs it.
     * This keeps role filters portable across installations with different IDs.
     */
    public static function roleIdForName($names)
    {
        try {
            if (!Schema::hasTable('tipousuario')) {
                return null;
            }

            $names = is_array($names) ? $names : array($names);
            $names = array_values(array_filter(array_map(function ($name) {
                return strtoupper(trim((string) $name));
            }, $names)));
            if (empty($names)) {
                return null;
            }

            $ids = DB::table('tipousuario')->where(function ($query) use ($names) {
                foreach ($names as $index => $name) {
                    $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                    $query->{$method}('UPPER(TRIM(nombre)) = ?', array($name));
                }
            })->pluck('id')->map(function ($id) {
                return (int) $id;
            })->unique()->values();

            return $ids->count() === 1 ? $ids->first() : null;
        } catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * The VIDEO profile is reserved for waiting-room television accounts.
     */
    public function isVideoDisplayUser()
    {
        return $this->hasUserTypeName('VIDEO');
    }

    public function canManageWaitingRoomDisplay()
    {
        return $this->hasUserTypeName('SISTEMAS') || $this->isVideoDisplayUser();
    }

    public function hasUserTypeName($names)
    {
        try {
            if (!Schema::hasTable('tipousuario')) {
                return false;
            }

            $names = is_array($names) ? $names : array($names);
            $names = array_values(array_filter(array_map(function ($name) {
                return strtoupper(trim((string) $name));
            }, $names)));
            if (empty($names)) {
                return false;
            }

            $names = array_values(array_unique($names));
            sort($names);
            return RequestQueryCache::remember(__METHOD__.json_encode([$this->id_tipo_usuario, $names]), function () use ($names) {
                return DB::table('tipousuario')
                    ->where('id', $this->id_tipo_usuario)
                    ->where(function ($query) use ($names) {
                        foreach ($names as $index => $name) {
                            $method = $index === 0 ? 'whereRaw' : 'orWhereRaw';
                            $query->{$method}('UPPER(TRIM(nombre)) = ?', array($name));
                        }
                    })
                    ->exists();
            }, null, false, ['tipousuario']);
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Check an authorization group using the stable catalog names.
     */
    public function hasAnyUserTypeName($names)
    {
        return $this->hasUserTypeName($names);
    }

    /**
     * Return the initial screen corresponding to the user's profile.
     *
     * @return string
     */
    public function defaultLandingPath()
    {
        if ($this->isVideoDisplayUser()) {
            return '/sala/espera/pacientes';
        }

        return $this->isDoctor() ? '/inicio' : '/v3/admin';
    }

    /**
     * Show the V3-to-medical workspace switch only to Systems, or to a
     * doctor who effectively owns at least one navigable V3 option.
     */
    public function canSwitchToMedicalWorkspace()
    {
        if ($this->isSystemUser()) {
            return true;
        }
        if (!$this->isDoctor()) {
            return false;
        }

        $permission = \Sis_medico\v3\contable\PermisosUsuario::getEffectivePermisos($this);
        $ids = $permission ? json_decode($permission->id_submenu_permiso, true) : array();
        $ids = is_array($ids) ? array_values(array_unique(array_map('intval', $ids))) : array();
        if (empty($ids)) {
            return false;
        }

        return \Sis_medico\v3\contable\Menu::whereIn('id_menu', $ids)
            ->where('estado_menu', 1)
            ->whereNotNull('funcion_menu')
            ->exists();
    }

    /**
     * The legacy clinical workspaces use the opcion_usuario catalog as their
     * route-level authorization source. Keep workspace links aligned with it.
     */
    public function canSwitchToHospitalWorkspace()
    {
        if ($this->isSystemUser()) return true;

        try {
            return \Sis_medico\Opcion_Usuario::where('id_opcion', 1)
                ->where('id_tipo_usuario', $this->id_tipo_usuario)
                ->exists();
        } catch (\Exception $exception) {
            return false;
        }
    }

    public function canSwitchToAmbulatoryWorkspace()
    {
        try {
            return \Sis_medico\Opcion_Usuario::where('id_opcion', 2)
                ->where('id_tipo_usuario', $this->id_tipo_usuario)
                ->exists();
        } catch (\Exception $exception) {
            return false;
        }
    }

}
