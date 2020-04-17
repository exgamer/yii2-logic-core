<?php

/**
 * Charset From String
 * Identifies predominant character set in a string.
 *
 * @version    2.2 (2017-07-15 07:39:00 GMT)
 * @author     Peter Kahl <peter.kahl@colossalmind.com>
 * @since      2017-01-27
 * @license    Apache License, Version 2.0
 *
 * Copyright 2017 Peter Kahl <peter.kahl@colossalmind.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *      <http://www.apache.org/licenses/LICENSE-2.0>
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace concepture\yii2logic\helpers;

use \Exception;

class CharsetFromString {

    #===================================================================

    public static function getCharset($str) {
        if (empty($str)) {
            throw new Exception('Argument str cannot be empty');
        }
        $ord = self::unistr2ords($str);
        return self::getDominant($ord);
    }

    #===================================================================

    private static function getDominant($arr) {
        $score = array(
            'ARABIC'     => 0,
            'ARMENIAN'   => 0,
            'BENGALI'    => 0,
            'BURMESE'    => 0,
            'CJK'        => 0,
            'CYRILLIC'   => 0,
            'DEVANAGARI' => 0,
            'GEORGIAN'   => 0,
            'GREEK'      => 0,
            'GUJARATI'   => 0,
            'HEBREW'     => 0,
            'JAPANESE'   => 0,
            'KHMER'      => 0,
            'KOREAN'     => 0,
            'LAO'        => 0,
            'LATIN'      => 0,
            'MALAYALAM'  => 0,
            'SINHALA'    => 0,
            'TAMIL'      => 0,
            'THAI'       => 0,
            'TIBETAN'    => 0,
            'OTHER'      => 0,
        );
        foreach ($arr as $n) {
            $type = self::ord2charset($n);
            $score[$type]++;
        }
        $score = array_filter($score);
        arsort($score);
        $score = array_keys($score);
        if ($score[0] == 'CJK' && isset($score[1]) && $score[1] == 'JAPANESE') {
            return 'JAPANESE';
        }
        return $score[0];
    }

    #===================================================================

    /**
     * Identifies charset by ordinal value.
     * @var integer
     * http://www.ssec.wisc.edu/~tomw/java/unicode.html
     */
    private static function ord2charset($n) {
        if ($n >= 0 && $n <= 64) {
            return 'OTHER'; # ASCII not printable
        }
        elseif ($n >= 65 && $n <= 90) {
            return 'LATIN'; # LATIN-BASIC
        }
        elseif ($n >= 91 && $n <= 96) {
            return 'OTHER'; # ASCII not printable
        }
        elseif ($n >= 97 && $n <= 122) {
            return 'LATIN'; # LATIN-BASIC
        }
        elseif ($n >= 123 && $n <= 127) {
            return 'OTHER'; # ASCII not printable
        }
        elseif ($n >= 128 && $n <= 255) {
            return 'LATIN'; # LATIN-SUPPL
        }
        elseif ($n >= 256 && $n <= 383) {
            return 'LATIN'; # LATIN-EXT-A
        }
        elseif ($n >= 384 && $n <= 591) {
            return 'LATIN'; # LATIN-EXT-B
        }
        elseif ($n >= 880 && $n <= 1023) {
            return 'GREEK';
        }
        elseif ($n >= 1024 && $n <= 1279) {
            return 'CYRILLIC';
        }
        elseif ($n >= 1328 && $n <= 1423) {
            return 'ARMENIAN';
        }
        elseif ($n >= 1424 && $n <= 1535) {
            return 'HEBREW';
        }
        elseif ($n >= 1536 && $n <= 1791) {
            return 'ARABIC';
        }
        elseif ($n >= 2304 && $n <= 2431) {
            return 'DEVANAGARI';
        }
        elseif ($n >= 2432 && $n <= 2559) {
            return 'BENGALI';
        }
        elseif ($n >= 2688 && $n <= 2815) {
            return 'GUJARATI';
        }
        elseif ($n >= 2944 && $n <= 3071) {
            return 'TAMIL';
        }
        elseif ($n >= 3328 && $n <= 3455) {
            return 'MALAYALAM';
        }
        elseif ($n >= 3456 && $n <= 3583) {
            return 'SINHALA';
        }
        elseif ($n >= 3584 && $n <= 3711) {
            return 'THAI';
        }
        elseif ($n >= 3712 && $n <= 3839) {
            return 'LAO';
        }
        elseif ($n >= 3840 && $n <= 4095) {
            return 'TIBETAN';
        }
        elseif ($n >= 4096 && $n <= 4255) {
            return 'BURMESE';
        }
        elseif ($n >= 4256 && $n <= 4351) {
            return 'GEORGIAN';
        }
        elseif ($n >= 4352 && $n <= 4607) {
            return 'KOREAN'; # HANGUL-JAMO
        }
        elseif ($n >= 6016 && $n <= 6143) {
            return 'KHMER';
        }
        elseif ($n >= 7680 && $n <= 7935) {
            return 'LATIN'; # LATIN-EXT-ADDL
        }
        elseif ($n >= 7936 && $n <= 8191) {
            return 'GREEK'; # Greek Extended
        }
        elseif ($n >= 12288 && $n <= 12351) {
            return 'OTHER'; # CJK Punctuation
        }
        elseif ($n >= 12352 && $n <= 12447) {
            return 'JAPANESE'; # Hiragana
        }
        elseif ($n >= 12448 && $n <= 12543) {
            return 'JAPANESE'; # Katakana
        }
        elseif ($n >= 13312 && $n <= 19893) {
            return 'CJK'; # CJK Unified Ideographs Extension A
        }
        elseif ($n >= 19968 && $n <= 40959) {
            return 'CJK'; # CJK Unified Ideographs
        }
        elseif ($n >= 44032 && $n <= 55203) {
            return 'KOREAN'; # Hangul Syllables
        }
        return 'OTHER';
    }

    #===================================================================

    /**
     * Turns a string of utf-8 characters into an array of ordinal values.
     *
     */
    private static function unistr2ords($str) {
        $ords = array();
        $len = iconv_strlen($str);
        for($i = 0; $i < $len; $i++) {
            $char = iconv_substr($str, $i, 1);
            $val = self::utf8ToUnicode($char);
            if ($val !== false && array_key_exists(0, $val)) {
                $ords[] = $val[0];
            }
        }
        return $ords;
    }

    #===================================================================

    /**
     * Takes an UTF-8 string and returns an array of integers representing the
     * Unicode characters.
     */
    private static function utf8ToUnicode($str) {
        $new = array();
        $len = iconv_strlen($str);
        for ($n = 0; $n < $len; $n++) {
            $char = iconv_substr($str, $n, 1);
            $new[] = self::uniord($char);
        }
        return $new;
    }

    #===================================================================

    private static function uniord($c) {
        $ord0 = ord($c{0}); if ($ord0 >= 0   && $ord0 <= 127) return  $ord0;
        $ord1 = ord($c{1}); if ($ord0 >= 192 && $ord0 <= 223) return ($ord0 - 192) *     64 + ($ord1 - 128);
        $ord2 = ord($c{2}); if ($ord0 >= 224 && $ord0 <= 239) return ($ord0 - 224) *   4096 + ($ord1 - 128) *   64 + ($ord2 - 128);
        $ord3 = ord($c{3}); if ($ord0 >= 240 && $ord0 <= 247) return ($ord0 - 240) * 262144 + ($ord1 - 128) * 4096 + ($ord2 - 128) * 64 + ($ord3 - 128);
        return false;
    }

    #===================================================================
}

