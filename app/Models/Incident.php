<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'citizen_id',
        'incident_type_id',
        'tracking_code',
        'title',
        'description',
        'location_address',
        'location_city',
        'latitude',
        'longitude',
        'threat_level', // Low, Medium, High, Critical
        'status',       // Pending, Dispatched, Responding, Resolved, Cancelled
    ];

    /**
     * Attribute casts.
     */
    protected $casts = [
        'latitude'  => 'float',
        'longitude' => 'float',
    ];

    // ==========================================
    // Database Relationships
    // ==========================================

    /**
     * The citizen who reported this incident.
     */
    public function citizen()
    {
        return $this->belongsTo(User::class, 'citizen_id');
    }

    /**
     * The category/type of paranormal emergency.
     */
    public function incidentType()
    {
        return $this->belongsTo(IncidentType::class, 'incident_type_id');
    }

    /**
     * Photos or evidence attached to this emergency.
     */
    public function evidence()
    {
        return $this->hasMany(Evidence::class, 'incident_id');
    }

    /**
     * All dispatch assignments for this incident.
     */
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'incident_id');
    }

    /**
     * The latest assignment for this emergency.
     */
    public function latestAssignment()
    {
        return $this->hasOne(Assignment::class, 'incident_id')->latestOfMany();
    }

    /**
     * The final resolution/response report filed by a responder.
     */
    public function responseReport()
    {
        return $this->hasOne(Response::class, 'incident_id');
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    public function isPending(): bool
    {
        return $this->status === 'Pending';
    }

    public function isDispatched(): bool
    {
        return $this->status === 'Dispatched';
    }

    public function isResponding(): bool
    {
        return $this->status === 'Responding';
    }

    public function isResolved(): bool
    {
        return $this->status === 'Resolved';
    }

    /**
     * Check if the incident has valid GPS coordinates for map plotting.
     */
    public function hasCoordinates(): bool
    {
        return !is_null($this->latitude) && !is_null($this->longitude);
    }

    /**
     * Helper to return badge color classes for threat levels in Blade views.
     */
    public function threatBadgeClass(): string
    {
        return match ($this->threat_level) {
            'Critical' => 'bg-red-500/20 text-red-400 border border-red-500/40',
            'High'     => 'bg-orange-500/20 text-orange-400 border border-orange-500/40',
            'Medium'   => 'bg-yellow-500/20 text-yellow-400 border border-yellow-500/40',
            default    => 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40',
        };
    }

    /**
     * Helper to return badge color classes for incident status in Blade views.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'Pending'    => 'bg-amber-500/20 text-amber-300 border border-amber-500/30',
            'Dispatched' => 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30',
            'Responding' => 'bg-purple-500/20 text-purple-300 border border-purple-500/30',
            'Resolved'   => 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30',
            'Cancelled'  => 'bg-zinc-500/20 text-zinc-400 border border-zinc-500/30',
            default      => 'bg-slate-500/20 text-slate-300 border border-slate-500/30',
        };
    }
}
