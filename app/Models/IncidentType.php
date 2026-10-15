<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidentType extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'name',
        'description',
        'default_threat_level',
    ];

    /**
     * All incidents categorized under this type.
     */
    public function incidents()
    {
        return $this->hasMany(Incident::class, 'incident_type_id');
    }
}
