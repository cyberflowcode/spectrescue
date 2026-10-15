<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'incident_id',
        'responder_id',
        'containment_method',
        'findings_summary',
        'threat_neutralized',
        'evidence_collected',
        'recommendations',
        'submitted_at',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'threat_neutralized' => 'boolean',
            'submitted_at'       => 'datetime',
        ];
    }

    /**
     * The incident this response resolved.
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    /**
     * The responder who filed this report.
     */
    public function responder()
    {
        return $this->belongsTo(Responder::class, 'responder_id');
    }
}
