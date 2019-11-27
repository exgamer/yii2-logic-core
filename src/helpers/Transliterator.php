<?php

namespace concepture\yii2logic\helpers;

class Transliterator
{


    static $lang2tr = array(
        // russian
        'й'=>'y','ц'=>'ch',
        'у'=>'u','к'=>'k',
        'е'=>'e','н'=>'n',
        'г'=>'g','ш'=>'sh',
        'щ'=>'shsh','з'=>'z',
        'х'=>'h','ъ'=>'',
        'ф'=>'f','ы'=>'y',
        'в'=>'v','а'=>'a',
        'п'=>'p','р'=>'r',
        'о'=>'o','л'=>'l',
        'д'=>'d','ж'=>'zh',
        'э'=>'e','я'=>'ya',
        'ч'=>'ch','с'=>'s',
        'м'=>'m','и'=>'i',
        'т'=>'t','ь'=>'',
        'б'=>'b','ю'=>'yu',
        'ё'=>'e','и'=>'i',
        'Й'=>'Y','Ц'=>'CH',
        'У'=>'U','К'=>'K',
        'Е'=>'E','Н'=>'N',
        'Г'=>'G','Ш'=>'SH',
        'Щ'=>'SHSH','З'=>'Z',
        'Х'=>'H','Ъ'=>'',
        'Ф'=>'F','Ы'=>'Y',
        'В'=>'V','А'=>'A',
        'П'=>'P','Р'=>'R',
        'О'=>'O','Л'=>'L',
        'Д'=>'D','Ж'=>'ZH',
        'Э'=>'E','Я'=>'YA',
        'Ч'=>'CH','С'=>'S',
        'М'=>'M','И'=>'I',
        'Т'=>'T','Ь'=>'',
        'Б'=>'B','Ю'=>'YU',
        'Ё'=>'E','И'=>'I',
        // czech
        'á'=>'a', 'ä'=>'a', 'ć'=>'c', 'č'=>'c', 'ď'=>'d', 'é'=>'e', 'ě'=>'e',
        'ë'=>'e', 'í'=>'i', 'ň'=>'n', 'ń'=>'n', 'ó'=>'o', 'ö'=>'o', 'ŕ'=>'r',
        'ř'=>'r', 'š'=>'s', 'Š'=>'S', 'ť'=>'t', 'ú'=>'u', 'ů'=>'u', 'ü'=>'u',
        'ý'=>'y', 'ź'=>'z', 'ž'=>'z',
        'і'=>'i', 'ї' => 'i', 'b' => 'b', 'І' => 'i',
        // special
        ' '=>'-', '_' => '-' ,
        '\''=>'', '"'=>'',
        '\t'=>'', '«'=>'',
        '»'=>'', '?'=>'',
        '!'=>'', '*'=>'',
        '+'=>'plus' , '№' => 'number',
        '`'=> '' , '?' => ''
    );

    /**
     * Транслит строки
     *
     * @param string $string
     * @return string
     */
    public static function translit($string)
    {
        $result = preg_replace( '/[\-]+/', '-', preg_replace( '/[^\w\-\*]/', '', strtolower( strtr( trim($string), self::$lang2tr ) ) ) );
        $result = self::utf8Normilize($result);

        return $result;
    }

    /**
     * Нормализация кодировки UTF-8
     *
     * @param string $string
     * @return string
     */
    public static function utf8Normilize($string)
    {
        $result =  mb_convert_encoding($string, 'UTF-8', 'UTF-8');
        #частный случай убирает знак вопроса после конвертации
        $result = str_replace('?', '', $result);

        return $result;
    }
}
