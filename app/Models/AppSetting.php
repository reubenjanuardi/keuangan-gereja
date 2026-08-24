<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppSetting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    /**
     * Get a setting value by key, with an optional default.
     */
    public static function get(string $key, string $default = ''): string
    {
        return static::find($key)?->value ?? $default;
    }

    /**
     * Set (upsert) a setting value.
     */
    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Get the public URL for the church logo from local public storage, or null if not set.
     */
    public static function getLogoUrl(): ?string
    {
        $logo = static::get('church_logo');
        if (empty($logo)) {
            return null;
        }

        if (filter_var($logo, FILTER_VALIDATE_URL)) {
            return $logo;
        }

        return asset('storage/' . ltrim($logo, '/'));
    }
}

