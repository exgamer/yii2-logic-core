<?php

namespace concepture\yii2logic\helpers;


/**
 * Вспомогательный класс для действии с данными между обьектами
 *
 * Class DataLoadHelper
 * @package concepture\yii2logic\helpers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DataLoadHelper
{
    /**
     * Загружает данные из одного в другое
     *
     * @param $from
     * @param $to
     * @param bool $ignoreEmpty
     * @return mixed
     */
    public static function loadData($from, $to, $ignoreEmpty = false)
    {
        $fromKeys = [];
        if (is_object($from)){
            $fromKeys = get_object_vars($from);
        }

        if (is_array($from)){
            $fromKeys = array_keys($from);
        }

        foreach ($fromKeys as $key){
            $to = static::loadByKey($from, $to, $key);
        }

        return  $to;
    }

    /**
     * Загружкет куда либо данные по ключу или аттрибуту
     *
     * @param $from
     * @param $to
     * @param $key
     * @param bool $ignoreEmpty
     * @return array
     */
    public static function loadByKey($from, $to, $key, $ignoreEmpty = false)
    {
        $newValue = null;
        if (is_object($from)){
            $newValue =  $from->{$key};
        }

        if (is_array($from)){
            $newValue =  $from[$key];
        }

        if ($ignoreEmpty && empty($newValue)){

            return  $to;
        }

        if (is_object($to)){
            $to->{$key} =  $newValue;
        }

        if (is_array($to)){
            $to[$key] =  $newValue;
        }

        return  $to;
    }
}