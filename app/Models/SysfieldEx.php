<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SysfieldEx extends Model
{
    protected $connection = 'print';
    protected $table = 'sysfield_ex';
    protected $primaryKey = 'recno';
    public $timestamps = false;

    protected $guarded = [];

    // Cache Configuration (Business Logic: 24 ชั่วโมง)
    public const CACHE_TTL_SECONDS = 86400; // 24 hours
    public const CACHE_KEY_HEADER_FIELDS = 'sysfield_ex_header_fields';
    public const CACHE_KEY_DETAIL_FIELDS = 'sysfield_ex_detail_fields';

    /**
     * Model Boot Event: ล้างแคชโดยอัตโนมัติเมื่อมีการบันทึกหรือลบข้อมูลผ่าน Model (แนวทางที่ 3)
     */
    protected static function booted()
    {
        static::saved(function () {
            static::clearFieldsCache();
        });

        static::deleted(function () {
            static::clearFieldsCache();
        });
    }

    /**
     * ดึงรายการฟิลด์ส่วนหัว (Header Template: print1 IN ('HL', 'HR'))
     * พร้อมระบบ Cache 24 ชม. และรองรับ Force Refresh (แนวทางที่ 1 & 2)
     */
    public static function getHeaderFields(bool $forceRefresh = false)
    {
        if ($forceRefresh) {
            Cache::forget(self::CACHE_KEY_HEADER_FIELDS);
        }

        return Cache::remember(self::CACHE_KEY_HEADER_FIELDS, self::CACHE_TTL_SECONDS, function () {
            return static::whereIn('print1', ['HL', 'HR'])
                ->where('app_show', 'Y')
                ->orderBy('app_showt', 'asc')
                ->get();
        });
    }

    /**
     * ดึงรายการฟิลด์ส่วนรายการ (Detail Template: print1 IN ('D1', 'D2'))
     * พร้อมระบบ Cache 24 ชม. และรองรับ Force Refresh (แนวทางที่ 1 & 2)
     */
    public static function getDetailFields(bool $forceRefresh = false)
    {
        if ($forceRefresh) {
            Cache::forget(self::CACHE_KEY_DETAIL_FIELDS);
        }

        return Cache::remember(self::CACHE_KEY_DETAIL_FIELDS, self::CACHE_TTL_SECONDS, function () {
            return static::whereIn('print1', ['D1', 'D2'])
                ->where('app_show', 'Y')
                ->orderBy('app_showt', 'asc')
                ->get();
        });
    }

    /**
     * สั่งล้าง Cache ของฟิลด์ sysfield_ex ทั้งหมด
     */
    public static function clearFieldsCache(): void
    {
        Cache::forget(self::CACHE_KEY_HEADER_FIELDS);
        Cache::forget(self::CACHE_KEY_DETAIL_FIELDS);
    }
}
