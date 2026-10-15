<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'incident_id',
        'responder_id',
        'dispatcher_id',
        'status', // assigned, accepted, declined
        'dispatcher_notes',
        'responder_notes',
        'assigned_at',
        'responded_at',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'responded_at' => 'datetime',
        ];
    }

    /**
     * The emergency incident being assigned.
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    /**
     * The responder (exorcist/ghost hunter) receiving the assignment.
     */
    public function responder()
    {
        return $this->belongsTo(Responder::class, 'responder_id');
    }

    /**
     * The dispatcher user who assigned the responder.
     */
    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'dispatcher_id');
    }

    public function isAssigned(): bool
    {
        return $this->status === 'assigned';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isDeclined(): bool
    {
        return $this->status === 'declined';
    }
}
