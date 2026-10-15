<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes.
     */
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'link',
        'is_read',
    ];

    /**
     * Attribute casting.
     */
    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    /**
     * The recipient user.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
