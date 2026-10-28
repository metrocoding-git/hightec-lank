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
     * Prefix ending in a number (e.g. "WL/OP/10"): that number IS the first
     * certificate number, and every next one adds 1 —
     *   WL/OP/10, WL/OP/11, WL/OP/12, ...
     * Leading zeros are kept ("FL/OP/0010" -> "FL/OP/0011").
     *
     * Prefix with no number at the end (e.g. "FL/OP/", "STB-AD"): unchanged
     * old behaviour — prefix + 8-digit running number (FL/OP/00000001).
     *
     * Either way, a number freed by undoing a generated certificate is
     * reused first (lowest gap first) before the series moves on, and the
     * series row is locked so two certificates generated at the same moment
     * can't get the same number.
     */
    public static function nextSerialFor(int $prefixId): string
    {
        return DB::transaction(function () use ($prefixId) {
            $row = self::where('id', $prefixId)->lockForUpdate()->firstOrFail();

            [$base, $start, $width] = self::parsePrefix($row->prefix);

            // Every certificate number already taken in this series.
            $taken = DB::table('certificates')
                ->where('certificate_number', 'like', addcslashes($base, '%_\\') . '%')
                ->pluck('certificate_number')
                ->flip();

            $n = $start;
            while ($taken->has(self::formatNumber($base, $n, $width))) {
                $n++;
            }

            if ($n > (int) $row->last_number) {
                $row->last_number = $n;
                $row->save();
            }

            return self::formatNumber($base, $n, $width);
        });
    }

    /**
     * Splits a prefix into [base, first number, digit width].
     *   "WL/OP/10" -> ["WL/OP/", 10, 2]
     *   "FL/OP/"   -> ["FL/OP/", 1, 8]   (old style: running number appended)
     *   "STB-AD"   -> ["STB-AD/", 1, 8]
     */
    public static function parsePrefix(string $prefix): array
    {
        $prefix = trim($prefix);

        if (preg_match('/^(.*?)(\d+)$/', $prefix, $m)) {
            return [$m[1], (int) $m[2], strlen($m[2])];
        }

        return [self::serialBase($prefix), 1, 8];
    }

    public static function formatNumber(string $base, int $number, int $width): string
    {
        return $base . str_pad((string) $number, $width, '0', STR_PAD_LEFT);
    }

    /** The number the next certificate in this series will get (preview only). */
    public static function previewNext(string $prefix): string
    {
        [$base, $start, $width] = self::parsePrefix($prefix);

        $taken = DB::table('certificates')
            ->where('certificate_number', 'like', addcslashes($base, '%_\\') . '%')
            ->pluck('certificate_number')
            ->flip();

        $n = $start;
        while ($taken->has(self::formatNumber($base, $n, $width))) {
            $n++;
        }

        return self::formatNumber($base, $n, $width);
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
