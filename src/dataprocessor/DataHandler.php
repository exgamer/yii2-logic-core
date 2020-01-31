<?php

namespace concepture\yii2logic\dataprocessor;

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
    public static function isExecute(DataProcessor $processor, &$inputData = null)
    {
        return true;
    }

    /**
     * Действия до запуска query
     * @param array $inputData
     */
    public static function beforeExecute(DataProcessor $processor, &$inputData = null){}
    /**
     * дествия после завершения всех запросов
     * @param array $inputData
     */
    public static function afterExecute(DataProcessor $processor, &$inputData = null){}
    /**
     * Действия после завершения операции
     * @param type $data
     * @param type $inputData
     */
    public static function finishProcess(DataProcessor $processor, &$data, &$inputData = null){}

    /**
     * Действия после завершения обработки 1 страницы данных
     * @param type $inputData
     */
    public static function afterPageProcess(DataProcessor $processor, &$inputData = null){}
    /**
     * returns array of prepared data
     * вносим необходимые изменения в данные
     *
     * return array
     */
    public static function prepareData(DataProcessor $processor, &$data){}

    /**
     * Выполняем необходимые действия с данными
     */
    public static function processData(DataProcessor $processor, &$data, &$inputData = null){}

    /**
     * настройка основного запроса
     * @param ActiveQuery $query
     * @param null $inputData
     */
    public static function setupQuery(ActiveQuery $query, $inputData = null){}

    /**
     * Показываем сообщение после выполнения
     */
    public static function showMessage(DataProcessor $processor, $isUpdate, $model, $endMessage){}
}

