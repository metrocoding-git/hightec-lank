/*
 * Typed values in DataTables Excel / CSV exports.
 *
 * "export eke di data okkoma string wage yanne.. number nam number ekak wage
 * wenna one, date ekak nam date ekak wage yanna one" — by default the table
 * export buttons write most cells as text (dates always, and numbers with
 * "Rs.", spaces, etc.), so Excel can't sort / filter / sum them properly.
 *
 * Loaded once after buttons.html5.min.js (components/partials/data_table_js),
 * so it applies to every table's export buttons without touching each page:
 *   - Excel: numbers become real numbers (#,##0 / #,##0.00 / %), dates
 *     become real Excel dates (yyyy-mm-dd, with time when there is one),
 *     times become real times (h:mm AM/PM). Header and title rows are
 *     left alone. A page's own `customize` still runs afterwards.
 *   - CSV: dates are written as yyyy-mm-dd (yyyy-mm-dd hh:mm) and
 *     "Rs. 1,250.00" style amounts as plain 1250.00 so Excel reads them as
 *     date / number; values with a leading 0 (phone numbers, codes) and long
 *     digit strings (NIC numbers) are kept as text so Excel doesn't drop the
 *     0 or turn them into 2.0001E+11.
 * Ids with a leading 0, NIC numbers and other long digit strings always stay
 * text in Excel too.
 */
