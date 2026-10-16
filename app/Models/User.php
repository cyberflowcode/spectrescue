<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Includes name, email, phone, role, and password.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'is_active',
        'is_online',
        'last_seen_at',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'         => 'hashed',
            'is_active'        => 'boolean',
            'is_online'        => 'boolean',
            'last_seen_at'     => 'datetime',
        ];
    }

    // ==========================================
    // Role Helper Methods (Student-Friendly)
    // ==========================================

    public function isCitizen(): bool
    {
        return $this->role === 'citizen';
    }

    public function isDispatcher(): bool
    {
        return $this->role === 'dispatcher';
    }

    public function isResponder(): bool
    {
        return $this->role === 'responder';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if this user account is active (not deactivated by admin).
     */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /**
     * Check if this user is currently online.
     * Evaluates is_online flag and verifies last activity was within 15 minutes.
     */
    public function isOnline(): bool
    {
        if (!$this->is_online) {
            return false;
        }

        if (!$this->last_seen_at) {
            return false;
        }

        return $this->last_seen_at->gt(now()->subMinutes(15));
    }

    // ==========================================
    // Database Relationships
    // ==========================================

    /**
     * Incidents reported by this user (Citizen role).
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class, 'citizen_id');
    }

    /**
     * Responder profile details if this user is a responder.
     */
    public function responderProfile()
    {
        return $this->hasOne(Responder::class, 'user_id');
    }

    /**
     * Assignments created by this user (Dispatcher role).
     */
    public function dispatchedAssignments()
    {
        return $this->hasMany(Assignment::class, 'dispatcher_id');
    }

    /**
     * In-app notifications for this user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id')->latest();
    }
}
