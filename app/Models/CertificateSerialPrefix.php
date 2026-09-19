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
     * Atomically claim the next serial number in this series, e.g.
     * "STB-NW-0001", then "STB-NW-0002", ... Uses lockForUpdate() inside a
     * transaction so two certificates generated at the same moment can
     * never be handed the same number.
     *
     * NOT called from anywhere yet — this is the template-management
     * side of the feature only. Whatever controller actually GENERATES a
     * certificate (not part of this delivery) should call
     * CertificateSerialPrefix::nextSerialFor($template->serial_prefix_id)
     * at the moment a certificate is created, and store the returned
     * string on the certificate record, e.g.:
     *
     *   if ($template->serial_prefix_id) {
     *       $certificate->serial_number = CertificateSerialPrefix::nextSerialFor($template->serial_prefix_id);
     *   }
     */
    public static function nextSerialFor(int $prefixId): string
    {
        return DB::transaction(function () use ($prefixId) {
            $row = self::where('id', $prefixId)->lockForUpdate()->firstOrFail();
            $row->last_number += 1;
            $row->save();

            return $row->prefix . '-' . str_pad((string) $row->last_number, 4, '0', STR_PAD_LEFT);
        });
    }
}
