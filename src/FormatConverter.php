<?php
/**
 * @author Christoph Möke <christophmoeke@gmail.com>
 * @copyright Copyright (c) 2019 Finally a fast
 * @license https://www.finally-a-fast.com/packages/fafcms-helpers/license MIT
 * @link https://www.finally-a-fast.com/packages/fafcms-helpers
 * @see https://www.finally-a-fast.com/packages/fafcms-helpers/docs Documentation of fafcms-helpers
 * @since File available since Release 1.0.0
 */

namespace fafcms\helpers;

use Yii;
use IntlDateFormatter;

/**
 * Class FormatConverter
 * @package fafcms\helpers
 */
class FormatConverter extends \yii\helpers\FormatConverter
{
    /**
     * @var array
     */
    public static $mysqlFallbackDatePatterns = [
        'short' => [
            'date' => 'n/j/y',
            'time' => 'H:i',
            'datetime' => 'n/j/y H:i',
        ],
        'medium' => [
            'date' => 'M j, Y',
            'time' => 'g:i:s A',
            'datetime' => 'M j, Y g:i:s A',
        ],
        'long' => [
            'date' => 'F j, Y',
            'time' => 'g:i:sA',
            'datetime' => 'F j, Y g:i:sA',
        ],
        'full' => [
            'date' => 'l, F j, Y',
            'time' => 'g:i:sA T',
            'datetime' => 'l, F j, Y g:i:sA T',
        ],
    ];

    /**
     * @var array
     */
    private static $_icuShortFormats = [
        'short' => 3, // IntlDateFormatter::SHORT,
        'medium' => 2, // IntlDateFormatter::MEDIUM,
        'long' => 1, // IntlDateFormatter::LONG,
        'full' => 0, // IntlDateFormatter::FULL,
    ];

