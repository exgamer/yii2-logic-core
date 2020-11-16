<?php

namespace concepture\yii2logic\helpers;

use Yii;
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
     * Форматы чисел для стран по iso
     *
     * @var string[][]
     */
    public static $number_formats = [
        'default' => ['.', ' '],
        'en' => ['.', ','],
        'fr' => [',', ' '],
    ];

    /**
     * Форматирует целое число по стране
     *
     * @param $number
     * @param int $decimals
     * @param string $country_iso
     * @return bool
     */
    public static function integerFormat($number, $country_iso = 'default')
    {
        return static::numberFormat($number, 0, $country_iso);
    }

    public static function numberFormat($number, $decimals = 0, $country_iso = 'default')
    {
        if (isset(static::$number_formats[$country_iso])){
            return number_format($number, $decimals, static::$number_formats[$country_iso][0], static::$number_formats[$country_iso][1]);
        }

        return number_format($number);
    }

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
     * проверка что строка это валидный json массив
     *
     * @param $string
     * @param null $decode
     * @return bool
     */
    public static function isJsonArray($string, &$decode = null)
    {
        $decode = json_decode($string);
        if (json_last_error() != JSON_ERROR_NONE) {

            return false;
        }

        if ($decode == $string) {
            return false;
        }

        return true;
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

    /**
     * Проверка содердит ли строка HTML
     *
     * @param $string
     * @return bool
     */
    public static function isHTML($string)
    {
        return $string != strip_tags($string) ? true:false;
    }

    /**
     * Возвращает числа в формате 725 тыс.,   824 млн.
     *
     * @param $number
     * @param int $smallestAccepted
     * @param int $decimals
     * @return int|string
     */
    public static function numberHumanize($number, $smallestAccepted=1000, $decimals = 1) {
        $number = intval($number);
        if($number < $smallestAccepted) return $number;

        if($number < 100) {
            return static::resolveDouble($number, $decimals);
        }

        if($number < 1000) {
            $newValue = $number / 100;
            return static::resolveDouble($newValue, $decimals) . " " . Yii::t('common', "hundred (short)");
        }

        if($number < 1000000) {
            $newValue = $number / 1000.0;

            return static::resolveDouble($newValue, $decimals) . " "  . Yii::t('common', "thousand (short)");
        }

        if($number < 1000000000) {
            $newValue = $number / 1000000.0;
            return static::resolveDouble($newValue, $decimals) . " "  . Yii::t('common', "million (short)");
        }

        // senseless on a 32 bit system probably.
        if($number < 1000000000000) {
            $newValue = $number / 1000000000.0;
            return static::resolveDouble($newValue, $decimals) . " "  . Yii::t('common', "billion (short)");
        }

        if($number < 1000000000000000) {
            $newValue = $number / 1000000000000.0;
            return static::resolveDouble($newValue, $decimals) . " "  . Yii::t('common', "trillion (short)");
        }

        return $number;	// too big.
    }

    /**
     * Возвращает форматированное число с дробью и если после запятой нули приводит их инту
     *
     * @param double $number
     * @param int $decimals
     * @param string $decimal
     * @param string $separator
     * @return int|string
     */
    private static function resolveDouble($number, $decimals=0, $decimal='.', $separator=',') {
        $result = number_format($number, $decimals, $decimal, $separator);
        $fractionCheck = $result - floor($result);
        if (! $fractionCheck) {
            return (int) $result;
        }

        return $result;
    }
}