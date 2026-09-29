<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting value by key with optional default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember('system_setting_'.$key, 3600, function () use ($key, $default) {
            $setting = static::find($key);

            return $setting?->value ?? $default;
        });
    }

    /**
     * Set/update a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        Cache::forget('system_setting_'.$key);
        Cache::forget('system_settings_all');
    }

    /**
     * Get all settings as an associative key-value array.
     */
    public static function getAll(): array
    {
        return Cache::remember('system_settings_all', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }
}
