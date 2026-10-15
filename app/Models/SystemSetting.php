<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $fillable = ['key', 'value', 'label', 'type'];

    /**
     * Helper: get a setting value by key.
     * Returns the default if not found.
     */
    public static function get(string $key, string $default = ''): string
    {
        $setting = static::where('key', $key)->first();
        return $setting ? (string) $setting->value : $default;
    }
}
