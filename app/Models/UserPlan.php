<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'payment_status',
        'payment_reference',
        'last_renewal_date',
        'auto_renewal',
        'notes',
        'hotspot_username',
        'hotspot_password'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'last_renewal_date' => 'date',
        'auto_renewal' => 'boolean'
    ];

    /**
     * Get the user that owns the subscription
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan associated with the subscription
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Scope a query to only include active subscriptions
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include subscriptions that are about to expire
     */
    public function scopeAboutToExpire($query, $days = 7)
    {
        return $query->where('status', 'active')
            ->whereDate('end_date', '<=', now()->addDays($days))
            ->whereDate('end_date', '>', now());
    }

    /**
     * Check if the subscription is active
     */
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    /**
     * Check if the subscription has expired
     */
    public function hasExpired()
    {
        return $this->end_date < now();
    }
}
