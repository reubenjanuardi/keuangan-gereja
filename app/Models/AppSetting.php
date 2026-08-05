<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
