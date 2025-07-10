<?php

namespace Arden28\Guardian\Models;

use Illuminate\Database\Eloquent\Model;

class ImpersonationLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'impersonation_logs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'impersonator_id',
        'impersonated_id',
        'started_at',
        'ended_at',
        'session_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Get the impersonator user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function impersonator()
    {
        return $this->belongsTo(config('guardian.user_model', 'App\Models\User'), 'impersonator_id');
    }

    /**
     * Get the impersonated user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function impersonated()
    {
        return $this->belongsTo(config('guardian.user_model', 'App\Models\User'), 'impersonated_id');
    }
}