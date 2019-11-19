<?php

namespace concepture\yii2logic\helpers;

use yii\helpers\StringHelper as BaseHelper;
use yii\helpers\ArrayHelper;

/**
 * Вспомогательный класс для работы со строками
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class StringHelper extends BaseHelper
{
    /**
     * Проверка строки на json
     *
     * @param string $string
     * @param array $decode
     *
     * @return boolean
     */
    public static function isJson($string, &$decode = null)
    {
        $decode = json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * Приобразует json в массив
     *
     * @param string $data
     * @return array
     */
    public static function jsonToArray(string $json)
    {
        $decode = [];
        if (! static::isJson($json, $decode)) {
            return false;
        }

        return ArrayHelper::toArray($decode);
    }

    /**
     * Разбиваем строку по заглавным буквам
     *
     * @param $string
     *
     * @return array[]|false|string[]
     */
    public static function splitStringByBigSymbol($string)
    {
        return preg_split('/(?<=[a-z])(?=[A-Z])/u', $string);
    }

    /**
     * Генерация случайно строки заданной длинны
     *
     * @param integer $length
     *
     * @return string
     */
    public static function generateRandomString($length = 8)
    {
        return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
    }
}