<?php
namespace concepture\yii2logic\converters;

/**
 * Класс для конвертации локали
 *
 * Class LocaleConverter
 * @package concepture\yii2logic\converters
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LocaleConverter extends Converter
{
    public static function key($value)
    {
        return $value;
    }

    public static function value($key)
    {
        return $key;
    }
}