(function ($) {
    'use strict';

    if (!$ || !$.fn || !$.fn.dataTable || !$.fn.dataTable.ext || !$.fn.dataTable.ext.buttons) {
        return;
    }

    var buttons = $.fn.dataTable.ext.buttons;

    var MONTHS = {
        jan: 1, feb: 2, mar: 3, apr: 4, may: 5, jun: 6, jul: 7, aug: 8, sep: 9, sept: 9, oct: 10, nov: 11, dec: 12,
        january: 1, february: 2, march: 3, april: 4, june: 6, july: 7, august: 8, september: 9,
        october: 10, november: 11, december: 12
    };

    function pad(n) {
        return (n < 10 ? '0' : '') + n;
    }

    function validDate(y, m, d) {
        if (y < 1900 || y > 2200 || m < 1 || m > 12 || d < 1 || d > 31) {
            return false;
        }
        var dt = new Date(Date.UTC(y, m - 1, d));
        return dt.getUTCFullYear() === y && dt.getUTCMonth() === m - 1 && dt.getUTCDate() === d;
    }

    // "09:30", "9:30 PM", "09:30:15 am" -> {h, i, s} (24h) or null
    function parseTime(str) {
        var m = /^(\d{1,2}):(\d{2})(?::(\d{2}))?\s*([ap]\.?m\.?)?$/i.exec(str);
        if (!m) {
            return null;
        }
        var h = parseInt(m[1], 10), i = parseInt(m[2], 10), s = m[3] ? parseInt(m[3], 10) : 0;
        if (m[4]) {
            if (h < 1 || h > 12) {
                return null;
            }
            var pm = m[4].charAt(0).toLowerCase() === 'p';
            h = (h % 12) + (pm ? 12 : 0);
        }
        if (h > 23 || i > 59 || s > 59) {
            return null;
        }
        return { h: h, i: i, s: s, ampm: !!m[4] };
    }

    // Date (optionally with a time) in the formats the app shows.
    // Day-first for d/m/y, as used in Sri Lanka.
    function parseDate(raw) {
        var str = $.trim(String(raw)).replace(/\s+/g, ' ');
        if (str.length < 8 || str.length > 30) {
            return null;
        }

        var datePart = str, timePart = '';
        var tm = /^(.*?)[ T,]+(\d{1,2}:\d{2}(?::\d{2})?(?:\s*[ap]\.?m\.?)?)$/i.exec(str);
        if (tm) {
            datePart = $.trim(tm[1]);
            timePart = tm[2];
        }

        var y, mo, d, m;
        if ((m = /^(\d{4})[-\/.](\d{1,2})[-\/.](\d{1,2})$/.exec(datePart))) {
            y = +m[1]; mo = +m[2]; d = +m[3];
        } else if ((m = /^(\d{1,2})[-\/.](\d{1,2})[-\/.](\d{4})$/.exec(datePart))) {
            d = +m[1]; mo = +m[2]; y = +m[3];
        } else if ((m = /^(\d{1,2})(?:st|nd|rd|th)?[ \-]([A-Za-z]{3,9})\.?,?[ \-](\d{4})$/.exec(datePart))) {
            d = +m[1]; mo = MONTHS[m[2].toLowerCase()]; y = +m[3];
        } else if ((m = /^([A-Za-z]{3,9})\.? (\d{1,2})(?:st|nd|rd|th)?,? (\d{4})$/.exec(datePart))) {
            mo = MONTHS[m[1].toLowerCase()]; d = +m[2]; y = +m[3];
        } else {
            return null;
        }

        if (!mo || !validDate(y, mo, d)) {
            return null;
        }

        var t = null;
        if (timePart) {
            t = parseTime(timePart);
            if (!t) {
                return null;
            }
        }

        return { y: y, m: mo, d: d, t: t };
    }

    // "Rs. 1,250.00", "LKR 1,250", "1,250", "-1 250.5", "(1,250.00)" -> number or null
    function parseNumber(raw) {
        var str = $.trim(String(raw));
        if (!str || str.length > 25) {
            return null;
        }
        var neg = false;
        var p = /^\((.*)\)$/.exec(str);
        if (p) {
            neg = true;
            str = $.trim(p[1]);
        }
        str = str.replace(/^(rs\.?|lkr|\$|usd)\s*/i, '').replace(/\s*(rs\.?|lkr|\/=|\/-)$/i, '');
        if (str.charAt(0) === '-') {
            neg = !neg;
            str = $.trim(str.slice(1));
        }
        str = str.replace(/^(rs\.?|lkr)\s*/i, '');

        if (!/^\d{1,3}([, ]\d{3})*(\.\d+)?$/.test(str) && !/^\d+(\.\d+)?$/.test(str)) {
            return null;
        }
        var plain = str.replace(/[, ]/g, '');
        var intPart = plain.split('.')[0];
        // Leading 0 (phone numbers, codes) and long digit strings (NIC numbers) stay text.
        if ((intPart.length > 1 && intPart.charAt(0) === '0') || intPart.length > 11) {
            return null;
        }
        var n = parseFloat(plain);
        if (!isFinite(n)) {
            return null;
        }
        return { value: neg ? -n : n, decimals: plain.indexOf('.') !== -1 };
    }

    function parsePercent(raw) {
        var m = /^(-?\d+(?:\.\d+)?)\s*%$/.exec($.trim(String(raw)));
        return m ? parseFloat(m[1]) / 100 : null;
    }

    function excelSerial(dt) {
        var days = (Date.UTC(dt.y, dt.m - 1, dt.d) - Date.UTC(1899, 11, 30)) / 86400000;
        if (dt.t) {
            days += (dt.t.h * 3600 + dt.t.i * 60 + dt.t.s) / 86400;
        }
        return days;
    }

    // ---- Excel ----------------------------------------------------------

    // Adds the date / time number formats to styles.xml once and returns
    // the cellXfs indexes to use.
    function addStyles(styles) {
        var ns = styles.documentElement.namespaceURI;
        var numFmts = styles.getElementsByTagName('numFmts')[0];
        var cellXfs = styles.getElementsByTagName('cellXfs')[0];
        if (!numFmts || !cellXfs) {
            return null;
        }

        var formats = {
            date: 'yyyy-mm-dd',
            datetime: 'yyyy-mm-dd hh:mm',
            time: 'h:mm AM/PM',
            integer: '#,##0',
            decimal: '#,##0.00',
            percent: '0.00%'
        };
        var maxId = 163;
        $(numFmts).children().each(function () {
            maxId = Math.max(maxId, parseInt(this.getAttribute('numFmtId'), 10) || 0);
        });

        var idx = {};
        $.each(formats, function (key, code) {
            var id = ++maxId;
            var nf = styles.createElementNS(ns, 'numFmt');
            nf.setAttribute('numFmtId', id);
            nf.setAttribute('formatCode', code);
            numFmts.appendChild(nf);

            var xf = styles.createElementNS(ns, 'xf');
            xf.setAttribute('numFmtId', id);
            xf.setAttribute('fontId', '0');
            xf.setAttribute('fillId', '0');
            xf.setAttribute('borderId', '0');
            xf.setAttribute('applyFont', '1');
            xf.setAttribute('applyFill', '1');
            xf.setAttribute('applyBorder', '1');
            xf.setAttribute('applyNumberFormat', '1');
            idx[key] = $(cellXfs).children('xf').length;
            cellXfs.appendChild(xf);
        });

        numFmts.setAttribute('count', $(numFmts).children().length);
        cellXfs.setAttribute('count', $(cellXfs).children('xf').length);
        return idx;
    }

    function setNumberCell(sheet, cell, value, style) {
        var ns = sheet.documentElement.namespaceURI;
        while (cell.firstChild) {
            cell.removeChild(cell.firstChild);
        }
        cell.removeAttribute('t');
        cell.setAttribute('s', style);
        var v = sheet.createElementNS(ns, 'v');
        v.appendChild(sheet.createTextNode(String(value)));
        cell.appendChild(v);
    }

    function setTextCell(sheet, cell, text) {
        var ns = sheet.documentElement.namespaceURI;
        while (cell.firstChild) {
            cell.removeChild(cell.firstChild);
        }
        cell.setAttribute('t', 'inlineStr');
        cell.removeAttribute('s');
        var is = sheet.createElementNS(ns, 'is');
        var t = sheet.createElementNS(ns, 't');
        t.appendChild(sheet.createTextNode(text));
        is.appendChild(t);
        cell.appendChild(is);
    }

    function typeExcelCells(xlsx) {
        try {
            var sheet = xlsx.xl.worksheets['sheet1.xml'];
            var styles = xlsx.xl['styles.xml'];
            var st = addStyles(styles);
            if (!st) {
                return;
            }

            $('sheetData row c', sheet).each(function () {
                var cell = this;
                var s = cell.getAttribute('s');
                // Title (51) and header / footer (2) rows stay text.
                if (s === '2' || s === '51') {
                    return;
                }
                if (cell.getAttribute('t') !== 'inlineStr') {
                    // DataTables already made plain digits a number — but a
                    // 12-digit NIC etc. must stay text (else 2.00012E+11).
                    var v = $(cell).children('v').text();
                    if (/^\d{12,}$/.test(v)) {
                        setTextCell(sheet, cell, v);
                    }
                    return;
                }
                var text = $(cell).find('t').text();
                if (!text) {
                    return;
                }

                var dt = parseDate(text);
                if (dt) {
                    setNumberCell(sheet, cell, excelSerial(dt), dt.t ? st.datetime : st.date);
                    return;
                }

                var t = parseTime($.trim(text));
                if (t && t.ampm) {
                    setNumberCell(sheet, cell, (t.h * 3600 + t.i * 60 + t.s) / 86400, st.time);
                    return;
                }

                var pc = parsePercent(text);
                if (pc !== null) {
                    setNumberCell(sheet, cell, pc, st.percent);
                    return;
                }

                var num = parseNumber(text);
                if (num) {
                    setNumberCell(sheet, cell, num.value, num.decimals ? st.decimal : st.integer);
                }
            });
        } catch (err) {
            // Never block the export — worst case the cell stays text.
            if (window.console) {
                console.warn('Excel export typing skipped:', err);
            }
        }
    }

    // ---- CSV ------------------------------------------------------------

    function csvValue(val) {
        if (val === null || val === undefined || typeof val === 'number') {
            return val;
        }
        var text = $.trim(String(val));
        if (!text) {
            return val;
        }

        var dt = parseDate(text);
        if (dt) {
            var out = dt.y + '-' + pad(dt.m) + '-' + pad(dt.d);
            if (dt.t) {
                out += ' ' + pad(dt.t.h) + ':' + pad(dt.t.i);
            }
            return out;
        }

        var num = parseNumber(text);
        if (num) {
            return String(num.value);
        }

        // Keep leading-zero codes / phone numbers and long digit strings
        // (NIC) as text when opened in Excel.
        if (/^0\d+$/.test(text) || /^\d{12,}$/.test(text)) {
            return '="' + text + '"';
        }
        return val;
    }

    function typeCsvData(data) {
        try {
            $.each(data.body || [], function (r, row) {
                for (var c = 0; c < row.length; c++) {
                    row[c] = csvValue(row[c]);
                }
            });
        } catch (err) {
            if (window.console) {
                console.warn('CSV export typing skipped:', err);
            }
        }
    }

    // ---- hook into every excel / csv button -----------------------------

    function wrapAction(name, hook) {
        var def = buttons[name];
        if (!def || typeof def.action !== 'function' || def._typedExport) {
            return;
        }
        var original = def.action;
        def._typedExport = true;
        def.action = function (e, dt, node, config) {
            if (config && config.typedExport !== false && !config._typedWrapped) {
                hook(config);
                config._typedWrapped = true;
            }
            return original.apply(this, arguments);
        };
    }

    wrapAction('excelHtml5', function (config) {
        var own = config.customize;
        config.customize = function (xlsx) {
            typeExcelCells(xlsx);
            if (typeof own === 'function') {
                return own.apply(this, arguments);
            }
        };
    });

    // CSV has no customize hook of its own in this Buttons version, so the
    // exported data is adjusted through exportOptions.customizeData (copied,
    // since a page may share one exportOptions object between buttons).
    wrapAction('csvHtml5', function (config) {
        var opts = $.extend({}, config.exportOptions || {});
        var own = opts.customizeData;
        opts.customizeData = function (data) {
            if (typeof own === 'function') {
                own.apply(this, arguments);
            }
            typeCsvData(data);
        };
        config.exportOptions = opts;
    });

    // Exposed for testing / reuse.
    window.DtExportTypes = {
        parseDate: parseDate,
        parseNumber: parseNumber,
        parseTime: parseTime,
        typeExcelCells: typeExcelCells,
        typeCsvData: typeCsvData
    };
})(window.jQuery);
