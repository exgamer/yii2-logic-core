<?php
namespace concepture\yii2logic\enum;

use ReflectionClass;

/**
 * Класс для хранения констант
 *
 * Class Enum
 * @package concepture\yii2logic\enum
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Enum {

    /**
     * Возвращает массив со значениями констант
     *
     * [
     *    1,
     *    2,
     *    3,
     *  ]
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function values() {
        $constants = static::all();
        $constants = array_values($constants);
        $constants = array_unique($constants);

        return $constants;
    }

    /**
     * Возвращает массив констант
     *
     * [
     *   "LOCALE_ID_RU",
     *   "LOCALE_ID_ES",
     *   "LOCALE_ID_RO",
     * ]
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function keys() {
        $constants = static::all();
        $constants = array_keys($constants);

        return $constants;
    }

    /**
     * Возвращает массив констант со значениями
     *
     *  [
     *   "LOCALE_ID_RU" => 1
     *   "LOCALE_ID_ES" => 2
     *   "LOCALE_ID_RO" => 3
     *   ]
     *
     * @return array
     * @throws \ReflectionException
     */
    public static function all() {
        $className = get_called_class();
        $class = new ReflectionClass($className);
        $constants = $class->getConstants();

        return $constants;
    }

    /**
     * Возвращает массив где ключ значение константы а значение метка
     *
     * [
     *   1 => "ru"
     *   2 => "es"
     *   3 => "ro"
     *  ]
     *
     * Для получения массива с ключами метками используем $reverse = true
     * @param bool $reverse
     * Для получения массива где метки это и ключи и значения
     * @param bool $labelsAsKeys
     * @return array
     * @throws ReflectionException
     */
    public static function arrayList($reverse = false, $labelsAsKeys = false) {
        $values = self::all();
        $list = [];

        foreach ($values as $value){
            $key = $value;
            if ($labelsAsKeys){
                $key = self::label($value);
            }
            $list[$key] = self::label($value);
        }

        if ($reverse){
            return array_flip($list);
        }

        return $list;
    }

    /**
     * Возвращает метку по значению константы из self::labels()
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public static function label($key, $default = null) {
        $labels = static::labels();

        if (isset($labels[$key])){
            return $labels[$key];
        }

        return $default;
    }

    /**
     * Вовзращает значение константы по метке из self::labels()
     * @param $value
     * @param int $default
     * @return int |null
     */
    public static function key($value, $default = 0) {
        $labels = static::labels();
        $labels = array_flip($labels);

        if (isset($labels[$value])){
            return $labels[$value];
        }

        return $default;
    }

    /**
     * Массив с метками дял констант
     * @return array
     */
    public static function labels()
    {
        return [];
    }

    public function __toString()
    {
        return "";
    }
}
