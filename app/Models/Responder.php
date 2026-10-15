<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Responder extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'user_id',
        'specialization',
        'badge_number',
        'bio',
        'verification_status', // pending, approved, rejected
        'is_available',
        'verified_at',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'is_available' => 'boolean',
            'verified_at' => 'datetime',
        ];
    }

    /**
     * The User account linked to this responder profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Emergency assignments given to this responder.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'responder_id');
    }

    /**
     * Post-incident response reports submitted by this responder.
     */
    public function responses()
    {
        return $this->hasMany(Response::class, 'responder_id');
    }

    /**
     * Check if responder is approved by admin.
     */
    public function isApproved(): bool
    {
        return $this->verification_status === 'approved';
    }

    /**
     * Check if responder application is pending.
     */
    public function isPending(): bool
    {
        return $this->verification_status === 'pending';
    }
}
