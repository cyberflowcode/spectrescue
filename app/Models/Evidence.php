<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Evidence extends Model
{
    use HasFactory;

    protected $table = 'evidence';

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'incident_id',
        'file_path',
        'file_name',
        'file_type',
        'caption',
    ];

    /**
     * The incident this piece of evidence belongs to.
     */
    public function incident()
    {
        return $this->belongsTo(Incident::class, 'incident_id');
    }

    /**
     * Get accessible URL for the uploaded evidence file.
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
