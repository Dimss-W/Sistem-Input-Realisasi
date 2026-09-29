<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    use HasFactory;

    protected $table = 'app_settings';

    protected $fillable = [
        'key',
        'value',
        'group',
        'description',
    ];

    /**
     * Ambil nilai setting berdasarkan key (dengan in-memory / cache support).
     */
    public static function getValue(string $key, $default = null)
    {
        try {
            $setting = static::where('key', $key)->first();
            return $setting && $setting->value !== null && $setting->value !== '' ? $setting->value : $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * Set atau update nilai setting.
     */
    public static function setValue(string $key, $value, string $group = 'general', ?string $description = null)
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->group = $group;
        if ($description) {
            $setting->description = $description;
        }
        $setting->save();

        return $setting;
    }

    /**
     * Ambil semua setting berdasarkan grup dalam bentuk key => value array.
     */
    public static function getGroup(string $group): array
    {
        try {
            return static::where('group', $group)->pluck('value', 'key')->toArray();
        } catch (\Exception $e) {
            return [];
        }
    }
}
