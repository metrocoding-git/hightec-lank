<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CertificateSerialPrefix extends Model
{
    protected $fillable = [
        'prefix',
        'last_number',
    ];

    public function templates()
    {
        return $this->hasMany(CertificateTemplate::class, 'serial_prefix_id');
    }

    /**
     * Atomically claim the next serial number in this series.
     *
     * "if back 20 cirial number certificate. if genarate onther set ... auto
     * fill for blank 20 and next one go 31" — numbers freed by undoing a
     * certificate (deleting it at the "generated" stage) are reused first,
     * lowest gap first. Only when there is no gap does the counter move on
     * (last_number + 1). Example: 1-30 issued, #20 undone -> next is 20,
     * the one after is 31.
     *
     * lockForUpdate() on the series row means two certificates generated at
     * the same moment still can't get the same number.
     */
    public static function nextSerialFor(int $prefixId): string
    {
        return DB::transaction(function () use ($prefixId) {
            $row = self::where('id', $prefixId)->lockForUpdate()->firstOrFail();

            $base = self::serialBase($row->prefix);

            $used = DB::table('certificates')
                ->where('certificate_number', 'like', addcslashes($base, '%_\\') . '%')
                ->pluck('certificate_number')
                ->map(fn($number) => substr($number, strlen($base)))
                ->filter(fn($digits) => ctype_digit($digits))
                ->map(fn($digits) => (int) $digits)
                ->flip();

            for ($n = 1; $n <= $row->last_number; $n++) {
                if (!$used->has($n)) {
                    return self::formatSerial($row->prefix, $n); // fill the gap
                }
            }

            $row->last_number += 1;
            $row->save();

            return self::formatSerial($row->prefix, $row->last_number);
        });
    }

    /**
     * Prefix + separator. A prefix that already ends in "/", "-" or a space
     * (e.g. "FL/OP/") isn't given a second "/" — previously this produced
     * "FL/OP//00000001".
     */
    public static function serialBase(string $prefix): string
    {
        return preg_match('~[/\- ]$~', $prefix) ? $prefix : $prefix . '/';
    }

    public static function formatSerial(string $prefix, int $number): string
    {
        return self::serialBase($prefix) . str_pad((string) $number, 8, '0', STR_PAD_LEFT);
    }
}
