<?php

declare(strict_types=1);

namespace Ddeboer\Imap\Message;

use Ddeboer\Imap\Exception\UnsupportedEncodingException;

final class Transcoder
{
    /**
     * @see https://dxr.mozilla.org/mozilla1.9.1/source/intl/uconv/src/charsetalias.properties
     */
    private static $charsetAliases = [
        'ascii' => 'us-ascii',
        'us-ascii' => 'us-ascii',
        'ansi_x3.4-1968' => 'us-ascii',
        '646' => 'us-ascii',
        'iso-8859-1' => 'ISO-8859-1',
        'iso-8859-2' => 'ISO-8859-2',
        'iso-8859-3' => 'ISO-8859-3',
        'iso-8859-4' => 'ISO-8859-4',
        'iso-8859-5' => 'ISO-8859-5',
        'iso-8859-6' => 'ISO-8859-6',
        'iso-8859-6-i' => 'ISO-8859-6-I',
        'iso-8859-6-e' => 'ISO-8859-6-E',
        'iso-8859-7' => 'ISO-8859-7',
        'iso-8859-8' => 'ISO-8859-8',
        'iso-8859-8-i' => 'ISO-8859-8-I',
        'iso-8859-8-e' => 'ISO-8859-8-E',
        'iso-8859-9' => 'ISO-8859-9',
        'iso-8859-10' => 'ISO-8859-10',
        'iso-8859-11' => 'ISO-8859-11',
        'iso-8859-13' => 'ISO-8859-13',
        'iso-8859-14' => 'ISO-8859-14',
        'iso-8859-15' => 'ISO-8859-15',
        'iso-8859-16' => 'ISO-8859-16',
        'iso-ir-111' => 'ISO-IR-111',
        'iso-2022-cn' => 'ISO-2022-CN',
        'iso-2022-cn-ext' => 'ISO-2022-CN',
        'iso-2022-kr' => 'ISO-2022-KR',
        'iso-2022-jp' => 'ISO-2022-JP',
        'utf-32be' => 'UTF-32BE',
        'utf-32le' => 'UTF-32LE',
        'utf-32' => 'UTF-32',
        'utf-16be' => 'UTF-16BE',
        'utf-16le' => 'UTF-16LE',
        'utf-16' => 'UTF-16',
        'windows-1250' => 'windows-1250',
        'windows-1251' => 'windows-1251',
        'windows-1252' => 'windows-1252',
        'windows-1253' => 'windows-1253',
        'windows-1254' => 'windows-1254',
        'windows-1255' => 'windows-1255',
        'windows-1256' => 'windows-1256',
        'windows-1257' => 'windows-1257',
        'windows-1258' => 'windows-1258',
        'ibm866' => 'IBM866',
        'ibm850' => 'IBM850',
        'ibm852' => 'IBM852',
        'ibm855' => 'IBM855',
        'ibm857' => 'IBM857',
        'ibm862' => 'IBM862',
        'ibm864' => 'IBM864',
        'ibm864i' => 'IBM864i',
        'utf-8' => 'UTF-8',
        'utf-7' => 'UTF-7',
        'shift_jis' => 'Shift_JIS',
        'big5' => 'Big5',
        'euc-jp' => 'EUC-JP',
        'euc-kr' => 'EUC-KR',
        'gb2312' => 'GB2312',
        'gb18030' => 'gb18030',
        'viscii' => 'VISCII',
        'koi8-r' => 'KOI8-R',
        'koi8-u' => 'KOI8-U',
        'tis-620' => 'TIS-620',
        't.61-8bit' => 'T.61-8bit',
        'hz-gb-2312' => 'HZ-GB-2312',
        'big5-hkscs' => 'Big5-HKSCS',
        'gbk' => 'x-gbk',
        'cns11643' => 'x-euc-tw',
        'x-imap4-modified-utf7' => 'x-imap4-modified-utf7',
        'x-euc-tw' => 'x-euc-tw',
        'x-mac-roman' => 'x-mac-roman',
        'x-mac-ce' => 'x-mac-ce',
        'x-mac-turkish' => 'x-mac-turkish',
        'x-mac-greek' => 'x-mac-greek',
        'x-mac-icelandic' => 'x-mac-icelandic',
        'x-mac-croatian' => 'x-mac-croatian',
        'x-mac-romanian' => 'x-mac-romanian',
        'x-mac-cyrillic' => 'x-mac-cyrillic',
        'x-mac-ukrainian' => 'x-mac-ukrainian',
        'x-mac-hebrew' => 'x-mac-hebrew',
        'x-mac-arabic' => 'x-mac-arabic',
        'x-mac-farsi' => 'x-mac-farsi',
        'x-mac-devanagari' => 'x-mac-devanagari',
        'x-mac-gujarati' => 'x-mac-gujarati',
        'x-mac-gurmukhi' => 'x-mac-gurmukhi',
        'geostd8' => 'GEOSTD8',
        'armscii-8' => 'armscii-8',
        'x-viet-tcvn5712' => 'x-viet-tcvn5712',
        'x-viet-vps' => 'x-viet-vps',
        'x-viet-vni' => 'x-viet-vni',
        'iso-10646-ucs-2' => 'UTF-16BE',
        'x-iso-10646-ucs-2-be' => 'UTF-16BE',
        'x-iso-10646-ucs-2-le' => 'UTF-16LE',
        'iso-10646-ucs-4' => 'UTF-32BE',
        'x-iso-10646-ucs-4-be' => 'UTF-32BE',
        'x-iso-10646-ucs-4-le' => 'UTF-32LE',
        'x-user-defined' => 'x-user-defined',
        'x-johab' => 'x-johab',
        'x-windows-949' => 'x-windows-949',
        'latin1' => 'ISO-8859-1',
        'iso_8859-1' => 'ISO-8859-1',
        'iso8859-1' => 'ISO-8859-1',
        'iso8859-2' => 'ISO-8859-2',
        'iso8859-3' => 'ISO-8859-3',
        'iso8859-4' => 'ISO-8859-4',
        'iso8859-5' => 'ISO-8859-5',
        'iso8859-6' => 'ISO-8859-6',
        'iso8859-7' => 'ISO-8859-7',
        'iso8859-8' => 'ISO-8859-8',
        'iso8859-9' => 'ISO-8859-9',
        'iso8859-10' => 'ISO-8859-10',
        'iso8859-11' => 'ISO-8859-11',
        'iso8859-13' => 'ISO-8859-13',
        'iso8859-14' => 'ISO-8859-14',
        'iso8859-15' => 'ISO-8859-15',
        'iso-ir-100' => 'ISO-8859-1',
        'l1' => 'ISO-8859-1',
        'ibm819' => 'ISO-8859-1',
        'cp819' => 'ISO-8859-1',
        'csisolatin1' => 'ISO-8859-1',
        'latin2' => 'ISO-8859-2',
        'iso_8859-2' => 'ISO-8859-2',
        'iso-ir-101' => 'ISO-8859-2',
        'l2' => 'ISO-8859-2',
        'csisolatin2' => 'ISO-8859-2',
        'latin3' => 'ISO-8859-3',
        'iso_8859-3' => 'ISO-8859-3',
        'iso-ir-109' => 'ISO-8859-3',
        'l3' => 'ISO-8859-3',
        'csisolatin3' => 'ISO-8859-3',
        'latin4' => 'ISO-8859-4',
        'iso_8859-4' => 'ISO-8859-4',
        'iso-ir-110' => 'ISO-8859-4',
        'l4' => 'ISO-8859-4',
        'csisolatin4' => 'ISO-8859-4',
        'cyrillic' => 'ISO-8859-5',
        'iso_8859-5' => 'ISO-8859-5',
        'iso-ir-144' => 'ISO-8859-5',
        'csisolatincyrillic' => 'ISO-8859-5',
        'arabic' => 'ISO-8859-6',
        'iso_8859-6' => 'ISO-8859-6',
        'iso-ir-127' => 'ISO-8859-6',
        'ecma-114' => 'ISO-8859-6',
        'asmo-708' => 'ISO-8859-6',
        'csisolatinarabic' => 'ISO-8859-6',
        'csiso88596i' => 'ISO-8859-6-I',
        'csiso88596e' => 'ISO-8859-6-E',
        'greek' => 'ISO-8859-7',
        'greek8' => 'ISO-8859-7',
        'sun_eu_greek' => 'ISO-8859-7',
        'iso_8859-7' => 'ISO-8859-7',
        'iso-ir-126' => 'ISO-8859-7',
        'elot_928' => 'ISO-8859-7',
        'ecma-118' => 'ISO-8859-7',
        'csisolatingreek' => 'ISO-8859-7',
        'hebrew' => 'ISO-8859-8',
        'iso_8859-8' => 'ISO-8859-8',
        'visual' => 'ISO-8859-8',
        'iso-ir-138' => 'ISO-8859-8',
        'csisolatinhebrew' => 'ISO-8859-8',
        'csiso88598i' => 'ISO-8859-8-I',
        'iso-8859-8i' => 'ISO-8859-8-I',
        'csiso88598e' => 'ISO-8859-8-E',
        'latin5' => 'ISO-8859-9',
        'iso_8859-9' => 'ISO-8859-9',
        'iso-ir-148' => 'ISO-8859-9',
        'l5' => 'ISO-8859-9',
        'csisolatin5' => 'ISO-8859-9',
        'unicode-1-1-utf-8' => 'UTF-8',
        'utf8' => 'UTF-8',
        'x-sjis' => 'Shift_JIS',
        'shift-jis' => 'Shift_JIS',
        'ms_kanji' => 'Shift_JIS',
        'csshiftjis' => 'Shift_JIS',
        'windows-31j' => 'Shift_JIS',
        'cp932' => 'Shift_JIS',
        'cseucjpkdfmtjapanese' => 'EUC-JP',
        'x-euc-jp' => 'EUC-JP',
        'csiso2022jp' => 'ISO-2022-JP',
        'iso-2022-jp-2' => 'ISO-2022-JP',
        'csiso2022jp2' => 'ISO-2022-JP',
        'csbig5' => 'Big5',
        'x-x-big5' => 'Big5',
        'zh_tw-big5' => 'Big5',
        'csueckr' => 'EUC-KR',
        'ks_c_5601-1987' => 'EUC-KR',
        'iso-ir-149' => 'EUC-KR',
        'ks_c_5601-1989' => 'EUC-KR',
        'ksc_5601' => 'EUC-KR',
        'ksc5601' => 'EUC-KR',
        'korean' => 'EUC-KR',
        'csksc56011987' => 'EUC-KR',
        '5601' => 'EUC-KR',
        'gb_2312-80' => 'GB2312',
        'iso-ir-58' => 'GB2312',
        'chinese' => 'GB2312',
        'csiso58gb231280' => 'GB2312',
        'csgb2312' => 'GB2312',
        'zh_cn.euc' => 'GB2312',
        'gb_2312' => 'GB2312',
        'x-cp1250' => 'windows-1250',
        'x-cp1251' => 'windows-1251',
        'x-cp1252' => 'windows-1252',
        'x-cp1253' => 'windows-1253',
        'x-cp1254' => 'windows-1254',
        'x-cp1255' => 'windows-1255',
        'x-cp1256' => 'windows-1256',
        'x-cp1257' => 'windows-1257',
        'x-cp1258' => 'windows-1258',
        'windows-874' => 'windows-874',
        'ibm874' => 'windows-874',
        'macintosh' => 'x-mac-roman',
        'mac' => 'x-mac-roman',
        'csMacintosh' => 'x-mac-roman',
        'cp866' => 'IBM866',
        'cp-866' => 'IBM866',
        '866' => 'IBM866',
        'csIBM866' => 'IBM866',
        'cp850' => 'IBM850',
        '850' => 'IBM850',
        'csIBM850' => 'IBM850',
        'cp852' => 'IBM852',
        '852' => 'IBM852',
        'csIBM852' => 'IBM852',
        'cp855' => 'IBM855',
        '855' => 'IBM855',
        'csIBM855' => 'IBM855',
        'cp857' => 'IBM857',
        '857' => 'IBM857',
        'csIBM857' => 'IBM857',
        'cp862' => 'IBM862',
        '862' => 'IBM862',
        'csIBM862' => 'IBM862',
        'cp864' => 'IBM864',
        '864' => 'IBM864',
        'csIBM864' => 'IBM864',
        'ibm-864' => 'IBM864',
        'cp864i' => 'IBM864i',
        '864i' => 'IBM864i',
        'csibm864i' => 'IBM864i',
        'ibm-864i' => 'IBM864i',
        't.61' => 'T.61-8bit',
        'iso-ir-103' => 'T.61-8bit',
        'csiso103t618bit' => 'T.61-8bit',
        'x-unicode-2-0-utf-7' => 'UTF-7',
        'unicode-2-0-utf-7' => 'UTF-7',
        'unicode-1-1-utf-7' => 'UTF-7',
        'csunicode11utf7' => 'UTF-7',
        'csunicode' => 'UTF-16BE',
        'csunicode11' => 'UTF-16BE',
        'iso-10646-ucs-basic' => 'UTF-16BE',
        'csunicodeascii' => 'UTF-16BE',
        'iso-10646-unicode-latin1' => 'UTF-16BE',
        'csunicodelatin1' => 'UTF-16BE',
        'iso-10646' => 'UTF-16BE',
        'iso-10646-j-1' => 'UTF-16BE',
        'latin6' => 'ISO-8859-10',
        'iso-ir-157' => 'ISO-8859-10',
        'l6' => 'ISO-8859-10',
        'csisolatin6' => 'ISO-8859-10',
        'iso_8859-15' => 'ISO-8859-15',
        'ecma-cyrillic' => 'ISO-IR-111',
        'csiso111ecmacyrillic' => 'ISO-IR-111',
        'csiso2022kr' => 'ISO-2022-KR',
        'csviscii' => 'VISCII',
        'csviqr' => 'VIQR',
        'zh_tw-euc' => 'x-euc-tw',
        'iso88591' => 'ISO-8859-1',
        'iso88592' => 'ISO-8859-2',
        'iso88593' => 'ISO-8859-3',
        'iso88594' => 'ISO-8859-4',
        'iso88595' => 'ISO-8859-5',
        'iso88596' => 'ISO-8859-6',
        'iso88597' => 'ISO-8859-7',
        'iso88598' => 'ISO-8859-8',
        'iso88599' => 'ISO-8859-9',
        'iso885910' => 'ISO-8859-10',
        'iso885911' => 'ISO-8859-11',
        'iso885912' => 'ISO-8859-12',
        'iso885913' => 'ISO-8859-13',
        'iso885914' => 'ISO-8859-14',
        'iso885915' => 'ISO-8859-15',
        'tis620' => 'TIS-620',
        'cp1250' => 'windows-1250',
        'cp1251' => 'windows-1251',
        'cp1252' => 'windows-1252',
        'cp1253' => 'windows-1253',
        'cp1254' => 'windows-1254',
        'cp1255' => 'windows-1255',
        'cp1256' => 'windows-1256',
        'cp1257' => 'windows-1257',
        'cp1258' => 'windows-1258',
        'x-obsoleted-shift_jis' => 'x-obsoleted-Shift_JIS',
        'x-obsoleted-iso-2022-jp' => 'x-obsoleted-ISO-2022-JP',
        'x-obsoleted-euc-jp' => 'x-obsoleted-EUC-JP',
        'x-gbk' => 'x-gbk',
        'windows-936' => 'windows-936',
        'ansi-1251' => 'windows-1251',
    ];

    public static function decode(string $text, string $toCharset)
    {
        $originalToCharset = $toCharset;
        $lowercaseToCharset = strtolower($toCharset);
        if (isset(self::$charsetAliases[$lowercaseToCharset])) {
            $toCharset = self::$charsetAliases[$lowercaseToCharset];
        }

        set_error_handler(function ($nr, $message) use ($originalToCharset, $toCharset) {
            throw new UnsupportedEncodingException(sprintf(
                'Unsupported charset "%s"%s: %s',
                $originalToCharset,
                ($toCharset !== $originalToCharset) ? sprintf(' (alias found: "%s")', $toCharset) : '',
                $message
            ), $nr);
        });

        $decodedText = mb_convert_encoding($text, 'UTF-8', $toCharset);

        restore_error_handler();

        return $decodedText;
    }

    public static function isUtf8Alias(string $alias)
    {
        static $utf8Aliases = [
            'utf8' => true,
            'utf-8' => true,
            'UTF8' => true,
            'UTF-8' => true,
        ];

        return isset($utf8Aliases[$alias]);
    }
}