    /**
     * @param $pattern
     * @param string $type
     * @param null $locale
     * @return string
     */
    public static function convertDateIcuToMysql($pattern, $type = 'date', $locale = null)
    {
        if (isset(self::$_icuShortFormats[$pattern])) {
            if (extension_loaded('intl')) {
                if ($locale === null) {
                    $locale = Yii::$app->language;
                }
                if ($type === 'date') {
                    $formatter = new IntlDateFormatter($locale, self::$_icuShortFormats[$pattern], IntlDateFormatter::NONE);
                } elseif ($type === 'time') {
                    $formatter = new IntlDateFormatter($locale, IntlDateFormatter::NONE, self::$_icuShortFormats[$pattern]);
                } else {
                    $formatter = new IntlDateFormatter($locale, self::$_icuShortFormats[$pattern], self::$_icuShortFormats[$pattern]);
                }
                $pattern = $formatter->getPattern();
            } else {
                return static::$mysqlFallbackDatePatterns[$pattern][$type];
            }
        }
        // http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
        // escaped text
        $escaped = [];
        if (preg_match_all('/(?<!\')\'(.*?[^\'])\'(?!\')/', $pattern, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $match[1] = str_replace('\'\'', '\'', $match[1]);
                $escaped[$match[0]] = '\\' . implode('\\', preg_split('//u', $match[1], -1, PREG_SPLIT_NO_EMPTY));
            }
        }

        return strtr($pattern, array_merge($escaped, [
            "''" => "\\'",  // two single quotes produce one
            'G' => '',      // era designator like (Anno Domini)
            'Y' => 'o',     // 4digit year of "Week of Year"
            'y' => '%Y',     // 4digit year e.g. 2014
            'yyyy' => '%Y',  // 4digit year e.g. 2014
            'yy' => '%y',    // 2digit year number eg. 14
            'u' => '',      // extended year e.g. 4601
            'U' => '',      // cyclic year name, as in Chinese lunar calendar
            'r' => '',      // related Gregorian year e.g. 1996
            'Q' => '',      // number of quarter
            'QQ' => '',     // number of quarter '02'
            'QQQ' => '',    // quarter 'Q2'
            'QQQQ' => '',   // quarter '2nd quarter'
            'QQQQQ' => '',  // number of quarter '2'
            'q' => '',      // number of Stand Alone quarter
            'qq' => '',     // number of Stand Alone quarter '02'
            'qqq' => '',    // Stand Alone quarter 'Q2'
            'qqqq' => '',   // Stand Alone quarter '2nd quarter'
            'qqqqq' => '',  // number of Stand Alone quarter '2'
            'M' => '%c',     // Numeric representation of a month, without leading zeros
            'MM' => '%m',    // Numeric representation of a month, with leading zeros
            'MMM' => '%b',   // A short textual representation of a month, three letters
            'MMMM' => '%M',  // A full textual representation of a month, such as January or March
            'MMMMM' => '',
            'L' => '%c',     // Stand alone month in year
            'LL' => '%m',    // Stand alone month in year
            'LLL' => '%b',   // Stand alone month in year
            'LLLL' => '%M',  // Stand alone month in year
            'LLLLL' => '',  // Stand alone month in year
            'w' => 'W',     // ISO-8601 week number of year
            'ww' => 'W',    // ISO-8601 week number of year
            'W' => '',      // week of the current month
            'd' => '%e',     // day without leading zeros
            'dd' => '%d',    // day with leading zeros
            'D' => '%j',     // day of the year 0 to 365
            'F' => '',      // Day of Week in Month. eg. 2nd Wednesday in July
            'g' => '',      // Modified Julian day. This is different from the conventional Julian day number in two regards.
            'E' => '%a',     // day of week written in short form eg. Sun
            'EE' => '%a',
            'EEE' => '%a',
            'EEEE' => '%w',  // day of week fully written eg. Sunday
            'EEEEE' => '',
            'EEEEEE' => '',
            'e' => 'N',     // ISO-8601 numeric representation of the day of the week 1=Mon to 7=Sun
            'ee' => 'N',    // php 'w' 0=Sun to 6=Sat isn't supported by ICU -> 'w' means week number of year
            'eee' => '%a',
            'eeee' => '%w',
            'eeeee' => '',
            'eeeeee' => '',
            'c' => 'N',     // ISO-8601 numeric representation of the day of the week 1=Mon to 7=Sun
            'cc' => 'N',    // php 'w' 0=Sun to 6=Sat isn't supported by ICU -> 'w' means week number of year
            'ccc' => '%a',
            'cccc' => '%w',
            'ccccc' => '',
            'cccccc' => '',
            'a' => 'A',     // AM/PM marker
            'h' => 'g',     // 12-hour format of an hour without leading zeros 1 to 12h
            'hh' => 'h',    // 12-hour format of an hour with leading zeros, 01 to 12 h
            'H' => 'G',     // 24-hour format of an hour without leading zeros 0 to 23h
            'HH' => 'H',    // 24-hour format of an hour with leading zeros, 00 to 23 h
            'k' => '',      // hour in day (1~24)
            'kk' => '',     // hour in day (1~24)
            'K' => '',      // hour in am/pm (0~11)
            'KK' => '',     // hour in am/pm (0~11)
            'm' => 'i',     // Minutes without leading zeros, not supported by php but we fallback
            'mm' => 'i',    // Minutes with leading zeros
            's' => 's',     // Seconds, without leading zeros, not supported by php but we fallback
            'ss' => 's',    // Seconds, with leading zeros
            'S' => '',      // fractional second
            'SS' => '',     // fractional second
            'SSS' => '',    // fractional second
            'SSSS' => '',   // fractional second
            'A' => '',      // milliseconds in day
            'z' => 'T',     // Timezone abbreviation
            'zz' => 'T',    // Timezone abbreviation
            'zzz' => 'T',   // Timezone abbreviation
            'zzzz' => 'T',  // Timezone full name, not supported by php but we fallback
            'Z' => 'O',     // Difference to Greenwich time (GMT) in hours
            'ZZ' => 'O',    // Difference to Greenwich time (GMT) in hours
            'ZZZ' => 'O',   // Difference to Greenwich time (GMT) in hours
            'ZZZZ' => '\G\M\TP', // Time Zone: long localized GMT (=OOOO) e.g. GMT-08:00
            'ZZZZZ' => '',  //  TIme Zone: ISO8601 extended hms? (=XXXXX)
            'O' => '',      // Time Zone: short localized GMT e.g. GMT-8
            'OOOO' => '\G\M\TP', //  Time Zone: long localized GMT (=ZZZZ) e.g. GMT-08:00
            'v' => '\G\M\TP', // Time Zone: generic non-location (falls back first to VVVV and then to OOOO) using the ICU defined fallback here
            'vvvv' => '\G\M\TP', // Time Zone: generic non-location (falls back first to VVVV and then to OOOO) using the ICU defined fallback here
            'V' => '',      // Time Zone: short time zone ID
            'VV' => 'e',    // Time Zone: long time zone ID
            'VVV' => '',    // Time Zone: time zone exemplar city
            'VVVV' => '\G\M\TP', // Time Zone: generic location (falls back to OOOO) using the ICU defined fallback here
            'X' => '',      // Time Zone: ISO8601 basic hm?, with Z for 0, e.g. -08, +0530, Z
            'XX' => 'O, \Z', // Time Zone: ISO8601 basic hm, with Z, e.g. -0800, Z
            'XXX' => 'P, \Z',    // Time Zone: ISO8601 extended hm, with Z, e.g. -08:00, Z
            'XXXX' => '',   // Time Zone: ISO8601 basic hms?, with Z, e.g. -0800, -075258, Z
            'XXXXX' => '',  // Time Zone: ISO8601 extended hms?, with Z, e.g. -08:00, -07:52:58, Z
            'x' => '',      // Time Zone: ISO8601 basic hm?, without Z for 0, e.g. -08, +0530
            'xx' => 'O',    // Time Zone: ISO8601 basic hm, without Z, e.g. -0800
            'xxx' => 'P',   // Time Zone: ISO8601 extended hm, without Z, e.g. -08:00
            'xxxx' => '',   // Time Zone: ISO8601 basic hms?, without Z, e.g. -0800, -075258
            'xxxxx' => '',  // Time Zone: ISO8601 extended hms?, without Z, e.g. -08:00, -07:52:58
        ]));
    }

