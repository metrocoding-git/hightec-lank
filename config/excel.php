<?php

/*
 * Only the settings changed from the Laravel Excel defaults
 * (vendor/maatwebsite/excel/config/excel.php) — the rest is merged in by the
 * package.
 */
return [
    /*
     * Excel downloads: date text becomes real Excel dates and "Rs. 1,250.00"
     * style amounts become numbers, so the sheet can be sorted / filtered /
     * summed. See App\Support\Excel\TypedValueBinder.
     */
    'value_binder' => [
        'default' => App\Support\Excel\TypedValueBinder::class,
    ],
];
