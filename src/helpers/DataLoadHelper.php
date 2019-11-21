<?php

namespace concepture\yii2logic\helpers;


use yii\db\ActiveRecord;

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
     * @param object|array|json $from
     * @param object|array|json $to
     * @param bool $ignoreEmpty
     * @return mixed
     */
    public static function loadData($from, $to, $ignoreEmpty = false)
    {
        /**
         * Чтобы можно было загружать данные из json строки
         */
        if(
            is_string($from)
            && StringHelper::isJson($from)
        ) {
            $from = StringHelper::jsonToArray($from);
        }

        $fromKeys = [];
        if (is_object($from)){
            if ($from instanceof ActiveRecord){
                $fromKeys = $from->attributes();
            }else {
                /**
                 * TODO Можно будет добавить другие варианты
                 */
                $fromKeys = get_object_vars($from);
            }
        }

        if (is_array($from)){
            $fromKeys = array_keys($from);
        }

        $returnJson = false;
        /**
         * Чтобы можно было загружать данные в json строку
         */
        if(
            is_string($to)
            && StringHelper::isJson($to)
        ) {
            $to = StringHelper::jsonToArray($to);
            $returnJson = true;
        }

        foreach ($fromKeys as $key){
            $to = static::loadByKey($from, $to, $key, $ignoreEmpty);
        }

        if ($returnJson){

            return json_encode($to);
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
            if (property_exists($to, $key)) {
                $to->{$key} = $newValue;
            }
        }

        if (is_array($to)){
            if (isset($to[$key])) {
                $to[$key] = $newValue;
            }
        }

        return  $to;
    }
}