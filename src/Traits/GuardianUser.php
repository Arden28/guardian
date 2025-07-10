<?php

namespace Arden28\Guardian\Traits;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Arden28\Guardian\Models\TwoFactorSetting;
use Arden28\Guardian\Models\ImpersonationLog;

trait GuardianUser
{
    use HasApiTokens, HasRoles;

    /**
     * Boot the trait.
     *
     * @return void
     */
    protected static function bootGuardianUser()
    {
        // Generate UUID for new users if configured
        if (config('guardian.use_uuid', false)) {
            static::creating(function ($model) {
                if (empty($model->id)) {
                    $model->id = Str::uuid()->toString();
                }
            });
        }
    }

    /**
     * Get the two-factor settings for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function twoFactorSettings()
    {
        return $this->hasOne(TwoFactorSetting::class, 'user_id');
    }

    /**
     * Get the impersonation logs where the user is the impersonator.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function impersonationLogsAsImpersonator()
    {
        return $this->hasMany(ImpersonationLog::class, 'impersonator_id');
    }

    /**
     * Get the impersonation logs where the user is the impersonated.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function impersonationLogsAsImpersonated()
    {
        return $this->hasMany(ImpersonationLog::class, 'impersonated_id');
    }

    /**
     * Check if the user has 2FA enabled.
     *
     * @return bool
     */
    public function hasTwoFactorEnabled()
    {
        return $this->twoFactorSettings && $this->twoFactorSettings->is_enabled;
    }

    /**
     * Check if the user can impersonate others.
     *
     * @return bool
     */
    public function canImpersonate()
    {
        return $this->hasRole(config('guardian.roles.admin_role', 'admin'));
    }
}