    /**
     * @var array
     */
    public static $momentFallbackDatePatterns = [
        'short' => [
            'date' => 'n/j/y',
            'time' => 'H:i',
            'datetime' => 'n/j/y H:i',
        ],
        'medium' => [
            'date' => 'M j, Y',
            'time' => 'g:i:s A',
            'datetime' => 'M j, Y g:i:s A',
        ],
        'long' => [
            'date' => 'F j, Y',
            'time' => 'g:i:sA',
            'datetime' => 'F j, Y g:i:sA',
        ],
        'full' => [
            'date' => 'l, F j, Y',
            'time' => 'g:i:sA T',
            'datetime' => 'l, F j, Y g:i:sA T',
        ],
    ];

    /**
     * @param $pattern
     * @param string $type
     * @param null $locale
     * @return string
     */
    public static function convertDateIcuToMoment($pattern, $type = 'date', $locale = null)
    {
        if (isset(self::$_icuShortFormats[$pattern])) {
            if (extension_loaded('intl')) {
                if ($locale === null) {
                    $locale = Yii::$app->language;
                }
                if ($type === 'date') {
                    $formatter = new IntlDateFormatter($locale, self::$_icuShortFormats[$pattern], IntlDateFormatter::NONE);
                } elseif ($type === 'time') {
                    $formatter = new IntlDateFormatter($locale, IntlDateFormatter::NONE, self::$_icuShortFormats[$pattern]);
                } else {
                    $formatter = new IntlDateFormatter($locale, self::$_icuShortFormats[$pattern], self::$_icuShortFormats[$pattern]);
                }
                $pattern = $formatter->getPattern();
            } else {
                return static::$momentFallbackDatePatterns[$pattern][$type];
            }
        }
        // http://userguide.icu-project.org/formatparse/datetime#TOC-Date-Time-Format-Syntax
        // escaped text
        $escaped = [];
        if (preg_match_all('/(?<!\')\'(.*?[^\'])\'(?!\')/', $pattern, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $match[1] = str_replace('\'\'', '\'', $match[1]);
                $escaped[$match[0]] = '\\' . implode('\\', preg_split('//u', $match[1], -1, PREG_SPLIT_NO_EMPTY));
            }
        }

        return strtr($pattern, array_merge($escaped, [
            "''" => "\\'",  // two single quotes produce one
            'G' => '',      // era designator like (Anno Domini)
            'Y' => '',     // 4digit year of "Week of Year"
            'y' => 'Y',     // 4digit year e.g. 2014
            'yyyy' => 'YYYY',  // 4digit year e.g. 2014
            'yy' => 'YY',    // 2digit year number eg. 14
            'u' => '',      // extended year e.g. 4601
            'U' => '',      // cyclic year name, as in Chinese lunar calendar
            'r' => '',      // related Gregorian year e.g. 1996
            'Q' => '',      // number of quarter
            'QQ' => '',     // number of quarter '02'
            'QQQ' => '',    // quarter 'Q2'
            'QQQQ' => '',   // quarter '2nd quarter'
            'QQQQQ' => '',  // number of quarter '2'
            'q' => '',      // number of Stand Alone quarter
            'qq' => '',     // number of Stand Alone quarter '02'
            'qqq' => '',    // Stand Alone quarter 'Q2'
            'qqqq' => '',   // Stand Alone quarter '2nd quarter'
            'qqqqq' => '',  // number of Stand Alone quarter '2'
            'M' => 'M',     // Numeric representation of a month, without leading zeros
            'MM' => 'MM',    // Numeric representation of a month, with leading zeros
            'MMM' => 'MMM',   // A short textual representation of a month, three letters
            'MMMM' => 'MMMM',  // A full textual representation of a month, such as January or March
            'MMMMM' => '',
            'L' => 'M',     // Stand alone month in year
            'LL' => 'MM',    // Stand alone month in year
            'LLL' => 'MMM',   // Stand alone month in year
            'LLLL' => 'MMMM',  // Stand alone month in year
            'LLLLL' => '',  // Stand alone month in year
            'w' => 'W',     // ISO-8601 week number of year
            'ww' => 'W',    // ISO-8601 week number of year
            'W' => '',      // week of the current month
            'd' => 'D',     // day without leading zeros
            'dd' => 'DD',    // day with leading zeros
            'D' => 'DDDD',     // day of the year 0 to 365
            'F' => '',      // Day of Week in Month. eg. 2nd Wednesday in July
            'g' => '',      // Modified Julian day. This is different from the conventional Julian day number in two regards.
            'E' => 'ddd',     // day of week written in short form eg. Sun
            'EE' => 'ddd',
            'EEE' => 'ddd',
            'EEEE' => 'dddd',  // day of week fully written eg. Sunday
            'EEEEE' => '',
            'EEEEEE' => '',
            'e' => 'N',     // ISO-8601 numeric representation of the day of the week 1=Mon to 7=Sun
            'ee' => 'N',    // php 'w' 0=Sun to 6=Sat isn't supported by ICU -> 'w' means week number of year
            'eee' => 'ddd',
            'eeee' => 'dddd',
            'eeeee' => '',
            'eeeeee' => '',
            'c' => 'N',     // ISO-8601 numeric representation of the day of the week 1=Mon to 7=Sun
            'cc' => 'N',    // php 'w' 0=Sun to 6=Sat isn't supported by ICU -> 'w' means week number of year
            'ccc' => 'ddd',
            'cccc' => 'dddd',
            'ccccc' => '',
            'cccccc' => '',
            'a' => 'A',     // AM/PM marker
            'h' => 'h',     // 12-hour format of an hour without leading zeros 1 to 12h
            'hh' => 'hh',    // 12-hour format of an hour with leading zeros, 01 to 12 h
            'H' => 'H',     // 24-hour format of an hour without leading zeros 0 to 23h
            'HH' => 'HH',    // 24-hour format of an hour with leading zeros, 00 to 23 h
            'k' => '',      // hour in day (1~24)
            'kk' => '',     // hour in day (1~24)
            'K' => '',      // hour in am/pm (0~11)
            'KK' => '',     // hour in am/pm (0~11)
            'm' => 'm',     // Minutes without leading zeros, not supported by php but we fallback
            'mm' => 'mm',    // Minutes with leading zeros
            's' => 's',     // Seconds, without leading zeros, not supported by php but we fallback
            'ss' => 'ss',    // Seconds, with leading zeros
            'S' => 'S',      // fractional second
            'SS' => 'SS',     // fractional second
            'SSS' => 'SSS',    // fractional second
            'SSSS' => 'SSSS',   // fractional second
            'A' => '',      // milliseconds in day
            'z' => 'z',     // Timezone abbreviation
            'zz' => 'zz',    // Timezone abbreviation
            'zzz' => '',   // Timezone abbreviation
            'zzzz' => '',  // Timezone full name, not supported by php but we fallback
            'Z' => 'Z',     // Difference to Greenwich time (GMT) in hours
            'ZZ' => 'ZZ',    // Difference to Greenwich time (GMT) in hours
            'ZZZ' => '',   // Difference to Greenwich time (GMT) in hours
            'ZZZZ' => '', // Time Zone: long localized GMT (=OOOO) e.g. GMT-08:00
            'ZZZZZ' => '',  //  TIme Zone: ISO8601 extended hms? (=XXXXX)
            'O' => '',      // Time Zone: short localized GMT e.g. GMT-8
            'OOOO' => '', //  Time Zone: long localized GMT (=ZZZZ) e.g. GMT-08:00
            'v' => '', // Time Zone: generic non-location (falls back first to VVVV and then to OOOO) using the ICU defined fallback here
            'vvvv' => '', // Time Zone: generic non-location (falls back first to VVVV and then to OOOO) using the ICU defined fallback here
            'V' => '',      // Time Zone: short time zone ID
            'VV' => '',    // Time Zone: long time zone ID
            'VVV' => '',    // Time Zone: time zone exemplar city
            'VVVV' => '', // Time Zone: generic location (falls back to OOOO) using the ICU defined fallback here
            'X' => '',      // Time Zone: ISO8601 basic hm?, with Z for 0, e.g. -08, +0530, Z
            'XX' => '', // Time Zone: ISO8601 basic hm, with Z, e.g. -0800, Z
            'XXX' => '',    // Time Zone: ISO8601 extended hm, with Z, e.g. -08:00, Z
            'XXXX' => '',   // Time Zone: ISO8601 basic hms?, with Z, e.g. -0800, -075258, Z
            'XXXXX' => '',  // Time Zone: ISO8601 extended hms?, with Z, e.g. -08:00, -07:52:58, Z
            'x' => '',      // Time Zone: ISO8601 basic hm?, without Z for 0, e.g. -08, +0530
            'xx' => '',    // Time Zone: ISO8601 basic hm, without Z, e.g. -0800
            'xxx' => '',   // Time Zone: ISO8601 extended hm, without Z, e.g. -08:00
            'xxxx' => '',   // Time Zone: ISO8601 basic hms?, without Z, e.g. -0800, -075258
            'xxxxx' => '',  // Time Zone: ISO8601 extended hms?, without Z, e.g. -08:00, -07:52:58
        ]));
    }
}
