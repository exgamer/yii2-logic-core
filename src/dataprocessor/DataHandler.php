<?php

namespace concepture\yii2logic\dataprocessor;

use concepture\yii2logic\console\traits\OutputTrait;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\data\ActiveDataProvider;

/**
 * Вспомогательный класс для обработки данных
 *
 * @author CitizenZet
 */
abstract class DataHandler implements DataHandlerInterface
{
    use OutputTrait;

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->getService()->getQuery();
    }

    /**
     * @param $config
     * @return ActiveDataProvider
     */
    public function getDataProvider($config)
    {
        return $this->getService()->getDataProvider([], $config);
    }

    /**
     * признак работать ли дальше
     * @param $processor
     * @param type $inputData
     * @return bool
     */
    public function isExecute(DataProcessor $processor, &$inputData = null)
    {
        return true;
    }

    /**
     * Действия до запуска query
     * @param array $inputData
     */
    public function beforeExecute(DataProcessor $processor, &$inputData = null){}
    /**
     * дествия после завершения всех запросов
     * @param array $inputData
     */
    public function afterExecute(DataProcessor $processor, &$inputData = null){}
    /**
     * Действия после завершения операции
     * @param type $data
     * @param type $inputData
     */
    public function finishProcessModel(DataProcessor $processor, &$data, &$inputData = null){}

    /**
     * Действия после завершения обработки 1 страницы данных
     * @param type $inputData
     */
    public function afterPageProcess(DataProcessor $processor, &$inputData = null){}
    public function beforePageProcess(DataProcessor $processor, &$inputData = null){}
    /**
     * returns array of prepared data
     * вносим необходимые изменения в данные
     *
     * return array
     */
    public function prepareModel(DataProcessor $processor, &$data){}

    /**
     * Выполняем необходимые действия с данными
     */
    public function processModel(DataProcessor $processor, &$data, &$inputData = null){}

    /**
     * настройка основного запроса
     * @param ActiveQuery $query
     * @param null $inputData
     */
    public function setupQuery(ActiveQuery $query, $inputData = null){}

    /**
     * Показываем сообщение после выполнения
     */
    public function showMessage(DataProcessor $processor, $isUpdate, $model, $endMessage){}
}