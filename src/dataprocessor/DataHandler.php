<?php

namespace concepture\yii2logic\dataprocessor;

use concepture\yii2logic\services\interfaces\DataHandlerInterface;
use yii\db\ActiveQuery;

/**
 * Вспомогательный класс для обработки данных
 * 
 * @author CitizenZet
 */
abstract class DataHandler implements DataHandlerInterface
{
    /**
     * признак работать ли дальше
     * @param $processor
     * @param type $inputData
     * @return bool
     */
    public static function isExecute($processor, &$inputData = null)
    {
        return true;
    }

    /**
     * Действия до запуска query
     * @param array $inputData
     */
    public static function beforeExecute($processor, &$inputData = null){}
    /**
     * дествия после завершения всех запросов
     * @param array $inputData
     */
    public static function afterExecute($processor, &$inputData = null){}
    /**
     * Действия после завершения операции
     * @param type $data
     * @param type $inputData
     */
    public static function finishProcess($processor, &$data, &$inputData = null){}
    
    /**
     * Действия после завершения обработки 1 страницы данных
     * @param type $inputData
     */
    public static function afterPageProcess($processor, &$inputData = null){}
    /**
     * returns array of prepared data
     * вносим необходимые изменения в данные
     * 
     * return array
     */
    public static function prepareData($processor, &$data){}

    /**
     * Выполняем необходимые действия с данными
     */
    public static function processData($processor, &$data, &$inputData = null){}
    
    /**
     * настройка основного запроса
     * @param ActiveQuery $query
     * @param null $inputData
     */
    public static function setupQuery(ActiveQuery $query, $inputData = null){}
    
    /**
     * Показываем сообщение после выполнения
     */
    public static function showMessage($processor, $isUpdate, $model, $endMessage){}
}

