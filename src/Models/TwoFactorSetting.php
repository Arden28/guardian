<?php

namespace Arden28\Guardian\Models;

use Illuminate\Database\Eloquent\Model;

class TwoFactorSetting extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'two_factor_settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'method',
        'secret',
        'phone_number',
        'is_enabled',
        'last_verified_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_enabled' => 'boolean',
        'last_verified_at' => 'datetime',
    ];

    /**
     * Get the user associated with the 2FA settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('guardian.user_model', 'App\Models\User'), 'user_id');
    }
}