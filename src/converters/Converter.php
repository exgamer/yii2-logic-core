<?php
namespace concepture\yii2logic\converters;

/**
 * Class Converter
 * @package concepture\yii2logic\converters
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Converter
{
    /**
     * Конвертация значения в ключ
     * @param $value
     * @return mixed
     */
    public abstract static function key($value);

    /**
     * Коневертация ключа в значение
     * @param $key
     * @return mixed
     */
    public abstract static function value($key);
}
