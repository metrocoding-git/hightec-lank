<?php

namespace App\Support\Excel;

use Maatwebsite\Excel\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/**
 * "export eke di data okkoma string wage yanne.. number nam number ekak wage
 * wenna one, date ekak nam date ekak wage yanna one" — every server-side
 * Excel download (Maatwebsite Excel::download) goes through this binder
 * (config/excel.php -> value_binder.default):
 *
 *  - dates written as text ("2026-11-02", "2026-11-02 14:30", "2026-11-02
 *    02:30 PM", "02/11/2026", "2 Nov 2026") become real Excel dates
 *    (yyyy-mm-dd, or yyyy-mm-dd hh:mm when there is a time);
 *  - "Rs. 12,500.00" / "12,500" / "LKR 500" become numbers (#,##0.00 / #,##0);
 *  - plain numbers stay numbers (as before);
 *  - leading-zero codes / phone numbers and 12+ digit values (NIC) stay text
 *    so Excel doesn't drop the 0 or show 2.00012E+11.
 *
 * Anything else ("3/5", "Yes", "05:30" durations, names...) is left as text.
 */
class TypedValueBinder extends DefaultValueBinder
{
    private const MONTHS = [
        'jan' => 1, 'feb' => 2, 'mar' => 3, 'apr' => 4, 'may' => 5, 'jun' => 6,
        'jul' => 7, 'aug' => 8, 'sep' => 9, 'sept' => 9, 'oct' => 10, 'nov' => 11, 'dec' => 12,
        'january' => 1, 'february' => 2, 'march' => 3, 'april' => 4, 'june' => 6, 'july' => 7,
        'august' => 8, 'september' => 9, 'october' => 10, 'november' => 11, 'december' => 12,
    ];

    public function bindValue(Cell $cell, $value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $this->setDate($cell, $value, $value->format('H:i:s') !== '00:00:00');
        }

        if (is_string($value)) {
            $text = trim($value);

            if ($text !== '' && strlen($text) <= 30) {
                // NIC / long digit strings stay text.
                if (preg_match('/^\d{12,}$/', $text)) {
                    $cell->setValueExplicit($text, DataType::TYPE_STRING);
                    return true;
                }

                if ($date = self::parseDate($text)) {
                    return $this->setDate($cell, $date['date'], $date['has_time']);
                }

                if (($number = self::parseAmount($text)) !== null) {
                    $cell->setValueExplicit($number['value'], DataType::TYPE_NUMERIC);
                    $cell->getStyle()->getNumberFormat()->setFormatCode($number['decimals'] ? '#,##0.00' : '#,##0');
                    return true;
                }
            }
        }

        return parent::bindValue($cell, $value);
    }

    private function setDate(Cell $cell, \DateTimeInterface $date, bool $hasTime): bool
    {
        $cell->setValueExplicit(ExcelDate::PHPToExcel($date), DataType::TYPE_NUMERIC);
        $cell->getStyle()->getNumberFormat()->setFormatCode(
            $hasTime ? 'yyyy-mm-dd hh:mm' : NumberFormat::FORMAT_DATE_YYYYMMDD
        );

        return true;
    }

    /** @return array{date: \DateTimeImmutable, has_time: bool}|null */
    public static function parseDate(string $text): ?array
    {
        $text = preg_replace('/\s+/', ' ', trim($text));
        if (strlen($text) < 8) {
            return null;
        }

        $datePart = $text;
        $timePart = null;
        if (preg_match('/^(.*?)[ T,]+(\d{1,2}:\d{2}(?::\d{2})?(?:\s*[ap]\.?m\.?)?)$/i', $text, $m)) {
            $datePart = trim($m[1]);
            $timePart = $m[2];
        }

        if (preg_match('/^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})$/', $datePart, $m)) {
            [$y, $mo, $d] = [(int) $m[1], (int) $m[2], (int) $m[3]];
        } elseif (preg_match('/^(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{4})$/', $datePart, $m)) {
            [$d, $mo, $y] = [(int) $m[1], (int) $m[2], (int) $m[3]]; // day first (Sri Lanka)
        } elseif (preg_match('/^(\d{1,2})(?:st|nd|rd|th)?[ \-]([A-Za-z]{3,9})\.?,?[ \-](\d{4})$/', $datePart, $m)) {
            [$d, $mo, $y] = [(int) $m[1], self::MONTHS[strtolower($m[2])] ?? 0, (int) $m[3]];
        } elseif (preg_match('/^([A-Za-z]{3,9})\.? (\d{1,2})(?:st|nd|rd|th)?,? (\d{4})$/', $datePart, $m)) {
            [$mo, $d, $y] = [self::MONTHS[strtolower($m[1])] ?? 0, (int) $m[2], (int) $m[3]];
        } else {
            return null;
        }

        if ($y < 1900 || $y > 2200 || !checkdate($mo, $d, $y)) {
            return null;
        }

        $h = $i = $s = 0;
        if ($timePart !== null) {
            if (!preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?\s*([ap])?\.?m?\.?$/i', $timePart, $t)) {
                return null;
            }
            [$h, $i, $s] = [(int) $t[1], (int) $t[2], (int) ($t[3] ?? 0)];
            if (!empty($t[4])) {
                if ($h < 1 || $h > 12) {
                    return null;
                }
                $h = ($h % 12) + (strtolower($t[4]) === 'p' ? 12 : 0);
            }
            if ($h > 23 || $i > 59 || $s > 59) {
                return null;
            }
        }

        $date = (new \DateTimeImmutable('@0'))->setDate($y, $mo, $d)->setTime($h, $i, $s);

        return ['date' => $date, 'has_time' => $timePart !== null];
    }

    /** "Rs. 12,500.00", "LKR 500", "12,500", "-1,000.50" -> number. Plain digits are left to the parent. */
    public static function parseAmount(string $text): ?array
    {
        $str = trim($text);
        $negative = false;
        if (preg_match('/^\((.*)\)$/', $str, $m)) {
            $negative = true;
            $str = trim($m[1]);
        }
        $hadCurrency = (bool) preg_match('/^(rs\.?|lkr)\s*/i', $str);
        $str = preg_replace('/^(rs\.?|lkr)\s*/i', '', $str);
        if (str_starts_with($str, '-')) {
            $negative = !$negative;
            $str = trim(substr($str, 1));
        }

        $grouped = (bool) preg_match('/^\d{1,3}(,\d{3})+(\.\d+)?$/', $str);
        $plain = (bool) preg_match('/^\d+(\.\d+)?$/', $str);

        // Plain digits without currency are already handled by the parent.
        if (!$grouped && !($plain && ($hadCurrency || $negative))) {
            return null;
        }

        $digits = str_replace(',', '', $str);
        $int = explode('.', $digits)[0];
        if ((strlen($int) > 1 && $int[0] === '0') || strlen($int) > 11) {
            return null;
        }

        $value = (float) $digits;

        return ['value' => $negative ? -$value : $value, 'decimals' => str_contains($digits, '.')];
    }
}
